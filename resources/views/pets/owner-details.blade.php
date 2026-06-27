@php
    $pet = App\Models\Pet::with(['appointments' => function($q) {
        $q->orderByDesc('appointment_date');
    }])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

    $nextAppointment = $pet->appointments
        ->where('status', 'approved')
        ->where('appointment_date', '>=', now())
        ->sortBy('appointment_date')
        ->first();

    $completedAppointments = $pet->appointments
        ->where('status', 'completed')
        ->sortByDesc('appointment_date');

    // Customer Theme
    $accentText   = 'text-teal-400';
    $cardBg       = 'bg-slate-900/40';
    $cardBorder   = 'border-slate-800/80';
    $accentBorder = 'border-teal-500/50';
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $pet->name }}</title>
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

            // Photo preview for edit modal
            const photoInput = document.getElementById('edit-photo-input');
            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const preview = document.getElementById('edit-photo-preview');
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            document.getElementById('edit-upload-placeholder').classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        function toggleEditPetModal() {
            const modal   = document.getElementById('edit-pet-modal');
            const content = document.getElementById('edit-pet-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                modal.classList.remove('opacity-100');
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'opacity-100');
                }, 300);
            }
        }

        function toggleModal() {
            const modal   = document.getElementById('profile-modal');
            const content = document.getElementById('profile-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                modal.classList.remove('opacity-100');
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'opacity-100');
                }, 300);
            }
        }

        function showVisitDetails(service, date, notes, status) {
            document.getElementById('visit-service').innerText = service;
            document.getElementById('visit-date').innerText    = date;
            document.getElementById('visit-notes').innerText   = notes || '—';
            document.getElementById('visit-status').innerText  = status;
            const modal   = document.getElementById('visit-modal');
            const content = document.getElementById('visit-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeVisitModal() {
            const modal   = document.getElementById('visit-modal');
            const content = document.getElementById('visit-modal-content');
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'opacity-100');
            }, 300);
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-4">
                <button onclick="toggleModal()" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all duration-300 hover:scale-105 active:scale-95">
                    <i class="bi bi-person-circle text-xl"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all duration-300">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="flex items-center gap-6">
                @if($pet->photo_url)
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                         class="w-24 h-24 rounded-2xl object-cover border-2 border-teal-500/30 shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-teal-500/20 flex items-center justify-center text-4xl border border-teal-500/30">
                        {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                    </div>
                @endif
                <div>
                    <h1 class="text-4xl font-bold text-white">{{ $pet->name }}</h1>
                    <p class="{{ $accentText }} font-medium">{{ $pet->breed }} &bull; {{ $pet->age }} {{ Str::plural('Year', $pet->age) }} Old</p>
                    <span class="text-xs text-slate-500 uppercase tracking-widest">{{ ucfirst($pet->type) }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                    ← Back
                </a>
                <button onclick="toggleEditPetModal()" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold transition-all hover:scale-105">
                    <i class="bi bi-pencil mr-1"></i> Edit Pet
                </button>
            </div>
        </header>

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- Appointment History -->
            <div class="lg:col-span-2 space-y-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                        <i class="bi bi-clock-history {{ $accentText }}"></i> Appointment History
                    </h3>
                    <div class="space-y-3">
                        @forelse($completedAppointments as $appt)
                            <button onclick="showVisitDetails(
                                        '{{ addslashes($appt->service_label) }}',
                                        '{{ $appt->appointment_date->format('F d, Y — g:i A') }}',
                                        '{{ addslashes($appt->notes ?? '') }}',
                                        '{{ ucfirst($appt->status) }}'
                                     )"
                                    class="w-full text-left p-4 border-l-2 {{ $accentBorder }} {{ $cardBg }} rounded-r-lg hover:bg-slate-800 transition-all duration-300 hover:scale-[1.01] hover:shadow-xl cursor-pointer">
                                <p class="font-medium text-white">{{ $appt->service_label }}</p>
                                <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                            </button>
                        @empty
                            <div class="text-center py-8">
                                <i class="bi bi-calendar-x text-3xl text-slate-700 mb-2 block"></i>
                                <p class="text-slate-500 text-sm italic">No completed appointments yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending/Approved appointments -->
                @php
                    $activeAppts = $pet->appointments->whereIn('status', ['pending','approved'])->sortBy('appointment_date');
                @endphp
                @if($activeAppts->count() > 0)
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-8">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                        <i class="bi bi-calendar-event {{ $accentText }}"></i> Upcoming & Pending
                    </h3>
                    <div class="space-y-3">
                        @foreach($activeAppts as $appt)
                            <div class="flex items-center justify-between p-4 border-l-2 border-amber-500/50 bg-amber-500/5 rounded-r-lg">
                                <div>
                                    <p class="font-medium text-white">{{ $appt->service_label }}</p>
                                    <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-300">

                <!-- About -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-info-circle {{ $accentText }}"></i> About {{ $pet->name }}
                    </h4>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        {{ $pet->special_notes ?: 'No special notes added.' }}
                    </p>
                </div>

                <!-- Next Appointment -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Next Appointment
                    </h4>
                    @if($nextAppointment)
                        <p class="text-lg font-semibold text-white">{{ $nextAppointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-sm text-slate-400">{{ $nextAppointment->appointment_date->format('g:i A') }} &bull; {{ $nextAppointment->service_label }}</p>
                    @else
                        <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                        <a href="{{ route('request.appointment') }}" class="mt-3 inline-block text-xs text-teal-400 hover:text-teal-300 transition-all">
                            Book one now →
                        </a>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6">
                    <h4 class="font-bold text-white mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="{{ route('request.appointment') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-teal-600/20 hover:bg-teal-600/40 border border-teal-500/20 text-teal-300 text-sm transition-all hover:translate-x-1">
                            <i class="bi bi-calendar-plus"></i> Book Appointment
                        </a>
                        <!-- Delete pet -->
                        <form method="POST" action="{{ route('pets.destroy', $pet) }}"
                              onsubmit="return confirm('Are you sure you want to remove {{ $pet->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 text-sm transition-all hover:translate-x-1">
                                <i class="bi bi-trash"></i> Remove Pet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Pet Modal -->
    <div id="edit-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleEditPetModal()">
        <div id="edit-pet-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-lg shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-6">Edit {{ $pet->name }}</h2>
            <form action="{{ route('pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Name</label>
                        <input type="text" name="name" value="{{ $pet->name }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Type</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            <option value="dog"   {{ $pet->type === 'dog'   ? 'selected' : '' }}>🐶 Dog</option>
                            <option value="cat"   {{ $pet->type === 'cat'   ? 'selected' : '' }}>🐱 Cat</option>
                            <option value="other" {{ $pet->type === 'other' ? 'selected' : '' }}>🐾 Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Breed</label>
                        <input type="text" name="breed" value="{{ $pet->breed }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Age</label>
                        <input type="number" name="age" value="{{ $pet->age }}" min="0" max="100" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Profile Picture</label>
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-teal-500 transition-all relative overflow-hidden">
                        <div id="edit-upload-placeholder" class="flex flex-col items-center justify-center {{ $pet->photo_url ? 'hidden' : '' }}">
                            <i class="bi bi-cloud-upload text-2xl text-teal-500 mb-1"></i>
                            <p class="text-xs text-slate-400"><span class="font-semibold">Click to update photo</span></p>
                        </div>
                        <img id="edit-photo-preview" src="{{ $pet->photo_url ?? '' }}" alt="{{ $pet->name }}"
     class="{{ $pet->photo_url ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-xl">
                        <input type="file" id="edit-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Special Notes</label>
                    <textarea name="special_notes" rows="3"
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm">{{ $pet->special_notes }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleEditPetModal()"
                            class="flex-1 px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold transition-all">Cancel</button>
                    <button type="submit"
                            class="flex-1 px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Visit Detail Modal -->
    <div id="visit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeVisitModal()">
        <div id="visit-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Service</label>
                    <p id="visit-service" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Date & Time</label>
                    <p id="visit-date" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                    <p id="visit-status" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Notes</label>
                    <p id="visit-notes" class="text-slate-300 bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1 text-sm"></p>
                </div>
                <button type="button" onclick="closeVisitModal()"
                        class="w-full mt-4 px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal()">
        <div id="profile-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">User Profile</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Name</label>
                    <p class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Email</label>
                    <p class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1">{{ auth()->user()->email }}</p>
                </div>
                <button type="button" onclick="toggleModal()"
                        class="w-full mt-4 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>

</body>
</html>