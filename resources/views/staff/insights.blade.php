@php
    $isAdmin = auth()->user()->role === 'admin';
    $prefix  = $isAdmin ? 'admin' : 'staff';
    $accent  = $isAdmin ? 'indigo' : 'violet';

    $volLabels = array_column($monthlyWithForecast, 'label');
    $volActual = [];
    $volForecast = [];
    foreach ($monthlyWithForecast as $i => $m) {
        $isF = $m['is_forecast'] ?? false;
        $volActual[]   = $isF ? null : $m['count'];
        $volForecast[] = $isF ? $m['count'] : null;
    }
    foreach ($volForecast as $i => $v) {
        if ($v !== null && $i > 0 && $volForecast[$i - 1] === null && $volActual[$i - 1] !== null) {
            $volForecast[$i - 1] = $volActual[$i - 1];
        }
    }

    $revLabels = array_column($revenueMonthlyWithForecast, 'label');
    $revActual = [];
    $revForecast = [];
    foreach ($revenueMonthlyWithForecast as $i => $m) {
        $isF = $m['is_forecast'] ?? false;
        $revActual[]   = $isF ? null : $m['total'];
        $revForecast[] = $isF ? $m['total'] : null;
    }
    foreach ($revForecast as $i => $v) {
        if ($v !== null && $i > 0 && $revForecast[$i - 1] === null && $revActual[$i - 1] !== null) {
            $revForecast[$i - 1] = $revActual[$i - 1];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $isAdmin ? 'Admin' : 'Staff' }} Insights</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }</script>
    <script>function toggleModalById(id) { const m = document.getElementById(id); m.classList.toggle('hidden'); m.classList.toggle('flex'); }</script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix.'.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                @if($isAdmin)
                    <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
                @else
                    <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
                @endif
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route($prefix.'.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix.'.directory') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix.'.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                @if(!$isAdmin)
                    <a href="{{ route('staff.services') }}" class="hover:text-gray-900 transition-all hover:scale-105">Services</a>
                @endif
                <a href="{{ route($prefix.'.insights') }}"     class="text-gray-900 font-semibold transition-all hover:scale-105">Insights</a>
                @if($isAdmin)
                    <a href="{{ route('admin.panel') }}" class="text-rose-700 font-semibold transition-all bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100 hover:scale-105">Admin Panel</a>
                @endif
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => $prefix.'.'])
            <form action="{{ route($prefix.'.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route($prefix.'.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
        <a href="{{ route($prefix.'.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-heart"></i> Pets</a>
        <a href="{{ route($prefix.'.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i> Appointments</a>
        @if(!$isAdmin)
            <a href="{{ route('staff.services') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-list-check"></i> Services</a>
        @endif
        <a href="{{ route($prefix.'.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
        @if($isAdmin)
            <a href="{{ route('admin.panel') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-gear"></i> Admin Panel</a>
        @endif
        <form action="{{ route($prefix.'.logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </div>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-10">
            <h1 class="text-2xl font-bold text-gray-900">Clinic Insights</h1>
            <p class="text-gray-500 text-sm">Real-time operational performance, with hybrid-ensemble forecasts.</p>
        </header>

        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div class="flex gap-1 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                @foreach(['week' => 'Week', 'month' => 'Month', '6months' => '6 Months', 'year' => 'Year'] as $key => $label)
                    <a href="{{ route($prefix.'.insights', ['period' => $key]) }}"
                       class="px-4 py-2 rounded-md text-sm font-semibold transition-all {{ $trendPeriod === $key ? "bg-$accent-600 text-white shadow-sm" : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            @if($trendPeriod === 'year')
                <div class="text-xs text-gray-400"><i class="bi bi-calendar3 mr-1"></i>{{ now()->year }} (Jan 1 – Dec 31, {{ now()->year }})</div>
            @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                <p class="text-amber-700 text-xs font-semibold uppercase tracking-wider mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                <p class="text-xs text-amber-600 mt-0.5">{{ $totalAppointments > 0 ? round(($pendingCount/$totalAppointments)*100) : 0 }}% of total</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                <p class="text-emerald-700 text-xs font-semibold uppercase tracking-wider mb-1">Completed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
                <p class="text-xs text-emerald-600 mt-0.5">₱{{ number_format($totalRevenue, 0) }} revenue</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl p-5">
                <p class="text-red-700 text-xs font-semibold uppercase tracking-wider mb-1">Cancelled / Rejected</p>
                <p class="text-2xl font-bold text-gray-900">{{ $cancelledCount }}</p>
                <p class="text-xs text-red-600 mt-0.5">{{ $totalAppointments > 0 ? round(($cancelledCount/$totalAppointments)*100) : 0 }}% of total</p>
            </div>
            <div class="bg-{{ $accent }}-50 border border-{{ $accent }}-100 rounded-2xl p-5">
                <p class="text-{{ $accent }}-700 text-xs font-semibold uppercase tracking-wider mb-1">Completion Rate</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</p>
                <p class="text-xs text-{{ $accent }}-600 mt-0.5">{{ $totalOwners }} owners on file</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1">{{ $trendTitle }}</h3>
                <p class="text-gray-400 text-xs mb-1">Solid = actual · dashed = hybrid ensemble forecast</p>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">How many appointments were booked in each {{ $bucketNoun }}. The dashed segment is where the ensemble takes over — it blends four different forecasting methods (trend line, moving average, exponential smoothing, and same-{{ $bucketNoun }}-last-cycle seasonality), weighting each by how accurately it actually predicted your own past data.</p>
                <div class="h-56"><canvas id="volumeChart"></canvas></div>
                @if($volumeMeta)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs bg-{{ $volumeMeta['pct_diff'] >= 0 ? 'emerald' : 'red' }}-50 border border-{{ $volumeMeta['pct_diff'] >= 0 ? 'emerald' : 'red' }}-100 rounded-lg p-3">
                        <span class="font-semibold text-{{ $volumeMeta['pct_diff'] >= 0 ? 'emerald' : 'red' }}-700">
                            Forecast is {{ abs($volumeMeta['pct_diff']) }}% {{ $volumeMeta['pct_diff'] >= 0 ? 'above' : 'below' }} actual so far this year
                        </span>
                        <span class="text-gray-500">— Actual: {{ $volumeMeta['actual_total'] }} → Projected rest of year: {{ $volumeMeta['forecast_total'] }} ({{ $volumeMeta['matched_periods'] }} months forecasted)</span>
                    </div>
                @else
                    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                        <div><p class="text-lg font-bold text-gray-900">{{ $periodTotal }}</p><p class="text-xs text-gray-400">Total this period</p></div>
                        <div><p class="text-lg font-bold text-gray-900">{{ $periodAverage }}</p><p class="text-xs text-gray-400">Avg per {{ $bucketNoun }}</p></div>
                        <div><p class="text-lg font-bold text-gray-900">{{ $busiestBucket['label'] ?? '—' }}</p><p class="text-xs text-gray-400">Busiest ({{ $busiestBucket['count'] ?? 0 }})</p></div>
                    </div>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1">Revenue (Last 12 Months)</h3>
                <p class="text-gray-400 text-xs mb-1">Completed appointments only · dashed = forecast</p>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">Same idea as the chart on the left, but tracking peso earnings from completed appointments instead of booking counts — useful for spotting whether a busy month actually translated into more revenue, or just more low-priced bookings.</p>
                <div class="h-56"><canvas id="revenueChart"></canvas></div>
                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($totalRevenue, 0) }}</p><p class="text-xs text-gray-400">Total earned</p></div>
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($thisMonthRevenue, 0) }}</p><p class="text-xs text-gray-400">This month</p></div>
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($predictedRevenueNextMonth, 0) }}</p><p class="text-xs text-gray-400">Predicted next month</p></div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1">Booking Distribution</h3>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">Of every appointment ever booked, what share ended in a completed visit versus a cancellation or rejection. A large red slice is worth investigating — it usually points to overbooked slots, no-shows, or a service/price mismatch rather than random chance.</p>
                <div class="h-56 flex items-center justify-center"><canvas id="distributionChart"></canvas></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1">Revenue Status</h3>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">"Earned" is money actually collected from completed appointments. "Lost" is what those cancelled/rejected slots would have earned had they gone through — money the calendar held space for but never converted. Net revenue growth below compares the two.</p>
                <div class="h-56"><canvas id="revenueStatusChart"></canvas></div>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs bg-{{ $netRevenueGrowthPct >= 0 ? 'emerald' : 'red' }}-50 border border-{{ $netRevenueGrowthPct >= 0 ? 'emerald' : 'red' }}-100 rounded-lg p-3">
                    <span class="font-semibold text-{{ $netRevenueGrowthPct >= 0 ? 'emerald' : 'red' }}-700">{{ $netRevenueGrowthPct }}% net revenue growth</span>
                    <span class="text-gray-500">— Earned: ₱{{ number_format($revenueEarned, 0) }} · Lost: ₱{{ number_format($revenueLost, 0) }}</span>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-gray-900">Service Popularity</h3>
                    @if($serviceStats->count() > 6)
                        <button type="button" onclick="toggleModalById('service-modal')" class="text-xs text-{{ $accent }}-600 hover:text-{{ $accent }}-700 font-semibold">View All ({{ $serviceStats->count() }}) →</button>
                    @endif
                </div>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">Every appointment ever made, regardless of status, counted against the service that was booked. Longer bars = more frequently requested — a good signal for which services to staff up for or feature more prominently.</p>
                <div class="h-56"><canvas id="serviceChart"></canvas></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-gray-900">Revenue by Service</h3>
                    @if($revenueServiceStats->count() > 6)
                        <button type="button" onclick="toggleModalById('revenue-service-modal')" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">View All ({{ $revenueServiceStats->count() }}) →</button>
                    @endif
                </div>
                <p class="text-gray-400 text-xs mb-4 leading-relaxed">Same list, but ranked by actual peso earnings from completed appointments instead of booking count. Worth comparing against the chart on the left — a service can be popular but low-earning, or the reverse.</p>
                <div class="h-56"><canvas id="revenueServiceChart"></canvas></div>
            </div>
        </div>

        <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm" onclick="if(event.target===this) toggleModalById('service-modal')">
            <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-lg shadow-2xl max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-900">All Services — Bookings</h3>
                    <button type="button" onclick="toggleModalById('service-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400"><i class="bi bi-x-lg"></i></button>
                </div>
                <p class="text-gray-400 text-xs mb-6">Most to least popular</p>
                <div class="space-y-5">
                    @foreach($serviceStats as $s)
                        @php $pct = round(($s->total / $maxService) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5"><span class="text-gray-500">{{ $s->name }}</span><span class="font-medium text-gray-500">{{ $s->total }} appts</span></div>
                            <div class="h-2 bg-gray-50 rounded-full overflow-hidden"><div class="h-full bg-{{ $accent }}-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="revenue-service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm" onclick="if(event.target===this) toggleModalById('revenue-service-modal')">
            <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-lg shadow-2xl max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-900">All Services — Revenue</h3>
                    <button type="button" onclick="toggleModalById('revenue-service-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400"><i class="bi bi-x-lg"></i></button>
                </div>
                <p class="text-gray-400 text-xs mb-6">Highest to lowest, all time</p>
                <div class="space-y-5">
                    @foreach($revenueServiceStats as $s)
                        @php $pct = $s->total > 0 ? round(($s->total / $maxRevenueService) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5"><span class="text-gray-500">{{ $s->name }}</span><span class="font-medium text-gray-500">₱{{ number_format($s->total, 0) }}</span></div>
                            <div class="h-2 bg-gray-50 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 mb-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2"><i class="bi bi-graph-up-arrow text-{{ $accent }}-600"></i> Predictive Insights</h3>
            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Hybrid ensemble — blends linear regression (long-term direction), moving average (recent stability), exponential smoothing (reacts faster to recent changes), and a seasonal-naive method (repeats what happened in the same period last cycle), weighted by how accurately each one has predicted your own past data so far. These four cards are the ensemble's headline outputs.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-{{ $accent }}-50 border border-{{ $accent }}-100 rounded-xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-{{ $accent }}-500 mb-1">Predicted Busiest Day</p>
                    @if($busiestDayLabel)
                        <p class="text-lg font-bold text-gray-900">{{ $busiestDayLabel }}</p>
                        <p class="text-xs text-gray-500 mt-1">~{{ $busiestDayAvg }} appts/week avg. Next: {{ $nextBusiestDate->format('M j, Y') }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic mt-1">Not enough booking history yet.</p>
                    @endif
                    <p class="text-[11px] text-gray-400 mt-2">Which weekday has historically had the most bookings on average.</p>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-500 mb-1">Trending Service</p>
                    @if($trendingService)
                        <p class="text-lg font-bold text-gray-900">{{ $trendingService->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Bookings up {{ round($trendingGrowth) }}% vs. prior 30 days</p>
                    @else
                        <p class="text-sm text-gray-400 italic mt-1">Not enough booking history yet.</p>
                    @endif
                    <p class="text-[11px] text-gray-400 mt-2">The service with the sharpest booking growth in the last 30 days.</p>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-500">Appts — Next Month</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $apptConfidence === 'High' ? 'bg-emerald-100 text-emerald-700' : ($apptConfidence === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ $apptConfidence }}</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">{{ $predictedAppointmentsNextMonth }}</p>
                    <p class="text-[11px] text-gray-400 mt-2">The ensemble's total booking-count forecast for next month. The badge reflects how much history backs the estimate.</p>
                </div>
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-500">Revenue — Next Month</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $revenueConfidence === 'High' ? 'bg-emerald-100 text-emerald-700' : ($revenueConfidence === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ $revenueConfidence }}</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">₱{{ number_format($predictedRevenueNextMonth, 0) }}</p>
                    <p class="text-[11px] text-gray-400 mt-2">Same idea, forecasting peso revenue instead of booking count.</p>
                </div>
            </div>
            <p class="text-gray-300 text-[11px] mt-4 italic">Statistical estimates based on your own past bookings — not a guarantee of future demand.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 mb-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2"><i class="bi bi-bar-chart-line text-{{ $accent }}-600"></i> Service Demand Forecast — Next Month</h3>
            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Each service forecast individually using its own last 6 months of bookings, so you can see which specific services are expected to pick up or slow down — not just the clinic total. "Trend" compares the predicted number to last month's actual: ↗ rising, ↘ falling, — roughly flat.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/60 border-b border-gray-100">
                        <tr class="text-xs uppercase text-gray-400 tracking-wider">
                            <th class="px-4 py-3 text-left">Service</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-right">Last Month</th>
                            <th class="px-4 py-3 text-right">Predicted Next Month</th>
                            <th class="px-4 py-3 text-center">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($serviceDemandForecast->take(10) as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $s->name }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $s->category }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $s->last_month }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $s->predicted }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($s->trend === 'up')<span class="text-emerald-600"><i class="bi bi-arrow-up-right"></i></span>
                                    @elseif($s->trend === 'down')<span class="text-red-500"><i class="bi bi-arrow-down-right"></i></span>
                                    @else<span class="text-gray-400"><i class="bi bi-dash"></i></span>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-gray-300 text-[11px] mt-4 italic">Based on each service's own last 6 months of bookings — a short window, so treat this as directional.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-1">Most Active Pets</h3>
            <p class="text-gray-400 text-xs mb-6 leading-relaxed">Your top 5 pets by total appointment count, all time — useful for spotting loyal regulars worth a perk, or noticing a pet whose visits have quietly stopped.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @forelse($topPets as $i => $pet)
                    <div class="bg-gray-50/50 border border-gray-200 rounded-xl p-4 text-center">
                        <span class="text-gray-400 font-bold text-xs">#{{ $i + 1 }}</span>
                        @if($pet->photo)
                            <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" class="w-10 h-10 rounded-full object-cover border border-{{ $accent }}-200 mx-auto my-2">
                        @else
                            <span class="text-xl block my-2">{{ $pet->type === 'cat' ? '🐱' : '🐶' }}</span>
                        @endif
                        <p class="text-sm font-semibold text-gray-900">{{ $pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $pet->breed }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $pet->appointments_count }} appts</p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm italic col-span-full">No pet data yet.</p>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        const chartFont = { family: 'Inter', size: 11 };
        Chart.defaults.font = chartFont;
        Chart.defaults.color = '#9ca3af';

        new Chart(document.getElementById('volumeChart'), {
            type: 'line',
            data: {
                labels: @json($volLabels),
                datasets: [
                    { label: 'Actual', data: @json($volActual), borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.12)', fill: true, tension: 0.3, spanGaps: false },
                    { label: 'Forecast', data: @json($volForecast), borderColor: '#a5b4fc', backgroundColor: 'rgba(165,180,252,0.15)', borderDash: [6,4], fill: true, tension: 0.3, spanGaps: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
        });

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revLabels),
                datasets: [
                    { label: 'Actual', data: @json($revActual), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.12)', fill: true, tension: 0.3, spanGaps: false },
                    { label: 'Forecast', data: @json($revForecast), borderColor: '#6ee7b7', backgroundColor: 'rgba(110,231,183,0.15)', borderDash: [6,4], fill: true, tension: 0.3, spanGaps: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } } } }
        });

        new Chart(document.getElementById('distributionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Cancelled / Rejected'],
                datasets: [{ data: [{{ $bookingCompleted }}, {{ $bookingCancelled }}], backgroundColor: ['#10b981', '#ef4444'], borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
        });

        new Chart(document.getElementById('revenueStatusChart'), {
            type: 'bar',
            data: {
                labels: ['Earned Revenue', 'Revenue Lost'],
                datasets: [{ data: [{{ $revenueEarned }}, {{ $revenueLost }}], backgroundColor: ['#10b981', '#ef4444'], borderRadius: 6, maxBarThickness: 70 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } } } }
        });

        new Chart(document.getElementById('serviceChart'), {
            type: 'bar',
            data: {
                labels: @json($topServices->pluck('name')),
                datasets: [{ data: @json($topServices->pluck('total')), backgroundColor: '#{{ $accent === "indigo" ? "6366f1" : "8b5cf6" }}', borderRadius: 6 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
        });

        new Chart(document.getElementById('revenueServiceChart'), {
            type: 'bar',
            data: {
                labels: @json($topRevenueServices->pluck('name')),
                datasets: [{ data: @json($topRevenueServices->pluck('total')), backgroundColor: '#10b981', borderRadius: 6 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() } } } }
        });
    </script>
</body>
</html>
