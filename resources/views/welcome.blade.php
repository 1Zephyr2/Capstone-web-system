<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Premium Pet Care Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Inter', 'ui-sans-serif', 'system-ui'],
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @keyframes floaty { 0%,100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-14px) rotate(1deg); } }
        @keyframes floaty2 { 0%,100% { transform: translateY(0); } 50% { transform: translateY(10px); } }
        .float-card { animation: floaty 6s ease-in-out infinite; }
        .float-badge { animation: floaty2 5s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) {
            .float-card, .float-badge { animation: none; }
        }
        .faq-content { max-height: 0; overflow: hidden; transition: max-height .35s ease, padding .35s ease; }
        .faq-open .faq-content { max-height: 240px; }
        .faq-open .faq-chevron { transform: rotate(180deg); }
    </style>
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
        function toggleFaq(el) { el.classList.toggle('faq-open'); }
    </script>
</head>
<body class="bg-white text-gray-800 antialiased font-sans">

    <!-- Navbar -->
    <nav class="fixed w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="#" class="text-xl font-display font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="#how"      class="hover:text-emerald-600 transition-all">How it works</a>
                <a href="#features" class="hover:text-emerald-600 transition-all">Features</a>
                <a href="{{ route('services') }}" class="hover:text-emerald-600 transition-all">Services</a>
                <a href="#faq"      class="hover:text-emerald-600 transition-all">FAQ</a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-emerald-600 transition-all">Login</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all shadow-sm">Get Started</a>
            </div>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="#how"      class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">How it works</a>
            <a href="#features" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Features</a>
            <a href="{{ route('services') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Services</a>
            <a href="#faq"      class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">FAQ</a>
            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Login</a>
            <a href="{{ route('register') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Get Started</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative pt-36 pb-24 md:pt-44 md:pb-32 bg-gradient-to-br from-emerald-50 via-white to-teal-50 overflow-hidden">
        <!-- decorative blobs -->
        <div class="absolute -top-16 -left-16 w-72 h-72 bg-emerald-200/40 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-20 w-96 h-96 bg-teal-200/40 rounded-full blur-3xl"></div>

        <div class="container relative mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <!-- Left: copy -->
            <div class="reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-widest mb-6">
                    <i class="bi bi-heart-fill"></i> Trusted pet care, made simple
                </span>
                <h1 class="text-4xl md:text-6xl font-display font-bold tracking-tight mb-6 text-gray-900 leading-[1.05]">
                    Every appointment,<br>
                    <span class="text-emerald-600">every pet,</span><br>
                    one calm dashboard.
                </h1>
                <p class="text-gray-500 text-lg max-w-lg mb-10 leading-relaxed">
                    FURCARE replaces the sticky notes and group chats with one clean system for booking grooming visits, tracking medical history, and keeping your whole clinic in sync.
                </p>
                <div class="flex items-center gap-4 flex-wrap mb-10">
                    <a href="{{ route('register') }}" class="px-8 py-4 font-bold text-sm text-white rounded-xl bg-emerald-600 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 hover:-translate-y-0.5">
                        Book your first visit
                    </a>
                    <a href="{{ route('services') }}" class="px-8 py-4 font-bold text-sm text-emerald-700 rounded-xl border-2 border-emerald-200 hover:bg-emerald-50 transition-all hover:-translate-y-0.5">
                        View Services
                    </a>
                </div>
                <div class="flex items-center gap-8">
                    <div><p class="text-2xl font-display font-bold text-gray-900">2,400+</p><p class="text-xs text-gray-400">Pets cared for</p></div>
                    <div class="w-px h-9 bg-gray-200"></div>
                    <div><p class="text-2xl font-display font-bold text-gray-900">4.9<span class="text-amber-400">★</span></p><p class="text-xs text-gray-400">Average rating</p></div>
                    <div class="w-px h-9 bg-gray-200"></div>
                    <div><p class="text-2xl font-display font-bold text-gray-900">98%</p><p class="text-xs text-gray-400">On-time visits</p></div>
                </div>
            </div>

            <!-- Right: floating product mockup -->
            <div class="relative reveal opacity-0 translate-y-6 transition-all duration-700 ease-out delay-150 hidden lg:block">
                <div class="relative mx-auto max-w-sm">
                    <div class="float-card bg-white border border-gray-200 rounded-3xl shadow-2xl shadow-emerald-100 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Today's Booking</span>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Confirmed</span>
                        </div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center text-2xl">🐶</div>
                            <div>
                                <p class="font-bold text-gray-900">Buddy</p>
                                <p class="text-xs text-gray-400">Golden Retriever · 3 yrs</p>
                            </div>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="bi bi-scissors text-emerald-600"></i> Full Groom & Bath
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="bi bi-clock text-emerald-600"></i> Today, 2:00 PM
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <i class="bi bi-person text-emerald-600"></i> Groomer: Alex
                            </div>
                        </div>
                        <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full w-3/4 bg-emerald-500 rounded-full"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Grooming in progress — 75% done</p>
                    </div>

                    <div class="float-badge absolute -top-6 -right-8 bg-white border border-gray-200 rounded-2xl shadow-xl px-4 py-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-sm">🐱</div>
                        <div>
                            <p class="text-xs font-bold text-gray-900">Milo</p>
                            <p class="text-[10px] text-gray-400">Checkup · 4:30 PM</p>
                        </div>
                    </div>

                    <div class="float-badge absolute -bottom-8 -left-10 bg-white border border-gray-200 rounded-2xl shadow-xl px-4 py-3" style="animation-delay: -2s;">
                        <div class="flex items-center gap-2 text-emerald-600">
                            <i class="bi bi-check-circle-fill"></i>
                            <p class="text-xs font-bold text-gray-900">Reminder sent</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how" class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">The Process</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900">Three steps to a happy pet</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8 relative reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <div class="hidden md:block absolute top-8 left-[16.5%] right-[16.5%] h-px bg-gradient-to-r from-emerald-200 via-teal-200 to-emerald-200"></div>
                @foreach([
                    ['01', 'calendar-plus', 'Book online', 'Pick a service, choose a time slot, and tell us a little about your pet — takes under two minutes.'],
                    ['02', 'car-front', 'Drop off', 'Bring your pet in at the scheduled time. We text you the moment their groomer is ready.'],
                    ['03', 'emoji-smile', 'Pick up & relax', 'Get a notification the second they\'re done, complete with notes from the visit.'],
                ] as $step)
                <div class="relative bg-white text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl mx-auto mb-5 shadow-lg shadow-emerald-200 relative z-10">
                        <i class="bi bi-{{ $step[1] }}"></i>
                    </div>
                    <span class="text-xs font-display font-bold text-emerald-300 tracking-widest">STEP {{ $step[0] }}</span>
                    <h3 class="font-display font-bold text-gray-900 text-lg mt-1 mb-2">{{ $step[2] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">{{ $step[3] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Services Summary -->
    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">What We Offer</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900 mb-3">Our Services</h2>
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
                    <h3 class="font-display font-bold text-gray-900 mb-1 text-sm">{{ $svc[1] }}</h3>
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
    <section id="features" class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">Under the hood</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900">Core capabilities</h2>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-5 mb-16 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['calendar-check','Easy Appointments','Quick online booking with live calendar availability'],
                    ['file-earmark-medical','Service Tracking','Complete visit and medical history for every pet'],
                    ['people','Staff Management','Organize your clinic team and daily workload'],
                    ['chat-dots','Client Communication','Automatic reminders keep pet owners in the loop'],
                ] as $feat)
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-4 text-emerald-600">
                        <i class="bi bi-{{ $feat[0] }}"></i>
                    </div>
                    <h5 class="font-display font-bold text-gray-900 mb-2 text-sm">{{ $feat[1] }}</h5>
                    <p class="text-xs text-gray-400">{{ $feat[2] }}</p>
                </div>
                @endforeach
            </div>
            <div class="grid md:grid-cols-2 gap-5 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <div class="border border-emerald-200 rounded-2xl p-8 bg-emerald-50">
                    <h4 class="font-display font-bold mb-2 text-emerald-800"><i class="bi bi-person-badge mr-2"></i>For Staff</h4>
                    <p class="text-gray-500 text-sm">Manage operations, appointment requests, visits, and medical records from one dashboard.</p>
                </div>
                <div class="border border-teal-200 rounded-2xl p-8 bg-teal-50">
                    <h4 class="font-display font-bold mb-2 text-teal-800"><i class="bi bi-people mr-2"></i>For Pet Owners</h4>
                    <p class="text-gray-500 text-sm">View appointments, manage pets, and access account details through a simple self-service experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-14 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">Loved by pet owners</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900">Don't just take our word for it</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['Jamie R.', 'Owner of Buddy', 'I get a text the moment Buddy\'s groom is done. No more calling the front desk to check.', '🐶'],
                    ['Priya N.', 'Owner of Milo & Luna', 'Two cats, two schedules, zero confusion. Everything lives in one place now.', '🐱'],
                    ['Dan O.', 'Owner of Rex', 'Booking took less time than making coffee. Rex\'s vet history is all right there too.', '🐾'],
                ] as $t)
                <div class="bg-white border border-gray-200 rounded-2xl p-7">
                    <div class="text-amber-400 mb-4 text-sm">★★★★★</div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6">"{{ $t[2] }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-lg">{{ $t[3] }}</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $t[0] }}</p>
                            <p class="text-xs text-gray-400">{{ $t[1] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-3xl">
            <div class="text-center mb-12 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 block">Questions</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-gray-900">Frequently asked questions</h2>
            </div>
            <div class="space-y-3 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                @foreach([
                    ['Do I need an account to book an appointment?', 'Yes — creating a free account lets you save your pet\'s profile, view visit history, and get reminders for upcoming appointments.'],
                    ['Can I reschedule or cancel a booking?', 'Absolutely. Open the appointment from your dashboard and choose reschedule or cancel, no phone call needed.'],
                    ['How far in advance should I book?', 'We recommend booking at least 2–3 days ahead for grooming, though same-day slots are sometimes available.'],
                    ['Will I be notified when the visit is done?', 'Yes, you\'ll get a notification as soon as your pet\'s groomer or vet marks the visit complete.'],
                ] as $faq)
                <div class="faq-item border border-gray-200 rounded-2xl overflow-hidden">
                    <button type="button" onclick="toggleFaq(this.parentElement)" class="w-full flex items-center justify-between text-left px-6 py-4 hover:bg-gray-50 transition-all">
                        <span class="font-semibold text-gray-900 text-sm">{{ $faq[0] }}</span>
                        <i class="bi bi-chevron-down text-emerald-600 faq-chevron transition-transform"></i>
                    </button>
                    <div class="faq-content px-6">
                        <p class="text-gray-500 text-sm leading-relaxed pb-5">{{ $faq[1] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Grooming Styles Gallery -->
    @php
        $homeStyles = App\Models\GroomingOption::where('type', 'style')->where('is_active', true)->orderBy('name')->get();
    @endphp
    @if($homeStyles->count() > 0)
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                <span class="inline-block px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-widest mb-4">See The Options</span>
                <h2 class="text-3xl font-display font-bold text-gray-900 mb-3">Grooming Styles</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Browse real examples so you know exactly what to expect for your pet.</p>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($homeStyles as $style)
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
                        @if($style->image)
                            <img src="{{ asset('storage/' . $style->image) }}" alt="{{ $style->name }}" class="w-full h-44 object-cover">
                        @else
                            <div class="w-full h-44 bg-emerald-50 flex items-center justify-center">
                                <i class="bi bi-scissors text-emerald-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-1">{{ $style->name }}</h3>
                            @if($style->description)
                                <p class="text-gray-400 text-sm">{{ $style->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('services') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm transition-all">See all services →</a>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA -->
    <section class="py-24 bg-emerald-600 relative overflow-hidden">
        <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-emerald-500/40 rounded-full blur-3xl"></div>
        <div class="container relative mx-auto px-6 text-center reveal opacity-0 translate-y-6 transition-all duration-700 ease-out">
            <h2 class="text-3xl md:text-4xl font-display font-bold text-white mb-4">Ready to get started?</h2>
            <p class="text-emerald-100 mb-8 max-w-xl mx-auto">Join pet owners and clinics already using FURCARE to simplify their care workflow.</p>
            <a href="{{ route('register') }}" class="inline-flex px-8 py-4 rounded-xl bg-white text-emerald-700 font-bold hover:bg-emerald-50 transition-all shadow-lg hover:-translate-y-0.5">
                Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-10 mb-12">
                <div>
                    <a href="#" class="text-lg font-display font-bold flex items-center gap-2 text-white mb-3">
                        <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
                    </a>
                    <p class="text-sm leading-relaxed">Modern appointment and care management for pet grooming clinics.</p>
                </div>
                <div>
                    <h5 class="text-white font-semibold text-sm mb-3">Product</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-all">Services</a></li>
                        <li><a href="#how" class="hover:text-white transition-all">How it works</a></li>
                        <li><a href="#features" class="hover:text-white transition-all">Features</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white font-semibold text-sm mb-3">Account</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-all">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-all">Create account</a></li>
                        <li><a href="{{ route('staff.login') }}" class="hover:text-white transition-all">Staff portal</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-white font-semibold text-sm mb-3">Contact Bark Park</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="tel:+639700577320" class="hover:text-white transition-all flex items-center gap-2"><i class="bi bi-telephone"></i> 0970 057 7320</a></li>
                        <li><a href="https://www.facebook.com/profile.php?id=61564144455710&rdid=X2RMaCnl9m4vtFp7&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1H3MLMSw7o" target="_blank" rel="noopener" class="hover:text-white transition-all flex items-center gap-2"><i class="bi bi-facebook"></i> facebook.com/barkpark</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs">&copy; {{ date('Y') }} FURCARE | Pet Care Appointment System</p>
                <div class="flex items-center gap-4 text-lg">
                    <a href="https://facebook.com/barkpark" target="_blank" rel="noopener" class="hover:text-white transition-all"><i class="bi bi-facebook"></i></a>
                    <i class="bi bi-instagram hover:text-white transition-all cursor-pointer"></i>
                    <i class="bi bi-twitter-x hover:text-white transition-all cursor-pointer"></i>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
