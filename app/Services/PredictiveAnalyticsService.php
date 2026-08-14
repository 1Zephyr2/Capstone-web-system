<?php

namespace App\Services;

/**
 * Hybrid ensemble forecasting for FurCare's business metrics (booking volume,
 * revenue, per-service demand, etc).
 *
 * "Hybrid ensemble" here means: several independent, well-established
 * forecasting methods are each run over the same historical series, then
 * combined into a single prediction — weighted by how accurate each method
 * actually was when backtested against FurCare's own recent history. A
 * method that predicted recent months well gets more say in the final
 * number; a method that did poorly gets less (or none). This is the same
 * "blending" idea behind ensemble learning in ML (e.g. stacking), just
 * built from transparent statistical methods instead of a black-box model
 * — appropriate here since there's no large training dataset or GPU/ML
 * stack involved, and every number can be explained to a reader.
 *
 * The four base methods:
 *   1. Linear Regression      — captures the overall long-term trend
 *   2. Simple Moving Average  — smooths short-term noise
 *   3. Exponential Smoothing  — leans on the most recent points
 *   4. Seasonal Naive         — reuses the value from one full cycle ago
 *                                (e.g. same month last year), when there's
 *                                enough history for that to make sense
 */
class PredictiveAnalyticsService
{
    /**
     * Forecast the next $periodsAhead points of a chronological numeric series.
     *
     * @param  array<int,float> $series        Historical values, oldest first.
     * @param  int               $periodsAhead  How many future points to predict.
     * @param  int|null          $seasonLength  Cycle length for the seasonal-naive
     *                                          method (e.g. 12 for monthly data
     *                                          with yearly seasonality). Pass null
     *                                          to disable that method entirely.
     * @return array{
     *     forecast: array<int,float>,
     *     confidence: string,
     *     weights: array<string,float>,
     *     method_forecasts: array<string,array<int,float>>
     * }
     */
    public function forecast(array $series, int $periodsAhead = 1, ?int $seasonLength = 12): array
    {
        $series = array_values($series);
        $n = count($series);

        // Not enough history to say anything meaningful.
        if ($n < 2) {
            $flat = $n === 1 ? $series[0] : 0;
            return [
                'forecast'         => array_fill(0, $periodsAhead, round($flat, 2)),
                'confidence'       => 'Low',
                'weights'          => [],
                'method_forecasts' => [],
            ];
        }

        $methods = $this->availableMethods($n, $seasonLength);

        // Backtest each method: how well would it have predicted the last
        // few known points, using only the data available before each one?
        $holdout = min(3, max(1, intdiv($n, 3)));
        $errors  = [];
        foreach ($methods as $name => $fn) {
            $errors[$name] = $this->backtestError($series, $fn, $holdout, $seasonLength);
        }

        // Turn errors into weights: lower error = more trust. A method that
        // couldn't be backtested (null) is excluded from the ensemble.
        $weights = [];
        foreach ($errors as $name => $err) {
            if ($err === null) continue;
            $weights[$name] = 1 / ($err + 1); // +1 avoids divide-by-zero and caps influence of a near-perfect fluke
        }
        $weightSum = array_sum($weights);
        if ($weightSum <= 0) {
            // Fall back to equal weighting across everything if backtesting
            // couldn't discriminate between methods (e.g. very short series).
            $weights = array_fill_keys(array_keys($methods), 1);
            $weightSum = count($weights);
        }
        foreach ($weights as $name => $w) {
            $weights[$name] = round($w / $weightSum, 3);
        }

        // Run every method forward for real, then blend.
        $methodForecasts = [];
        foreach ($methods as $name => $fn) {
            if (!isset($weights[$name])) continue;
            $methodForecasts[$name] = $fn($series, $periodsAhead, $seasonLength);
        }

        $forecast = array_fill(0, $periodsAhead, 0.0);
        foreach ($methodForecasts as $name => $points) {
            foreach ($points as $i => $val) {
                $forecast[$i] += $val * $weights[$name];
            }
        }
        $forecast = array_map(fn($v) => round(max(0, $v), 2), $forecast);

        // Confidence: rough read on how much the backtested methods agreed
        // with reality, and how much history backs the prediction up.
        $avgError  = count($errors) ? array_sum(array_filter($errors, fn($e) => $e !== null)) / max(1, count(array_filter($errors, fn($e) => $e !== null))) : null;
        $confidence = 'Low';
        if ($n >= 6 && $avgError !== null) {
            $confidence = $avgError < 0.20 ? 'High' : ($avgError < 0.45 ? 'Medium' : 'Low');
        } elseif ($n >= 4) {
            $confidence = 'Medium';
        }

        return [
            'forecast'         => $forecast,
            'confidence'       => $confidence,
            'weights'          => $weights,
            'method_forecasts' => $methodForecasts,
        ];
    }

