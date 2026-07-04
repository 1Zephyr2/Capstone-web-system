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

            const photoInput = document.getElementById('pet-photo-input');
            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            document.getElementById('photo-preview').src = e.target.result;
                            document.getElementById('photo-preview').classList.remove('hidden');
                            document.getElementById('upload-placeholder').classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            @if($errors->any())
                toggleAddPetModal();
            @endif
        });

        function toggleModal() {
            const modal = document.getElementById('profile-modal');
            const content = document.getElementById('profile-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex','opacity-100'); }, 300);
            }
        }

        function toggleAddPetModal() {
            const modal = document.getElementById('add-pet-modal');
            const content = document.getElementById('add-pet-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex','opacity-100'); }, 300);
            }
        }

        function showAppointmentDetails(title, date, status) {
            document.getElementById('appointment-title').innerText = title;
            document.getElementById('appointment-date').innerText  = date;
            document.getElementById('appointment-status').innerText = status;
            const modal = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');
            modal.classList.remove('hidden'); modal.classList.add('flex');
            setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
        }

        function closeAppointmentModal() {
            const modal = document.getElementById('appointment-modal');
            const content = document.getElementById('appointment-modal-content');
            modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex','opacity-100'); }, 300);
        }

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                    el.classList.remove('opacity-100','translate-y-0');
                    el.classList.add('opacity-0','translate-y-10');
                });
                setTimeout(() => {
                    document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                        el.classList.add('opacity-100','translate-y-0');
                        el.classList.remove('opacity-0','translate-y-10');
                    });
                }, 50);
            }
        });
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all">
                    <i class="bi bi-calendar-check mr-1"></i> My Appointments
                </a>
                <button onclick="toggleModal()" class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all hover:scale-105">
                    <i class="bi bi-person-circle text-lg"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-10">

        @if(session('success'))
            <div class="mb-5 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 px-5 py-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3 text-sm">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Welcome -->
        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <h1 class="text-2xl font-bold text-white">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-500 text-sm mt-1">Here's your pet care overview.</p>
        </header>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-3 gap-4 mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-teal-500/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-heart-pulse text-teal-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">My Pets</p>
                    <p class="text-lg font-bold text-white">{{ $pets->count() }}</p>
                </div>
            </div>
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-hourglass-split text-amber-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Pending</p>
                    <p class="text-lg font-bold text-white">{{ $appointments->where('status','pending')->count() }}</p>
                </div>
            </div>
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-xl p-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-check text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Confirmed</p>
                    <p class="text-lg font-bold text-white">{{ $appointments->where('status','approved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-5 gap-6">

            <!-- My Pets — compact list -->
            <div class="lg:col-span-2 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-white flex items-center gap-2 text-sm">
                        <i class="bi bi-houses text-teal-400"></i> My Pets
                    </h3>
                    <button onclick="toggleAddPetModal()" class="text-xs px-3 py-1.5 bg-teal-600/20 hover:bg-teal-600/40 border border-teal-500/30 text-teal-300 rounded-lg transition-all">
                        <i class="bi bi-plus mr-1"></i>Add
                    </button>
                </div>

                <div class="space-y-2">
                    @forelse($pets as $pet)
                        <a href="{{ route('pets.details', ['id' => $pet->id]) }}"
                           class="flex items-center gap-3 p-3 rounded-xl bg-slate-950/50 border border-slate-800/50 hover:border-teal-500/30 transition-all hover:translate-x-1 group">
                            @if($pet->photo_url)
                                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}"
                                     class="w-10 h-10 rounded-full object-cover border border-teal-500/30 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-teal-500/15 flex items-center justify-center text-lg shrink-0 border border-teal-500/20">
                                    {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-white text-sm truncate">{{ $pet->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $pet->breed }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-slate-600 text-xs ml-auto group-hover:text-teal-400 transition-all"></i>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <span class="text-3xl block mb-2">🐾</span>
                            <p class="text-slate-500 text-xs">No pets yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="lg:col-span-3 bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-white flex items-center gap-2 text-sm">
                        <i class="bi bi-calendar-event text-teal-400"></i> Upcoming Appointments
                    </h3>
                    <a href="{{ route('request.appointment') }}" class="text-xs px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white rounded-lg transition-all font-medium">
                        + Request
                    </a>
                </div>

                <div class="space-y-2">
                    @forelse($appointments as $appt)
                        <div onclick="showAppointmentDetails(
                                '{{ addslashes($appt->pet->name) }} — {{ addslashes($appt->service_label) }}',
                                '{{ $appt->appointment_date->format('M d, Y — g:i A') }}',
                                '{{ ucfirst($appt->status) }}'
                             )"
                             class="cursor-pointer flex items-center justify-between p-3 rounded-xl bg-slate-950/50 border border-slate-800/50 hover:border-teal-500/30 transition-all hover:translate-x-1">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-teal-500/15 flex items-center justify-center shrink-0">
                                    <i class="bi bi-calendar2-check text-teal-400 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-white text-sm truncate">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                    <p class="text-xs text-slate-500">{{ $appt->appointment_date->format('M d, Y · g:i A') }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }} shrink-0 ml-2">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="bi bi-calendar-x text-3xl text-slate-700 mb-2 block"></i>
                            <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                            <a href="{{ route('request.appointment') }}" class="mt-2 inline-block text-xs text-teal-400 hover:text-teal-300">Book one now →</a>
                        </div>
                    @endforelse
                </div>

                @if($appointments->count() > 0)
                    <a href="{{ route('appointments.index') }}" class="block text-center text-xs text-slate-600 hover:text-teal-400 mt-4 transition-all">
                        View all appointments →
                    </a>
                @endif
            </div>
        </div>
    </main>

    <!-- Add Pet Modal -->
    <div id="add-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleAddPetModal()">
        <div id="add-pet-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-6">Add New Pet</h2>
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                </div>
            @endif
            <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 transition-all" placeholder="e.g. Max">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet Type</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            <option value="dog"   {{ old('type')==='dog'  ?'selected':'' }}>🐶 Dog</option>
                            <option value="cat"   {{ old('type')==='cat'  ?'selected':'' }}>🐱 Cat</option>
                            <option value="other" {{ old('type')==='other'?'selected':'' }}>🐾 Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Breed</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" required
                               class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 transition-all" placeholder="e.g. Labrador">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Age (years)</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="100" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="e.g. 3">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Profile Picture <span class="normal-case font-normal text-slate-600">(optional)</span></label>
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-slate-800 border-dashed rounded-lg cursor-pointer bg-slate-950 hover:border-teal-500 transition-all relative overflow-hidden">
                        <div id="upload-placeholder" class="flex flex-col items-center justify-center">
                            <i class="bi bi-cloud-upload text-xl text-teal-500 mb-1"></i>
                            <p class="text-xs text-slate-400">Click to upload · JPG, PNG up to 2MB</p>
                        </div>
                        <img id="photo-preview" src="" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        <input type="file" id="pet-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Special Notes <span class="normal-case font-normal text-slate-600">(optional)</span></label>
                    <textarea name="special_notes" rows="2"
                              class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm"
                              placeholder="Allergies, temperament...">{{ old('special_notes') }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleAddPetModal()" class="flex-1 px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all font-semibold">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02]">
                        <i class="bi bi-plus-circle mr-1"></i> Add Pet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointment Details Modal -->
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
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('appointments.index') }}" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-center text-sm font-semibold transition-all">View All</a>
                    <button type="button" onclick="closeAppointmentModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
                </div>
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
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-center text-sm font-semibold transition-all">Edit Profile</a>
                    <button type="button" onclick="toggleModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>