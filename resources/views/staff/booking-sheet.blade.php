@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $bgNav = 'bg-white';
    $portalLabel = $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL';
    $badgeBg = $isAdmin ? 'bg-rose-100' : 'bg-violet-100';
    $badgeBorder = $isAdmin ? 'border-rose-200' : 'border-violet-200';
    $badgeText = $isAdmin ? 'text-rose-700' : 'text-violet-700';
    $accentText = $isAdmin ? 'text-indigo-600' : 'text-violet-600';
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $isAdmin ? 'Admin' : 'Staff' }} Booking Sheet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
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
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Navbar -->
     <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('staff.dashboard') }}"    class="hover:text-gray-900 transition-all">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="hover:text-gray-900 transition-all">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-gray-900 transition-all">Appointments</a>
                <a href="{{ route('staff.services') }}"     class="text-gray-900 font-semibold transition-all">Services</a>
                <a href="{{ route('staff.insights') }}"     class="hover:text-gray-900 transition-all">Insights</a>
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

    <main class="container mx-auto px-6 py-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <!-- Controls & Filter Bar -->
        <header class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Appointment Booking</h1>
                <p class="text-gray-500">Tuesday, June 23, 2026</p>
            </div>
            <div class="flex items-center gap-2 bg-white p-1 rounded-xl border border-gray-200">
                <button class="px-4 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 transition-all text-gray-700 font-medium">[ &lt; Prev ]</button>
                <button class="px-4 py-2 rounded-lg text-sm bg-gray-900 hover:bg-gray-800 transition-all text-white font-medium">[ Today ]</button>
                <button class="px-4 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 transition-all text-gray-700 font-medium">[ Next &gt; ]</button>
                <input type="text" value="06/23/2026" class="px-4 py-2 rounded-lg text-sm bg-gray-50 border border-gray-300 text-gray-900 outline-none w-32 text-center">
            </div>
        </header>

        <!-- Core Grid Table -->
        <div class="bg-white border-gray-200 border rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 border-gray-200 border-b">
                    <tr class="text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-4">Time</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Package / Service</th>
                        <th class="px-6 py-4">Size</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <!-- Available slots -->
                    @foreach(['9:00 AM', '10:00 AM'] as $time)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">{{ $time }}</td>
                        <td colspan="5" class="px-6 py-5 text-center text-gray-400 italic">— Available —</td>
                        <td class="px-6 py-5"><button class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all">+ Book</button></td>
                    </tr>
                    @endforeach

                    <!-- Pending Approval -->
                    <tr onclick="showBookingModal('11:00 AM', 'Medge', 'Full Grooming', 'S', 'Nervous dog', 'Pending Staff Approval')"
                        class="cursor-pointer hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">11:00 AM</td>
                        <td class="px-6 py-5 font-medium">Medge</td>
                        <td class="px-6 py-5">Full Grooming</td>
                        <td class="px-6 py-5">S</td>
                        <td class="px-6 py-5 text-gray-500 text-sm">Nervous dog</td>
                        <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-amber-50 text-amber-500 border border-amber-200">[ 🟡 Pending Staff Approval ]</span></td>
                        <td class="px-6 py-5 space-x-2">
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600/20 text-emerald-600 hover:bg-emerald-600/40 transition-all">✔️ Approve</button>
                            <button class="text-xs px-3 py-1.5 rounded-lg bg-red-600/20 text-red-600 hover:bg-red-600/40 transition-all">❌ Deny</button>
                        </td>
                    </tr>

                    <!-- Booked -->
                    <tr onclick="showBookingModal('1:00 PM', 'Shamaimah', 'Basic Bath', 'XS', 'Check ears', 'Confirmed')"
                        class="cursor-pointer hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">1:00 PM</td>
                        <td class="px-6 py-5 font-medium">Shamaimah</td>
                        <td class="px-6 py-5">Basic Bath</td>
                        <td class="px-6 py-5">XS</td>
                        <td class="px-6 py-5 text-gray-500 text-sm">Check ears</td>
                        <td class="px-6 py-5"><span class="px-3 py-1 rounded-full text-xs bg-emerald-50 text-emerald-600 border border-emerald-200">[ 🟢 Confirmed ]</span></td>
                        <td class="px-6 py-5"><button class="text-xs text-slate-700 hover:text-gray-900 underline transition-all">📝 Edit Notes</button></td>
                    </tr>

                    <!-- Available slots -->
                    @foreach(['2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM'] as $time)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold {{ $accentText }}">{{ $time }}</td>
                        <td colspan="5" class="px-6 py-5 text-center text-gray-400 italic">— Available —</td>
                        <td class="px-6 py-5"><button class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all">+ Book</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <!-- Booking Details Modal -->
    <div id="booking-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeBookingModal()">
        <div id="booking-modal-content" class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Booking Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Time</label>
                    <p id="modal-time" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Owner</label>
                    <p id="modal-owner" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Service</label>
                    <p id="modal-service" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Notes</label>
                    <p id="modal-notes" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Status</label>
                    <p id="modal-status" class="text-emerald-600 font-semibold bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1"></p>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="button" onclick="closeBookingModal()" class="flex-1 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">Close</button>
                    <button type="button" class="flex-1 px-4 py-2 rounded-xl bg-violet-600 text-white hover:bg-violet-700 transition-all">Update Status</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
