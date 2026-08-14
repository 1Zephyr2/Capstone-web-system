<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use App\Services\PredictiveAnalyticsService;
use Illuminate\Http\Request;

class AdminInsightsController extends Controller
{
    public function index(Request $request, PredictiveAnalyticsService $predictor)
    {
        // ── Headline stats ───────────────────────────────────────────────
        $totalAppointments = Appointment::count();
        $completedCount    = Appointment::where('status', 'completed')->count();
        $pendingCount      = Appointment::where('status', 'pending')->count();
        $cancelledCount    = Appointment::whereIn('status', ['cancelled', 'rejected'])->count();
        $completionRate    = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100) : 0;
        $totalOwners       = User::where('role', 'owner')->count();
        $totalStaff        = User::where('role', 'staff')->count();

        // ── Service popularity (by booking count) — counts EVERY appointment,
        // including legacy ones that predate the service_id column and only
        // have a service_type string. Grouping by the service_label accessor
        // (which already falls back correctly) instead of raw service_id
        // fixes stats that were silently dropping most historical bookings.
        $allServices = Service::notArchived()->orderBy('category')->orderBy('name')->get();
        $allAppointments = Appointment::with('service')->get();

        $labelCounts = $allAppointments->groupBy('service_label')->map->count();
        $serviceStats = $allServices->map(fn($svc) => (object) [
            'name'  => $svc->name,
            'total' => $labelCounts[$svc->name] ?? 0,
        ])->sortByDesc('total')->values();
        $maxService  = $serviceStats->max('total') ?: 1;
        $topServices = $serviceStats->take(6);

        $statusBreakdown = Appointment::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        // ── Booking distribution — completed vs. cancelled/rejected ─────
        $bookingCompleted = $statusBreakdown['completed'] ?? 0;
        $bookingCancelled = ($statusBreakdown['cancelled'] ?? 0) + ($statusBreakdown['rejected'] ?? 0);

        // ── Revenue status — earned vs. lost (price is set at booking
        // time regardless of outcome, so cancelled/rejected rows still
        // carry what that slot would have earned) ───────────────────────
        $revenueEarned = (float) Appointment::where('status', 'completed')->sum('price');
        $revenueLost   = (float) Appointment::whereIn('status', ['cancelled', 'rejected'])->sum('price');
        $netRevenueGrowthPct = $revenueEarned > 0 ? round((($revenueEarned - $revenueLost) / $revenueEarned) * 100, 1) : 0;

        // ── Appointment volume trend (period toggle) ─────────────────────
        // For 'year', buildVolumeSeries already returns the full Jan–Dec
        // series with actual-to-date plus a multi-month ensemble forecast
        // for the remainder of the year baked in (see $volumeMeta for the
        // actual-vs-forecast summary). Every other period still uses the
        // simpler "append one forecasted bucket" approach below.
        $trendPeriod = $request->get('period', '6months');
        [$monthly, $trendTitle, $seasonLength, $volumeMeta] = $this->buildVolumeSeries($trendPeriod, $predictor);

        if ($trendPeriod === 'year') {
            $monthlyWithForecast = $monthly;
        } else {
            $volumeForecast = $predictor->forecast(array_column($monthly, 'count'), 1, $seasonLength);
            $forecastLabel = match ($trendPeriod) {
                'week'  => now()->addDay()->format('D'),
                'month' => now()->addWeek()->startOfWeek()->format('M d'),
                default => now()->addMonthNoOverflow()->format('M'),
            };
            $monthlyWithForecast = $monthly;
            $monthlyWithForecast[] = [
                'label'       => $forecastLabel,
                'count'       => $volumeForecast['forecast'][0] ?? 0,
                'is_forecast' => true,
            ];
        }
        $maxMonthly = max(array_column($monthlyWithForecast, 'count')) ?: 1;

        $actualOnly    = array_filter($monthlyWithForecast, fn($m) => empty($m['is_forecast']));
        $periodTotal   = array_sum(array_column($actualOnly, 'count'));
        $periodAverage = count($actualOnly) > 0 ? round($periodTotal / count($actualOnly), 1) : 0;
        $busiestBucket = collect($actualOnly)->sortByDesc('count')->first();
        $bucketNoun    = match ($trendPeriod) { 'week' => 'day', 'month' => 'week', 'year' => 'month', default => 'month' };


        // ── Predicted busiest day-of-week (last 8 weeks of history) ─────
        $historyStart = now()->subWeeks(8)->startOfDay();
        $dayOfWeekCounts = Appointment::where('appointment_date', '>=', $historyStart)
            ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
            ->get()
            ->groupBy(fn($a) => $a->appointment_date->dayOfWeek)
            ->map->count();

        $dayNames = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $busiestDayOfWeek = $dayOfWeekCounts->isNotEmpty() ? $dayOfWeekCounts->sortDesc()->keys()->first() : null;
        $busiestDayLabel  = $busiestDayOfWeek !== null ? $dayNames[$busiestDayOfWeek] : null;
        $busiestDayAvg    = $busiestDayOfWeek !== null ? round($dayOfWeekCounts[$busiestDayOfWeek] / 8, 1) : 0;

