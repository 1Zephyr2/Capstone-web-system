@php
    use App\Models\Appointment;
    use App\Models\Pet;
    use App\Models\User;

    // Summary stats
    $totalAppointments  = Appointment::count();
    $completedCount     = Appointment::where('status', 'completed')->count();
    $pendingCount       = Appointment::where('status', 'pending')->count();
    $cancelledCount     = Appointment::whereIn('status', ['cancelled', 'rejected'])->count();

    // Service breakdown (real counts)
    $serviceStats = Appointment::selectRaw('service_type, count(*) as total')
        ->groupBy('service_type')
        ->orderByDesc('total')
        ->get();

    $maxService = $serviceStats->max('total') ?: 1;

    // Appointments by status for summary
    $statusBreakdown = Appointment::selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    // Monthly appointments (last 6 months)
    $monthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthly[] = [
            'label' => $month->format('M'),
            'count' => Appointment::whereYear('appointment_date', $month->year)
                                  ->whereMonth('appointment_date', $month->month)
                                  ->count(),
        ];
    }
    $maxMonthly = max(array_column($monthly, 'count')) ?: 1;

    // Top pets by appointment count
    $topPets = Pet::withCount('appointments')
        ->orderByDesc('appointments_count')
        ->take(5)
        ->get();

    $completionRate = $totalAppointments > 0
        ? round(($completedCount / $totalAppointments) * 100)
        : 0;
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Staff Insights</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('staff.dashboard') }}"    class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('staff.insights') }}"     class="text-gray-900 font-semibold transition-all duration-300 hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('staff.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-gray-900">Clinic Insights</h1>
            <p class="text-gray-500 text-sm">Real-time operational performance overview.</p>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-calendar-check text-violet-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Total Appointments</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalAppointments }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-check2-all text-emerald-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Completed</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completedCount }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-hourglass-split text-amber-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <i class="bi bi-graph-up text-teal-600 text-xl mb-2 block"></i>
                <p class="text-gray-500 text-xs mb-1">Completion Rate</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completionRate }}%</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">

            <!-- Monthly Appointments Bar Chart -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">Appointments (Last 6 Months)</h3>
                <p class="text-gray-400 text-xs mb-6">Based on appointment date</p>
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                    @foreach($monthly as $m)
                        @php $pct = $maxMonthly > 0 ? ($m['count'] / $maxMonthly) * 100 : 0; @endphp
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <span class="text-xs text-gray-500 font-semibold">{{ $m['count'] }}</span>
                            <div class="w-full rounded-t-lg transition-all duration-700 hover:bg-violet-500"
                                 style="height: {{ max($pct, 4) }}%; background: rgba(139,92,246,0.75);">
                            </div>
                            <span class="text-xs text-gray-400">{{ $m['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Service Breakdown -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">Service Popularity</h3>
                <p class="text-gray-400 text-xs mb-6">Based on completed + all appointments</p>
                <div class="space-y-5">
                    @forelse($serviceStats as $s)
                        @php
                            $pct = round(($s->total / $maxService) * 100);
                            $label = match($s->service_type) {
                                'grooming'    => 'Grooming',
                                'veterinary'  => 'Veterinary Checkup',
                                'vaccination' => 'Vaccination',
                                'boarding'    => 'Boarding',
                                default       => ucfirst($s->service_type),
                            };
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-600">{{ $label }}</span>
                                <span class="font-medium text-violet-600">{{ $s->total }} appts</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-violet-500 rounded-full transition-all duration-1000"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">No appointment data yet.</p>
                    @endforelse
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
                                <span class="text-gray-600">{{ $cfg['label'] }}</span>
                                <span class="text-gray-500">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
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
                                         class="w-8 h-8 rounded-full object-cover border border-violet-200">
                                @else
                                    <span class="text-lg">{{ $pet->type === 'cat' ? '🐱' : '🐶' }}</span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $pet->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $pet->breed }}</p>
                                </div>
                            </div>
                            <span class="text-violet-600 font-semibold text-sm">{{ $pet->appointments_count }} appts</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm italic">No pet data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center border-t border-gray-200 text-gray-400 text-sm mt-12">
        &copy; {{ date('Y') }} FURCARE | Staff System.
    </footer>
</body>
</html>