    /**
     * Convenience: forecast just the single next point.
     */
    public function forecastNext(array $series, ?int $seasonLength = 12): array
    {
        $result = $this->forecast($series, 1, $seasonLength);
        return [
            'value'      => $result['forecast'][0] ?? 0,
            'confidence' => $result['confidence'],
            'weights'    => $result['weights'],
        ];
    }

    // ── Base forecasting methods ─────────────────────────────────────────

    private function availableMethods(int $n, ?int $seasonLength): array
    {
        $methods = [
            'linear_regression'    => [$this, 'linearRegressionForecast'],
            'moving_average'       => [$this, 'movingAverageForecast'],
            'exponential_smoothing'=> [$this, 'exponentialSmoothingForecast'],
        ];

        if ($seasonLength && $n >= $seasonLength + 1) {
            $methods['seasonal_naive'] = [$this, 'seasonalNaiveForecast'];
        }

        return $methods;
    }

    private function linearRegressionForecast(array $series, int $periodsAhead, ?int $seasonLength): array
    {
        $n = count($series);
        $sumX = $sumY = $sumXY = $sumX2 = 0;
        foreach ($series as $x => $y) {
            $sumX  += $x;
            $sumY  += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        $denominator = ($n * $sumX2 - $sumX * $sumX);
        $slope = $denominator != 0 ? ($n * $sumXY - $sumX * $sumY) / $denominator : 0;
        $intercept = ($sumY - $slope * $sumX) / $n;

        $out = [];
        for ($i = 0; $i < $periodsAhead; $i++) {
            $out[] = $intercept + $slope * ($n + $i);
        }
        return $out;
    }

    private function movingAverageForecast(array $series, int $periodsAhead, ?int $seasonLength): array
    {
        $window = min(3, count($series));
        $recent = array_slice($series, -$window);
        $avg = array_sum($recent) / count($recent);
        return array_fill(0, $periodsAhead, $avg);
    }

    private function exponentialSmoothingForecast(array $series, int $periodsAhead, ?int $seasonLength): array
    {
        $alpha = 0.4;
        $s = $series[0];
        foreach ($series as $i => $y) {
            if ($i === 0) continue;
            $s = $alpha * $y + (1 - $alpha) * $s;
        }
        return array_fill(0, $periodsAhead, $s);
    }

    private function seasonalNaiveForecast(array $series, int $periodsAhead, ?int $seasonLength): array
    {
        $n = count($series);
        $out = [];
        for ($i = 0; $i < $periodsAhead; $i++) {
            $idx = $n + $i - $seasonLength;
            $out[] = $idx >= 0 && $idx < $n ? $series[$idx] : end($series);
        }
        return $out;
    }

    /**
     * Backtest a method: repeatedly train on data up to a cutoff point,
     * predict the next known value, and measure the error against what
     * actually happened. Returns the average absolute percentage error
     * (as a fraction, e.g. 0.15 = 15% off on average), or null if there
     * wasn't enough history to test this method at all.
     */
    private function backtestError(array $series, callable $fn, int $holdout, ?int $seasonLength): ?float
    {
        $n = count($series);
        $minTrain = 3;
        if ($n - $holdout < $minTrain) {
            return null;
        }

        $errors = [];
        for ($cut = $n - $holdout; $cut < $n; $cut++) {
            $train  = array_slice($series, 0, $cut);
            $actual = $series[$cut];

            $predicted = $fn($train, 1, $seasonLength)[0] ?? null;
            if ($predicted === null) continue;

            if ($actual == 0) {
                // Avoid divide-by-zero; treat as 100% error unless the
                // prediction was also ~0.
                $errors[] = abs($predicted) < 0.5 ? 0 : 1;
            } else {
                $errors[] = abs(($actual - $predicted) / $actual);
            }
        }

        return count($errors) ? array_sum($errors) / count($errors) : null;
    }
}
