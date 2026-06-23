@php
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
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Pet Details</title>
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
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-4">
                <button class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 text-slate-300">
                    <i class="bi bi-person-circle text-xl"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-red-900/10">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-teal-500/20 flex items-center justify-center text-4xl border border-teal-500/30">🐾</div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Max</h1>
                    <p class="{{ $accentText }} font-medium">Golden Retriever • 3 Years Old</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                Back to Dashboard
            </a>
        </header>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Visit Records -->
            <div class="lg:col-span-2 space-y-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2"><i class="bi bi-clock-history {{ $accentText }}"></i> Visit History</h3>
                    <div class="space-y-4">
                        <div class="p-4 border-l-2 {{ $accentBorder }} {{ $cardBg }} rounded-r-lg">
                            <p class="font-medium text-white">Full Grooming Session</p>
                            <p class="text-xs text-slate-400">January 15, 2026</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pet Info Sidebar -->
            <div class="space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-300">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-info-circle {{ $accentText }}"></i> About Max
                    </h4>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Max is a friendly Golden Retriever who loves belly rubs and treats.
                    </p>
                </div>
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Next Appointment
                    </h4>
                    <p class="text-lg font-semibold text-white">July 15, 2026</p>
                    <p class="text-sm text-slate-400">Scheduled: Grooming</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
