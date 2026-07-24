@php
    use App\Models\Appointment;
    use App\Models\Pet;
    use App\Models\User;

    $totalAppointments = Appointment::count();
    $completedCount    = Appointment::where('status', 'completed')->count();
    $pendingCount      = Appointment::where('status', 'pending')->count();
    $cancelledCount    = Appointment::whereIn('status', ['cancelled', 'rejected'])->count();

    // Every active service shows up, even with zero bookings, so popularity is accurate
    $allServices = \App\Models\Service::notArchived()->orderBy('category')->orderBy('name')->get();
    $serviceCounts = Appointment::whereNotNull('service_id')
        ->selectRaw('service_id, count(*) as total')
        ->groupBy('service_id')
        ->pluck('total', 'service_id');

    $serviceStats = $allServices->map(function ($svc) use ($serviceCounts) {
        return (object) ['name' => $svc->name, 'total' => $serviceCounts[$svc->id] ?? 0];
    })->sortByDesc('total')->values();
    $maxService = $serviceStats->max('total') ?: 1;
    $topServices = $serviceStats->take(6);

    $statusBreakdown = Appointment::selectRaw('status, count(*) as total')
        ->groupBy('status')->pluck('total', 'status');

    $trendPeriod = request('period', '6months');

    $monthly = [];
    if ($trendPeriod === 'week') {
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $monthly[] = ['label' => $day->format('D'), 'count' => Appointment::whereDate('appointment_date', $day->toDateString())->count()];
        }
        $trendTitle = 'Appointments (Last 7 Days)';
    } elseif ($trendPeriod === 'month') {
        for ($i = 4; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            $monthly[] = ['label' => $weekStart->format('M d'), 'count' => Appointment::whereBetween('appointment_date', [$weekStart, $weekEnd])->count()];
        }
        $trendTitle = 'Appointments (Last 5 Weeks)';
    } elseif ($trendPeriod === 'year') {
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthly[] = ['label' => $month->format('M'), 'count' => Appointment::whereYear('appointment_date', $month->year)->whereMonth('appointment_date', $month->month)->count()];
        }
        $trendTitle = 'Appointments (Last 12 Months)';
    } else {
        $trendPeriod = '6months';
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthly[] = ['label' => $month->format('M'), 'count' => Appointment::whereYear('appointment_date', $month->year)->whereMonth('appointment_date', $month->month)->count()];
        }
        $trendTitle = 'Appointments (Last 6 Months)';
    }
    $maxMonthly = max(array_column($monthly, 'count')) ?: 1;

    $periodTotal   = array_sum(array_column($monthly, 'count'));
    $periodAverage = count($monthly) > 0 ? round($periodTotal / count($monthly), 1) : 0;
    $busiestBucket = collect($monthly)->sortByDesc('count')->first();
    $bucketNoun    = match($trendPeriod) { 'week' => 'day', 'month' => 'week', 'year' => 'month', default => 'month' };

    $topPets = Pet::withCount('appointments')->orderByDesc('appointments_count')->take(5)->get();

    $completionRate = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100) : 0;

    $totalOwners = User::where('role', 'owner')->count();
    $totalStaff  = User::where('role', 'staff')->count();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Insights</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-10');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
        });
    </script>
    <script>function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }</script>
    <script>
        function toggleServiceModal() {
            const modal = document.getElementById('service-modal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('admin.dashboard') }}"    class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}"    class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="text-gray-900 font-semibold transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-rose-700 font-semibold transition-all bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
            <a href="{{ route('admin.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><svg class="inline w-[1em] h-[1em]" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg> Pets</a>
            <a href="{{ route('admin.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i> Appointments</a>
            <a href="{{ route('admin.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
            <a href="{{ route('admin.panel') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-gear"></i> Admin Panel</a>
            <form action="{{ route('admin.logout') }}" method="POST" >
                @csrf
                <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
    </div>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-gray-900">Clinic Insights</h1>
            <p class="text-gray-500 text-sm">Real-time operational performance overview.</p>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-calendar-check text-gray-500 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Total Appointments</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalAppointments }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-check2-all text-emerald-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Completed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-people text-violet-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Total Owners</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalOwners }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-graph-up text-teal-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Completion Rate</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6 items-start">

            <!-- Monthly Bar Chart -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $trendTitle }}</h3>
                        <p class="text-gray-400 text-xs">Based on appointment date</p>
                    </div>
                    <div class="flex gap-1 bg-gray-50 p-1 rounded-lg border border-gray-100">
                        @foreach(['week' => 'Week', 'month' => 'Month', '6months' => '6 Months', 'year' => 'Year'] as $key => $label)
                            <a href="{{ route('admin.insights', ['period' => $key]) }}"
                               class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $trendPeriod === $key ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                    @foreach($monthly as $m)
                        @php $pct = ($m['count'] / $maxMonthly) * 100; @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end gap-2">
                            <span class="text-xs text-gray-500 font-semibold">{{ $m['count'] }}</span>
                            <div class="w-full rounded-t-lg transition-all duration-700 hover:bg-indigo-500"
                                 style="height: {{ max($pct, 4) }}%; background: rgba(99,102,241,0.75);">
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $m['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-5 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $periodTotal }}</p>
                        <p class="text-xs text-gray-400">Total this period</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $periodAverage }}</p>
                        <p class="text-xs text-gray-400">Avg per {{ $bucketNoun }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $busiestBucket['label'] ?? '—' }}</p>
                        <p class="text-xs text-gray-400">Busiest ({{ $busiestBucket['count'] ?? 0 }})</p>
                    </div>
                </div>
            </div>

            <!-- Service Breakdown -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-gray-900">Service Popularity</h3>
                    @if($serviceStats->count() > 6)
                        <button type="button" onclick="toggleServiceModal()" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold transition-all">
                            View All ({{ $serviceStats->count() }}) →
                        </button>
                    @endif
                </div>
                <p class="text-gray-400 text-xs mb-6">Total appointments per service</p>
                <div class="space-y-5">
                    @forelse($topServices as $s)
                        @php $pct = round(($s->total / $maxService) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-500">{{ $s->name }}</span>
                                <span class="font-medium text-gray-500">{{ $s->total }} appts</span>
                            </div>
                            <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">No services configured yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- All Services Modal -->
            <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
                 onclick="if(event.target===this) toggleServiceModal()">
                <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-lg shadow-2xl max-h-[80vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-lg font-bold text-gray-900">All Services — Popularity</h3>
                        <button type="button" onclick="toggleServiceModal()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-all">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <p class="text-gray-400 text-xs mb-6">Total appointments per service, most to least popular</p>
                    <div class="space-y-5">
                        @foreach($serviceStats as $s)
                            @php $pct = round(($s->total / $maxService) * 100); @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-500">{{ $s->name }}</span>
                                    <span class="font-medium text-gray-500">{{ $s->total }} appts</span>
                                </div>
                                <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

            <!-- Status Breakdown -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <h3 class="font-bold text-gray-900 mb-6">Appointment Status Breakdown</h3>
                <div class="space-y-4">
                    @php
                        $statusConfig = [
                            'pending'   => ['color' => 'bg-amber-500',  'label' => 'Pending'],
                            'approved'  => ['color' => 'bg-emerald-500','label' => 'Approved'],
                            'completed' => ['color' => 'bg-blue-500',   'label' => 'Completed'],
                            'rejected'  => ['color' => 'bg-red-500',    'label' => 'Rejected'],
                            'cancelled' => ['color' => 'bg-gray-400',  'label' => 'Cancelled'],
                        ];
                    @endphp
                    @foreach($statusConfig as $key => $cfg)
                        @php $count = $statusBreakdown[$key] ?? 0; $pct = $totalAppointments > 0 ? round(($count/$totalAppointments)*100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-500">{{ $cfg['label'] }}</span>
                                <span class="text-gray-500">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                                <div class="h-full {{ $cfg['color'] }} rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Pets -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <h3 class="font-bold text-gray-900 mb-6">Most Active Pets</h3>
                <div class="space-y-3">
                    @forelse($topPets as $i => $pet)
                        <div class="flex items-center justify-between bg-gray-50/50 border border-gray-200 rounded-xl p-3">
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 font-bold text-sm w-5">#{{ $i + 1 }}</span>
                                @if($pet->photo)
                                    <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}"
                                         class="w-8 h-8 rounded-full object-cover border border-indigo-200">
                                @else
                                    <span class="text-lg">{{ $pet->type === 'cat' ? '🐱' : '🐶' }}</span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $pet->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $pet->breed }}</p>
                                </div>
                            </div>
                            <span class="text-gray-500 font-semibold text-sm">{{ $pet->appointments_count }} appts</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">No pet data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>