@php
    $isAdmin = auth()->user()->role === 'admin';
    $prefix = $isAdmin ? 'admin' : 'staff';
    $labels = array_column($monthlyWithForecast, 'label');
    $actual = array_map(fn ($row) => (($row['is_forecast'] ?? false) ? null : $row['count']), $monthlyWithForecast);
    $forecast = array_map(fn ($row) => (($row['is_forecast'] ?? false) ? $row['count'] : null), $monthlyWithForecast);
    $completedBookings = (int) $statusBreakdown->get('completed', 0);
    $pendingBookings = (int) $statusBreakdown->get('pending', 0);
    $approvedBookings = (int) $statusBreakdown->get('approved', 0);
    $cancelledBookings = (int) $statusBreakdown->get('cancelled', 0);
    $rejectedBookings = (int) $statusBreakdown->get('rejected', 0);
    $topService = $topServices->firstWhere('total', '>', 0);
    $topForecastService = $serviceDemandForecast->sortByDesc('predicted')->first();
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
                    <x-info-tip title="How to read appointment trends"><p>Each point on the horizontal axis is one {{ strtolower($bucketNoun) }} in the selected {{ strtolower($windowLabel) }} range; the vertical axis is the number of appointments scheduled during that bucket. The solid green line counts pending, approved, and completed appointments; rejected and cancelled appointments are excluded. The dashed purple line estimates the count for {{ strtolower($horizonLabel) }}. A higher point means more bookings for that period, not more available slots. Compare the last solid points to see whether booking volume is rising or falling, and use the forecast as a planning estimate.</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="trend-chart"></canvas></div><p class="text-xs text-gray-400 mt-2">Dashed line shows the appointment forecast.</p>
                <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600"><strong class="text-gray-800">Summary:</strong> {{ number_format($periodTotal) }} appointments recorded in this period ({{ number_format($periodAverage, 1) }} per period on average). The busiest period was {{ $busiestBucket['label'] ?? '—' }} with {{ number_format($busiestBucket['count'] ?? 0) }} bookings.</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Booking distribution
                    <x-info-tip title="How to read booking distribution" align="right"><p>Each appointment in the selected date range is counted once and placed in its current status slice. Pending means staff have not decided yet; approved means the booking is accepted; completed means the service was finished; rejected means staff declined it; cancelled means it was cancelled or expired. Slice size represents the number of appointments in that status, not a percentage of income. The largest slice is the status with the most bookings.</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="distribution-chart"></canvas></div>
                <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600"><strong class="text-gray-800">Summary:</strong> {{ number_format($completedBookings) }} completed, {{ number_format($pendingBookings + $approvedBookings) }} pending or approved, and {{ number_format($cancelledBookings + $rejectedBookings) }} cancelled or rejected appointments.</p>
            </div>
        </section>
        <section class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Popular services
                    <x-info-tip title="How to read popular services"><p>Each horizontal bar counts appointments in the selected date range whose primary package or service is the label on the left; pending, approved, completed, rejected, and cancelled requests are included. The number along the bottom is the booking count, so longer bars mean more appointments chose that primary service. Add-on selections are stored separately and are not added to these bars. Use the ranking to see which main services owners requested most often.</p></x-info-tip>
                </h2>
                <div class="h-72"><canvas id="services-chart"></canvas></div>
                <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600"><strong class="text-gray-800">Summary:</strong> @if($topService) {{ $topService->name }} was the most requested service with {{ number_format($topService->total) }} bookings. @else No service bookings were recorded in this period yet. @endif</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h2 class="font-semibold mb-4 flex items-center gap-2">Service demand forecast · {{ $horizonLabel }}
                    <x-info-tip title="How to read service demand forecast" align="right"><p>Each row is a service with recent or predicted demand. The number before the arrow is the count of pending, approved, and completed appointments using that primary service in the selected date range; rejected and cancelled bookings are excluded. The number after the arrow estimates bookings for the matching future interval ({{ strtolower($horizonLabel) }}). The system combines historical trend, moving average, smoothing, and seasonality methods when enough history is available, then weights methods by how well they predicted past data. A higher predicted count may require more staff time or supplies; treat it as an estimate and compare with actual results as new bookings come in.</p></x-info-tip>
                </h2>
                <div class="divide-y divide-gray-100">@forelse($serviceDemandForecast->take(8) as $service)<div class="py-3 flex justify-between gap-3 text-sm"><span>{{ $service->name }}</span><span class="text-gray-500">{{ $service->last_month }} → {{ $service->predicted }} bookings</span></div>@empty<p class="text-sm text-gray-400">No service bookings in this period yet.</p>@endforelse</div>
                <p class="mt-4 rounded-lg bg-gray-50 p-3 text-sm text-gray-600"><strong class="text-gray-800">Summary:</strong> @if($topForecastService) {{ $topForecastService->name }} is forecast to have the highest demand at {{ number_format($topForecastService->predicted) }} bookings in the next period. @else There is not enough service booking history to show a forecast. @endif</p>
            </div>
        </section>
    </main>
    <script>
        const labels = @json($labels);
        new Chart(document.getElementById('trend-chart'), {type:'line',data:{labels,datasets:[{label:'Appointments',data:@json($actual),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',fill:true,tension:.3},{label:'Forecast',data:@json($forecast),borderColor:'#6366f1',borderDash:[6,5],tension:.3}]},options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
        new Chart(document.getElementById('distribution-chart'), {type:'doughnut',data:{labels:@json($statusBreakdown->keys()),datasets:[{data:@json($statusBreakdown->values()),backgroundColor:['#f59e0b','#10b981','#3b82f6','#ef4444','#9ca3af']}]},options:{responsive:true,maintainAspectRatio:false}});
        new Chart(document.getElementById('services-chart'), {type:'bar',data:{labels:@json($topServices->pluck('name')),datasets:[{label:'Bookings',data:@json($topServices->pluck('total')),backgroundColor:'#8b5cf6'}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,scales:{x:{beginAtZero:true,ticks:{precision:0}}}}});
    </script>
</body>
</html>
