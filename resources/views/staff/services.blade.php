<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Services</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
        function toggleServiceCategory(id) {
            document.getElementById('svc-panel-' + id).classList.toggle('hidden');
            document.getElementById('svc-chevron-' + id).classList.toggle('rotate-180');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-violet-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-violet-100 border border-violet-200">STAFF PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('staff.dashboard') }}"    class="hover:text-gray-900 transition-all">Dashboard</a>
                <a href="{{ route('staff.directory') }}"    class="hover:text-gray-900 transition-all">Pets</a>
                <a href="{{ route('staff.appointments') }}" class="hover:text-gray-900 transition-all">Appointments</a>
                <a href="{{ route('staff.services') }}"     class="text-gray-900 font-semibold transition-all">Services</a>
                <a href="{{ route('staff.insights') }}"     class="hover:text-gray-900 transition-all">Insights</a>
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => 'staff.'])
            <form action="{{ route('staff.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
        <a href="{{ route('staff.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-heart"></i> Pets</a>
        <a href="{{ route('staff.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i> Appointments</a>
        <a href="{{ route('staff.services') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-list-check"></i> Services</a>
        <a href="{{ route('staff.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
        <form action="{{ route('staff.logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </div>

    <main class="container mx-auto px-6 py-12 max-w-4xl">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Services</h1>
            <p class="text-gray-500 text-sm">The same services owners see when booking. Ask an admin to add or change one.</p>
        </header>

        @php
            $categoryMeta = [
                'Grooming Packages' => ['icon' => 'bi-scissors',      'bar' => 'bg-emerald-500'],
                'Add-ons'           => ['icon' => 'bi-droplet',       'bar' => 'bg-teal-500'],
                'Ala Carte'         => ['icon' => 'bi-check2-circle', 'bar' => 'bg-violet-500'],
            ];
        @endphp
        <div class="space-y-3">
            @foreach(\App\Models\Service::CATEGORIES as $i => $category)
                @php
                    $items = $bookingServices->get($category, collect());
                    $meta = $categoryMeta[$category] ?? ['icon' => 'bi-tag', 'bar' => 'bg-gray-500'];
                @endphp
                @if($items->isNotEmpty())
                    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
                        <button type="button" onclick="toggleServiceCategory('{{ $i }}')"
                                class="w-full flex items-center justify-between gap-4 px-5 py-4 {{ $meta['bar'] }} text-white text-left transition-all hover:opacity-95">
                            <span class="flex items-center gap-3">
                                <i class="bi {{ $meta['icon'] }}"></i>
                                <span class="font-bold">{{ $category }}</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $items->count() }}</span>
                            </span>
                            <i id="svc-chevron-{{ $i }}" class="bi bi-chevron-down transition-transform duration-200 {{ $i === 0 ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="svc-panel-{{ $i }}" class="{{ $i === 0 ? '' : 'hidden' }} p-4 space-y-2">
                            @foreach($items as $svc)
                                <div class="bg-gray-50/40 border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $svc->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $svc->price_range }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </main>
</body>
</html>
