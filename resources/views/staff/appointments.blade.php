<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Appointments</title>
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
        });

        function showAppointmentModal(pet, owner, time, status) {
            document.getElementById('appt-pet').innerText = pet;
            document.getElementById('appt-owner').innerText = owner;
            document.getElementById('appt-time').innerText = time;
            document.getElementById('appt-status').innerText = status;

            const modal = document.getElementById('appt-modal');
            const content = document.getElementById('appt-modal-content');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAppointmentModal() {
            const modal = document.getElementById('appt-modal');
            const content = document.getElementById('appt-modal-content');

            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'opacity-100');
            }, 300);
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0c1220] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-500/20 border border-violet-500/30">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="{{ route('staff.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('staff.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Upcoming Appointments</h1>
            <p class="text-slate-400 text-sm">View and manage confirmed grooming sessions.</p>
        </header>

        <!-- Appointments List -->
        <div class="space-y-4">
            <!-- Appointment Card -->
            <div onclick="showAppointmentModal('Max (Golden Retriever)', 'Shamaimah', '10:00 AM - 11:30 AM', 'Confirmed')"
                 class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-6 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between cursor-pointer">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-violet-500/20 flex items-center justify-center text-violet-400">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white">Max (Golden Retriever)</h5>
                        <p class="text-sm text-slate-400">Owner: Shamaimah | 10:00 AM - 11:30 AM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 font-medium">Confirmed</span>
                </div>
            </div>

            <!-- Appointment Card -->
            <div onclick="showAppointmentModal('Luna (Persian Cat)', 'Shamaimah', '01:00 PM - 02:00 PM', 'Confirmed')"
                 class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-6 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between cursor-pointer">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-violet-500/20 flex items-center justify-center text-violet-400">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white">Luna (Persian Cat)</h5>
                        <p class="text-sm text-slate-400">Owner: Shamaimah | 01:00 PM - 02:00 PM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 font-medium">Confirmed</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Appointment Modal -->
    <div id="appt-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeAppointmentModal()">
        <div id="appt-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Pet</label>
                    <p id="appt-pet" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Owner</label>
                    <p id="appt-owner" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Time</label>
                    <p id="appt-time" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                    <p id="appt-status" class="text-emerald-400 font-semibold bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8">
                    <button type="button" onclick="closeAppointmentModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                    <button type="button" class="px-4 py-2 rounded-xl bg-violet-600 text-white hover:bg-violet-500 transition-all duration-300">Complete</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
