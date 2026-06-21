<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Appointments</title>
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

    <!-- Navbar -->
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
            <p class="text-indigo-300 text-sm">View and manage confirmed grooming sessions.</p>
        </header>

        <!-- Appointments List -->
        <div class="space-y-4">
            <!-- Appointment Card -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-xl p-6 hover:border-indigo-600 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white">Max (Golden Retriever)</h5>
                        <p class="text-sm text-indigo-300">Owner: Shamaimah | 10:00 AM - 11:30 AM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 font-medium">Confirmed</span>
                </div>
            </div>

            <!-- Appointment Card -->
            <div class="bg-indigo-900/20 border border-indigo-800/80 rounded-xl p-6 hover:border-indigo-600 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-white">Luna (Persian Cat)</h5>
                        <p class="text-sm text-indigo-300">Owner: Shamaimah | 01:00 PM - 02:00 PM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 font-medium">Confirmed</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
