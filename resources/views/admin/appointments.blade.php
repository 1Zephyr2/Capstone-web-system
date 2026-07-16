<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Appointments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}" class="hover:text-gray-900 transition-all duration-300 hover:scale-105">Insights</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-12">
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-gray-900">Upcoming Appointments</h1>
            <p class="text-gray-500 text-sm">View and manage confirmed grooming sessions.</p>
        </header>

        <!-- Appointments List -->
        <div class="space-y-4">
            <!-- Appointment Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-indigo-300 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-gray-500">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-gray-900">Max (Golden Retriever)</h5>
                        <p class="text-sm text-gray-500">Owner: Shamaimah | 10:00 AM - 11:30 AM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-600 border border-emerald-200 font-medium">Confirmed</span>
                </div>
            </div>

            <!-- Appointment Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-indigo-300 transition-all duration-300 hover:shadow-[0_0_20px_rgba(79,70,229,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-gray-500">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h5 class="font-semibold text-gray-900">Luna (Persian Cat)</h5>
                        <p class="text-sm text-gray-500">Owner: Shamaimah | 01:00 PM - 02:00 PM</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-xs bg-emerald-100 text-emerald-600 border border-emerald-200 font-medium">Confirmed</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
