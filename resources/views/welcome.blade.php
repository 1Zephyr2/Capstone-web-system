<!-- Hidden Staff Login Gateway is routed securely via /staff/login -->
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Premium Pet Care Management</title>
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
<body class="bg-slate-950 text-slate-200 antialiased relative overflow-x-hidden">

    <!-- Ambient Background Glows -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-[#0c1220] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-check text-teal-500"></i>FURCARE
            </a>
            <div class="flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#about" class="hover:text-teal-400 transition-colors">About</a>
                <a href="#features" class="hover:text-teal-400 transition-colors">Features</a>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-full bg-teal-600 hover:bg-teal-500 text-white font-semibold transform hover:-translate-y-0.5 transition-all active:translate-y-0">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="relative z-10 py-32 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6 flex flex-col items-center justify-center text-center max-w-3xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-6 text-white">
                Pet Care Appointment System
            </h1>
            <p class="text-white text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
                Set Appointments, pet grooming services, and schedules in one secure platform designed for pet grooming clinics.
            </p>
            <a href="{{ route('register') }}" class="px-8 py-4 font-bold text-sm text-white rounded-xl bg-teal-500 hover:bg-teal-400 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-teal-500/25">
                Get Started
            </a>
        </div>
    </header>

    <!-- About -->
    <section id="about" class="relative z-10 py-20 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                    <span class="tracking-widest uppercase text-xs font-semibold text-teal-400 mb-3 block">About FURCARE</span>
                    <h2 class="text-3xl font-bold mb-4 text-white">A cleaner way to manage pet care</h2>
                    <p class="text-slate-400 leading-relaxed">
                        FURCARE brings appointments, pet profiles, service history, and communication into one smooth experience for pet owners and staff.
                    </p>
                </div>
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                    <i class="bi bi-layers text-teal-400 text-2xl mb-4 block"></i>
                    <p class="text-slate-400 leading-relaxed">
                        FURCARE replaces fragmented spreadsheets and disconnected booking tools with a unified management interface. Designed for modern clinics, it streamlines your entire operation from the first contact to the final service.
                    </p>
                    <p class="text-slate-400 leading-relaxed mt-4">
                        Experience a professional, intuitive system that scales with your business. Manage schedules, detailed visit record history, and client communications in one centralized, high-performance platform.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features & Values -->
    <section id="features" class="relative z-10 py-20 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-white">Core Capabilities</h2>
            <div class="grid md:grid-cols-4 gap-6 mb-20">
                @foreach([['calendar-check', 'Easy Appointments', 'Quick booking'], ['file-earmark-medical', 'Service Tracking', 'Complete history'], ['people', 'Staff Management', 'Organize schedules'], ['chat-dots', 'Client Communication', 'Stay connected']] as $feat)
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-slate-700 group shadow-xl hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                    <div class="w-10 h-10 rounded-lg bg-slate-800/50 flex items-center justify-center mb-6 text-teal-400">
                        <i class="bi bi-{{$feat[0]}}"></i>
                    </div>
                    <h5 class="font-semibold mb-2 text-white group-hover:text-teal-400 transition-colors">{{$feat[1]}}</h5>
                    <p class="text-sm text-slate-400">{{$feat[2]}}</p>
                </div>
                @endforeach
            </div>

            <h2 class="text-3xl font-bold text-center mb-12 text-white">System Value</h2>
            <div class="grid md:grid-cols-3 gap-6 mb-20">
                @foreach([['database', 'Accessible records', 'Maintain complete profiles and service history.'], ['graph-up', 'Operational Insights', 'Monitor activity trends and performance.'], ['person-gear', 'Owner Self-Service', 'Direct visibility into appointments and profiles.']] as $val)
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                    <i class="bi bi-{{$val[0]}} text-2xl text-teal-400 mb-4 block"></i>
                    <h5 class="font-semibold mb-2 text-white">{{$val[1]}}</h5>
                    <p class="text-sm text-slate-400 leading-relaxed">{{$val[2]}}</p>
                </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="border border-slate-800 rounded-2xl p-8 bg-slate-900/40">
                    <h4 class="font-bold mb-2 text-white">For Staff</h4>
                    <p class="text-slate-400 text-sm">Manage operations, requests, visits, and records.</p>
                </div>
                <div class="border border-slate-800 rounded-2xl p-8 bg-slate-900/40">
                    <h4 class="font-bold mb-2 text-white">For Pet Owners</h4>
                    <p class="text-slate-400 text-sm">View appointments, pets, and account details through a simple self-service experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-10 text-center border-t border-white/5">
        <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} FURCARE | Appointment System</p>
    </footer>

</body>
</html>
