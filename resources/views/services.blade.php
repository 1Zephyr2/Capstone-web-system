@php
    $styles = App\Models\GroomingOption::where('type','style')->where('is_active',true)->orderBy('name')->get();
    $addons = App\Models\GroomingOption::where('type','addon')->where('is_active',true)->orderBy('name')->get();

    $sizes = [
        [
            'label'   => 'Small',
            'range'   => 'Under 10kg',
            'icon'    => 'bi-emoji-smile',
            'color'   => 'text-teal-400',
            'bg'      => 'bg-teal-500/10',
            'border'  => 'border-teal-500/20',
            'breeds'  => 'Chihuahua, Pomeranian, Shih Tzu, Maltese',
            'desc'    => 'Delicate handling with gentle grooming techniques suited for small breeds.',
        ],
        [
            'label'   => 'Medium',
            'range'   => '10–25kg',
            'icon'    => 'bi-emoji-laughing',
            'color'   => 'text-violet-400',
            'bg'      => 'bg-violet-500/10',
            'border'  => 'border-violet-500/20',
            'breeds'  => 'Beagle, Cocker Spaniel, French Bulldog, Shiba Inu',
            'desc'    => 'Balanced grooming for medium breeds with standard coat requirements.',
        ],
        [
            'label'   => 'Large',
            'range'   => '25–45kg',
            'icon'    => 'bi-emoji-heart-eyes',
            'color'   => 'text-amber-400',
            'bg'      => 'bg-amber-500/10',
            'border'  => 'border-amber-500/20',
            'breeds'  => 'Golden Retriever, Labrador, Husky, German Shepherd',
            'desc'    => 'Full-service grooming for large breeds including double coats.',
        ],
        [
            'label'   => 'Extra Large',
            'range'   => '45kg+',
            'icon'    => 'bi-award',
            'color'   => 'text-rose-400',
            'bg'      => 'bg-rose-500/10',
            'border'  => 'border-rose-500/20',
            'breeds'  => 'Saint Bernard, Great Dane, Rottweiler, Mastiff',
            'desc'    => 'Specialized grooming for extra large breeds requiring extended sessions.',
        ],
    ];

    $serviceIcons = [
        'grooming'    => ['icon' => 'bi-scissors',      'color' => 'text-teal-400',   'bg' => 'bg-teal-500/10'],
        'veterinary'  => ['icon' => 'bi-heart-pulse',   'color' => 'text-red-400',    'bg' => 'bg-red-500/10'],
        'vaccination' => ['icon' => 'bi-shield-plus',   'color' => 'text-blue-400',   'bg' => 'bg-blue-500/10'],
        'boarding'    => ['icon' => 'bi-house-heart',   'color' => 'text-violet-400', 'bg' => 'bg-violet-500/10'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Our Services</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100','translate-y-0');
                        entry.target.classList.remove('opacity-0','translate-y-10');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <!-- Ambient glow -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-teal-500/8 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-violet-500/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-[#0c1220] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="/#about"    class="hover:text-white transition-all">About</a>
                <a href="/#features" class="hover:text-white transition-all">Features</a>
                <a href="{{ route('services') }}" class="text-teal-400 font-semibold">Services</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-full bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="relative z-10 pt-28 pb-20">
        <div class="container mx-auto px-6">

            <!-- Hero -->
            <div class="text-center mb-16 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <span class="text-xs font-bold uppercase tracking-widest text-teal-400 mb-3 block">What We Offer</span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">Our Services</h1>
                <p class="text-slate-400 text-lg max-w-2xl mx-auto">Professional pet care services tailored to every breed and size.</p>
            </div>

            <!-- ── Core Services ───────────────────────────────────────── -->
            <section class="mb-20">
                <h2 class="text-2xl font-bold text-white mb-8 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Core Services</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach(\App\Models\Appointment::SERVICE_TYPES as $key => $label)
                        @php $cfg = $serviceIcons[$key] ?? ['icon'=>'bi-star','color'=>'text-teal-400','bg'=>'bg-teal-500/10']; @endphp
                        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700 transition-all hover:-translate-y-1 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                            <div class="w-12 h-12 rounded-xl {{ $cfg['bg'] }} flex items-center justify-center mb-4">
                                <i class="{{ $cfg['icon'] }} {{ $cfg['color'] }} text-xl"></i>
                            </div>
                            <h3 class="font-bold text-white mb-2">{{ $label }}</h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                @switch($key)
                                    @case('grooming')    Professional coat styling and hygiene treatment. @break
                                    @case('veterinary')  Health assessments and clinical checkups. @break
                                    @case('vaccination') Scheduled immunizations to keep your pet protected. @break
                                    @case('boarding')    Safe and comfortable overnight stays. @break
                                @endswitch
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ── Pet Size Categories ────────────────────────────────── -->
            <section class="mb-20">
                <h2 class="text-2xl font-bold text-white mb-3 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Service by Pet Size</h2>
                <p class="text-slate-400 text-sm mb-8 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Our services are tailored based on your pet's weight and breed characteristics.</p>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($sizes as $size)
                        <div class="bg-slate-900/40 border {{ $size['border'] }} rounded-2xl p-6 hover:-translate-y-1 transition-all duration-300 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                            <div class="w-12 h-12 rounded-xl {{ $size['bg'] }} flex items-center justify-center mb-4">
                                <i class="{{ $size['icon'] }} {{ $size['color'] }} text-xl"></i>
                            </div>
                            <div class="flex items-baseline gap-2 mb-1">
                                <h3 class="font-bold text-white">{{ $size['label'] }}</h3>
                                <span class="text-xs {{ $size['color'] }} font-semibold">{{ $size['range'] }}</span>
                            </div>
                            <p class="text-slate-500 text-xs mb-3 italic">{{ $size['breeds'] }}</p>
                            <p class="text-slate-400 text-sm leading-relaxed">{{ $size['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- ── Grooming Styles ─────────────────────────────────────── -->
            @if($styles->count() > 0)
            <section class="mb-20">
                <h2 class="text-2xl font-bold text-white mb-3 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Grooming Styles</h2>
                <p class="text-slate-400 text-sm mb-8 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Choose the perfect cut and style for your pet.</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($styles as $style)
                        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl overflow-hidden hover:border-teal-500/30 hover:-translate-y-1 transition-all duration-300 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                            <!-- Image or icon placeholder -->
                            @if($style->image)
                                <img src="{{ asset('storage/' . $style->image) }}"
                                     alt="{{ $style->name }}"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-teal-500/20 to-slate-800/80 flex items-center justify-center">
                                    <i class="bi bi-scissors text-teal-400 text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-bold text-white mb-1">{{ $style->name }}</h3>
                                @if($style->description)
                                    <p class="text-slate-400 text-sm">{{ $style->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- ── Add-on Services ─────────────────────────────────────── -->
            @if($addons->count() > 0)
            <section class="mb-20">
                <h2 class="text-2xl font-bold text-white mb-3 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Add-on Services</h2>
                <p class="text-slate-400 text-sm mb-8 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">Enhance your pet's grooming session with these extras.</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($addons as $addon)
                        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl overflow-hidden hover:border-violet-500/30 hover:-translate-y-1 transition-all duration-300 reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                            @if($addon->image)
                                <img src="{{ asset('storage/' . $addon->image) }}"
                                     alt="{{ $addon->name }}"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-violet-500/20 to-slate-800/80 flex items-center justify-center">
                                    <i class="bi bi-plus-circle text-violet-400 text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-bold text-white mb-1">{{ $addon->name }}</h3>
                                @if($addon->description)
                                    <p class="text-slate-400 text-sm">{{ $addon->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- ── CTA ───────────────────────────────────────────────── -->
            <section class="text-center reveal opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="bg-slate-900/40 border border-teal-500/20 rounded-3xl p-12 max-w-2xl mx-auto">
                    <i class="bi bi-calendar-check text-teal-400 text-4xl mb-4 block"></i>
                    <h2 class="text-2xl font-bold text-white mb-3">Ready to Book?</h2>
                    <p class="text-slate-400 mb-8">Request an appointment online and we'll confirm your slot.</p>
                    @auth
                        <a href="{{ route('request.appointment') }}"
                           class="px-8 py-4 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold transition-all hover:scale-105 shadow-lg shadow-teal-900/20">
                            Book an Appointment
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="px-8 py-4 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold transition-all hover:scale-105 shadow-lg shadow-teal-900/20">
                            Get Started — It's Free
                        </a>
                    @endauth
                </div>
            </section>
        </div>
    </main>

    <footer class="relative z-10 py-8 text-center border-t border-white/5">
        <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} FURCARE | Appointment System</p>
    </footer>
</body>
</html>