        $nextBusiestDate = null;
        if ($busiestDayOfWeek !== null) {
            $nextBusiestDate = today()->addDay();
            while ((int) $nextBusiestDate->dayOfWeek !== (int) $busiestDayOfWeek) {
                $nextBusiestDate->addDay();
            }
        }

        // ── Trending service (30-day booking growth) ─────────────────────
        $last30Start  = now()->subDays(30);
        $prior30Start = now()->subDays(60);

        $recentCounts = Appointment::where('appointment_date', '>=', $last30Start)
            ->whereNotNull('service_id')
            ->selectRaw('service_id, count(*) as total')->groupBy('service_id')->pluck('total', 'service_id');
        $priorCounts = Appointment::whereBetween('appointment_date', [$prior30Start, $last30Start])
            ->whereNotNull('service_id')
            ->selectRaw('service_id, count(*) as total')->groupBy('service_id')->pluck('total', 'service_id');

        $trendingService = null;
        $trendingGrowth  = 0;
        foreach ($recentCounts as $svcId => $recentCount) {
            if ($recentCount < 2) continue;
            $priorCount = $priorCounts[$svcId] ?? 0;
            $growth = $priorCount > 0 ? (($recentCount - $priorCount) / $priorCount) * 100 : 100;
            if ($growth > $trendingGrowth) {
                $trendingGrowth  = $growth;
                $trendingService = Service::find($svcId);
            }
        }

        $topPets = Pet::withCount('appointments')->orderByDesc('appointments_count')->take(5)->get();

        // ── Revenue ───────────────────────────────────────────────────
        $totalRevenue = (float) Appointment::where('status', 'completed')->sum('price');
        $avgRevenuePerAppointment = $completedCount > 0 ? round($totalRevenue / $completedCount, 2) : 0;
        $thisMonthRevenue = (float) Appointment::where('status', 'completed')
            ->whereYear('appointment_date', now()->year)
            ->whereMonth('appointment_date', now()->month)
            ->sum('price');

