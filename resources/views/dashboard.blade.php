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
            @if($errors->any()) toggleAddPetModal(); @endif
        });

        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }

        function showModal(id, cId) {
            const m = document.getElementById(id), c = document.getElementById(cId);
            m.classList.remove('hidden'); m.classList.add('flex');
            setTimeout(() => { m.classList.add('opacity-100'); c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10);
        }
        function closeModal(id, cId) {
            const m = document.getElementById(id), c = document.getElementById(cId);
            m.classList.remove('opacity-100'); c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0');
            setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
        }
        function toggleAddPetModal() {
            const modal = document.getElementById('add-pet-modal');
            modal.classList.contains('hidden') ? showModal('add-pet-modal','add-pet-modal-content') : closeModal('add-pet-modal','add-pet-modal-content');
        }
        function toggleProfileModal() {
            const modal = document.getElementById('profile-modal');
            modal.classList.contains('hidden') ? showModal('profile-modal','profile-modal-content') : closeModal('profile-modal','profile-modal-content');
        }
        function showAppointmentDetails(title, date, status) {
            document.getElementById('appt-title').innerText  = title;
            document.getElementById('appt-date').innerText   = date;
            document.getElementById('appt-status').innerText = status;
            showModal('appt-modal','appt-modal-content');
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
            </a>
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition-all font-medium">
                    <i class="bi bi-calendar-check mr-1 text-emerald-600"></i> My Appointments
                </a>
                <button onclick="toggleProfileModal()" class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                    <i class="bi bi-person-circle text-lg"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="px-4 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-500 border border-red-100 transition-all font-medium">Logout</button>
                </form>
            </div>
            <button onclick="toggleNav()" class="sm:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('appointments.index') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm"><i class="bi bi-calendar-check text-emerald-600"></i> My Appointments</a>
            <button onclick="toggleProfileModal(); toggleNav()" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm"><i class="bi bi-person-circle text-emerald-600"></i> Profile</button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-4 sm:px-6 py-8">

        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill text-emerald-500 shrink-0"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 flex items-center gap-3 text-sm">
                <i class="bi bi-exclamation-circle-fill shrink-0"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-400 text-sm mt-1">Here's your pet care overview.</p>
        </header>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-heart-pulse text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">My Pets</p>
                    <p class="text-lg font-bold text-gray-900">{{ $pets->count() }}</p>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-hourglass-split text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Pending</p>
                    <p class="text-lg font-bold text-gray-900">{{ $appointments->where('status','pending')->count() }}</p>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-check text-teal-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Confirmed</p>
                    <p class="text-lg font-bold text-gray-900">{{ $appointments->where('status','approved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- My Pets -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                        <i class="bi bi-houses text-emerald-600"></i> My Pets
                    </h3>
                    <button onclick="toggleAddPetModal()" class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all font-medium">
                        <i class="bi bi-plus mr-1"></i> Add
                    </button>
                </div>
                <div class="space-y-2">
                    @forelse($pets as $pet)
                        <a href="{{ route('pets.details', ['id' => $pet->id]) }}"
                           class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all group">
                            @if($pet->photo_url)
                                <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-emerald-200 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-lg shrink-0 border border-emerald-200">
                                    {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 text-sm truncate">{{ $pet->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $pet->breed }}</p>
                            </div>
                            <i class="bi bi-chevron-right text-gray-300 text-xs ml-auto group-hover:text-emerald-500 transition-all"></i>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <span class="text-3xl block mb-2">🐾</span>
                            <p class="text-gray-400 text-xs">No pets yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Appointments -->
            <div class="lg:col-span-3 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                        <i class="bi bi-calendar-event text-emerald-600"></i> Upcoming Appointments
                    </h3>
                    <a href="{{ route('request.appointment') }}" class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-all font-medium">
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
                             class="cursor-pointer flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                    <i class="bi bi-calendar2-check text-emerald-600 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 text-sm truncate">{{ $appt->pet->name }} — {{ $appt->service_label }}</p>
                                    <p class="text-xs text-gray-400">{{ $appt->appointment_date->format('M d, Y · g:i A') }}</p>
                                </div>
                            </div>
                            @php
                                $badgeClass = match($appt->status) {
                                    'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                                    'approved'  => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                    'rejected'  => 'bg-red-100 text-red-600 border border-red-200',
                                    'completed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                    'cancelled' => 'bg-gray-100 text-gray-500 border border-gray-200',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }} shrink-0 ml-2">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="bi bi-calendar-x text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-gray-400 text-sm">No upcoming appointments.</p>
                            <a href="{{ route('request.appointment') }}" class="mt-2 inline-block text-xs text-emerald-600 hover:underline">Book one now →</a>
                        </div>
                    @endforelse
                </div>
                @if($appointments->count() > 0)
                    <a href="{{ route('appointments.index') }}" class="block text-center text-xs text-gray-400 hover:text-emerald-600 mt-4 transition-all">
                        View all appointments →
                    </a>
                @endif
            </div>
        </div>
    </main>

    <!-- Add Pet Modal -->
    <div id="add-pet-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center sm:p-6 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
         onclick="if(event.target===this) toggleAddPetModal()">
        <div id="add-pet-modal-content" class="bg-white border border-gray-200 rounded-t-2xl sm:rounded-2xl p-6 sm:p-8 w-full sm:max-w-md shadow-xl transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Add New Pet</h2>
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                </div>
            @endif
            <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pet Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 transition-all text-sm" placeholder="e.g. Max">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Type</label>
                        <select name="type" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                            <option value="dog">🐶 Dog</option>
                            <option value="cat">🐱 Cat</option>
                            <option value="other">🐾 Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Breed</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" required
                               class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm" placeholder="e.g. Labrador">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Age (years)</label>
                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="100" required
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm [appearance:textfield]" placeholder="e.g. 3">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Photo <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:border-emerald-400 hover:bg-emerald-50 transition-all relative overflow-hidden">
                        <div id="upload-placeholder" class="flex flex-col items-center">
                            <i class="bi bi-cloud-upload text-xl text-emerald-500 mb-1"></i>
                            <p class="text-xs text-gray-400">Click to upload</p>
                        </div>
                        <img id="photo-preview" src="" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-lg">
                        <input type="file" id="pet-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Notes <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <textarea name="special_notes" rows="2"
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm"
                              placeholder="Allergies, temperament...">{{ old('special_notes') }}</textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="toggleAddPetModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all font-semibold text-sm">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all text-sm">Add Pet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointment Modal -->
    <div id="appt-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center sm:p-6 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
         onclick="if(event.target===this) closeModal('appt-modal','appt-modal-content')">
        <div id="appt-modal-content" class="bg-white border border-gray-200 rounded-t-2xl sm:rounded-2xl p-6 sm:p-8 w-full sm:max-w-md shadow-xl transform scale-95 opacity-0 transition-all duration-300">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Appointment Details</h2>
            <div class="space-y-3">
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Service</label>
                    <p id="appt-title" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1 text-sm"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Date & Time</label>
                    <p id="appt-date" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1 text-sm"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Status</label>
                    <p id="appt-status" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1 text-sm"></p></div>
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('appointments.index') }}" class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-center text-sm font-semibold transition-all">View All</a>
                    <button onclick="closeModal('appt-modal','appt-modal-content')" class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center sm:p-6 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
         onclick="if(event.target===this) toggleProfileModal()">
        <div id="profile-modal-content" class="bg-white border border-gray-200 rounded-t-2xl sm:rounded-2xl p-6 sm:p-8 w-full sm:max-w-md shadow-xl transform scale-95 opacity-0 transition-all duration-300">
            <h2 class="text-lg font-bold text-gray-900 mb-5">User Profile</h2>
            <div class="space-y-3">
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Name</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1 text-sm">{{ auth()->user()->name }}</p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Email</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 mt-1 text-sm">{{ auth()->user()->email }}</p></div>
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-center text-sm font-semibold transition-all">Edit Profile</a>
                    <button onclick="toggleProfileModal()" class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all text-sm">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>