@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    // Admin uses Indigo theme, Staff uses Slate/Violet theme
    $bgMain = $isAdmin ? 'bg-indigo-950' : 'bg-slate-950';
    $bgNav = $isAdmin ? 'bg-[#1e1b4b]' : 'bg-[#0c1220]';
    $badgeBg = $isAdmin ? 'bg-rose-500/20' : 'bg-violet-500/20';
    $badgeBorder = $isAdmin ? 'border-rose-500/30' : 'border-violet-500/30';
    $badgeText = $isAdmin ? 'text-rose-300' : 'text-violet-300';
    $accentText = $isAdmin ? 'text-indigo-400' : 'text-violet-400';
    $cardBg = $isAdmin ? 'bg-indigo-900/20' : 'bg-slate-900/40';
    $cardBorder = $isAdmin ? 'border-indigo-800/80' : 'border-slate-800/80';
    $accentBorder = $isAdmin ? 'border-indigo-500/50' : 'border-violet-500/50';
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Pet Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="{{ $bgMain }} text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $badgeText }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md {{ $badgeBg }} border {{ $badgeBorder }}">
                    {{ $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL' }}
                </span>
            </a>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                    <a href="{{ route('admin.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                    <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                    <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                @else
                    <a href="{{ route('staff.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                    <a href="{{ route('staff.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                    <a href="{{ route('staff.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                    <a href="{{ route('staff.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                @endif
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-12 flex items-start justify-between">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-4xl border border-indigo-500/30">🐾</div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Max</h1>
                    <p class="{{ $accentText }} font-medium">Golden Retriever • 3 Years Old</p>
                </div>
            </div>
            <a href="{{ $isAdmin ? route('admin.directory') : route('staff.directory') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                Back
            </a>
        </header>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-white flex items-center gap-2"><i class="bi bi-clock-history {{ $accentText }}"></i> Visit Records</h3>
                        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-sm font-semibold transition-all hover:scale-105 active:scale-95 text-white shadow-lg">
                            + Add Record
                        </button>
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
        </div>
    </main>
</body>
</html>
