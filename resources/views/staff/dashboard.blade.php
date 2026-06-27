<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Staff Dashboard</title>
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
<body class="bg-slate-950 text-slate-200 antialiased relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-violet-500/10 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#0c1220] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-500/20 border border-violet-500/30">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="{{ route('staff.dashboard') }}"    class="text-white font-semibold transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('staff.insights') }}"     class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-6 py-12 relative z-10">

        <!-- Flash -->
        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white">Clinic Overview</h1>
            <p class="text-slate-400">Welcome back, <span class="text-violet-300">{{ auth()->user()->name }}</span>. Here's today's snapshot.</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            @php
                $statCards = [
                    ['icon' => 'sun',            'label' => "Today's Appointments", 'value' => $stats['todays_appointments'], 'color' => 'text-yellow-400'],
                    ['icon' => 'paw',            'label' => 'Total Pets',           'value' => $stats['total_pets'],          'color' => 'text-violet-400'],
                    ['icon' => 'hourglass-split','label' => 'Pending Requests',     'value' => $stats['pending_appointments'],'color' => 'text-amber-400'],
                    ['icon' => 'people',         'label' => 'Pet Owners',           'value' => $stats['total_owners'],        'color' => 'text-teal-400'],
                ];
            @endphp
            @foreach($statCards as $card)
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 hover:border-violet-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <i class="bi bi-{{ $card['icon'] }} {{ $card['color'] }} text-xl mb-2 block"></i>
                <h5 class="text-slate-400 text-sm mb-1">{{ $card['label'] }}</h5>
                <p class="text-2xl font-bold text-white">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>

        <!-- Main Content -->
        <div class="grid md:grid-cols-3 gap-6">

            <!-- Pending Requests -->
            <div class="md:col-span-2 bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-violet-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Pending Requests</h3>
                    @if($stats['pending_appointments'] > 0)
                        <a href="{{ route('staff.appointments', ['status' => 'pending']) }}"
                           class="text-xs text-violet-400 hover:text-violet-300 transition-all">View all →</a>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($pendingAppointments as $appt)
                        <div class="flex items-center justify-between bg-slate-950/50 border border-slate-800 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                                    <i class="bi bi-hourglass-split text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                    <p class="text-xs text-slate-400">{{ $appt->user->name }} &bull; {{ $appt->appointment_date->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <!-- Quick Approve -->
                                <form method="POST" action="{{ route('staff.appointments.approve', $appt) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600/80 hover:bg-emerald-500 text-white text-xs font-semibold transition-all hover:scale-105">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                                <!-- View Details -->
                                <a href="{{ route('staff.appointments') }}"
                                   class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                                    Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="bi bi-check-circle text-3xl text-slate-700 mb-2 block"></i>
                            <p class="text-slate-500 text-sm italic">No pending requests — all clear!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Upcoming Today -->
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-violet-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                    <h3 class="text-base font-bold text-white mb-4">Upcoming Appointments</h3>
                    <div class="space-y-3">
                        @forelse($upcomingAppointments as $appt)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                                    <i class="bi bi-calendar-check text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $appt->pet->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $appt->service_label }}</p>
                                    <p class="text-xs text-emerald-400">{{ $appt->appointment_date->format('M d — g:i A') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-violet-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                    <h3 class="text-base font-bold text-white mb-4">Quick Actions</h3>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('staff.appointments') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-800/50 hover:bg-violet-600 transition-all duration-300 hover:translate-x-1 text-sm">
                            <i class="bi bi-calendar-event text-violet-400"></i> View All Appointments
                        </a>
                        <a href="{{ route('staff.directory') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-800/50 hover:bg-violet-600 transition-all duration-300 hover:translate-x-1 text-sm">
                            <i class="bi bi-paw text-violet-400"></i> Pet Directory
                        </a>
                        <a href="{{ route('staff.appointments', ['status' => 'pending']) }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-300 hover:translate-x-1 text-sm text-amber-300">
                            <i class="bi bi-hourglass-split"></i>
                            Pending Requests
                            @if($stats['pending_appointments'] > 0)
                                <span class="ml-auto bg-amber-500 text-slate-900 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['pending_appointments'] }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 py-10 text-center border-t border-white/5 text-slate-500 text-sm">
        &copy; {{ date('Y') }} FURCARE | Staff System.
    </footer>

</body>
</html>