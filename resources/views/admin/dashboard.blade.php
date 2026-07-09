@php
    $stats = [
        'pending_appointments'  => App\Models\Appointment::where('status', 'pending')->count(),
        'todays_appointments'   => App\Models\Appointment::whereDate('appointment_date', today())
                                        ->where('status', 'approved')->count(),
        'total_pets'            => App\Models\Pet::count(),
        'total_owners'          => App\Models\User::where('role', 'owner')->count(),
        'total_staff'           => App\Models\User::where('role', 'staff')->count(),
        'total_appointments'    => App\Models\Appointment::count(),
    ];

    $recentAppointments = App\Models\Appointment::with(['user', 'pet'])
        ->orderByDesc('created_at')
        ->take(5)
        ->get();

    $pendingAppointments = App\Models\Appointment::with(['user', 'pet'])
        ->where('status', 'pending')
        ->orderBy('appointment_date')
        ->take(5)
        ->get();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Dashboard</title>
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
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('admin.dashboard') }}"    class="text-gray-900 font-semibold transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}"    class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-rose-700 font-semibold transition-all duration-300 bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100 hover:scale-105">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-gray-900">Admin Overview</h1>
            <p class="text-gray-500 text-sm">Welcome back, <span class="text-gray-900">{{ auth()->user()->name }}</span>. Here's the system snapshot.</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-3 lg:grid-cols-6 gap-4 mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            @php
                $statCards = [
                    ['icon' => 'hourglass-split', 'label' => 'Pending',       'value' => $stats['pending_appointments'],  'color' => 'text-amber-600',  'bg' => 'bg-amber-50',  'border' => 'border-amber-200'],
                    ['icon' => 'sun',             'label' => "Today's Appts", 'value' => $stats['todays_appointments'],   'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200'],
                    ['icon' => 'calendar-check',  'label' => 'Total Appts',   'value' => $stats['total_appointments'],    'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200'],
                    ['icon' => 'paw',             'label' => 'Total Pets',    'value' => $stats['total_pets'],            'color' => 'text-violet-600', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200'],
                    ['icon' => 'people',          'label' => 'Owners',        'value' => $stats['total_owners'],          'color' => 'text-teal-600',   'bg' => 'bg-teal-50',   'border' => 'border-teal-200'],
                    ['icon' => 'person-badge',    'label' => 'Staff',         'value' => $stats['total_staff'],           'color' => 'text-rose-600',   'bg' => 'bg-rose-50',   'border' => 'border-rose-200'],
                ];
            @endphp
            @foreach($statCards as $card)
                <div class="{{ $card['bg'] }} border {{ $card['border'] }} rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                    <i class="bi bi-{{ $card['icon'] }} {{ $card['color'] }} text-xl mb-2 block"></i>
                    <p class="text-gray-500 text-xs mb-1">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            <!-- Pending Appointments -->
            <div class="md:col-span-2 bg-white border border-gray-200 rounded-2xl p-8 shadow-sm reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-gray-900">Pending Appointments</h3>
                    @if($stats['pending_appointments'] > 0)
                        <a href="{{ route('admin.appointments', ['status' => 'pending']) }}"
                           class="text-xs text-gray-500 hover:text-indigo-700 transition-all">View all →</a>
                    @endif
                </div>
                <div class="space-y-3">
                    @forelse($pendingAppointments as $appt)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                    <i class="bi bi-hourglass-split text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                    <p class="text-xs text-gray-500">{{ $appt->user->name }} &bull; {{ $appt->appointment_date->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <form method="POST" action="{{ route('admin.appointments.approve', $appt) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-all hover:scale-105">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                                <a href="{{ route('admin.appointments') }}"
                                   class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-semibold transition-all">
                                    Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="bi bi-check-circle text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-gray-400 text-sm italic">No pending requests — all clear!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('admin.appointments') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-indigo-50 border border-gray-100 transition-all hover:translate-x-1 text-sm">
                            <i class="bi bi-calendar-event text-gray-500"></i> All Appointments
                        </a>
                        <a href="{{ route('admin.directory') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-indigo-50 border border-gray-100 transition-all hover:translate-x-1 text-sm">
                            <i class="bi bi-paw text-gray-500"></i> Pet Directory
                        </a>
                        <a href="{{ route('admin.insights') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-indigo-50 border border-gray-100 transition-all hover:translate-x-1 text-sm">
                            <i class="bi bi-graph-up text-gray-500"></i> Insights
                        </a>
                        <a href="{{ route('admin.panel') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all hover:translate-x-1 text-sm text-rose-700">
                            <i class="bi bi-gear"></i> Admin Panel
                        </a>
                        @if($stats['pending_appointments'] > 0)
                            <a href="{{ route('admin.appointments', ['status' => 'pending']) }}"
                               class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-all hover:translate-x-1 text-sm text-amber-700">
                                <i class="bi bi-hourglass-split"></i> Pending Requests
                                <span class="ml-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['pending_appointments'] }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4">Recent Appointments</h3>
                    <div class="space-y-3">
                        @foreach($recentAppointments as $appt)
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }} shrink-0 mt-0.5">
                                    {{ ucfirst($appt->status) }}
                                </span>
                                <div>
                                    <p class="text-xs text-gray-900 font-medium">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                    <p class="text-xs text-gray-400">{{ $appt->appointment_date->format('M d, g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>