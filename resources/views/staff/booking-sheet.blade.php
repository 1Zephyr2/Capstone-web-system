@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $bgNav = $isAdmin ? 'bg-[#1e1b4b]' : 'bg-[#0c1220]';
    $portalLabel = $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL';
    $badgeBg = $isAdmin ? 'bg-rose-500/20' : 'bg-violet-500/20';
    $badgeBorder = $isAdmin ? 'border-rose-500/30' : 'border-violet-500/30';
    $badgeText = $isAdmin ? 'text-rose-300' : 'text-violet-300';
    $accentText = $isAdmin ? 'text-indigo-400' : 'text-violet-400';
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $isAdmin ? 'Admin' : 'Staff' }} Booking Sheet</title>
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

        function showBookingModal(time, owner, service, size, notes, status) {
            console.log("showBookingModal called with:", time, owner);

            const modal = document.getElementById('booking-modal');
            const content = document.getElementById('booking-modal-content');

            if (!modal || !content) {
                console.error("Modal elements not found!");
                return;
            }

            const elements = {
                'modal-time': time,
                'modal-owner': owner,
                'modal-service': service,
                'modal-size': size,
                'modal-notes': notes,
                'modal-status': status
            };

            for (const [id, value] of Object.entries(elements)) {
                const el = document.getElementById(id);
                if (el) {
                    el.innerText = value || 'N/A';
                } else {
                    console.warn(`Element with id ${id} not found!`);
                }
            }

            modal.style.display = 'flex';
            modal.classList.remove('hidden');

            // Force browser to acknowledge the change
            modal.offsetHeight;

            modal.classList.add('opacity-100');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }

        function closeBookingModal() {
            const modal = document.getElementById('booking-modal');
            const content = document.getElementById('booking-modal-content');

            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                modal.classList.remove('flex', 'opacity-100');
            }, 300);
        }
    </script>
