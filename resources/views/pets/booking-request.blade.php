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
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Request Appointment</title>
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
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all duration-300">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white mb-2">Request an Appointment</h1>
            <p class="text-slate-400">Select a time slot and your pet to initiate a request.</p>
        </header>

        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Booking Settings -->
            <div class="lg:col-span-1 space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-100">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Select Date</label>
                    <input type="text" value="06/23/2026" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none mb-6">

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Which Pet?</label>
                    <select class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white outline-none">
                        <option>Max (Golden Retriever)</option>
                        <option>Luna (Persian Cat)</option>
                    </select>
                </div>
            </div>

            <!-- Booking Sheet -->
            <div class="lg:col-span-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-950/50 border-b border-slate-800">
                            <tr class="text-xs uppercase text-slate-400 tracking-wider">
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4">Status / Availability</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <!-- State 1: Filled -->
                            <tr>
                                <td class="px-6 py-5 font-bold text-teal-400">9:00 AM</td>
                                <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-slate-800 text-slate-500 border border-slate-700">[ 🔒 Filled / Unavailable ]</span></td>
                                <td class="px-6 py-5 text-right"><button disabled class="px-4 py-2 rounded-lg text-sm bg-slate-800 text-slate-600 cursor-not-allowed">Unavailable</button></td>
                            </tr>
                            <!-- State 2: Pending -->
                            <tr>
                                <td class="px-6 py-5 font-bold text-teal-400">10:00 AM</td>
                                <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-amber-500/10 text-amber-500 border border-amber-500/20">[ 🟡 Pending Staff Approval ]</span></td>
                                <td class="px-6 py-5 text-right"><button disabled class="px-4 py-2 rounded-lg text-sm bg-amber-900/20 text-amber-500 border border-amber-500/20 cursor-default">Awaiting Review</button></td>
                            </tr>
                            <!-- State 3 & 4: Open -->
                            @foreach(['11:00 AM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'] as $time)
                            <tr>
                                <td class="px-6 py-5 font-bold text-teal-400">{{ $time }}</td>
                                <td class="px-6 py-5 text-sm text-slate-400">— Open Slot —</td>
                                <td class="px-6 py-5 text-right"><button class="px-4 py-2 rounded-lg text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all">+ Request Time</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
