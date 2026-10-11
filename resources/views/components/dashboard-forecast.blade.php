@php
    use App\Models\Appointment;
    $predictor = app(\App\Services\PredictiveAnalyticsService::class);

    $fcLabels = []; $fcActual = []; $fcRevenue = [];
    for ($i = 11; $i >= 0; $i--) {
        $m = now()->startOfMonth()->subMonths($i);
        $fcLabels[] = $m->format('M');
        $fcActual[] = Appointment::whereYear('appointment_date', $m->year)->whereMonth('appointment_date', $m->month)
            ->whereNotIn('status', ['rejected', 'cancelled'])->count();
        $fcRevenue[] = (float) Appointment::where('status', 'completed')->whereYear('appointment_date', $m->year)
            ->whereMonth('appointment_date', $m->month)->sum('price');
    }
    $apptF = $predictor->forecast($fcActual, 1, 12);
    $revF  = $predictor->forecast($fcRevenue, 1, 12);
    $nextAppts = (int) round($apptF['forecast'][0] ?? 0);
    $nextRev   = (float) ($revF['forecast'][0] ?? 0);
    $lastAppts = end($fcActual) ?: 0;
    $delta     = $lastAppts > 0 ? round((($nextAppts - $lastAppts) / $lastAppts) * 100) : null;

    $fcChartLabels = array_merge($fcLabels, [now()->addMonthNoOverflow()->format('M') . ' (forecast)']);
    $fcChartActual = array_merge($fcActual, [null]);
    $fcChartForecast = array_fill(0, count($fcActual) - 1, null);
    $fcChartForecast[] = end($fcActual);
    $fcChartForecast[] = $nextAppts;
    $fcConfidence = $apptF['confidence'] ?? null;
    $fcRoute = auth()->user()->role === 'admin' ? route('admin.insights') : route('staff.insights');
@endphp

<section class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm mb-8">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
        <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="bi bi-graph-up-arrow text-{{ $accent }}-600"></i> Predictive Analytics</h3>
        <a href="{{ $fcRoute }}" class="text-xs text-gray-500 hover:text-{{ $accent }}-700 transition-all">Full insights →</a>
    </div>
    <p class="text-gray-400 text-xs mb-5 leading-relaxed">Appointments booked per month for the last 12 months (solid) and the hybrid-ensemble forecast for next month (dashed). It blends a trend line, moving average, exponential smoothing and last-year seasonality, weighted by how well each predicted your own past data.</p>
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 h-60"><canvas id="dashForecastChart"></canvas></div>
        <div class="grid grid-cols-2 lg:grid-cols-1 gap-4">
            <div class="rounded-xl bg-{{ $accent }}-50 border border-{{ $accent }}-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Predicted appointments next month</p>
                <p class="text-2xl font-bold text-gray-900">{{ $nextAppts }}
                    @if($delta !== null)<span class="text-xs font-semibold {{ $delta >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $delta >= 0 ? '↗' : '↘' }} {{ abs($delta) }}% vs this month</span>@endif
                </p>
            </div>
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                <p class="text-xs text-gray-500 mb-1">Predicted revenue next month</p>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($nextRev, 0) }}</p>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('dashForecastChart');
        if (!el || typeof Chart === 'undefined') return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: @json($fcChartLabels),
                datasets: [
                    { label: 'Actual', data: @json($fcChartActual), borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.3 },
                    { label: 'Forecast', data: @json($fcChartForecast), borderColor: '#a5b4fc', borderDash: [6,4], tension: 0.3, spanGaps: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    });
</script>
