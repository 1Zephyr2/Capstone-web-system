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
    <title>Bark Park | {{ $isAdmin ? 'Admin' : 'Staff' }} Insights</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 125%; }</style>
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
            <a href="{{ route($prefix . '.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> Bark Park
                <span class="{{ $isAdmin ? 'text-rose-700 bg-rose-100 border-rose-200' : 'text-violet-700 bg-violet-100 border-violet-200' }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md border">{{ $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL' }}</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route($prefix . '.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix . '.directory') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix . '.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                @if(!$isAdmin)
                <a href="{{ route('staff.services') }}" class="hover:text-gray-900 transition-all hover:scale-105">Services</a>
                @endif
                <a href="{{ route($prefix . '.insights') }}"     class="text-gray-900 font-semibold transition-all hover:scale-105">Insights</a>
                @if($isAdmin)
                <a href="{{ route('admin.panel') }}" class="text-rose-700 font-semibold bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100 transition-all">Admin Panel</a>
                @endif
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => $prefix . '.'])
            <form action="{{ route($prefix . '.logout') }}" method="POST" class="m-0 hidden md:block">
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
        @php
            $vm = $volumeMeta;
            $dirWord = fn($p) => $p > 5 ? 'higher' : ($p < -5 ? 'lower' : 'about the same');
            $apptDir = $dirWord($vm['pct_diff']);
            $revDir  = $dirWord($vm['revenue_pct']);
            $cancelPct = $totalAppointments > 0 ? round(($cancelledCount / $totalAppointments) * 100) : 0;
            $topSvc = $serviceStats->first();
            $topRev = $revenueServiceStats->first();
            $confNote = fn($c) => $c === 'High' ? 'The methods agreed closely on your history, so this estimate is fairly reliable.'
                : ($c === 'Medium' ? 'There is some history to go on, but the methods disagree a little. Treat it as a good guess.'
                : 'There is little history for this interval, so treat it only as a rough direction.');
        @endphp

        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">Clinic Insights
                <x-info-tip title="How this page works">
                    <p>Every number, chart and forecast on this page follows the interval you pick (Week, Month, 6 Months or Year). Nothing here is all-time.</p>
                    <p><strong>Actual</strong> = what really happened in the selected interval. <strong>Forecast</strong> = what the system expects in the following interval of the same length.</p>
                    <p>The forecast uses a hybrid ensemble: four methods (trend line, moving average, exponential smoothing, same-period-last-cycle) are blended, and the ones that predicted your recent history best get the most weight.</p>
                    <p>Forecasts are estimates from your own past bookings, not guarantees.</p>
                </x-info-tip>
            </h1>
            <p class="text-gray-500 text-sm">Showing <strong class="text-gray-700">{{ strtolower($windowLabel) }}</strong> · forecast for the <strong class="text-gray-700">{{ $horizonLabel }}</strong></p>
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
            <div class="text-xs text-gray-400"><i class="bi bi-calendar3 mr-1"></i>{{ $windowStart->format('M j, Y') }} – {{ today()->format('M j, Y') }}</div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                <p class="text-amber-700 text-xs font-semibold uppercase tracking-wider mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                <p class="text-xs text-amber-600 mt-0.5">{{ $totalAppointments > 0 ? round(($pendingCount/$totalAppointments)*100) : 0 }}% of this period</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                <p class="text-emerald-700 text-xs font-semibold uppercase tracking-wider mb-1">Completed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
                <p class="text-xs text-emerald-600 mt-0.5">₱{{ number_format($revenueEarned, 0) }} revenue</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl p-5">
                <p class="text-red-700 text-xs font-semibold uppercase tracking-wider mb-1">Cancelled / Rejected</p>
                <p class="text-2xl font-bold text-gray-900">{{ $cancelledCount }}</p>
                <p class="text-xs text-red-600 mt-0.5">{{ $cancelPct }}% of this period</p>
            </div>
            <div class="bg-{{ $accent }}-50 border border-{{ $accent }}-100 rounded-2xl p-5">
                <p class="text-{{ $accent }}-700 text-xs font-semibold uppercase tracking-wider mb-1">Completion Rate</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</p>
                <p class="text-xs text-{{ $accent }}-600 mt-0.5">{{ $activeCustomers }} {{ Str::plural('customer', $activeCustomers) }} booked</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">{{ $trendTitle }}
                    <x-info-tip title="Appointments trend">
                        <p><strong>What it shows:</strong> bookings per {{ $bucketNoun }} (rejected and cancelled ones are not counted). The solid line is actual, the dashed line is the forecast for the {{ $horizonLabel }}.</p>
                        <p><strong>How to read it:</strong> a line sloping up means demand is growing; down means it is shrinking. Peaks are your busy {{ $bucketNoun }}s, dips are quiet ones. If the dashed line continues the solid line's direction, the forecast expects the current trend to carry on; if it bends, the forecast expects a change (often because the same period last cycle was different).</p>
                        <p><strong>Your result:</strong> {{ $periodTotal }} bookings this period; the forecast expects about {{ $vm['predicted_total'] }} in the {{ $horizonLabel }}, which is {{ $apptDir }} ({{ $vm['pct_diff'] >= 0 ? '+' : '' }}{{ $vm['pct_diff'] }}%).</p>
                        <p><strong>Use it to:</strong> plan staff hours and slot availability ahead of busy stretches.</p>
                    </x-info-tip>
                </h3>
                <p class="text-gray-400 text-xs mb-4">Solid = actual · dashed = forecast</p>
                <div class="h-56"><canvas id="volumeChart"></canvas></div>
                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                    <div><p class="text-lg font-bold text-gray-900">{{ $periodTotal }}</p><p class="text-xs text-gray-400">This period</p></div>
                    <div><p class="text-lg font-bold text-gray-900">{{ $periodAverage }}</p><p class="text-xs text-gray-400">Avg per {{ $bucketNoun }}</p></div>
                    <div><p class="text-lg font-bold text-gray-900">{{ $busiestBucket['label'] ?? '—' }}</p><p class="text-xs text-gray-400">Busiest ({{ $busiestBucket['count'] ?? 0 }})</p></div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">Revenue — {{ $windowLabel }}
                    <x-info-tip title="Revenue trend" align="right">
                        <p><strong>What it shows:</strong> peso earnings from completed appointments per {{ $bucketNoun }}, with the dashed forecast for the {{ $horizonLabel }}.</p>
                        <p><strong>How to read it:</strong> compare its shape with the appointments chart. If bookings rise but revenue stays flat, customers are choosing cheaper services. If revenue rises faster than bookings, higher-priced services are growing.</p>
                        <p><strong>Your result:</strong> ₱{{ number_format($revenueEarned, 0) }} earned this period; expected about ₱{{ number_format($predictedRevenueNextMonth, 0) }} in the {{ $horizonLabel }} ({{ $revDir }}, {{ $vm['revenue_pct'] >= 0 ? '+' : '' }}{{ $vm['revenue_pct'] }}%).</p>
                        <p>Only appointments marked Completed count as earnings.</p>
                    </x-info-tip>
                </h3>
                <p class="text-gray-400 text-xs mb-4">Completed appointments only · dashed = forecast</p>
                <div class="h-56"><canvas id="revenueChart"></canvas></div>
                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($revenueEarned, 0) }}</p><p class="text-xs text-gray-400">Earned this period</p></div>
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($avgRevenuePerAppointment, 0) }}</p><p class="text-xs text-gray-400">Avg per visit</p></div>
                    <div><p class="text-lg font-bold text-gray-900">₱{{ number_format($predictedRevenueNextMonth, 0) }}</p><p class="text-xs text-gray-400">Predicted {{ $horizonLabel }}</p></div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">Booking Distribution
                    <x-info-tip title="Booking distribution">
                        <p><strong>What it shows:</strong> of all appointments in this period, the share that were completed (green) versus cancelled or rejected (red).</p>
                        <p><strong>How to read it:</strong> a mostly green ring means most bookings turn into real visits. A growing red slice points to no-shows, overbooked slots or price/service mismatches.</p>
                        <p><strong>Your result:</strong> {{ $completedCount }} completed and {{ $cancelledCount }} cancelled/rejected, so {{ $cancelPct }}% of bookings did not go through. {{ $cancelPct >= 25 ? 'That is high; check the reasons on rejected and cancelled appointments.' : ($cancelPct >= 10 ? 'That is moderate and worth watching.' : 'That is healthy.') }}</p>
                    </x-info-tip>
                </h3>
                <p class="text-gray-400 text-xs mb-4">Completed vs cancelled / rejected</p>
                <div class="h-56 flex items-center justify-center"><canvas id="distributionChart"></canvas></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">Revenue Status
                    <x-info-tip title="Revenue status" align="right">
                        <p><strong>What it shows:</strong> <em>Earned</em> is money from completed appointments. <em>Lost</em> is what the cancelled or rejected bookings would have earned.</p>
                        <p><strong>How to read it:</strong> the closer the red bar is to the green bar, the more money the calendar is losing. The net growth figure is (earned − lost) ÷ earned.</p>
                        <p><strong>Your result:</strong> ₱{{ number_format($revenueEarned, 0) }} earned and ₱{{ number_format($revenueLost, 0) }} lost, a net of {{ $netRevenueGrowthPct }}%.</p>
                    </x-info-tip>
                </h3>
                <p class="text-gray-400 text-xs mb-4">Earned vs lost, this period</p>
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
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">Service Popularity
                        <x-info-tip title="Service popularity">
                            <p><strong>What it shows:</strong> how many appointments each service received in this period, whatever their status.</p>
                            <p><strong>How to read it:</strong> longer bars mean more requested services. A very short bar means little demand for that service right now.</p>
                            <p><strong>Your result:</strong> {{ $topSvc && $topSvc->total > 0 ? $topSvc->name . ' is the most requested with ' . $topSvc->total . ' bookings.' : 'No bookings in this period yet.' }}</p>
                            <p><strong>Use it to:</strong> decide which services to staff for or promote.</p>
                        </x-info-tip>
                    </h3>
                    @if($serviceStats->count() > 6)
                        <button type="button" onclick="toggleModalById('service-modal')" class="text-xs text-{{ $accent }}-600 hover:text-{{ $accent }}-700 font-semibold">View All ({{ $serviceStats->count() }}) →</button>
                    @endif
                </div>
                <p class="text-gray-400 text-xs mb-4">Bookings per service, this period</p>
                <div class="h-56"><canvas id="serviceChart"></canvas></div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">Revenue by Service
                        <x-info-tip title="Revenue by service" align="right">
                            <p><strong>What it shows:</strong> peso earnings per service from completed appointments in this period.</p>
                            <p><strong>How to read it:</strong> compare with Service Popularity. A service that is popular but earns little is a volume service; one that is rare but earns a lot is a premium service. Both matter.</p>
                            <p><strong>Your result:</strong> {{ $topRev && $topRev->total > 0 ? $topRev->name . ' earns the most (₱' . number_format($topRev->total, 0) . ').' : 'No completed appointments in this period yet.' }}</p>
                        </x-info-tip>
                    </h3>
                    @if($revenueServiceStats->count() > 6)
                        <button type="button" onclick="toggleModalById('revenue-service-modal')" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">View All ({{ $revenueServiceStats->count() }}) →</button>
                    @endif
                </div>
                <p class="text-gray-400 text-xs mb-4">Completed earnings per service, this period</p>
                <div class="h-56"><canvas id="revenueServiceChart"></canvas></div>
            </div>
        </div>

        <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm" onclick="if(event.target===this) toggleModalById('service-modal')">
            <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-lg shadow-2xl max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-900">All Services — Bookings</h3>
                    <button type="button" onclick="toggleModalById('service-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400"><i class="bi bi-x-lg"></i></button>
                </div>
                <p class="text-gray-400 text-xs mb-6">Most to least popular · {{ strtolower($windowLabel) }}</p>
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
                <p class="text-gray-400 text-xs mb-6">Highest to lowest · {{ strtolower($windowLabel) }}</p>
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
            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2"><i class="bi bi-graph-up-arrow text-{{ $accent }}-600"></i> Predictive Insights — {{ ucfirst($horizonLabel) }}
                <x-info-tip title="How to read the predictions">
                    <p>These four cards use only the selected interval ({{ strtolower($windowLabel) }}) and forecast the {{ $horizonLabel }}.</p>
                    <p><strong>Busiest day</strong> is the weekday with the most bookings in this interval. <strong>Trending service</strong> is the service whose bookings grew the most compared with the previous interval of the same length.</p>
                    <p><strong>Appointments and revenue</strong> are the expected totals for the {{ $horizonLabel }}. Compare each with your current figure: higher means growth, lower means a slowdown.</p>
                    <p><strong>Confidence badge:</strong> High = the forecasting methods agreed on your history. Medium = some disagreement. Low = not enough history yet; only the direction is useful.</p>
                </x-info-tip>
            </h3>
            <p class="text-gray-400 text-xs mb-6">Estimates from your own bookings, not a guarantee.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-{{ $accent }}-50 border border-{{ $accent }}-100 rounded-xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-{{ $accent }}-500 mb-1 flex items-center gap-1">Busiest Day
                        <x-info-tip><p>The weekday with the most bookings in the selected interval. Plan extra staff for it. "Next" is the coming date that falls on that weekday.</p></x-info-tip>
                    </p>
                    @if($busiestDayLabel)
                        <p class="text-lg font-bold text-gray-900">{{ $busiestDayLabel }}</p>
                        <p class="text-xs text-gray-500 mt-1">~{{ $busiestDayAvg }} appts per {{ $busiestDayLabel }}. Next: {{ $nextBusiestDate->format('M j, Y') }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic mt-1">No bookings in this period yet.</p>
                    @endif
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-500 mb-1 flex items-center gap-1">Trending Service
                        <x-info-tip><p>The service with the sharpest growth versus the previous interval of the same length (needs at least 2 bookings). Good candidate to promote or add capacity for.</p></x-info-tip>
                    </p>
                    @if($trendingService)
                        <p class="text-lg font-bold text-gray-900">{{ $trendingService->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Bookings up {{ round($trendingGrowth) }}% vs the previous period</p>
                    @else
                        <p class="text-sm text-gray-400 italic mt-1">No service is growing yet.</p>
                    @endif
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-500 flex items-center gap-1">Appts — {{ ucfirst($horizonLabel) }}
                            <x-info-tip><p>Total bookings the ensemble expects in the {{ $horizonLabel }}. This period you had {{ $periodTotal }}. {{ $confNote($apptConfidence) }}</p></x-info-tip>
                        </p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $apptConfidence === 'High' ? 'bg-emerald-100 text-emerald-700' : ($apptConfidence === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ $apptConfidence }}</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">{{ $predictedAppointmentsNextMonth }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $vm['pct_diff'] >= 0 ? '+' : '' }}{{ $vm['pct_diff'] }}% vs this period</p>
                </div>
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-500 flex items-center gap-1">Revenue — {{ ucfirst($horizonLabel) }}
                            <x-info-tip align="right"><p>Expected earnings from completed appointments in the {{ $horizonLabel }}. This period you earned ₱{{ number_format($revenueEarned, 0) }}. {{ $confNote($revenueConfidence) }}</p></x-info-tip>
                        </p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $revenueConfidence === 'High' ? 'bg-emerald-100 text-emerald-700' : ($revenueConfidence === 'Medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">{{ $revenueConfidence }}</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">₱{{ number_format($predictedRevenueNextMonth, 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $vm['revenue_pct'] >= 0 ? '+' : '' }}{{ $vm['revenue_pct'] }}% vs this period</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 mb-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2"><i class="bi bi-bar-chart-line text-{{ $accent }}-600"></i> Service Demand Forecast — {{ ucfirst($horizonLabel) }}
                <x-info-tip title="Service demand forecast">
                    <p><strong>What it shows:</strong> for each service, the bookings it had in this period, the bookings expected in the {{ $horizonLabel }}, and the direction.</p>
                    <p><strong>How to read it:</strong> ↗ means the predicted number is higher than this period (demand rising), ↘ lower (falling), — about the same. Look at the size of the gap, not only the arrow.</p>
                    <p><strong>Use it to:</strong> stock supplies and schedule groomers for the services about to pick up, and rethink services that keep falling.</p>
                    <p>Each service is forecast on its own history, so small numbers can swing; treat it as directional.</p>
                </x-info-tip>
            </h3>
            <p class="text-gray-400 text-xs mb-6">Top 10 services by predicted demand</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/60 border-b border-gray-100">
                        <tr class="text-xs uppercase text-gray-400 tracking-wider">
                            <th class="px-4 py-3 text-left">Service</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-right">This Period</th>
                            <th class="px-4 py-3 text-right">Predicted</th>
                            <th class="px-4 py-3 text-center">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($serviceDemandForecast->take(10) as $s)
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
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">No service bookings in this period yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">Most Active Pets
                <x-info-tip title="Most active pets">
                    <p>The five pets with the most appointments in the selected interval. Regulars here are good candidates for a loyalty perk. If a usual regular is missing, they may have stopped visiting.</p>
                </x-info-tip>
            </h3>
            <p class="text-gray-400 text-xs mb-6">Top 5 pets, this period</p>
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
                    <p class="text-gray-400 text-sm italic col-span-full">No pet visits in this period yet.</p>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        function infoTipToggle(btn, e) {
            e.stopPropagation();
            const panel = btn.parentElement.querySelector('.info-tip-panel');
            const wasHidden = panel.classList.contains('hidden');
            document.querySelectorAll('.info-tip-panel').forEach(p => p.classList.add('hidden'));
            if (wasHidden) panel.classList.remove('hidden');
        }
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.info-tip-panel')) document.querySelectorAll('.info-tip-panel').forEach(p => p.classList.add('hidden'));
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') document.querySelectorAll('.info-tip-panel').forEach(p => p.classList.add('hidden'));
        });
    </script>

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
