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
@endphp

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Edit Pet</title>
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
    </script>
</head>
<body class="{{ $bgMain }} text-slate-200 antialiased min-h-screen">

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
                <h1 class="text-3xl font-bold text-white mb-2">Edit Pet Information</h1>
                <p class="text-slate-400">Update the details for your pet.</p>
            </div>
        </header>

        <div class="max-w-2xl mx-auto reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-100">
            <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-3xl p-8 shadow-xl">
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Name</label>
                            <input type="text" value="Max" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Type</label>
                            <select class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300 appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:1.5em_1.5em] bg-[right_0.5rem_center] bg-no-repeat">
                                <option>Dog</option>
                                <option>Cat</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Breed</label>
                            <input type="text" value="Golden Retriever" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Age</label>
                            <input type="number" min="0" value="3" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Profile Picture</label>
                        <div class="mt-1 flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-teal-500 transition-all duration-300">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="bi bi-cloud-upload text-xl text-teal-500"></i>
                                    <p class="mb-2 text-xs text-slate-400"><span class="font-semibold">Click to update picture</span></p>
                                </div>
                                <input type="file" class="hidden">
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Special Notes</label>
                        <textarea rows="4" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all">Max is a friendly Golden Retriever who loves belly rubs and treats.</textarea>
                    </div>
                    <div class="pt-4 flex justify-end gap-4">
                        <a href="{{ route('pets.details', ['id' => 'max']) }}" class="px-8 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold transition-all hover:scale-105 active:scale-95">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105 active:scale-95 shadow-lg shadow-teal-900/20">
                            Save Changes
                        </button>
                    </div>
                </form>
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
