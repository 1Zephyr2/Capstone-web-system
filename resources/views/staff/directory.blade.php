<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Pet List</title>
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

        function toggleAddPetModal() {
            const modal = document.getElementById('add-pet-modal');
            const content = document.getElementById('add-pet-modal-content');

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

        function toggleAccordion(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (el) el.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-90');
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

    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Pet List</h1>
            <p class="text-slate-400 text-sm">Manage client profiles and associated pet records.</p>
        </header>

        <div class="mb-8 max-w-lg reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-3 text-slate-500"></i>
                <input type="text" placeholder="Search by name or phone..." class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-3 text-white placeholder-slate-600 focus:border-violet-500 transition-all outline-none">
            </div>
        </div>

        <div class="space-y-4">
            <!-- Owner Card -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl overflow-hidden hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <button onclick="toggleAccordion('shamaimah')" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-slate-800/30 transition-all duration-300">
                    <span class="font-semibold text-white">Shamaimah</span>
                    <span class="text-slate-500 text-sm">012-345-6789</span>
                    <i id="icon-shamaimah" class="bi bi-chevron-right text-violet-400 transition-transform duration-300"></i>
                </button>
                <div id="shamaimah" class="hidden px-6 pb-4 border-t border-slate-800/50">
                    <div class="pt-4 pb-4 space-y-2">
                        <a href="{{ route('pets.details', ['id' => 'pom-s']) }}" class="block flex items-center justify-between bg-slate-900/50 p-3 rounded-lg border border-slate-800/50 hover:border-violet-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Pom-S</span>
                            <span class="text-xs text-slate-500">Golden Retriever</span>
                        </a>
                        <a href="{{ route('pets.details', ['id' => 'toby']) }}" class="block flex items-center justify-between bg-slate-900/50 p-3 rounded-lg border border-slate-800/50 hover:border-violet-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Toby</span>
                            <span class="text-xs text-slate-500">Persian Cat</span>
                        </a>
                        <button onclick="toggleAddPetModal()" class="mt-2 text-xs text-violet-400 hover:text-violet-300 font-medium transition-colors hover:underline">
                            + Add New Pet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Pet Modal -->
    <div id="add-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleAddPetModal()">
        <div id="add-pet-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Add New Pet</h2>
            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Pet Name</label>
                    <input type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white mt-1 outline-none focus:border-violet-500 hover:border-slate-600 transition-all duration-300">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Pet Type</label>
                        <select class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white mt-1 outline-none focus:border-violet-500 hover:border-slate-600 transition-all duration-300 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em_1.5em] bg-[right_0.5rem_center] bg-no-repeat">
                            <option>Dog</option>
                            <option>Cat</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Breed</label>
                        <input type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white mt-1 outline-none focus:border-violet-500 hover:border-slate-600 transition-all duration-300">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Profile Picture</label>
                    <div class="mt-1 flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-800 border-dashed rounded-lg cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-violet-500 transition-all duration-300">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="bi bi-cloud-upload text-xl text-violet-500"></i>
                                <p class="mb-2 text-xs text-slate-400"><span class="font-semibold">Click to upload</span></p>
                            </div>
                            <input type="file" class="hidden">
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Age</label>
                    <input type="number" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white mt-1 outline-none focus:border-violet-500 hover:border-slate-600 transition-all duration-300">
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="toggleAddPetModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold transition-all duration-300">Add Pet</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
