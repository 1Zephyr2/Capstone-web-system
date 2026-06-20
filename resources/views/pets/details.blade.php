@php
    $isAdminOrStaff = auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'staff');
    $bgMain = $isAdminOrStaff ? 'bg-indigo-950' : 'bg-slate-950';
    $bgNav = $isAdminOrStaff ? 'bg-[#1e1b4b]' : 'bg-[#0b0f19]';
    $textPrimary = $isAdminOrStaff ? 'text-indigo-100' : 'text-slate-200';
    $textSecondary = $isAdminOrStaff ? 'text-indigo-300' : 'text-slate-400';
    $textAccent = $isAdminOrStaff ? 'text-indigo-400' : 'text-teal-400';
    $bgCard = $isAdminOrStaff ? 'bg-indigo-900/20' : 'bg-slate-900/40';
    $borderCard = $isAdminOrStaff ? 'border-indigo-800/80' : 'border-slate-800/80';
    $borderHover = $isAdminOrStaff ? 'hover:border-indigo-600' : 'hover:border-slate-700';
    $accentBorder = $isAdminOrStaff ? 'border-indigo-500/30' : 'border-teal-500/30';
    $accentBg = $isAdminOrStaff ? 'bg-indigo-500/20' : 'bg-teal-500/20';
    $accentLBorder = $isAdminOrStaff ? 'border-indigo-500/50' : 'border-teal-500/50';
    $buttonBg = $isAdminOrStaff ? 'bg-indigo-900/30' : 'bg-slate-800';
    $buttonHover = $isAdminOrStaff ? 'hover:bg-indigo-900/50' : 'hover:bg-slate-700';
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
<body class="{{ $bgMain }} {{ $textPrimary }} antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ $isAdminOrStaff ? route('admin.dashboard') : route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE @if($isAdminOrStaff) Admin @endif
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium {{ $isAdminOrStaff ? 'text-indigo-300' : 'text-slate-300' }}">
                @if($isAdminOrStaff)
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                    <a href="{{ route('admin.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                    <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                    <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                    <a href="{{ route('admin.panel') }}" class="text-white hover:text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50 hover:scale-105 active:scale-95">Admin Panel</a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm {{ $buttonBg }} {{ $buttonHover }} transition-all duration-300 shadow-lg text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <!-- Header -->
        <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl {{ $accentBg }} flex items-center justify-center text-4xl border {{ $accentBorder }}">🐾</div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Max</h1>
                    <p class="{{ $textAccent }} font-medium">Golden Retriever • 3 Years Old</p>
                </div>
            </div>
            <a href="{{ $isAdminOrStaff ? route('admin.directory') : route('dashboard') }}" class="px-5 py-2.5 rounded-xl {{ $buttonBg }} {{ $buttonHover }} text-white text-sm font-semibold transition-all hover:scale-105">
                Back
            </a>
        </header>

        <!-- Details Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Left Column: Medical & Notes -->
            <div class="lg:col-span-2 space-y-8">
                <div class="{{ $bgCard }} border {{ $borderCard }} rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2"><i class="bi bi-clipboard-pulse {{ $textAccent }}"></i> Medical History</h3>
                    <div class="space-y-4">
                        <div class="p-4 border-l-2 {{ $accentLBorder }} bg-opacity-50 {{ $bgCard }} rounded-r-lg">
                            <p class="font-medium text-white">Annual Vaccination</p>
                            <p class="text-xs {{ $textSecondary }}">January 15, 2026 - Dr. Smith</p>
                        </div>
                        <div class="p-4 border-l-2 border-slate-700 bg-opacity-50 {{ $bgCard }} rounded-r-lg">
                            <p class="font-medium text-white">General Checkup</p>
                            <p class="text-xs {{ $textSecondary }}">October 10, 2025 - Dr. Smith</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Notes -->
            <div class="space-y-8">
                <div class="{{ $bgCard }} border {{ $borderCard }} rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-150">
                    <h3 class="font-bold text-white mb-6">Special Notes</h3>
                    <p class="text-sm {{ $textSecondary }} leading-relaxed italic">"Max is generally friendly but gets nervous around loud dryers. Please use the quiet setting if available."</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
