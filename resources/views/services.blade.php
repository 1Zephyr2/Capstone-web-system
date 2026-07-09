@php
    $styles = App\Models\GroomingOption::where('type','style')->where('is_active',true)->orderBy('name')->get();
    $addons = App\Models\GroomingOption::where('type','addon')->where('is_active',true)->orderBy('name')->get();
    $sizes = [
        ['Small','Under 10kg','Chihuahua, Pomeranian, Shih Tzu, Maltese','Delicate handling with gentle grooming techniques.','bg-emerald-100 text-emerald-700 border-emerald-200','bi-emoji-smile'],
        ['Medium','10–25kg','Beagle, Cocker Spaniel, French Bulldog','Balanced grooming for medium breeds.','bg-teal-100 text-teal-700 border-teal-200','bi-emoji-laughing'],
        ['Large','25–45kg','Golden Retriever, Labrador, Husky','Full-service grooming including double coats.','bg-amber-100 text-amber-700 border-amber-200','bi-emoji-heart-eyes'],
        ['Extra Large','45kg+','Saint Bernard, Great Dane, Rottweiler','Specialized grooming requiring extended sessions.','bg-orange-100 text-orange-700 border-orange-200','bi-award'],
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
        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
    </script>
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen">

    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="/#about"    class="hover:text-emerald-600 transition-all">About</a>
                <a href="/#features" class="hover:text-emerald-600 transition-all">Features</a>
                <a href="{{ route('services') }}" class="text-emerald-600 font-semibold">Services</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">Login</a>
                @endauth
            </div>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="/" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm">Home</a>
            @auth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Login</a>
            @endauth
        </div>
    </nav>

    <main class="pt-24 pb-20">
        <div class="container mx-auto px-4 sm:px-6">

            <!-- Hero -->
            <div class="text-center mb-14">
                <span class="inline-block px-4 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-widest mb-4">What We Offer</span>
                <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Our Services</h1>
                <p class="text-gray-500 max-w-xl mx-auto">Professional pet care services tailored to every breed and size.</p>
            </div>

            <!-- Core Services -->
            <section class="mb-16">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Core Services</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach([
                        ['bi-scissors',    'Grooming',           'Professional coat styling and hygiene.', 'bg-emerald-100 text-emerald-600'],
                        ['bi-heart-pulse', 'Veterinary Checkup', 'Health assessments and clinical care.',  'bg-red-100 text-red-500'],
                        ['bi-shield-plus', 'Vaccination',         'Scheduled immunizations and protection.','bg-blue-100 text-blue-600'],
                        ['bi-house-heart', 'Boarding',            'Safe and comfortable overnight stays.',  'bg-violet-100 text-violet-600'],
                    ] as $svc)
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-md hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 {{ explode(' ',$svc[3])[0] }} rounded-xl flex items-center justify-center mb-4">
                            <i class="{{ $svc[0] }} {{ explode(' ',$svc[3])[1] }} text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $svc[1] }}</h3>
                        <p class="text-gray-400 text-sm">{{ $svc[2] }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Size Categories -->
            <section class="mb-16">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Service by Pet Size</h2>
                <p class="text-gray-400 text-sm mb-6">Tailored based on your pet's weight and breed characteristics.</p>
                <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach($sizes as [$label,$range,$breeds,$desc,$cls,$icon])
                        @php $parts = explode(' ',$cls); @endphp
                        <div class="border {{ $parts[2] }} {{ $parts[0] }} rounded-2xl p-5 hover:-translate-y-1 transition-all">
                            <i class="bi {{ $icon }} {{ $parts[1] }} text-xl mb-3 block"></i>
                            <div class="flex items-baseline gap-2 mb-1">
                                <h3 class="font-bold {{ $parts[1] }}">{{ $label }}</h3>
                                <span class="text-xs {{ $parts[1] }} opacity-75 font-semibold">{{ $range }}</span>
                            </div>
                            <p class="text-gray-500 text-xs mb-2 italic">{{ $breeds }}</p>
                            <p class="text-gray-600 text-sm">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Grooming Styles -->
            @if($styles->count() > 0)
            <section class="mb-16">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Grooming Styles</h2>
                <p class="text-gray-400 text-sm mb-6">Choose the perfect cut and style for your pet.</p>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($styles as $style)
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all">
                            @if($style->image)
                                <img src="{{ asset('storage/' . $style->image) }}" alt="{{ $style->name }}" class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-emerald-50 flex items-center justify-center">
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
            </section>
            @endif

            <!-- Add-ons -->
            @if($addons->count() > 0)
            <section class="mb-16">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Add-on Services</h2>
                <p class="text-gray-400 text-sm mb-6">Enhance your pet's grooming session.</p>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($addons as $addon)
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all">
                            @if($addon->image)
                                <img src="{{ asset('storage/' . $addon->image) }}" alt="{{ $addon->name }}" class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-teal-50 flex items-center justify-center">
                                    <i class="bi bi-plus-circle text-teal-400 text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1">{{ $addon->name }}</h3>
                                @if($addon->description)
                                    <p class="text-gray-400 text-sm">{{ $addon->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- CTA -->
            <section class="bg-emerald-600 rounded-3xl p-12 text-center">
                <i class="bi bi-calendar-check text-white text-4xl mb-4 block"></i>
                <h2 class="text-2xl font-bold text-white mb-3">Ready to Book?</h2>
                <p class="text-emerald-100 mb-8">Request an appointment online — we'll confirm your slot.</p>
                @auth
                    <a href="{{ route('request.appointment') }}" class="px-8 py-4 rounded-xl bg-white text-emerald-700 font-bold transition-all hover:bg-emerald-50 hover:shadow-lg">Book an Appointment</a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-white text-emerald-700 font-bold transition-all hover:bg-emerald-50 hover:shadow-lg">Get Started — It's Free</a>
                @endauth
            </section>
        </div>
    </main>

    <footer class="py-8 text-center border-t border-gray-200">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE | Pet Care Appointment System</p>
    </footer>
</body>
</html>