@php
    $pet = App\Models\Pet::with(['user', 'appointments' => function($q) {
        $q->orderByDesc('appointment_date');
    }])->findOrFail($id);

    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $isStaff = auth()->check() && auth()->user()->role === 'staff';

    $nextAppointment = $pet->appointments
        ->where('status', 'approved')
        ->where('appointment_date', '>=', now())
        ->sortBy('appointment_date')
        ->first();

    $completedAppointments = $pet->appointments->where('status', 'completed')->sortByDesc('appointment_date');

    if ($isAdmin) {
        $bgMain = 'bg-indigo-950'; $bgNav = 'bg-[#1e1b4b]';
        $badgeBg = 'bg-rose-500/20'; $badgeBorder = 'border-rose-500/30'; $badgeText = 'text-rose-300';
        $accentText = 'text-indigo-400'; $cardBg = 'bg-indigo-900/20';
        $cardBorder = 'border-indigo-800/80'; $accentBorder = 'border-indigo-500/50';
        $portalLabel = 'ADMIN PORTAL';
    } else {
        $bgMain = 'bg-slate-950'; $bgNav = 'bg-[#0c1220]';
        $badgeBg = 'bg-violet-500/20'; $badgeBorder = 'border-violet-500/30'; $badgeText = 'text-violet-300';
        $accentText = 'text-violet-400'; $cardBg = 'bg-slate-900/40';
        $cardBorder = 'border-slate-800/80'; $accentBorder = 'border-violet-500/50';
        $portalLabel = 'STAFF PORTAL';
    }
    $prefix = $isAdmin ? 'admin' : 'staff';
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $pet->name }}</title>
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

        function showVisitDetails(service, date, notes, status) {
            document.getElementById('visit-service').innerText = service;
            document.getElementById('visit-date').innerText    = date;
            document.getElementById('visit-notes').innerText   = notes || '—';
            document.getElementById('visit-status').innerText  = status;
            showModal('visit-modal', 'visit-modal-content');
        }

        function showOwnerProfile(name, email) {
            document.getElementById('owner-name').innerText  = name;
            document.getElementById('owner-email').innerText = email;
            showModal('profile-modal', 'profile-modal-content');
        }

        function showModal(id, contentId) {
            const modal   = document.getElementById(id);
            const content = document.getElementById(contentId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal(id, contentId) {
            const modal   = document.getElementById(id);
            const content = document.getElementById(contentId);
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }
    </script>
</head>
<body class="{{ $bgMain }} text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix . '.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $badgeText }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md {{ $badgeBg }} border {{ $badgeBorder }}">
                    {{ $portalLabel }}
                </span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="{{ route($prefix . '.dashboard') }}"    class="hover:text-white transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix . '.directory') }}"    class="hover:text-white transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix . '.appointments') }}" class="hover:text-white transition-all hover:scale-105">Appointments</a>
                <a href="{{ route($prefix . '.insights') }}"     class="hover:text-white transition-all hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="flex items-center gap-6">
                @if($pet->photo_url)
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                         class="w-24 h-24 rounded-2xl object-cover border-2 border-violet-500/30 shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-2xl {{ $isAdmin ? 'bg-indigo-500/20 border-indigo-500/30' : 'bg-violet-500/20 border-violet-500/30' }} flex items-center justify-center text-4xl border">
                        {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                    </div>
                @endif
                <div>
                    <h1 class="text-4xl font-bold text-white">{{ $pet->name }}</h1>
                    <p class="{{ $accentText }} font-medium">{{ $pet->breed }} &bull; {{ $pet->age }} {{ Str::plural('Year', $pet->age) }} Old</p>
                    <span class="text-xs text-slate-500 uppercase tracking-widest">{{ ucfirst($pet->type) }}</span>
                </div>
            </div>
            <a href="{{ route($prefix . '.directory') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                ← Back
            </a>
        </header>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Appointment History -->
            <div class="lg:col-span-2 space-y-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                        <i class="bi bi-clock-history {{ $accentText }}"></i> Appointment History
                    </h3>
                    <div class="space-y-3">
                        @forelse($completedAppointments as $appt)
                            <button onclick="showVisitDetails(
                                        '{{ addslashes($appt->service_label) }}',
                                        '{{ $appt->appointment_date->format('F d, Y — g:i A') }}',
                                        '{{ addslashes($appt->notes ?? '') }}',
                                        '{{ ucfirst($appt->status) }}'
                                     )"
                                    class="w-full text-left p-4 border-l-2 {{ $accentBorder }} {{ $cardBg }} rounded-r-lg hover:bg-slate-800 transition-all hover:scale-[1.01] hover:shadow-xl cursor-pointer">
                                <p class="font-medium text-white">{{ $appt->service_label }}</p>
                                <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                            </button>
                        @empty
                            <div class="text-center py-8">
                                <i class="bi bi-calendar-x text-3xl text-slate-700 mb-2 block"></i>
                                <p class="text-slate-500 text-sm italic">No completed appointments yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Active appointments -->
                @php
                    $activeAppts = $pet->appointments->whereIn('status', ['pending','approved'])->sortBy('appointment_date');
                @endphp
                @if($activeAppts->count() > 0)
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-event {{ $accentText }}"></i> Pending / Upcoming
                    </h3>
                    <div class="space-y-3">
                        @foreach($activeAppts as $appt)
                            <div class="flex items-center justify-between p-4 border-l-2 border-amber-500/50 bg-amber-500/5 rounded-r-lg">
                                <div>
                                    <p class="font-medium text-white">{{ $appt->service_label }}</p>
                                    <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-300">

                <!-- Special Notes -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-info-circle {{ $accentText }}"></i> About {{ $pet->name }}
                    </h4>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        {{ $pet->special_notes ?: 'No special notes.' }}
                    </p>
                </div>

                <!-- Next Appointment -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Next Appointment
                    </h4>
                    @if($nextAppointment)
                        <p class="text-lg font-semibold text-white">{{ $nextAppointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-sm text-slate-400">{{ $nextAppointment->appointment_date->format('g:i A') }} &bull; {{ $nextAppointment->service_label }}</p>
                    @else
                        <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                    @endif
                </div>

                <!-- Owner Info -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-person {{ $accentText }}"></i> Owner
                    </h4>
                    <p class="text-sm text-white font-semibold">{{ $pet->user->name }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $pet->user->email }}</p>
                    <button onclick="showOwnerProfile('{{ addslashes($pet->user->name) }}', '{{ addslashes($pet->user->email) }}')"
                            class="w-full mt-4 text-center px-4 py-2 border {{ $cardBorder }} rounded-lg text-sm hover:bg-slate-800 transition-all">
                        View Profile
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Visit Details Modal -->
    <div id="visit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('visit-modal','visit-modal-content')">
        <div id="visit-modal-content" class="{{ $isAdmin ? 'bg-indigo-950 border-indigo-800' : 'bg-slate-900 border-slate-800' }} border rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Service</label>
                    <p id="visit-service" class="text-white {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Date & Time</label>
                    <p id="visit-date" class="text-white {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Status</label>
                    <p id="visit-status" class="text-white {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Notes</label>
                    <p id="visit-notes" class="text-slate-300 {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1 text-sm"></p>
                </div>
                <button onclick="closeModal('visit-modal','visit-modal-content')"
                        class="w-full mt-4 px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>

    <!-- Owner Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('profile-modal','profile-modal-content')">
        <div id="profile-modal-content" class="{{ $isAdmin ? 'bg-indigo-950 border-indigo-800' : 'bg-slate-900 border-slate-800' }} border rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Owner Profile</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Name</label>
                    <p id="owner-name" class="text-white {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }}">Email</label>
                    <p id="owner-email" class="text-white {{ $isAdmin ? 'bg-indigo-900 border-indigo-700' : 'bg-slate-950 border-slate-800' }} p-3 rounded-lg border mt-1"></p>
                </div>
                <button onclick="closeModal('profile-modal','profile-modal-content')"
                        class="w-full mt-4 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>

</body>
</html>