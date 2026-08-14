<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Staff Dashboard</title>
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
</head>
<body class="bg-gray-50 text-gray-800 antialiased relative overflow-x-hidden min-h-screen flex flex-col">

    <nav class="relative w-full z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('staff.dashboard') }}"    class="hover:text-violet-600 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="hover:text-violet-600 transition-all hover:scale-105">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-violet-600 transition-all hover:scale-105">Appointments</a>
                <a href="{{ route('staff.insights') }}"     class="hover:text-violet-600 transition-all hover:scale-105">Insights</a>
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => 'staff.'])
            <form action="{{ route('staff.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
            <a href="{{ route('staff.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><svg class="inline w-[1em] h-[1em]" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg> Pets</a>
            <a href="{{ route('staff.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i> Appointments</a>
            <a href="{{ route('staff.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
            <form action="{{ route('staff.logout') }}" method="POST" >
                @csrf
                <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
    </div>

    <main class="flex-grow container mx-auto px-6 py-12 relative z-10">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <h1 class="text-3xl font-bold text-gray-900">Clinic Overview</h1>
            <p class="text-gray-500 text-sm">Welcome back, <span class="text-violet-700">{{ auth()->user()->name }}</span>. Here's today's snapshot.</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-5 gap-4 mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            @php
                $statCards = [
                    ['icon' => 'sun',             'label' => "Today's Appts",  'value' => $stats['todays_appointments'],  'color' => 'text-yellow-600', 'bg' => 'bg-white',   'border' => 'border-gray-200'],
                    ['icon' => 'paw',             'label' => 'Total Pets',     'value' => $stats['total_pets'],           'color' => 'text-violet-600', 'bg' => 'bg-white',   'border' => 'border-gray-200'],
                    ['icon' => 'hourglass-split', 'label' => 'Pending',        'value' => $stats['pending_appointments'], 'color' => 'text-amber-600',  'bg' => 'bg-amber-50',    'border' => 'border-amber-200'],
                    ['icon' => 'people',          'label' => 'Pet Owners',     'value' => $stats['total_owners'],         'color' => 'text-teal-600',   'bg' => 'bg-white',   'border' => 'border-gray-200'],
                    ['icon' => 'person-slash',    'label' => 'Inactive (3mo)', 'value' => $stats['inactive_owners'],      'color' => 'text-red-600',    'bg' => 'bg-red-50',      'border' => 'border-red-200'],
                ];
            @endphp
            @foreach($statCards as $card)
                <div class="{{ $card['bg'] }} border {{ $card['border'] }} rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                    <i class="bi bi-{{ $card['icon'] }} {{ $card['color'] }} text-xl mb-2 block"></i>
                    <h5 class="text-gray-500 text-xs mb-1">{{ $card['label'] }}</h5>
                    <p class="text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Main Content -->
        <div class="grid md:grid-cols-3 gap-6">

            <!-- Pending Requests -->
            <div class="md:col-span-2 bg-white border border-gray-200 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Pending Requests</h3>
                    @if($stats['pending_appointments'] > 0)
                        <a href="{{ route('staff.appointments', ['status' => 'pending']) }}"
                           class="text-xs text-violet-600 hover:text-violet-700 transition-all">View all →</a>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($pendingAppointments as $appt)
                        <div class="flex items-center justify-between bg-gray-50/50 border border-gray-200 rounded-xl p-4">
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
                                <form method="POST" action="{{ route('staff.appointments.approve', $appt) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-all hover:scale-105">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                                <a href="{{ route('staff.appointments') }}"
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

            <!-- Sidebar -->
            <div class="space-y-5">

                <!-- Upcoming -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm">Upcoming Appointments</h3>
                    <div class="space-y-3">
                        @forelse($upcomingAppointments as $appt)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mt-0.5">
                                    <i class="bi bi-calendar-check text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $appt->pet->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $appt->service_label }}</p>
                                    <p class="text-xs text-emerald-600">{{ $appt->appointment_date->format('M d — g:i A') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">No upcoming appointments.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm">Quick Actions</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('staff.appointments') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-violet-600 transition-all hover:translate-x-1 text-sm">
                            <i class="bi bi-calendar-event text-violet-600"></i> All Appointments
                        </a>
                        <a href="{{ route('staff.directory') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-violet-600 transition-all hover:translate-x-1 text-sm">
                            <svg class="inline w-[1em] h-[1em] text-violet-600" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg> Pet Directory
                        </a>
                        <a href="{{ route('staff.appointments', ['status' => 'pending']) }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-all hover:translate-x-1 text-sm text-amber-700">
                            <i class="bi bi-hourglass-split"></i> Pending
                            @if($stats['pending_appointments'] > 0)
                                <span class="ml-auto bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['pending_appointments'] }}</span>
                            @endif
                        </a>
                        @if($stats['inactive_owners'] > 0)
                        <a href="{{ route('staff.directory', ['filter' => 'inactive']) }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 transition-all hover:translate-x-1 text-sm text-red-700">
                            <i class="bi bi-person-slash"></i> Inactive Owners
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['inactive_owners'] }}</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 py-8 text-center border-t border-gray-200 text-gray-400 text-sm">
        &copy; {{ date('Y') }} FURCARE | Staff System.
    </footer>
</body>
</html>