</head>
<body class="{{ $isAdmin ? 'bg-indigo-950' : 'bg-slate-950' }} text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full {{ $bgNav }} backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $badgeText }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md {{ $badgeBg }} border {{ $badgeBorder }}">
                    {{ $portalLabel }}
                </span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium {{ $isAdmin ? 'text-indigo-300' : 'text-slate-300' }}">
                <a href="{{ $isAdmin ? route('admin.dashboard') : route('staff.dashboard') }}" class="hover:text-white transition-all">Dashboard</a>
                <a href="{{ $isAdmin ? route('admin.directory') : route('staff.directory') }}" class="hover:text-white transition-all">Pets</a>
                <a href="{{ $isAdmin ? route('admin.appointments') : route('staff.appointments') }}" class="text-white">Appointments</a>
                <a href="{{ $isAdmin ? route('admin.insights') : route('staff.insights') }}" class="hover:text-white transition-all">Insights</a>
            </div>
            <form action="{{ $isAdmin ? route('admin.logout') : route('staff.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <!-- Controls & Filter Bar -->
        <header class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">Appointment Booking</h1>
                <p class="text-slate-400">Tuesday, June 23, 2026</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-900/40 p-1 rounded-xl border border-slate-800">
                <button class="px-4 py-2 rounded-lg text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white font-medium">[ < Prev ]</button>
                <button class="px-4 py-2 rounded-lg text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white font-medium">[ Today ]</button>
                <button class="px-4 py-2 rounded-lg text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white font-medium">[ Next > ]</button>
                <input type="text" value="06/23/2026" class="px-4 py-2 rounded-lg text-sm bg-slate-950 border border-slate-700 text-white outline-none w-32 text-center">
            </div>
        </header>

        <!-- Core Grid Table -->
        <div class="{{ $isAdmin ? 'bg-indigo-900/20 border-indigo-800/80' : 'bg-slate-900/40 border-slate-800/80' }} border rounded-2xl overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead class="{{ $isAdmin ? 'bg-indigo-950/50 border-indigo-800' : 'bg-slate-950/50 border-slate-800' }} border-b">
                    <tr class="text-xs uppercase {{ $isAdmin ? 'text-indigo-400' : 'text-slate-400' }} tracking-wider">
                        <th class="px-6 py-4">Time</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Package / Service</th>
                        <th class="px-6 py-4">Size</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y {{ $isAdmin ? 'divide-indigo-800' : 'divide-slate-800' }}">
                    <!-- Available slots -->
                    @foreach(['9:00 AM', '10:00 AM'] as $time)
                    <tr class="hover:{{ $isAdmin ? 'bg-indigo-800/20' : 'bg-slate-800/20' }} transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">{{ $time }}</td>
                        <td colspan="5" class="px-6 py-5 text-center {{ $isAdmin ? 'text-indigo-500' : 'text-slate-500' }} italic">— Available —</td>
                        <td class="px-6 py-5"><button class="text-emerald-400 font-semibold hover:text-emerald-300 transition-all">+ Book</button></td>
                    </tr>
                    @endforeach

                    <!-- Pending Approval -->
                    <tr onclick="showBookingModal('11:00 AM', 'Medge', 'Full Grooming', 'S', 'Nervous dog', 'Pending Staff Approval')"
                        class="cursor-pointer hover:{{ $isAdmin ? 'bg-indigo-800/20' : 'bg-slate-800/20' }} transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">11:00 AM</td>
                        <td class="px-6 py-5 font-medium">Medge</td>
                        <td class="px-6 py-5">Full Grooming</td>
                        <td class="px-6 py-5">S</td>
                        <td class="px-6 py-5 text-slate-400 text-sm">Nervous dog</td>
                        <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-amber-500/10 text-amber-500 border border-amber-500/20">[ 🟡 Pending Staff Approval ]</span></td>
                        <td class="px-6 py-5 space-x-2">
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600/40 transition-all">✔️ Approve</button>
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-red-600/20 text-red-400 hover:bg-red-600/40 transition-all">❌ Deny</button>
                        </td>
                    </tr>

                    <!-- Booked -->
                    <tr onclick="showBookingModal('1:00 PM', 'Shamaimah', 'Basic Bath', 'XS', 'Check ears', 'Confirmed')"
                        class="cursor-pointer hover:{{ $isAdmin ? 'bg-indigo-800/20' : 'bg-slate-800/20' }} transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">1:00 PM</td>
                        <td class="px-6 py-5 font-medium">Shamaimah</td>
                        <td class="px-6 py-5">Basic Bath</td>
                        <td class="px-6 py-5">XS</td>
                        <td class="px-6 py-5 text-slate-400 text-sm">Check ears</td>
                        <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">[ 🟢 Confirmed ]</span></td>
                        <td class="px-6 py-5"><button class="text-xs text-slate-300 hover:text-white underline transition-all">📝 Edit Notes</button></td>
                    </tr>

                    <!-- Available slots -->
                    @foreach(['2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'] as $time)
                    <tr class="hover:{{ $isAdmin ? 'bg-indigo-800/20' : 'bg-slate-800/20' }} transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">{{ $time }}</td>
                        <td colspan="5" class="px-6 py-5 text-center {{ $isAdmin ? 'text-indigo-500' : 'text-slate-500' }} italic">— Available —</td>
                        <td class="px-6 py-5"><button class="text-emerald-400 font-semibold hover:text-emerald-300 transition-all">+ Book</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <!-- Booking Details Modal -->
    <div id="booking-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeBookingModal()">
        <div id="booking-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Booking Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Time</label>
                    <p id="modal-time" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Owner</label>
                    <p id="modal-owner" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Service</label>
                    <p id="modal-service" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Notes</label>
                    <p id="modal-notes" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                    <p id="modal-status" class="text-emerald-400 font-semibold bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="button" onclick="closeBookingModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
                    <button type="button" class="flex-1 px-4 py-2 rounded-xl bg-violet-600 text-white hover:bg-violet-500 transition-all">Update Status</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
