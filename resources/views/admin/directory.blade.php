@php
    $owners = App\Models\User::where('role', 'owner')
        ->with(['pets' => function($q) {
            $q->orderBy('name');
        }])
        ->orderBy('name')
        ->get();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Pet Directory</title>
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

            // Live search
            document.getElementById('search').addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.owner-card').forEach(card => {
                    const name = card.dataset.name.toLowerCase();
                    const pets = card.dataset.pets.toLowerCase();
                    card.style.display = (name.includes(q) || pets.includes(q)) ? '' : 'none';
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
                <a href="{{ route('admin.directory') }}"    class="text-white font-semibold transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-white hover:text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Pet Directory</h1>
            <p class="text-indigo-300 text-sm">Browse all registered owners and their pets.</p>
        </header>

        <!-- Search -->
        <div class="mb-8 max-w-lg reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-3 text-indigo-400"></i>
                <input id="search" type="text" placeholder="Search by owner name or pet name..."
                       class="w-full bg-indigo-900/20 border border-indigo-800 rounded-xl pl-12 pr-4 py-3 text-white placeholder-indigo-400 focus:border-indigo-500 transition-all outline-none">
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <p class="text-indigo-400 text-xs mb-1">Total Owners</p>
                <p class="text-2xl font-bold text-white">{{ $owners->count() }}</p>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <p class="text-indigo-400 text-xs mb-1">Total Pets</p>
                <p class="text-2xl font-bold text-white">{{ $owners->sum(fn($o) => $o->pets->count()) }}</p>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-5">
                <p class="text-indigo-400 text-xs mb-1">Owners with Pets</p>
                <p class="text-2xl font-bold text-white">{{ $owners->filter(fn($o) => $o->pets->count() > 0)->count() }}</p>
            </div>
        </div>

        <!-- Owner List -->
        <div class="space-y-4">
            @forelse($owners as $owner)
                @php $petNames = $owner->pets->pluck('name')->join(', '); @endphp
                <div class="owner-card bg-indigo-900/20 border border-indigo-800/80 rounded-xl overflow-hidden hover:border-indigo-600 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out"
                     data-name="{{ strtolower($owner->name) }}"
                     data-pets="{{ strtolower($petNames) }}">

                    <button onclick="toggleAccordion({{ $owner->id }})"
                            class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-indigo-900/40 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0">
                                <i class="bi bi-person text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-white">{{ $owner->name }}</p>
                                <p class="text-xs text-indigo-400">{{ $owner->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-indigo-500 hidden sm:block">
                                {{ $owner->pets->count() }} {{ Str::plural('pet', $owner->pets->count()) }}
                            </span>
                            <i id="icon-{{ $owner->id }}" class="bi bi-chevron-right text-indigo-400 transition-transform duration-300"></i>
                        </div>
                    </button>

                    <div id="acc-{{ $owner->id }}" class="hidden px-6 pb-4 border-t border-indigo-800/50">
                        <div class="pt-4 space-y-2">
                            @forelse($owner->pets as $pet)
                                <a href="{{ route('pets.details', ['id' => $pet->id]) }}"
                                   class="flex items-center justify-between bg-indigo-950/50 p-3 rounded-lg border border-indigo-800/50 hover:border-indigo-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                                    <div class="flex items-center gap-3">
                                        @if($pet->photo)
                                            <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}"
                                                 class="w-8 h-8 rounded-full object-cover border border-indigo-500/30">
                                        @else
                                            <span class="text-lg">{{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}</span>
                                        @endif
                                        <span class="text-sm text-white font-medium">{{ $pet->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-indigo-400">{{ $pet->breed }}</span>
                                        <i class="bi bi-chevron-right text-indigo-600 text-xs"></i>
                                    </div>
                                </a>
                            @empty
                                <p class="text-indigo-600 text-sm italic py-2">No pets registered yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-2xl p-12 text-center">
                    <i class="bi bi-people text-4xl text-indigo-800 mb-3 block"></i>
                    <p class="text-indigo-500">No owners registered yet.</p>
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>