@php
    use App\Models\Appointment;
    use App\Models\Pet;
    use App\Models\User;

    $totalAppointments = Appointment::count();
    $completedCount    = Appointment::where('status', 'completed')->count();
    $pendingCount      = Appointment::where('status', 'pending')->count();
    $cancelledCount    = Appointment::whereIn('status', ['cancelled', 'rejected'])->count();

    $serviceStats = Appointment::selectRaw('service_type, count(*) as total')
        ->groupBy('service_type')->orderByDesc('total')->get();
    $maxService = $serviceStats->max('total') ?: 1;

    $statusBreakdown = Appointment::selectRaw('status, count(*) as total')
        ->groupBy('status')->pluck('total', 'status');

    $monthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $monthly[] = [
            'label' => $month->format('M'),
            'count' => Appointment::whereYear('appointment_date', $month->year)
                                  ->whereMonth('appointment_date', $month->month)->count(),
        ];
    }
    $maxMonthly = max(array_column($monthly, 'count')) ?: 1;

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
<body class="bg-indigo-950 text-indigo-100 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-500/20 border border-rose-500/30">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-indigo-300">
                <a href="{{ route('admin.dashboard') }}"    class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}"    class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="text-white font-semibold transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Clinic Insights</h1>
            <p class="text-indigo-300 text-sm">Real-time operational performance overview.</p>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <i class="bi bi-calendar-check text-indigo-400 text-xl mb-2 block"></i>
                <p class="text-indigo-400 text-xs mb-1">Total Appointments</p>
                <p class="text-2xl font-bold text-white">{{ $totalAppointments }}</p>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <i class="bi bi-check2-all text-emerald-400 text-xl mb-2 block"></i>
                <p class="text-indigo-400 text-xs mb-1">Completed</p>
                <p class="text-2xl font-bold text-white">{{ $completedCount }}</p>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <i class="bi bi-people text-violet-400 text-xl mb-2 block"></i>
                <p class="text-indigo-400 text-xs mb-1">Total Owners</p>
                <p class="text-2xl font-bold text-white">{{ $totalOwners }}</p>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <i class="bi bi-graph-up text-teal-400 text-xl mb-2 block"></i>
                <p class="text-indigo-400 text-xs mb-1">Completion Rate</p>
                <p class="text-2xl font-bold text-white">{{ $completionRate }}%</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">

            <!-- Monthly Bar Chart -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-2">Appointments (Last 6 Months)</h3>
                <p class="text-indigo-500 text-xs mb-6">Based on appointment date</p>
                <div class="h-48 flex items-end justify-between gap-3 px-2">
                    @foreach($monthly as $m)
                        @php $pct = ($m['count'] / $maxMonthly) * 100; @endphp
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <span class="text-xs text-indigo-400 font-semibold">{{ $m['count'] }}</span>
                            <div class="w-full rounded-t-lg transition-all duration-700 hover:bg-indigo-500"
                                 style="height: {{ max($pct, 4) }}%; background: rgba(99,102,241,0.35);">
                            </div>
                            <span class="text-xs text-indigo-500">{{ $m['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Service Breakdown -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-2">Service Popularity</h3>
                <p class="text-indigo-500 text-xs mb-6">Total appointments per service</p>
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
                                <span class="text-indigo-200">{{ $label }}</span>
                                <span class="font-medium text-indigo-400">{{ $s->total }} appts</span>
                            </div>
                            <div class="h-2 bg-indigo-950 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-indigo-500 text-sm italic">No appointment data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

            <!-- Status Breakdown -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-6">Appointment Status Breakdown</h3>
                <div class="space-y-4">
                    @php
                        $statusConfig = [
                            'pending'   => ['color' => 'bg-amber-500',  'label' => 'Pending'],
                            'approved'  => ['color' => 'bg-emerald-500','label' => 'Approved'],
                            'completed' => ['color' => 'bg-blue-500',   'label' => 'Completed'],
                            'rejected'  => ['color' => 'bg-red-500',    'label' => 'Rejected'],
                            'cancelled' => ['color' => 'bg-slate-500',  'label' => 'Cancelled'],
                        ];
                    @endphp
                    @foreach($statusConfig as $key => $cfg)
                        @php $count = $statusBreakdown[$key] ?? 0; $pct = $totalAppointments > 0 ? round(($count/$totalAppointments)*100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-indigo-200">{{ $cfg['label'] }}</span>
                                <span class="text-indigo-400">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 bg-indigo-950 rounded-full overflow-hidden">
                                <div class="h-full {{ $cfg['color'] }} rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Pets -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-6">Most Active Pets</h3>
                <div class="space-y-3">
                    @forelse($topPets as $i => $pet)
                        <div class="flex items-center justify-between bg-indigo-950/50 border border-indigo-800/50 rounded-xl p-3">
                            <div class="flex items-center gap-3">
                                <span class="text-indigo-500 font-bold text-sm w-5">#{{ $i + 1 }}</span>
                                @if($pet->photo)
                                    <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}"
                                         class="w-8 h-8 rounded-full object-cover border border-indigo-500/30">
                                @else
                                    <span class="text-lg">{{ $pet->type === 'cat' ? '🐱' : '🐶' }}</span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $pet->name }}</p>
                                    <p class="text-xs text-indigo-400">{{ $pet->breed }}</p>
                                </div>
                            </div>
                            <span class="text-indigo-400 font-semibold text-sm">{{ $pet->appointments_count }} appts</span>
                        </div>
                    @empty
                        <p class="text-indigo-500 text-sm italic">No pet data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>