        $revenueMonthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueMonthly[] = [
                'label' => $month->format('M'),
                'total' => (float) Appointment::where('status', 'completed')
                    ->whereYear('appointment_date', $month->year)
                    ->whereMonth('appointment_date', $month->month)
                    ->sum('price'),
            ];
        }
        $revenueForecast = $predictor->forecast(array_column($revenueMonthly, 'total'), 1, 12);
        $predictedRevenueNextMonth = $revenueForecast['forecast'][0] ?? 0;
        $revenueConfidence = $revenueForecast['confidence'];

        $revenueMonthlyWithForecast = $revenueMonthly;
        $revenueMonthlyWithForecast[] = [
            'label'       => now()->addMonthNoOverflow()->format('M'),
            'total'       => $predictedRevenueNextMonth,
            'is_forecast' => true,
        ];
        $maxRevenue = max(array_column($revenueMonthlyWithForecast, 'total')) ?: 1;

        // ── Predicted total appointments next month (headline number) ───
        $apptMonthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $apptMonthly[] = Appointment::whereYear('appointment_date', $month->year)
                ->whereMonth('appointment_date', $month->month)
                ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
                ->count();
        }
        $apptForecast = $predictor->forecast($apptMonthly, 1, 12);
        $predictedAppointmentsNextMonth = $apptForecast['forecast'][0] ?? 0;
        $apptConfidence = $apptForecast['confidence'];

        // ── Per-service demand forecast (next month, all active services) ─
        $serviceDemandForecast = $allServices->map(function ($svc) use ($predictor) {
            $series = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $series[] = Appointment::where('service_id', $svc->id)
                    ->whereYear('appointment_date', $month->year)
                    ->whereMonth('appointment_date', $month->month)
                    ->whereNotIn('status', [Appointment::STATUS_REJECTED, Appointment::STATUS_CANCELLED])
                    ->count();
            }
            $result = $predictor->forecast($series, 1, null); // 6 months isn't enough for yearly seasonality
            $lastActual = end($series);
            $predicted  = $result['forecast'][0] ?? 0;
            $trend = $predicted > $lastActual ? 'up' : ($predicted < $lastActual ? 'down' : 'flat');

            return (object) [
                'name'       => $svc->name,
                'category'   => $svc->category,
                'last_month' => $lastActual,
                'predicted'  => $predicted,
                'trend'      => $trend,
                'confidence' => $result['confidence'],
            ];
        })->sortByDesc('predicted')->values();

        // ── Revenue by service (all-time, completed only) — grouped by
        // service_label so legacy service_type rows count here too. ──────
        $revenueByLabel = $allAppointments->where('status', 'completed')
            ->groupBy('service_label')
            ->map(fn($group) => (float) $group->sum('price'));

        $revenueServiceStats = $allServices->map(fn($svc) => (object) [
            'name'  => $svc->name,
            'total' => $revenueByLabel[$svc->name] ?? 0.0,
        ])->sortByDesc('total')->values();
        $maxRevenueService  = $revenueServiceStats->max('total') ?: 1;
        $topRevenueServices = $revenueServiceStats->take(6);


        // Both admin and staff share one insights template (same pattern
        // used by staff.appointments), which adapts its nav/branding based
        // on the logged-in user's role.
        return view('staff.insights', compact(
            'totalAppointments', 'completedCount', 'pendingCount', 'cancelledCount', 'completionRate',
            'totalOwners', 'totalStaff',
            'serviceStats', 'maxService', 'topServices',
            'statusBreakdown',
            'bookingCompleted', 'bookingCancelled',
            'revenueEarned', 'revenueLost', 'netRevenueGrowthPct',
            'trendPeriod', 'trendTitle', 'monthlyWithForecast', 'maxMonthly', 'volumeMeta',
            'periodTotal', 'periodAverage', 'busiestBucket', 'bucketNoun',
            'busiestDayLabel', 'busiestDayAvg', 'nextBusiestDate',
            'trendingService', 'trendingGrowth',
            'topPets',
            'totalRevenue', 'avgRevenuePerAppointment', 'thisMonthRevenue',
            'revenueMonthlyWithForecast', 'maxRevenue',
            'predictedRevenueNextMonth', 'revenueConfidence',
            'predictedAppointmentsNextMonth', 'apptConfidence',
            'serviceDemandForecast',
            'revenueServiceStats', 'maxRevenueService', 'topRevenueServices'
        ));
    }

    /**
     * Builds the appointment-count series (and matching seasonal cycle
     * length) for the selected trend period.
     *
     * Every period returns [$monthly, $title, $seasonLength, $meta].
     * $meta is null except for 'year', where $monthly already contains
     * the full Jan–Dec series — actual counts for months up to now,
     * ensemble-forecast counts (flagged is_forecast) for the rest of the
     * year — and $meta carries the actual-vs-forecast summary used for
     * the "Forecast is X% above actual this year" caption.
     */
    private function buildVolumeSeries(string &$trendPeriod, PredictiveAnalyticsService $predictor): array
    {
        $monthly = [];

        if ($trendPeriod === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $monthly[] = ['label' => $day->format('D'), 'count' => Appointment::whereDate('appointment_date', $day->toDateString())->count()];
            }
            return [$monthly, 'Appointments (Last 7 Days)', 7, null];
        }

        if ($trendPeriod === 'month') {
            for ($i = 4; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $monthly[] = ['label' => $weekStart->format('M d'), 'count' => Appointment::whereBetween('appointment_date', [$weekStart, $weekEnd])->count()];
            }
            return [$monthly, 'Appointments (Last 5 Weeks)', 4, null];
        }

        if ($trendPeriod === 'year') {
            $currentMonth = (int) now()->month;
            $year = now()->year;

            // Actual counts, Jan through the current month.
            $actual = [];
            for ($m = 1; $m <= $currentMonth; $m++) {
                $actual[] = Appointment::whereYear('appointment_date', $year)->whereMonth('appointment_date', $m)->count();
            }

            $remaining = 12 - $currentMonth;
            $forecastValues = [];
            if ($remaining > 0) {
                // Feed the ensemble last year's monthly counts too, so it has
                // more than a handful of points to backtest and season against.
                $history = [];
                for ($m = 1; $m <= 12; $m++) {
                    $history[] = Appointment::whereYear('appointment_date', $year - 1)->whereMonth('appointment_date', $m)->count();
                }
                $series = array_merge($history, $actual);
                $result = $predictor->forecast($series, $remaining, 12);
                $forecastValues = $result['forecast'];
            }

            for ($m = 1; $m <= 12; $m++) {
                $label = \Carbon\Carbon::create($year, $m, 1)->format('M');
                if ($m <= $currentMonth) {
                    $monthly[] = ['label' => $label, 'count' => $actual[$m - 1]];
                } else {
                    $monthly[] = ['label' => $label, 'count' => $forecastValues[$m - $currentMonth - 1] ?? 0, 'is_forecast' => true];
                }
            }

            $actualTotal   = array_sum($actual);
            $forecastTotal = round(array_sum($forecastValues));
            $pctDiff = $actualTotal > 0
                ? round((($forecastTotal - $actualTotal) / $actualTotal) * 100, 1)
                : ($forecastTotal > 0 ? 100.0 : 0.0);

            $meta = [
                'actual_total'    => $actualTotal,
                'forecast_total'  => $forecastTotal,
                'pct_diff'        => $pctDiff,
                'matched_periods' => $remaining,
            ];

            return [$monthly, "Appointments — $year", 12, $meta];
        }

        $trendPeriod = '6months';
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthly[] = ['label' => $month->format('M'), 'count' => Appointment::whereYear('appointment_date', $month->year)->whereMonth('appointment_date', $month->month)->count()];
        }
        return [$monthly, 'Appointments (Last 6 Months)', 12, null];
    }
}
