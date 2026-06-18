<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Pet List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function toggleAccordion(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            el.classList.toggle('hidden');
            icon.classList.toggle('rotate-90');
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-lock text-violet-400"></i>FURCARE <span class="text-violet-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-500/20 border border-violet-500/30">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="{{ route('staff.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('staff.directory') }}" class="text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="#" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="#" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-indigo-500/10">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-white">Pet List</h1>
            <p class="text-slate-400 text-sm">Manage client profiles and associated pet records.</p>
        </header>

        <!-- Search Bar -->
        <div class="mb-8 max-w-lg">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-3 text-slate-500"></i>
                <input type="text" placeholder="Search by name or phone..." class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-12 pr-4 py-3 text-white placeholder-slate-600 focus:border-violet-500 transition-all outline-none">
            </div>
        </div>

        <!-- Directory Stack -->
        <div class="space-y-4">
            <!-- Owner Card: Shamaimah -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl overflow-hidden hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                <button onclick="toggleAccordion('shamaimah')" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-slate-800/30 transition-all duration-300">
                    <span class="font-semibold text-white">Shamaimah</span>
                    <span class="text-slate-500 text-sm">012-345-6789</span>
                    <i id="icon-shamaimah" class="bi bi-chevron-right text-violet-400 transition-transform duration-300"></i>
                </button>
                <div id="shamaimah" class="hidden px-6 pb-4 bg-slate-950/20 border-t border-slate-800/50 transition-all duration-300 ease-in-out">
                    <div class="pt-4 space-y-2">
                        <div class="flex items-center justify-between bg-slate-900/50 p-3 rounded-lg border border-slate-800/50 hover:border-violet-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Pom-S</span>
                            <span class="text-xs text-slate-500">Golden Retriever</span>
                        </div>
                        <div class="flex items-center justify-between bg-slate-900/50 p-3 rounded-lg border border-slate-800/50 hover:border-violet-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Toby</span>
                            <span class="text-xs text-slate-500">Persian Cat</span>
                        </div>
                        <button class="mt-2 text-xs text-violet-400 hover:text-violet-300 font-medium transition-colors hover:underline">
                            + Add New Pet
                        </button>
                    </div>
                </div>
            </div>

            <!-- Owner Card: Medge -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl overflow-hidden hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                <button onclick="toggleAccordion('medge')" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-slate-800/30 transition-all duration-300">
                    <span class="font-semibold text-white">Medge</span>
                    <span class="text-slate-500 text-sm">018-999-0000</span>
                    <i id="icon-medge" class="bi bi-chevron-right text-violet-400 transition-transform duration-300"></i>
                </button>
                <div id="medge" class="hidden px-6 pb-4 bg-slate-950/20 border-t border-slate-800/50 transition-all duration-300 ease-in-out">
                    <div class="pt-4 space-y-2">
                         <p class="text-slate-500 text-sm italic">No pets found for this owner.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
