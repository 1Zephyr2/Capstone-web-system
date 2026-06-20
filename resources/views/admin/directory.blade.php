<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Pet List</title>
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

        function toggleAccordion(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            el.classList.toggle('hidden');
            icon.classList.toggle('rotate-90');
        }
    </script>
</head>
<body class="bg-indigo-950 text-indigo-100 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-lock text-indigo-400"></i> FURCARE Admin
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-indigo-300">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}" class="text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}" class="text-white hover:text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50 hover:scale-105 active:scale-95">Admin Panel</a>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-rose-900/30 hover:bg-rose-900/50 text-rose-300 transition-all duration-300 shadow-lg hover:shadow-rose-900/10">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Pet List</h1>
            <p class="text-indigo-300 text-sm">Manage client profiles and associated pet records.</p>
        </header>

        <div class="mb-8 max-w-lg reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="relative">
                <i class="bi bi-search absolute left-4 top-3 text-indigo-400"></i>
                <input type="text" placeholder="Search by name or phone..." class="w-full bg-indigo-900/20 border border-indigo-800 rounded-xl pl-12 pr-4 py-3 text-white placeholder-indigo-400 focus:border-indigo-500 transition-all outline-none">
            </div>
        </div>

        <div class="space-y-4">
            <!-- Owner Card -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-xl overflow-hidden hover:border-indigo-600 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <button onclick="toggleAccordion('shamaimah')" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-indigo-900/40 transition-all duration-300">
                    <span class="font-semibold text-white">Shamaimah</span>
                    <span class="text-indigo-400 text-sm">012-345-6789</span>
                    <i id="icon-shamaimah" class="bi bi-chevron-right text-indigo-400 transition-transform duration-300"></i>
                </button>
                <div id="shamaimah" class="hidden px-6 pb-4 border-t border-indigo-800/50">
                    <div class="pt-4 pb-4 space-y-2">
                        <div class="flex items-center justify-between bg-indigo-950/50 p-3 rounded-lg border border-indigo-800/50 hover:border-indigo-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Pom-S</span>
                            <span class="text-xs text-indigo-400">Golden Retriever</span>
                        </div>
                        <div class="flex items-center justify-between bg-indigo-950/50 p-3 rounded-lg border border-indigo-800/50 hover:border-indigo-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <span class="text-sm">🐾 Toby</span>
                            <span class="text-xs text-indigo-400">Persian Cat</span>
                        </div>
                        <button class="mt-2 text-xs text-indigo-400 hover:text-indigo-200 font-medium transition-colors hover:underline">
                            + Add New Pet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
