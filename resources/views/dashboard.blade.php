<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Dashboard</title>
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
            const view = document.getElementById('profile-view');
            const edit = document.getElementById('profile-edit');

            if (modal.classList.contains('hidden')) {
                // Open modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // Trigger reflow for transition
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                // Close modal
                modal.classList.remove('opacity-100');
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'opacity-100');
                }, 300);
            }

            // Reset to view mode whenever modal is opened
            view.classList.remove('hidden');
            edit.classList.add('hidden');
        }

        function showAppointmentDetails(title, date, status) {
            document.getElementById('appointment-title').innerText = title;
            document.getElementById('appointment-date').innerText = date;
            document.getElementById('appointment-status').innerText = status;

            const modal = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAppointmentModal() {
            const modal = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');

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

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
            </div>
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

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white">Welcome back, John!</h1>
            <p class="text-slate-400 text-sm">Here is the status of your pets and upcoming grooming.</p>
        </header>

        <!-- Dashboard Grid -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Appointments -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(13,148,136,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-white flex items-center gap-2"><i class="bi bi-calendar-check text-teal-400"></i> Upcoming Appointments</h3>
                    <button class="text-xs px-3 py-1 bg-teal-600 hover:bg-teal-500 rounded-lg text-white font-medium transition-all duration-300">
                        Request an Appointment
                    </button>
                </div>
                <div class="space-y-4">
                    <div onclick="showAppointmentDetails('Max\'s Grooming', 'July 15, 2026 - 10:00 AM', 'Confirmed')" class="cursor-pointer flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-800/50 hover:border-teal-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                        <div>
                            <p class="font-medium text-white">Max's Grooming</p>
                            <p class="text-xs text-slate-400">July 15, 2026 - 10:00 AM</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/20">Confirmed</span>
                    </div>
                </div>
            </div>

            <!-- My Pets -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(13,148,136,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-6 flex items-center gap-2"><i class="bi bi-paw text-teal-400"></i> My Pets</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('pets.details', ['id' => 'max']) }}" class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/50 text-center hover:border-teal-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg block">
                        <span class="text-2xl mb-2 block">🐾</span>
                        <p class="font-medium text-white">Max</p>
                        <p class="text-xs text-slate-400">Golden Retriever</p>
                    </a>
                    <a href="{{ route('pets.details', ['id' => 'luna']) }}" class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/50 text-center hover:border-teal-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg block">
                        <span class="text-2xl mb-2 block">🐾</span>
                        <p class="font-medium text-white">Luna</p>
                        <p class="text-xs text-slate-400">Persian Cat</p>
                    </a>
                </div>
                <button class="w-full mt-6 py-3 rounded-xl border border-dashed border-slate-700 text-slate-400 hover:text-white hover:border-teal-500 transition-all duration-300">
                    + Add Pet
                </button>
            </div>
        </div>
    </main>

    <footer class="py-10 text-center border-t border-white/5 text-slate-500 text-sm">
        &copy; {{ date('Y') }} FURCARE. All rights reserved.
    </footer>

    <!-- Appointment Details Modal -->
    <div id="appointment-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeAppointmentModal()">
        <div id="appointment-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Service</label>
                    <p id="appointment-title" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Date & Time</label>
                    <p id="appointment-date" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                    <p id="appointment-status" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div class="mt-8">
                    <button type="button" onclick="closeAppointmentModal()" class="w-full px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal()">
        <div id="profile-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">User Profile</h2>

            <!-- View Mode -->
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
                    <button type="button" onclick="toggleModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                    <button type="button" onclick="toggleEdit()" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-500 transition-all duration-300 hover:-translate-y-0.5">Edit Profile</button>
                </div>
            </div>

            <!-- Edit Mode -->
            <form id="profile-edit" action="{{ route('profile.update') }}" method="POST" class="hidden space-y-4">
                @csrf @method('patch')
                <div>
                    <label class="block text-sm text-slate-400">Name</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-teal-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm text-slate-400">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-teal-500 outline-none transition-all">
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="toggleEdit()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-teal-500 text-white font-semibold hover:bg-teal-400 transition-all duration-300 hover:-translate-y-0.5">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
