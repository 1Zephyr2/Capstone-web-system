<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Services\PredictiveAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminInsightsController extends Controller
{
    /**
     * Every figure on the Insights page is scoped to ONE selected interval
     * (week / month / 6 months / year). Per interval we define:
     *   unit     – bucket size used for the charts and the forecasting series
     *   window   – how many buckets the interval itself covers
     *   history  – how many buckets of history feed the forecast (at least 2x window,
     *              so we can also compare against the previous interval)
     *   horizon  – how many buckets ahead we forecast (= one more interval)
     *   season   – cycle length for the seasonal method (null = not enough cycles)
     */
    private const PERIODS = [
        'week'    => ['unit' => 'day',   'window' => 7,  'history' => 28, 'horizon' => 7,  'season' => 7,
                      'title' => 'Last 7 days',     'next' => 'next 7 days',   'noun' => 'day'],
        'month'   => ['unit' => 'week',  'window' => 4,  'history' => 12, 'horizon' => 4,  'season' => null,
                      'title' => 'Last 4 weeks',    'next' => 'next 4 weeks',  'noun' => 'week'],
        '6months' => ['unit' => 'month', 'window' => 6,  'history' => 18, 'horizon' => 6,  'season' => 12,
                      'title' => 'Last 6 months',   'next' => 'next 6 months', 'noun' => 'month'],
        'year'    => ['unit' => 'month', 'window' => 12, 'history' => 36, 'horizon' => 12, 'season' => 12,
                      'title' => 'Last 12 months',  'next' => 'next 12 months','noun' => 'month'],
    ];

    /** Bucket k: 0 = current, 1 = previous … negative = future. Returns [start, end, label]. */
    private function bucket(string $unit, int $k, bool $withYear = false): array
    {
        if ($unit === 'day') {
            $d = today()->subDays($k);
            return [$d->copy()->startOfDay(), $d->copy()->endOfDay(), $d->format('D j')];
        }
        if ($unit === 'week') {
            $end = today()->subDays(7 * $k)->endOfDay();
            $start = $end->copy()->subDays(6)->startOfDay();
            return [$start, $end, $start->format('M j')];
        }
        $base = now()->startOfMonth()->subMonthsNoOverflow($k);
        return [$base->copy(), $base->copy()->endOfMonth(), $base->format($withYear ? "M 'y" : 'M')];
    }

    public function index(Request $request, PredictiveAnalyticsService $predictor)
    {
        $trendPeriod = array_key_exists($request->get('period'), self::PERIODS) ? $request->get('period') : '6months';
        $cfg   = self::PERIODS[$trendPeriod];
        $unit  = $cfg['unit'];
        $W     = $cfg['window'];
        $N     = $cfg['history'];
        $H     = $cfg['horizon'];
        $withYear = $unit === 'month' && $N > 12;

        // Buckets oldest → newest
        $buckets = [];
        for ($k = $N - 1; $k >= 0; $k--) {
            [$s, $e, $label] = $this->bucket($unit, $k, $withYear);
            $buckets[] = ['start' => $s, 'end' => $e, 'label' => $label];
        }
        $windowStart = $buckets[$N - $W]['start'];
        $prevStart   = $buckets[$N - 2 * $W]['start'];
        $historyEnd  = today()->endOfDay();

        $all = Appointment::with('service')
            ->whereBetween('appointment_date', [$buckets[0]['start'], $historyEnd])
            ->get();

        $isLive = fn($a) => !in_array($a->status, [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED], true);

        // Assign every appointment to its bucket index
        $byBucket = array_fill(0, $N, collect());
        $grouped = $all->groupBy(function ($a) use ($buckets, $N) {
            for ($i = $N - 1; $i >= 0; $i--) {
                if ($a->appointment_date >= $buckets[$i]['start']) return $i;
            }
            return 0;
        });
        foreach ($grouped as $i => $rows) { $byBucket[$i] = $rows; }

        $apptSeries = []; $revSeries = [];
        foreach ($byBucket as $rows) {
            $apptSeries[] = $rows->filter($isLive)->count();
            $revSeries[]  = (float) $rows->where('status', Appointment::STATUS_COMPLETED)->sum('price');
        }

        // ── Forecast the NEXT interval (horizon buckets), summed ────────────
        $apptForecast = $predictor->forecast($apptSeries, $H, $cfg['season']);
        $revForecast  = $predictor->forecast($revSeries, $H, $cfg['season']);
        $apptFuture = array_map(fn($v) => max(0, round($v)), $apptForecast['forecast']);
        $revFuture  = array_map(fn($v) => max(0, round($v)), $revForecast['forecast']);

        $predictedAppointments = (int) array_sum($apptFuture);
        $predictedRevenue      = (float) array_sum($revFuture);
        $apptConfidence        = $apptForecast['confidence'];
        $revenueConfidence     = $revForecast['confidence'];

        // ── Scoped window data ───────────────────────────────────────────────
        $inWindow = $all->filter(fn($a) => $a->appointment_date >= $windowStart);
        $prevWindow = $all->filter(fn($a) => $a->appointment_date >= $prevStart && $a->appointment_date < $windowStart);

        $totalAppointments = $inWindow->count();
        $completedCount    = $inWindow->where('status', 'completed')->count();
        $pendingCount      = $inWindow->where('status', 'pending')->count();
        $cancelledCount    = $inWindow->whereIn('status', ['cancelled', 'rejected'])->count();
        $completionRate    = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100) : 0;
        $activeCustomers   = $inWindow->filter($isLive)->pluck('user_id')->unique()->count();
        $statusBreakdown   = $inWindow->groupBy('status')->map->count();

        $bookingCompleted = $completedCount;
        $bookingCancelled = $cancelledCount;

        $revenueEarned = (float) $inWindow->where('status', 'completed')->sum('price');
        $revenueLost   = (float) $inWindow->whereIn('status', ['cancelled', 'rejected'])->sum('price');
        $netRevenueGrowthPct = $revenueEarned > 0 ? round((($revenueEarned - $revenueLost) / $revenueEarned) * 100, 1) : 0;
        $avgRevenuePerAppointment = $completedCount > 0 ? round($revenueEarned / $completedCount, 2) : 0;

        // ── Charts: actual window buckets + forecast buckets ────────────────
        $monthlyWithForecast = [];
        $revenueMonthlyWithForecast = [];
        for ($i = $N - $W; $i < $N; $i++) {
            $monthlyWithForecast[]        = ['label' => $buckets[$i]['label'], 'count' => $apptSeries[$i]];
            $revenueMonthlyWithForecast[] = ['label' => $buckets[$i]['label'], 'total' => $revSeries[$i]];
        }
        for ($f = 0; $f < $H; $f++) {
            $label = $this->bucket($unit, -($f + 1), $withYear)[2];
            $monthlyWithForecast[]        = ['label' => $label, 'count' => $apptFuture[$f] ?? 0, 'is_forecast' => true];
            $revenueMonthlyWithForecast[] = ['label' => $label, 'total' => $revFuture[$f] ?? 0, 'is_forecast' => true];
        }
        $maxMonthly = max(array_column($monthlyWithForecast, 'count')) ?: 1;
        $maxRevenue = max(array_column($revenueMonthlyWithForecast, 'total')) ?: 1;

        $trendTitle    = 'Appointments — ' . $cfg['title'];
        $bucketNoun    = $cfg['noun'];
        $actualOnly    = array_slice($monthlyWithForecast, 0, $W);
        $periodTotal   = array_sum(array_column($actualOnly, 'count'));
        $periodAverage = round($periodTotal / max(1, $W), 1);
        $busiestBucket = collect($actualOnly)->sortByDesc('count')->first();

        $volumeMeta = [
            'window_total'    => $periodTotal,
            'predicted_total' => $predictedAppointments,
            'pct_diff'        => $periodTotal > 0 ? round((($predictedAppointments - $periodTotal) / $periodTotal) * 100, 1)
                                                  : ($predictedAppointments > 0 ? 100.0 : 0.0),
            'revenue_window'  => $revenueEarned,
            'revenue_pct'     => $revenueEarned > 0 ? round((($predictedRevenue - $revenueEarned) / $revenueEarned) * 100, 1)
                                                    : ($predictedRevenue > 0 ? 100.0 : 0.0),
        ];

        // ── Busiest weekday inside the selected interval ────────────────────
        $dayNames = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $dowCounts = $inWindow->filter($isLive)->groupBy(fn($a) => $a->appointment_date->dayOfWeek)->map->count();
        $busiestDayOfWeek = $dowCounts->isNotEmpty() ? $dowCounts->sortDesc()->keys()->first() : null;
        $busiestDayLabel = $busiestDayOfWeek !== null ? $dayNames[$busiestDayOfWeek] : null;
        $occurrences = 0;
        if ($busiestDayOfWeek !== null) {
            for ($d = $windowStart->copy(); $d->lte(today()); $d->addDay()) {
                if ((int) $d->dayOfWeek === (int) $busiestDayOfWeek) $occurrences++;
            }
        }
        $busiestDayAvg = $busiestDayOfWeek !== null ? round($dowCounts[$busiestDayOfWeek] / max(1, $occurrences), 1) : 0;
        $nextBusiestDate = null;
        if ($busiestDayOfWeek !== null) {
            $nextBusiestDate = today()->addDay();
            while ((int) $nextBusiestDate->dayOfWeek !== (int) $busiestDayOfWeek) { $nextBusiestDate->addDay(); }
        }

        // ── Services (within interval) ──────────────────────────────────────
        $allServices = Service::notArchived()->orderBy('category')->orderBy('name')->get();

        $labelCounts = $inWindow->groupBy('service_label')->map->count();
        $serviceStats = $allServices->map(fn($svc) => (object) [
            'name'  => $svc->name,
            'total' => $labelCounts[$svc->name] ?? 0,
        ])->sortByDesc('total')->values();
        $maxService  = $serviceStats->max('total') ?: 1;
        $topServices = $serviceStats->take(6);

        $revenueByLabel = $inWindow->where('status', 'completed')->groupBy('service_label')
            ->map(fn($g) => (float) $g->sum('price'));
        $revenueServiceStats = $allServices->map(fn($svc) => (object) [
            'name'  => $svc->name,
            'total' => $revenueByLabel[$svc->name] ?? 0.0,
        ])->sortByDesc('total')->values();
        $maxRevenueService  = $revenueServiceStats->max('total') ?: 1;
        $topRevenueServices = $revenueServiceStats->take(6);

        // Trending: growth versus the previous interval of the same length
        $recentCounts = $inWindow->filter($isLive)->whereNotNull('service_id')->groupBy('service_id')->map->count();
        $priorCounts  = $prevWindow->filter($isLive)->whereNotNull('service_id')->groupBy('service_id')->map->count();
        $trendingService = null; $trendingGrowth = 0;
        foreach ($recentCounts as $svcId => $recentCount) {
            if ($recentCount < 2) continue;
            $priorCount = $priorCounts[$svcId] ?? 0;
            $growth = $priorCount > 0 ? (($recentCount - $priorCount) / $priorCount) * 100 : 100;
            if ($growth > $trendingGrowth) { $trendingGrowth = $growth; $trendingService = $allServices->firstWhere('id', $svcId); }
        }

        // Per-service demand forecast for the next interval
        $serviceDemandForecast = $allServices->map(function ($svc) use ($byBucket, $isLive, $predictor, $cfg, $N, $W, $H) {
            $series = [];
            foreach ($byBucket as $rows) {
                $series[] = $rows->filter($isLive)->where('service_id', $svc->id)->count();
            }
            $result    = $predictor->forecast($series, $H, $cfg['season']);
            $lastTotal = array_sum(array_slice($series, $N - $W));
            $predicted = (int) round(array_sum(array_map(fn($v) => max(0, $v), $result['forecast'])));
            return (object) [
                'name'       => $svc->name,
                'category'   => $svc->category,
                'last_month' => $lastTotal,
                'predicted'  => $predicted,
                'trend'      => $predicted > $lastTotal ? 'up' : ($predicted < $lastTotal ? 'down' : 'flat'),
                'confidence' => $result['confidence'],
            ];
        })->filter(fn($s) => $s->last_month > 0 || $s->predicted > 0)->sortByDesc('predicted')->values();

        // Most active pets within the interval
        $petCounts = $inWindow->filter($isLive)->groupBy('pet_id')->map->count()->sortDesc()->take(5);
        $petModels = Pet::whereIn('id', $petCounts->keys())->get()->keyBy('id');
        $topPets = $petCounts->map(function ($n, $id) use ($petModels) {
            $pet = $petModels->get($id);
            if ($pet) $pet->appointments_count = $n;
            return $pet;
        })->filter()->values();

        // Labels used throughout the view
        $windowLabel  = $cfg['title'];
        $horizonLabel = $cfg['next'];
        $predictedAppointmentsNextMonth = $predictedAppointments;   // kept name for the view
        $predictedRevenueNextMonth      = $predictedRevenue;

        return view('staff.insights', compact(
            'trendPeriod', 'windowLabel', 'horizonLabel', 'windowStart',
            'totalAppointments', 'completedCount', 'pendingCount', 'cancelledCount', 'completionRate', 'activeCustomers',
            'serviceStats', 'maxService', 'topServices', 'statusBreakdown',
            'bookingCompleted', 'bookingCancelled',
            'revenueEarned', 'revenueLost', 'netRevenueGrowthPct', 'avgRevenuePerAppointment',
            'trendTitle', 'monthlyWithForecast', 'maxMonthly', 'volumeMeta',
            'periodTotal', 'periodAverage', 'busiestBucket', 'bucketNoun',
            'busiestDayLabel', 'busiestDayAvg', 'nextBusiestDate',
            'trendingService', 'trendingGrowth', 'topPets',
            'revenueMonthlyWithForecast', 'maxRevenue',
            'predictedRevenueNextMonth', 'revenueConfidence',
            'predictedAppointmentsNextMonth', 'apptConfidence',
            'serviceDemandForecast',
            'revenueServiceStats', 'maxRevenueService', 'topRevenueServices'
        ));
    }
}
