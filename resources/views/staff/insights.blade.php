@php
    $isAdmin = auth()->user()->role === 'admin';
    $prefix = $isAdmin ? 'admin' : 'staff';
    $labels = array_column($monthlyWithForecast, 'label');
    $actual = array_map(fn ($row) => (($row['is_forecast'] ?? false) ? null : $row['count']), $monthlyWithForecast);
    $forecast = array_map(fn ($row) => (($row['is_forecast'] ?? false) ? $row['count'] : null), $monthlyWithForecast);
    $scheduled = array_map(fn ($row) => (($row['is_forecast'] ?? false) ? ($row['scheduled'] ?? 0) : null), $monthlyWithForecast);
    $futureRows = collect($monthlyWithForecast)->filter(fn ($row) => $row['is_forecast'] ?? false)->values();
    $completedBookings = (int) $statusBreakdown->get('completed', 0);
    $pendingBookings = (int) $statusBreakdown->get('pending', 0);
    $approvedBookings = (int) $statusBreakdown->get('approved', 0);
    $cancelledBookings = (int) $statusBreakdown->get('cancelled', 0);
    $rejectedBookings = (int) $statusBreakdown->get('rejected', 0);
    $topService = $topServices->firstWhere('total', '>', 0);
    $topForecastService = $serviceDemandForecast->sortByDesc('predicted')->first();
    $statusOrder = ['completed', 'pending', 'approved', 'cancelled', 'rejected'];
    $statusLabels = collect($statusOrder)->filter(fn ($status) => $statusBreakdown->has($status))->values();
    $statusValues = $statusLabels->map(fn ($status) => (int) $statusBreakdown->get($status))->values();
    $servicePercentages = $topServices->map(fn ($service) => $totalAppointments > 0 ? round(($service->total / $totalAppointments) * 100, 1) : 0)->values();
    $chartValuesPlugin = <<<'JS'
    {
        id: 'valueLabels',
        afterDatasetsDraw(chart) {
            const {ctx} = chart;
            chart.data.datasets.forEach((dataset, datasetIndex) => {
                const meta = chart.getDatasetMeta(datasetIndex);
                if (meta.hidden) return;
                meta.data.forEach((element, index) => {
                    const raw = dataset.data[index];
                    if (raw === null || raw === undefined || Number(raw) === 0) return;
                    ctx.save();
                    ctx.font = '600 11px sans-serif';
                    if (chart.config.type === 'doughnut') {
                        const total = dataset.data.reduce((sum, value) => sum + (Number(value) || 0), 0);
                        const pct = total ? (Number(raw) / total * 100) : 0;
                        const pos = element.tooltipPosition();
                        ctx.fillStyle = '#fff';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(`${pct.toFixed(1)}%`, pos.x, pos.y);
                    } else if (chart.config.type === 'bar') {
                        const pos = element.tooltipPosition();
                        ctx.fillStyle = '#374151';
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';
                        const percentage = dataset.percentages?.[index];
                        ctx.fillText(percentage === undefined ? String(raw) : `${raw} (${percentage}%)`, pos.x + 6, pos.y);
                    } else {
                        const pos = element.tooltipPosition();
                        ctx.fillStyle = dataset.borderColor || '#374151';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(String(raw), pos.x, pos.y - 7);
                    }
                    ctx.restore();
                });
            });
        }
    }
    JS;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bark Pack | Appointment Analytics</title>
    <link rel="icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 125%; }</style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix.'.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> Bark Pack
                <span class="{{ $isAdmin ? 'text-rose-700 bg-rose-100 border-rose-200' : 'text-violet-700 bg-violet-100 border-violet-200' }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md border">{{ $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL' }}</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route($prefix.'.dashboard') }}" class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix.'.directory') }}" class="hover:text-gray-900 transition-all hover:scale-105">Pet Owner Directory</a>
                <a href="{{ route($prefix.'.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                @if(!$isAdmin)<a href="{{ route('staff.services') }}" class="hover:text-gray-900 transition-all hover:scale-105">Services</a>@endif
                <a href="{{ route($prefix.'.insights') }}" class="text-gray-900 font-semibold transition-all hover:scale-105">Insights</a>
                @if($isAdmin)<a href="{{ route('admin.panel') }}" class="text-rose-700 font-semibold bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100 transition-all">Admin Panel</a>@endif
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => $prefix . '.'])
            <form action="{{ route($prefix.'.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="document.getElementById('analytics-mobile-menu').classList.toggle('hidden')" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500" aria-label="Toggle navigation">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="analytics-mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route($prefix.'.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i>Dashboard</a>
        <a href="{{ route($prefix.'.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-people"></i>Pet Owner Directory</a>
        <a href="{{ route($prefix.'.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i>Appointments</a>
        @if(!$isAdmin)<a href="{{ route('staff.services') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-scissors"></i>Services</a>@endif
        <a href="{{ route($prefix.'.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold"><i class="bi bi-graph-up"></i>Insights</a>
        @if($isAdmin)<a href="{{ route('admin.panel') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-rose-50 text-rose-700 text-sm font-semibold"><i class="bi bi-gear"></i>Admin Panel</a>@endif
        <form action="{{ route($prefix.'.logout') }}" method="POST">@csrf<button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i>Logout</button></form>
    </div>
    <main class="container mx-auto px-6 py-12 space-y-6">
        <header class="flex flex-wrap justify-between items-end gap-4">
            <div><h1 class="text-2xl font-bold">Appointment Analytics</h1><p class="text-gray-500 text-sm">Booking trends, distribution, and popular services.</p></div>
            <form method="GET" class="flex items-center gap-2"><label for="period" class="text-sm">Period</label><select id="period" name="period" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                @foreach(['week'=>'Last 7 days','month'=>'Last 4 weeks','6months'=>'Last 6 months','year'=>'Last 12 months'] as $value=>$label)<option value="{{ $value }}" @selected($trendPeriod===$value)>{{ $label }}</option>@endforeach
            </select></form>
        </header>
        <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([['Total appointments',$totalAppointments],['Pending',$pendingCount],['Completed',$completedCount],['Completion rate',$completionRate.'%']] as [$label,$value])
                <div class="bg-white border border-gray-200 rounded-xl p-5"><p class="text-xs text-gray-500">{{ $label }}</p><p class="text-2xl font-bold mt-1">{{ $value }}</p></div>
            @endforeach
        </section>
        <section class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Appointment trends · {{ $windowLabel }}
                    <x-info-tip title="Appointment trends"><p>Each chart point shows an appointment count for its labeled {{ strtolower($bucketNoun) }}. Green is the past period; purple is the forecast for {{ strtolower($horizonLabel) }}. Rejected and cancelled appointments are excluded from the trend.</p><p>{{ number_format($periodTotal) }} active appointments were recorded in the selected past period ({{ number_format($periodAverage, 1) }} per {{ strtolower($bucketNoun) }} on average). The busiest was {{ $busiestBucket['label'] ?? '—' }} with {{ number_format($busiestBucket['count'] ?? 0) }}. For the next {{ strtolower($horizonLabel) }}, {{ number_format($upcomingAppointmentCount) }} appointments are already scheduled; the forecast estimates {{ number_format($predictedAppointmentsNextMonth) }} in total.</p><p>Future dates are grouped as days for 7 days, consecutive 7-day weeks for 4 weeks, and calendar months for 6 or 12 months.</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="trend-chart"></canvas></div>
                <details class="mt-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm">
                    <summary class="cursor-pointer font-medium text-gray-700">Upcoming appointments by period</summary>
                    <div class="mt-2 max-h-56 divide-y divide-gray-200 overflow-y-auto">
                        @foreach($futureRows as $row)
                            <div class="grid grid-cols-3 gap-2 py-2 text-xs sm:text-sm">
                                <span class="font-medium">{{ $row['label'] }}</span>
                                <span class="text-gray-500">{{ number_format($row['scheduled'] ?? 0) }} scheduled</span>
                                <span class="text-right text-indigo-700">{{ number_format($row['count']) }} estimated</span>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Booking distribution
                    <x-info-tip title="Booking distribution" align="right"><p>Each slice is a current appointment status. The percentage label is that status's share of all appointments in the selected period; hover or focus a slice for the exact count and percentage.</p><p>{{ number_format($completedBookings) }} completed, {{ number_format($pendingBookings + $approvedBookings) }} pending or approved, and {{ number_format($cancelledBookings + $rejectedBookings) }} cancelled or rejected.</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="distribution-chart"></canvas></div>
            </div>
        </section>
        <section class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Popular services
                    <x-info-tip title="Popular services"><p>Each bar shows the exact booking count and its percentage of all appointments in the selected past period. Add-ons are stored separately. The most requested was @if($topService) {{ $topService->name }} with {{ number_format($topService->total) }} bookings. @else none yet. @endif</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="services-chart"></canvas></div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Service demand forecast · {{ $horizonLabel }}
                    <x-info-tip title="Service demand forecast" align="right"><p>Open a day, week, or month below to see each service's expected bookings for that exact period. “Scheduled” counts bookings already on the calendar; “Estimated” is the forecast total and will not be lower than bookings already scheduled. The forecast combines historical trend, moving average, smoothing, and seasonal methods when there is enough history.</p><p>@if($topForecastService) {{ $topForecastService->name }} has the highest estimated demand across the selected horizon at {{ number_format($topForecastService->predicted) }} bookings. @else No upcoming demand can be estimated yet. @endif</p></x-info-tip>
                </h2>
                <p class="mb-2 text-xs text-gray-500">Top 8 services per period. Period totals include all non-archived services.</p>
                <div class="divide-y divide-gray-100">
                    @foreach($serviceForecastPeriods as $index => $period)
                        <details class="py-2" @if($index === 0) open @endif>
                            <summary class="cursor-pointer rounded-lg px-2 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                                {{ $period['label'] }} <span class="font-normal text-gray-500">· {{ number_format($period['scheduled']) }} scheduled · {{ number_format($period['predicted']) }} estimated</span>
                            </summary>
                            <div class="divide-y divide-gray-100 px-2">
                                @forelse($period['services'] as $service)
                                    <div class="grid grid-cols-[minmax(0,1fr)_auto_auto] gap-3 py-2 text-xs sm:text-sm">
                                        <span class="truncate">{{ $service->name }}</span>
                                        <span class="text-right text-gray-500">{{ number_format($service->scheduled) }} scheduled</span>
                                        <span class="text-right font-medium text-indigo-700">{{ number_format($service->predicted) }} estimated</span>
                                    </div>
                                @empty
                                    <p class="px-2 py-3 text-xs text-gray-400">No scheduled or forecast service bookings for this period.</p>
                                @endforelse
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
    <script>
        const labels = @json($labels);
        const valueLabelsPlugin = {!! $chartValuesPlugin !!};
        const scheduledBookings = @json($scheduled);
        new Chart(document.getElementById('trend-chart'), {type:'line',plugins:[valueLabelsPlugin],data:{labels,datasets:[{label:'Appointments',data:@json($actual),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',fill:true,tension:.3},{label:'Forecast',data:@json($forecast),borderColor:'#6366f1',borderDash:[6,5],tension:.3}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{precision:0}}},plugins:{tooltip:{callbacks:{afterLabel:(context)=>context.datasetIndex===1?`Already scheduled: ${scheduledBookings[context.dataIndex]}`:''}}}}});
        new Chart(document.getElementById('distribution-chart'), {type:'doughnut',plugins:[valueLabelsPlugin],data:{labels:@json($statusLabels),datasets:[{data:@json($statusValues),backgroundColor:['#f59e0b','#ef4444','#10b981','#9ca3af','#3b82f6']}]},options:{responsive:true,maintainAspectRatio:false,plugins:{tooltip:{callbacks:{label:(context)=>{const values=context.dataset.data;const total=values.reduce((sum,value)=>sum+Number(value||0),0);const pct=total?(Number(context.raw)/total*100):0;return `${context.label}: ${context.raw} (${pct.toFixed(1)}%)`;}}}}}});
        new Chart(document.getElementById('services-chart'), {type:'bar',plugins:[valueLabelsPlugin],data:{labels:@json($topServices->pluck('name')),datasets:[{label:'Bookings',data:@json($topServices->pluck('total')),percentages:@json($servicePercentages),backgroundColor:'#8b5cf6'}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,layout:{padding:{right:60}},scales:{x:{beginAtZero:true,ticks:{precision:0}}}}});
    </script>
</body>
</html>
