<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Dashboard</title>
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

            // Preview uploaded photo
            const photoInput = document.getElementById('pet-photo-input');
            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const preview = document.getElementById('photo-preview');
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                            document.getElementById('upload-placeholder').classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Open add-pet modal if validation failed (errors exist)
            @if($errors->any())
                toggleAddPetModal();
            @endif
        });

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

        function toggleAddPetModal() {
            const modal   = document.getElementById('add-pet-modal');
            const content = document.getElementById('add-pet-modal-content');
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

        function showAppointmentDetails(title, date, status) {
            document.getElementById('appointment-title').innerText = title;
            document.getElementById('appointment-date').innerText  = date;
            document.getElementById('appointment-status').innerText = status;
            const modal   = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAppointmentModal() {
            const modal   = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'opacity-100');
            }, 300);
        }

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                    el.classList.remove('opacity-100', 'translate-y-0');
                    el.classList.add('opacity-0', 'translate-y-10');
                });
                setTimeout(() => {
                    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                        el.classList.add('opacity-100', 'translate-y-0');
                        el.classList.remove('opacity-0', 'translate-y-10');
                    });
                }, 50);
            }
        });
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
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all">
                    <i class="bi bi-calendar-check mr-1"></i> My Appointments
                </a>
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

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <i class="bi bi-check-circle-fill text-lg"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill text-lg"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-12 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-3xl font-bold text-white">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-400 text-sm">Here is the status of your pets and upcoming appointments.</p>
        </header>

        <div class="grid md:grid-cols-2 gap-8">

            <!-- Upcoming Appointments -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(13,148,136,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-white flex items-center gap-2">
                        <i class="bi bi-calendar-check text-teal-400"></i> Upcoming Appointments
                    </h3>
                    <a href="{{ route('request.appointment') }}" class="text-xs px-3 py-1 bg-teal-600 hover:bg-teal-500 rounded-lg text-white font-medium transition-all">
                        + Request
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($appointments as $appt)
                        <div onclick="showAppointmentDetails(
                                '{{ addslashes($appt->pet->name) }} — {{ addslashes($appt->service_label) }}',
                                '{{ $appt->appointment_date->format('F d, Y — g:i A') }}',
                                '{{ ucfirst($appt->status) }}'
                             )"
                             class="cursor-pointer flex items-center justify-between bg-slate-900/50 p-4 rounded-xl border border-slate-800/50 hover:border-teal-500/30 transition-all duration-300 hover:translate-x-1 hover:shadow-lg">
                            <div>
                                <p class="font-medium text-white">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="bi bi-calendar-x text-3xl text-slate-700 mb-2 block"></i>
                            <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                            <a href="{{ route('request.appointment') }}" class="mt-3 inline-block text-xs text-teal-400 hover:text-teal-300 transition-all">Book one now →</a>
                        </div>
                    @endforelse
                </div>

                @if($appointments->count() > 0)
                    <a href="{{ route('appointments.index') }}" class="block text-center text-xs text-slate-500 hover:text-teal-400 mt-4 transition-all">
                        View all appointments →
                    </a>
                @endif
            </div>

            <!-- My Pets -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(13,148,136,0.1)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                    <i class="bi bi-paw text-teal-400"></i> My Pets
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    @forelse($pets as $pet)
                        <a href="{{ route('pets.details', ['id' => $pet->id]) }}"
                           class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/50 text-center hover:border-teal-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg block">
                            @if($pet->photo_url)
                                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                                     class="w-14 h-14 rounded-full object-cover mx-auto mb-2 border-2 border-teal-500/30">
                            @else
                                <span class="text-3xl mb-2 block">
                                    {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                                </span>
                            @endif
                            <p class="font-medium text-white text-sm">{{ $pet->name }}</p>
                            <p class="text-xs text-slate-400">{{ $pet->breed }}</p>
                        </a>
                    @empty
                        <div class="col-span-2 text-center py-4">
                            <p class="text-slate-500 text-sm italic">No pets added yet.</p>
                        </div>
                    @endforelse
                </div>

                <button onclick="toggleAddPetModal()"
                        class="w-full mt-6 py-3 rounded-xl border border-dashed border-slate-700 text-slate-400 hover:text-white hover:border-teal-500 transition-all duration-300 text-sm">
                    <i class="bi bi-plus-circle mr-1"></i> Add Pet
                </button>
            </div>
        </div>
    </main>

    <!-- ── Add Pet Modal ──────────────────────────────────────────────── -->
    <div id="add-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleAddPetModal()">
        <div id="add-pet-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-6">Add New Pet</h2>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- FIXED: correct action, POST method, enctype for file upload --}}
            <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300"
                           placeholder="e.g. Max">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet Type</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300 appearance-none">
                            <option value="dog"   {{ old('type') === 'dog'   ? 'selected' : '' }}>🐶 Dog</option>
                            <option value="cat"   {{ old('type') === 'cat'   ? 'selected' : '' }}>🐱 Cat</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>🐾 Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Breed</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300"
                               placeholder="e.g. Labrador">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Age (years)</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="100" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                           placeholder="e.g. 3">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Profile Picture <span class="normal-case font-normal text-slate-600">(optional)</span></label>
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-slate-800 border-dashed rounded-lg cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-teal-500 transition-all duration-300 relative overflow-hidden">
                        <div id="upload-placeholder" class="flex flex-col items-center justify-center">
                            <i class="bi bi-cloud-upload text-2xl text-teal-500 mb-1"></i>
                            <p class="text-xs text-slate-400"><span class="font-semibold">Click to upload</span> &bull; JPG, PNG, WEBP up to 2MB</p>
                        </div>
                        <img id="photo-preview" src="" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        {{-- FIXED: name="photo" added --}}
                        <input type="file" id="pet-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Special Notes <span class="normal-case font-normal text-slate-600">(optional)</span></label>
                    <textarea name="special_notes" rows="2"
                              class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 hover:border-slate-600 transition-all duration-300 resize-none text-sm"
                              placeholder="Allergies, temperament, special needs...">{{ old('special_notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleAddPetModal()"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300 font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all duration-300 hover:scale-[1.02]">
                        <i class="bi bi-plus-circle mr-1"></i> Add Pet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Appointment Details Modal ─────────────────────────────────── -->
    <div id="appointment-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeAppointmentModal()">
        <div id="appointment-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Service</label>
                    <p id="appointment-title" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Date & Time</label>
                    <p id="appointment-date" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                    <p id="appointment-status" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800 mt-1"></p>
                </div>
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('appointments.index') }}" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-center text-sm font-semibold transition-all">View All</a>
                    <button type="button" onclick="closeAppointmentModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Profile Modal ──────────────────────────────────────────────── -->
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
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-center text-sm font-semibold transition-all">Edit Profile</a>
                    <button type="button" onclick="toggleModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all duration-300">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>