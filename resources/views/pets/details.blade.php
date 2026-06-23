@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $isStaff = auth()->check() && auth()->user()->role === 'staff';

    // Theme selection: Admin, Staff, or Customer
    if ($isAdmin) {
        $bgMain = 'bg-indigo-950';
        $bgNav = 'bg-[#1e1b4b]';
        $badgeBg = 'bg-rose-500/20';
        $badgeBorder = 'border-rose-500/30';
        $badgeText = 'text-rose-300';
        $accentText = 'text-indigo-400';
        $cardBg = 'bg-indigo-900/20';
        $cardBorder = 'border-indigo-800/80';
        $accentBorder = 'border-indigo-500/50';
        $portalLabel = 'ADMIN PORTAL';
    } elseif ($isStaff) {
        $bgMain = 'bg-slate-950';
        $bgNav = 'bg-[#0c1220]';
        $badgeBg = 'bg-violet-500/20';
        $badgeBorder = 'border-violet-500/30';
        $badgeText = 'text-violet-300';
        $accentText = 'text-violet-400';
        $cardBg = 'bg-slate-900/40';
        $cardBorder = 'border-slate-800/80';
        $accentBorder = 'border-violet-500/50';
        $portalLabel = 'STAFF PORTAL';
    } else {
        // Customer Theme
        $bgMain = 'bg-slate-950';
        $bgNav = 'bg-[#0b0f19]';
        $badgeBg = 'bg-teal-500/20';
        $badgeBorder = 'border-teal-500/30';
        $badgeText = 'text-teal-300';
        $accentText = 'text-teal-400';
        $cardBg = 'bg-slate-900/40';
        $cardBorder = 'border-slate-800/80';
        $accentBorder = 'border-teal-500/50';
        $portalLabel = 'CLIENT PORTAL';
    }
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Pet Details</title>
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
<body class="{{ $bgMain }} text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ $isAdmin ? route('admin.dashboard') : ($isStaff ? route('staff.dashboard') : route('dashboard')) }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $badgeText }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md {{ $badgeBg }} border {{ $badgeBorder }}">
                    {{ $portalLabel }}
                </span>
            </a>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                    <a href="{{ route('admin.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                    <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                    <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                @elseif($isStaff)
                    <a href="{{ route('staff.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                    <a href="{{ route('staff.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                    <a href="{{ route('staff.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                    <a href="{{ route('staff.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                @endif
            </div>

            <form action="{{ $isAdmin ? route('admin.logout') : ($isStaff ? route('staff.logout') : route('logout')) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
            <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-4xl border border-indigo-500/30">🐾</div>
                    <div>
                        <h1 class="text-4xl font-bold text-white">Max</h1>
                        <p class="{{ $accentText }} font-medium">Golden Retriever • 3 Years Old</p>
                    </div>
                </div>
                <a href="{{ $isAdmin ? route('admin.directory') : ($isStaff ? route('staff.directory') : route('dashboard')) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                    Back
                </a>
            </header>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-white flex items-center gap-2"><i class="bi bi-clock-history {{ $accentText }}"></i> Visit Records</h3>
                        @if($isAdmin || $isStaff)
                            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-sm font-semibold transition-all hover:scale-105 active:scale-95 text-white shadow-lg">
                                + Add Record
                            </button>
                        @endif
                    </div>
                    <div class="space-y-4">
                        <div class="p-4 border-l-2 {{ $accentBorder }} {{ $cardBg }} rounded-r-lg">
                            <p class="font-medium text-white">Full Grooming Session</p>
                            <p class="text-xs text-slate-400">January 15, 2026 - Staff: John</p>
                            <div class="mt-3 flex gap-2">
                                <div class="w-16 h-16 rounded-lg bg-slate-800 flex items-center justify-center border border-slate-700">
                                    <i class="bi bi-image text-slate-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-300">
                <!-- Last Visit -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Last Visit
                    </h4>
                    <p class="text-lg font-semibold text-white">January 15, 2026</p>
                    <p class="text-sm text-slate-400">Full Grooming Session</p>
                </div>

                <!-- Owner Information -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-person {{ $accentText }}"></i> Owner Info
                    </h4>
                    <div class="space-y-2">
                        <p class="text-sm text-slate-300">
                            <span class="text-slate-400">Name:</span> Jane Doe
                        </p>
                        <p class="text-sm text-slate-300">
                            <span class="text-slate-400">Phone:</span> +1 (555) 123-4567
                        </p>
                    </div>
                    @if($isAdmin || $isStaff)
                        <button class="w-full mt-4 text-center px-4 py-2 border border-slate-700 rounded-lg text-sm hover:bg-slate-800 transition-all">
                            View Profile
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
