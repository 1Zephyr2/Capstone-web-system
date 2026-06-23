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

        function toggleModal() {
            const modal = document.getElementById('profile-modal');
            const content = document.getElementById('profile-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                modal.classList.remove('opacity-100');
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'opacity-100');
                }, 300);
            }
        }

        // Reset animations on page show (handles Back/Forward cache)
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                    el.classList.remove('opacity-100', 'translate-y-0');
                    el.classList.add('opacity-0', 'translate-y-10');
                });
                setTimeout(() => {
                    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                        el.classList.add('opacity-100', 'translate-y-0');
                        el.classList.remove('opacity-0', 'translate-y-10');
                    });
                }, 50);
            }
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
                <button onclick="toggleModal()" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg hover:shadow-slate-800/50">
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
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Request an Appointment</h1>
                <p class="text-slate-400">Select a time slot and your pet to initiate a request.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                Back to Dashboard
            </a>
        </header>

        <div class="grid lg:grid-cols-4 gap-10">
            <!-- Booking Settings -->
            <div class="lg:col-span-1 space-y-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-100">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-3xl p-8 shadow-xl">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Select Date</label>
                    <div class="relative mb-8">
                        <i class="bi bi-calendar3 absolute left-4 top-3 text-teal-500"></i>
                        <input type="text" value="06/23/2026" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all">
                    </div>

                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Which Pet?</label>
                    <div class="relative">
                        <i class="bi bi-paw absolute left-4 top-3 text-teal-500"></i>
                        <select class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            <option>Max (Golden Retriever)</option>
                            <option>Luna (Persian Cat)</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-3 text-slate-500 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- Booking Sheet -->
            <div class="lg:col-span-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-3xl overflow-hidden shadow-2xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-950/30 border-b border-slate-800">
                            <tr class="text-xs uppercase text-slate-400 tracking-widest">
                                <th class="px-8 py-6">Time</th>
                                <th class="px-8 py-6">Status / Availability</th>
                                <th class="px-8 py-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <!-- State 1: Filled -->
                            <tr>
                                <td class="px-8 py-7 font-bold text-teal-400 text-lg">9:00 AM</td>
                                <td class="px-8 py-7"><span class="px-4 py-1.5 rounded-full text-xs font-medium bg-slate-800 text-slate-500 border border-slate-700 uppercase tracking-wide">[ 🔒 Filled / Unavailable ]</span></td>
                                <td class="px-8 py-7 text-right"><button disabled class="px-6 py-2.5 rounded-xl text-sm bg-slate-800 text-slate-600 cursor-not-allowed font-semibold">Unavailable</button></td>
                            </tr>
                            <!-- State 2: Pending -->
                            <tr>
                                <td class="px-8 py-7 font-bold text-teal-400 text-lg">10:00 AM</td>
                                <td class="px-8 py-7"><span class="px-4 py-1.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-500 border border-amber-500/20 uppercase tracking-wide">[ 🟡 Pending Staff Approval ]</span></td>
                                <td class="px-8 py-7 text-right"><button disabled class="px-6 py-2.5 rounded-xl text-sm bg-amber-900/20 text-amber-500 border border-amber-500/20 cursor-default font-semibold">Awaiting Review</button></td>
                            </tr>
                            <!-- State 3 & 4: Open -->
                            @foreach(['11:00 AM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'] as $time)
                            <tr class="hover:bg-teal-900/5 transition-colors">
                                <td class="px-8 py-7 font-bold text-teal-400 text-lg">{{ $time }}</td>
                                <td class="px-8 py-7 text-sm text-slate-400">— Open Slot —</td>
                                <td class="px-8 py-7 text-right"><button class="px-6 py-2.5 rounded-xl text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105 active:scale-95 shadow-lg shadow-teal-900/20">+ Request Time</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal()">
        <div id="profile-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">User Profile</h2>
            <div id="profile-view" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Name</label>
                    <p class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
                    <p class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1">{{ auth()->user()->email }}</p>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="toggleModal()" class="w-full px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
