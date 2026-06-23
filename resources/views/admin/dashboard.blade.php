<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Dashboard</title>
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
<body class="bg-indigo-950 text-indigo-100 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-500/20 border border-rose-500/30">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-indigo-300">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}" class="text-white hover:text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50 hover:scale-105 active:scale-95">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white">Admin Overview</h1>
            <p class="text-indigo-300 text-sm">System management and configurations.</p>
        </header>

        <div class="grid md:grid-cols-3 gap-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-100">
            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 hover:border-indigo-600 transition-all duration-300">
                <h3 class="font-bold text-white mb-2"><i class="bi bi-calendar-check text-indigo-400 mr-2"></i> Appointments</h3>
                <p class="text-sm text-indigo-300 mb-6">Manage all incoming appointment requests.</p>
                <a href="{{ route('staff.appointments') }}" class="text-indigo-400 hover:text-white text-sm font-semibold transition-colors">Manage appointments &rarr;</a>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 hover:border-indigo-600 transition-all duration-300">
                <h3 class="font-bold text-white mb-2"><i class="bi bi-people text-indigo-400 mr-2"></i> Staff Accounts</h3>
                <p class="text-sm text-indigo-300 mb-6">Manage staff users and permissions.</p>
                <a href="{{ route('staff.directory') }}" class="text-indigo-400 hover:text-white text-sm font-semibold transition-colors">Manage staff &rarr;</a>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 hover:border-indigo-600 transition-all duration-300">
                <h3 class="font-bold text-white mb-2"><i class="bi bi-cash-stack text-indigo-400 mr-2"></i> Services & Pricing</h3>
                <p class="text-sm text-indigo-300 mb-6">Update service rates and available packages.</p>
                <a href="#" class="text-indigo-400 hover:text-white text-sm font-semibold transition-colors">Manage services &rarr;</a>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8 mt-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 hover:border-indigo-600 transition-all duration-300">
                <h3 class="font-bold text-white mb-2"><i class="bi bi-graph-up-arrow text-indigo-400 mr-2"></i> Insights & Reports</h3>
                <p class="text-sm text-indigo-300 mb-6">View system performance and business metrics.</p>
                <a href="{{ route('staff.insights') }}" class="text-indigo-400 hover:text-white text-sm font-semibold transition-colors">View insights &rarr;</a>
            </div>
            <div class="bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 hover:border-indigo-600 transition-all duration-300">
                <h3 class="font-bold text-white mb-2"><i class="bi bi-gear text-indigo-400 mr-2"></i> System Settings</h3>
                <p class="text-sm text-indigo-300 mb-6">General system configuration and defaults.</p>
                <a href="#" class="text-indigo-400 hover:text-white text-sm font-semibold transition-colors">System settings &rarr;</a>
            </div>
        </div>
    </main>
</body>
</html>
