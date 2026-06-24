<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Staff Dashboard</title>
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
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Background (Staff: Violet/Indigo) -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-violet-500/10 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar (Staff: Indigo-900 base) -->
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
            <form action="{{ route('staff.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all duration-300 text-white shadow-lg">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-6 py-12 relative z-10">
        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white">Clinic Overview</h1>
            <p class="text-slate-400">Welcome back, staff member. Here is today's snapshot.</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            @foreach([['calendar-event', 'Today\'s Appointments', '12'], ['paw', 'Pets in Care', '5'], ['envelope-paper', 'Pending Requests', '3'], ['graph-up', 'Analytics Score', '94%']] as $stat)
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 hover:border-violet-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <i class="bi bi-{{$stat[0]}} text-violet-400 text-xl mb-2 block"></i>
                <h5 class="text-slate-400 text-sm mb-1">{{$stat[1]}}</h5>
                <p class="text-2xl font-bold text-white">{{$stat[2]}}</p>
            </div>
            @endforeach
        </div>

        <!-- Main Content Area -->
        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-violet-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                <h3 class="text-lg font-bold text-white mb-6">Pending Appointment Requests</h3>
                <div class="space-y-4">
                    <p class="text-slate-400 italic text-sm">No pending requests at this time.</p>
                </div>
            </div>
            <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-violet-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">
                <h3 class="text-lg font-bold text-white mb-6">Quick Actions</h3>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('staff.directory') }}" class="block px-4 py-3 rounded-lg bg-slate-800/50 hover:bg-violet-600 transition-all duration-300 hover:translate-x-1">Customers List</a>
                    <a href="{{ route('staff.appointments') }}" class="block px-4 py-3 rounded-lg bg-slate-800/50 hover:bg-violet-600 transition-all duration-300 hover:translate-x-1">Create Appointment</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="relative z-10 py-10 text-center border-t border-white/5 text-slate-500 text-sm">
        &copy; {{ date('Y') }} FURCARE | Staff System.
    </footer>

</body>
</html>
