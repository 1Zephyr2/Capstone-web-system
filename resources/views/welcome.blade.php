<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Premium Pet Care Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-6');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
    </script>
</head>
<body class="bg-white text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="#" class="text-xl font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="#about"    class="hover:text-emerald-600 transition-all">About</a>
                <a href="#features" class="hover:text-emerald-600 transition-all">Features</a>
                <a href="{{ route('services') }}" class="hover:text-emerald-600 transition-all">Services</a>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all shadow-sm">Login</a>
            </div>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="#about"    class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">About</a>
            <a href="#features" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Features</a>
            <a href="{{ route('services') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Services</a>
            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Login</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-emerald-50 via-white to-teal-50">
        <div class="container mx-auto px-6 text-center max-w-3xl reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
            <span class="inline-block px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-widest mb-6">Pet Grooming System</span>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-6 text-gray-900 leading-tight">
                FURCARE<br><span class="text-emerald-600">Pet Care Appointment System</span>
            </h1>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                Set appointments, manage grooming services, and track schedules in one secure platform designed for pet grooming clinics.
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="{{ route('register') }}" class="px-8 py-4 font-bold text-sm text-white rounded-xl bg-emerald-600 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 hover:-translate-y-0.5">
                    Get Started — It's Free
                </a>
                <a href="{{ route('services') }}" class="px-8 py-4 font-bold text-sm text-emerald-700 rounded-xl border-2 border-emerald-200 hover:bg-emerald-50 transition-all hover:-translate-y-0.5">
                    View Services
                </a>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-6 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-8">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">About FURCARE</span>
                    <h2 class="text-2xl font-bold mb-4 text-gray-900">A cleaner way to manage pet care</h2>
                    <p class="text-gray-500 leading-relaxed">
                        FURCARE brings appointments, pet profiles, service history, and communication into one smooth experience for pet owners and clinic staff.
                    </p>
                </div>
                <div class="bg-teal-50 border border-teal-100 rounded-2xl p-8">
                    <i class="bi bi-layers text-emerald-600 text-2xl mb-4 block"></i>
                    <p class="text-gray-500 leading-relaxed">
                        Replace fragmented spreadsheets and disconnected booking tools with a unified management interface — streamlining your entire operation from first contact to final service.
                    </p>
                    <p class="text-gray-500 leading-relaxed mt-4">
                        Manage schedules, detailed visit history, and client communications in one centralized platform.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Summary -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">What We Offer</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Our Services</h2>
                <p class="text-gray-500 max-w-xl mx-auto text-sm">Professional pet care tailored for every breed and size.</p>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-5 mb-8 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['bi-scissors',    'Grooming',           'Coat styling and hygiene treatments',    'bg-emerald-100 text-emerald-600'],
                    ['bi-heart-pulse', 'Veterinary Checkup', 'Health assessments and clinical care',   'bg-red-100 text-red-500'],
                    ['bi-shield-plus', 'Vaccination',         'Scheduled immunizations and protection', 'bg-blue-100 text-blue-600'],
                    ['bi-house-heart', 'Boarding',            'Safe overnight stays for your pet',      'bg-violet-100 text-violet-600'],
                ] as $svc)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 {{ explode(' ',$svc[3])[0] }} rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="{{ $svc[0] }} {{ explode(' ',$svc[3])[1] }} text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 text-sm">{{ $svc[1] }}</h3>
                    <p class="text-gray-400 text-xs">{{ $svc[2] }}</p>
                </div>
                @endforeach
            </div>
            <!-- Size categories -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['Small','Under 10kg','bg-emerald-100 text-emerald-700 border-emerald-200'],
                    ['Medium','10–25kg','bg-teal-100 text-teal-700 border-teal-200'],
                    ['Large','25–45kg','bg-amber-100 text-amber-700 border-amber-200'],
                    ['Extra Large','45kg+','bg-orange-100 text-orange-700 border-orange-200'],
                ] as [$label,$range,$cls])
                <div class="border {{ explode(' ',$cls)[2] }} {{ explode(' ',$cls)[0] }} rounded-xl p-4 text-center">
                    <p class="font-bold {{ explode(' ',$cls)[1] }} text-sm">{{ $label }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $range }}</p>
                </div>
                @endforeach
            </div>
            <div class="text-center reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-emerald-200 text-emerald-700 hover:bg-emerald-50 font-semibold text-sm transition-all">
                    View Full Services <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">Core Capabilities</h2>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-5 mb-16 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['calendar-check','Easy Appointments','Quick online booking with calendar'],
                    ['file-earmark-medical','Service Tracking','Complete visit and medical history'],
                    ['people','Staff Management','Organize your clinic team'],
                    ['chat-dots','Client Communication','Stay connected with pet owners'],
                ] as $feat)
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-4 text-emerald-600">
                        <i class="bi bi-{{ $feat[0] }}"></i>
                    </div>
                    <h5 class="font-bold text-gray-900 mb-2 text-sm">{{ $feat[1] }}</h5>
                    <p class="text-xs text-gray-400">{{ $feat[2] }}</p>
                </div>
                @endforeach
            </div>
            <div class="grid md:grid-cols-2 gap-5 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <div class="border border-emerald-200 rounded-2xl p-8 bg-emerald-50">
                    <h4 class="font-bold mb-2 text-emerald-800"><i class="bi bi-person-badge mr-2"></i>For Staff</h4>
                    <p class="text-gray-500 text-sm">Manage operations, appointment requests, visits, and medical records from one dashboard.</p>
                </div>
                <div class="border border-teal-200 rounded-2xl p-8 bg-teal-50">
                    <h4 class="font-bold mb-2 text-teal-800"><i class="bi bi-people mr-2"></i>For Pet Owners</h4>
                    <p class="text-gray-500 text-sm">View appointments, manage pets, and access account details through a simple self-service experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-emerald-600">
        <div class="container mx-auto px-6 text-center reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to get started?</h2>
            <p class="text-emerald-100 mb-8 max-w-xl mx-auto">Join pet owners and clinics already using FURCARE to simplify their care workflow.</p>
            <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-white text-emerald-700 font-bold hover:bg-emerald-50 transition-all shadow-lg hover:-translate-y-0.5">
                Create Free Account
            </a>
        </div>
    </section>

    <footer class="py-8 text-center border-t border-gray-200 bg-white">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE | Pet Care Appointment System</p>
    </footer>
</body>
</html>