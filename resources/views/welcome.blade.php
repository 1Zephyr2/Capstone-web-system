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

    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-[#0c1220] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="#about"    class="hover:text-white transition-all hover:scale-105">About</a>
                <a href="#features" class="hover:text-white transition-all hover:scale-105">Features</a>
                <a href="{{ route('services') }}" class="hover:text-white transition-all hover:scale-105">Services</a>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-full bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:-translate-y-0.5">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="relative z-10 pt-40 pb-24 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6 text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter mb-6 text-white">
                FURCARE : Pet Grooming Appointment System
            </h1>
            <p class="text-slate-300 text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
                Set appointments, manage grooming services, and track schedules in one secure platform designed for pet grooming clinics.
            </p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 font-bold text-sm text-white rounded-xl bg-teal-500 hover:bg-teal-400 transition-all hover:-translate-y-1 shadow-lg shadow-teal-500/25">
                    Get Started
                </a>
                <a href="{{ route('services') }}" class="px-8 py-4 font-bold text-sm text-teal-400 rounded-xl border border-teal-500/30 hover:bg-teal-500/10 transition-all hover:-translate-y-1">
                    View Services
                </a>
            </div>
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
                        FURCARE replaces fragmented spreadsheets and disconnected booking tools with a unified management interface — streamlining your entire operation from first contact to final service.
                    </p>
                    <p class="text-slate-400 leading-relaxed mt-4">
                        Manage schedules, detailed visit record history, and client communications in one centralized, high-performance platform.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Summary -->
    <section id="services-summary" class="relative z-10 py-20 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <span class="tracking-widest uppercase text-xs font-semibold text-teal-400 mb-3 block">What We Offer</span>
                <h2 class="text-3xl font-bold text-white mb-3">Our Services</h2>
                <p class="text-slate-400 max-w-xl mx-auto text-sm">Professional pet care tailored for every breed and size — from small companions to extra-large dogs.</p>
            </div>

            <!-- Core service cards -->
            <div class="grid md:grid-cols-4 gap-5 mb-8">
                @foreach([
                    ['icon'=>'bi-scissors',    'label'=>'Grooming',          'desc'=>'Coat styling and hygiene treatments',       'color'=>'text-teal-400',   'bg'=>'bg-teal-500/10'],
                    ['icon'=>'bi-heart-pulse', 'label'=>'Veterinary Checkup','desc'=>'Health assessments and clinical care',       'color'=>'text-red-400',    'bg'=>'bg-red-500/10'],
                    ['icon'=>'bi-shield-plus', 'label'=>'Vaccination',        'desc'=>'Scheduled immunizations and protection',    'color'=>'text-blue-400',   'bg'=>'bg-blue-500/10'],
                    ['icon'=>'bi-house-heart', 'label'=>'Boarding',           'desc'=>'Safe overnight stays for your pet',         'color'=>'text-violet-400', 'bg'=>'bg-violet-500/10'],
                ] as $svc)
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 text-center hover:border-slate-700 transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 {{ $svc['bg'] }} rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="{{ $svc['icon'] }} {{ $svc['color'] }} text-xl"></i>
                    </div>
                    <h3 class="font-bold text-white mb-1 text-sm">{{ $svc['label'] }}</h3>
                    <p class="text-slate-400 text-xs">{{ $svc['desc'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- Size categories mini -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                @foreach([
                    ['Small','Under 10kg','text-teal-400','border-teal-500/20','bg-teal-500/5'],
                    ['Medium','10–25kg','text-violet-400','border-violet-500/20','bg-violet-500/5'],
                    ['Large','25–45kg','text-amber-400','border-amber-500/20','bg-amber-500/5'],
                    ['Extra Large','45kg+','text-rose-400','border-rose-500/20','bg-rose-500/5'],
                ] as [$label, $range, $color, $border, $bg])
                <div class="border {{ $border }} {{ $bg }} rounded-xl p-4 text-center">
                    <p class="font-bold text-white text-sm">{{ $label }}</p>
                    <p class="text-xs {{ $color }} mt-0.5">{{ $range }}</p>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-teal-500/30 text-teal-400 hover:bg-teal-500/10 font-semibold text-sm transition-all hover:-translate-y-0.5">
                    View Full Services Page <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="relative z-10 py-20 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-white">Core Capabilities</h2>
            <div class="grid md:grid-cols-4 gap-6 mb-20">
                @foreach([
                    ['calendar-check','Easy Appointments','Quick online booking'],
                    ['file-earmark-medical','Service Tracking','Complete visit history'],
                    ['people','Staff Management','Organize your team'],
                    ['chat-dots','Client Communication','Stay connected'],
                ] as $feat)
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-6 hover:-translate-y-1 hover:border-slate-700 transition-all group shadow-xl hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
                    <div class="w-10 h-10 rounded-lg bg-slate-800/50 flex items-center justify-center mb-6 text-teal-400">
                        <i class="bi bi-{{ $feat[0] }}"></i>
                    </div>
                    <h5 class="font-semibold mb-2 text-white group-hover:text-teal-400 transition-colors">{{ $feat[1] }}</h5>
                    <p class="text-sm text-slate-400">{{ $feat[2] }}</p>
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

    <footer class="relative z-10 py-10 text-center border-t border-white/5">
        <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} FURCARE | Appointment System</p>
    </footer>
</body>
</html>