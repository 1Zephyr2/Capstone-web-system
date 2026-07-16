@php
    $threeMonthsAgo = now()->subMonths(3);

    $owners = App\Models\User::where('role', 'owner')
        ->with([
            'pets' => fn($q) => $q->orderBy('name'),
            'appointments' => fn($q) => $q->orderByDesc('appointment_date')->limit(1),
        ])
        ->orderBy('name')
        ->get()
        ->map(function($owner) use ($threeMonthsAgo) {
            $lastAppt = $owner->appointments->first();
            $owner->is_inactive = !$lastAppt || $lastAppt->appointment_date->lt($threeMonthsAgo);
            $owner->last_visit   = $lastAppt ? $lastAppt->appointment_date : null;
            return $owner;
        });

    $inactiveCount = $owners->filter(fn($o) => $o->is_inactive)->count();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Pet Directory</title>
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

            // Live search
            document.getElementById('search').addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.owner-card').forEach(card => {
                    const name = card.dataset.name.toLowerCase();
                    const pets = card.dataset.pets.toLowerCase();
                    card.style.display = (name.includes(q) || pets.includes(q)) ? '' : 'none';
                });
            });

            // Filter tabs
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => {
                        b.classList.remove('bg-violet-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    });
                    this.classList.add('bg-violet-600', 'text-white');
                    this.classList.remove('bg-gray-100', 'text-gray-600');

                    const filter = this.dataset.filter;
                    document.querySelectorAll('.owner-card').forEach(card => {
                        if (filter === 'all') {
                            card.style.display = '';
                        } else if (filter === 'inactive') {
                            card.style.display = card.dataset.inactive === 'true' ? '' : 'none';
                        } else if (filter === 'active') {
                            card.style.display = card.dataset.inactive === 'false' ? '' : 'none';
                        }
                    });
                });
            });
        });

        function toggleAccordion(id) {
            const el   = document.getElementById('acc-' + id);
            const icon = document.getElementById('icon-' + id);
            if (el)   el.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-90');
        }
    </script>
    <script>function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }</script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('staff.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="text-gray-900 font-semibold transition-all hover:scale-105">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                <a href="{{ route('staff.insights') }}"     class="hover:text-gray-900 transition-all hover:scale-105">Insights</a>
            </div>
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

    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-gray-900">Pet Directory</h1>
            <p class="text-gray-500 text-sm">Browse all registered owners and their pets.</p>
        </header>

        <!-- Search -->
        <div class="mb-5 max-w-lg reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-3 text-gray-400"></i>
                <input id="search" type="text" placeholder="Search by owner name or pet name..."
                       class="w-full bg-white border border-gray-200 rounded-xl pl-12 pr-4 py-3 text-gray-900 placeholder-slate-600 focus:border-violet-500 transition-all outline-none text-sm">
            </div>
        </div>

        <!-- Filter tabs -->
        <div class="flex items-center gap-2 mb-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <button data-filter="all"      class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold transition-all bg-violet-600 text-white">All</button>
            <button data-filter="active"   class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold transition-all bg-gray-100 text-gray-600">Active</button>
            <button data-filter="inactive" class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold transition-all bg-gray-100 text-gray-600 flex items-center gap-1.5">
                Inactive
                @if($inactiveCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $inactiveCount }}</span>
                @endif
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <p class="text-gray-500 text-xs mb-1">Total Owners</p>
                <p class="text-2xl font-bold text-gray-900">{{ $owners->count() }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <p class="text-gray-500 text-xs mb-1">Total Pets</p>
                <p class="text-2xl font-bold text-gray-900">{{ $owners->sum(fn($o) => $o->pets->count()) }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                <p class="text-red-600 text-xs mb-1">Inactive (3+ months)</p>
                <p class="text-2xl font-bold text-gray-900">{{ $inactiveCount }}</p>
            </div>
        </div>

        <!-- Owner List -->
        <div class="space-y-3">
            @forelse($owners as $owner)
                @php $petNames = $owner->pets->pluck('name')->join(', '); @endphp
                <div class="owner-card bg-white border {{ $owner->is_inactive ? 'border-red-200' : 'border-gray-200' }} rounded-xl overflow-hidden hover:border-gray-300 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.08)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out"
                     data-name="{{ strtolower($owner->name) }}"
                     data-pets="{{ strtolower($petNames) }}"
                     data-inactive="{{ $owner->is_inactive ? 'true' : 'false' }}">

                    <button onclick="toggleAccordion({{ $owner->id }})"
                            class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $owner->is_inactive ? 'bg-red-100' : 'bg-violet-100' }} flex items-center justify-center {{ $owner->is_inactive ? 'text-red-600' : 'text-violet-600' }} shrink-0">
                                <i class="bi bi-person text-sm"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $owner->name }}</p>
                                    @if($owner->is_inactive)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600 border border-red-200">
                                            <i class="bi bi-exclamation-circle mr-1"></i>Inactive
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-600 border border-emerald-200">Active</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400">{{ $owner->email }}</p>
                                @if($owner->last_visit)
                                    <p class="text-xs {{ $owner->is_inactive ? 'text-red-600' : 'text-gray-400' }} mt-0.5">
                                        Last visit: {{ $owner->last_visit->format('M d, Y') }}
                                        @if($owner->is_inactive)
                                            <span class="text-red-600">({{ $owner->last_visit->diffForHumans() }})</span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-xs text-red-600 mt-0.5">No appointments yet</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-400 hidden sm:block">
                                {{ $owner->pets->count() }} {{ Str::plural('pet', $owner->pets->count()) }}
                            </span>
                            <i id="icon-{{ $owner->id }}" class="bi bi-chevron-right text-violet-600 transition-transform duration-300"></i>
                        </div>
                    </button>

                    <div id="acc-{{ $owner->id }}" class="hidden px-6 pb-4 border-t border-gray-200">
                        <div class="pt-4 space-y-2">
                            @forelse($owner->pets as $pet)
                                <a href="{{ route('pets.details', ['id' => $pet->id]) }}"
                                   class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:border-violet-200 transition-all hover:translate-x-1">
                                    <div class="flex items-center gap-3">
                                        @if($pet->photo)
                                            <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}"
                                                 class="w-8 h-8 rounded-full object-cover border border-violet-200">
                                        @else
                                            <span class="text-lg">{{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}</span>
                                        @endif
                                        <span class="text-sm text-gray-900 font-medium">{{ $pet->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-400">{{ $pet->breed }}</span>
                                        <i class="bi bi-chevron-right text-gray-500 text-xs"></i>
                                    </div>
                                </a>
                            @empty
                                <p class="text-gray-500 text-sm italic py-2">No pets registered yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                    <i class="bi bi-people text-4xl text-gray-300 mb-3 block"></i>
                    <p class="text-gray-400">No owners registered yet.</p>
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>