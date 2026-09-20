@php
    $pet = App\Models\Pet::with([
        'appointments' => fn($q) => $q->orderByDesc('appointment_date'),
        'records'      => fn($q) => $q->with('recorder')->orderByDesc('record_date'),
    ])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

    $nextAppointment       = $pet->appointments->where('status','approved')->where('appointment_date','>=',now())->sortBy('appointment_date')->first();
    $completedAppointments = $pet->appointments->where('status','completed')->sortByDesc('appointment_date');
    $activeAppts           = $pet->appointments->whereIn('status',['pending','approved'])->sortBy('appointment_date');
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | {{ $pet->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        // ── Breed lists, shared by the Add/Edit pet forms ──────────────
        const BREED_LISTS = {
            dog: ["Aspin (Askal)", "Beagle", "Chihuahua", "Chow Chow", "Cocker Spaniel", "Dachshund", "Dalmatian", "French Bulldog", "German Shepherd", "Golden Retriever", "Great Dane", "Labrador Retriever", "Maltese", "Mixed Breed", "Pekingese", "Pomeranian", "Poodle", "Pug", "Rottweiler", "Shiba Inu", "Shih Tzu", "Siberian Husky", "Yorkshire Terrier"],
            cat: ["American Shorthair", "Bengal", "British Shorthair", "Domestic Longhair", "Domestic Shorthair (Puspin)", "Himalayan", "Maine Coon", "Munchkin", "Persian", "Ragdoll", "Russian Blue", "Scottish Fold", "Siamese", "Sphynx"],
            other: []
        };

        function populateBreedOptions(prefix, type, currentBreed) {
            const select = document.getElementById(prefix + '-breed-select');
            const otherInput = document.getElementById(prefix + '-breed-other');
            const hidden = document.getElementById(prefix + '-breed-hidden');
            const list = BREED_LISTS[type] || [];
            select.innerHTML = '';
            list.forEach(breed => {
                const opt = document.createElement('option');
                opt.value = breed; opt.textContent = breed;
                select.appendChild(opt);
            });
            const otherOpt = document.createElement('option');
            otherOpt.value = '__other__'; otherOpt.textContent = 'Other (please specify)';
            select.appendChild(otherOpt);

            if (currentBreed && list.includes(currentBreed)) {
                select.value = currentBreed;
                otherInput.classList.add('hidden');
                if (hidden) hidden.value = currentBreed;
            } else if (currentBreed) {
                select.value = '__other__';
                otherInput.classList.remove('hidden');
                otherInput.value = currentBreed;
                if (hidden) hidden.value = currentBreed;
            } else {
                otherInput.classList.add('hidden');
                otherInput.value = '';
            }
        }

        function onBreedSelectChange(prefix) {
            const select = document.getElementById(prefix + '-breed-select');
            const otherInput = document.getElementById(prefix + '-breed-other');
            const hidden = document.getElementById(prefix + '-breed-hidden');
            if (select.value === '__other__') {
                otherInput.classList.remove('hidden');
                otherInput.focus();
                if (hidden) hidden.value = otherInput.value;
            } else {
                otherInput.classList.add('hidden');
                if (hidden) hidden.value = select.value;
            }
        }

        function onBreedOtherInput(prefix) {
            const hidden = document.getElementById(prefix + '-breed-hidden');
            const otherInput = document.getElementById(prefix + '-breed-other');
            if (hidden) hidden.value = otherInput.value;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            switchTab('appointments');
            populateBreedOptions('edit', @json($pet->type), @json($pet->breed));

            @if(request('edit'))
                showModal('edit-pet-modal', 'edit-pet-modal-content');
            @endif

            document.getElementById('edit-photo-input')?.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('edit-photo-preview').src = e.target.result;
                    document.getElementById('edit-photo-preview').classList.remove('hidden');
                    document.getElementById('edit-upload-placeholder').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });
        });

        function switchTab(tab) {
            ['appointments','records'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('btn-' + t).classList.remove('border-emerald-500','text-emerald-700','bg-emerald-50');
                document.getElementById('btn-' + t).classList.add('border-transparent','text-gray-500');
            });
            document.getElementById('tab-' + tab).classList.remove('hidden');
            document.getElementById('btn-' + tab).classList.add('border-emerald-500','text-emerald-700','bg-emerald-50');
            document.getElementById('btn-' + tab).classList.remove('border-transparent','text-gray-500');
        }

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
        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
            </a>
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition-all">← Dashboard</a>
                @include('components.notification-bell', ['notifRoutePrefix' => ''])
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 transition-all">Logout</button>
                </form>
            </div>
            <button onclick="toggleNav()" class="sm:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-4 sm:px-6 py-8">

        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill shrink-0"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <header class="mb-8 flex items-start justify-between gap-4">
            <div class="flex items-center gap-5">
                @if($pet->photo_url)
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-200 shadow-sm shrink-0">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-emerald-100 flex items-center justify-center text-3xl border-2 border-emerald-200 shrink-0">
                        {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $pet->name }}</h1>
                    <p class="text-emerald-600 font-medium text-sm">{{ $pet->breed }} · {{ $pet->age }} {{ Str::plural('yr', $pet->age) }} · {{ ucfirst($pet->type) }}</p>
                    @if($pet->special_notes)
                        <p class="text-gray-400 text-xs mt-1 max-w-sm truncate">{{ $pet->special_notes }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="showModal('edit-pet-modal','edit-pet-modal-content')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-all">
                    <i class="bi bi-pencil mr-1"></i> Edit
                </button>
            </div>
        </header>

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Main: Tabs -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Tab buttons -->
                <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                    <button id="btn-appointments" onclick="switchTab('appointments')"
                            class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold border-b-2 transition-all">
                        <i class="bi bi-calendar-check mr-1"></i> Appointments
                    </button>
                    <button id="btn-records" onclick="switchTab('records')"
                            class="flex-1 py-2 px-4 rounded-lg text-sm font-semibold border-b-2 transition-all">
                        <i class="bi bi-clipboard2-pulse mr-1"></i> Medical Records
                    </button>
                </div>

                <!-- Appointments Tab -->
                <div id="tab-appointments">
                    @if($activeAppts->count() > 0)
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 mb-4 shadow-sm">
                        <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                            <i class="bi bi-calendar-event text-emerald-600"></i> Upcoming & Pending
                        </h3>
                        <div class="space-y-2">
                            @foreach($activeAppts as $appt)
                                @php $badgeClass = match($appt->status) { 'pending' => 'bg-amber-100 text-amber-700 border border-amber-200', 'approved' => 'bg-emerald-100 text-emerald-700 border border-emerald-200', default => 'bg-gray-100 text-gray-500' }; @endphp
                                <div class="flex items-center justify-between p-3 border-l-4 border-l-amber-400 bg-amber-50 rounded-r-xl">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $appt->service_label }}</p>
                                        <p class="text-xs text-gray-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ ucfirst($appt->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- History table -->
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <i class="bi bi-clock-history text-emerald-600"></i> Appointment History
                            </h3>
                            <span class="text-xs text-gray-400">{{ $completedAppointments->count() }} completed</span>
                        </div>
                        @if($completedAppointments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr class="text-xs uppercase text-gray-400 tracking-wider">
                                        <th class="px-5 py-3 text-left">Date</th>
                                        <th class="px-5 py-3 text-left">Service</th>
                                        <th class="px-5 py-3 text-left">Notes</th>
                                        <th class="px-5 py-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($completedAppointments as $appt)
                                        <tr class="hover:bg-gray-50 transition-all">
                                            <td class="px-5 py-3 text-gray-600 whitespace-nowrap text-xs">{{ $appt->appointment_date->format('M d, Y') }}</td>
                                            <td class="px-5 py-3 text-gray-900 font-medium text-sm">{{ $appt->service_label }}</td>
                                            <td class="px-5 py-3 text-gray-400 text-xs max-w-[160px] truncate">{{ $appt->notes ?: '—' }}</td>
                                            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">Completed</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-calendar-x text-3xl text-gray-300 mb-2 block"></i>
                                <p class="text-gray-400 text-sm">No completed appointments yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Medical Records Tab -->
                <div id="tab-records" class="hidden">
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <i class="bi bi-clipboard2-pulse text-emerald-600"></i> Medical Records
                            </h3>
                            <span class="text-xs text-gray-400">{{ $pet->records->count() }} {{ Str::plural('record', $pet->records->count()) }}</span>
                        </div>
                        @if($pet->records->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr class="text-xs uppercase text-gray-400 tracking-wider">
                                        <th class="px-5 py-3 text-left">Date</th>
                                        <th class="px-5 py-3 text-left">Diagnosis</th>
                                        <th class="px-5 py-3 text-left">Medications</th>
                                        <th class="px-5 py-3 text-left">Vet Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($pet->records as $record)
                                        <tr class="hover:bg-gray-50 transition-all">
                                            <td class="px-5 py-3 text-gray-600 whitespace-nowrap text-xs">{{ $record->record_date->format('M d, Y') }}</td>
                                            <td class="px-5 py-3 text-gray-900 font-medium text-sm">{{ $record->diagnosis }}</td>
                                            <td class="px-5 py-3 text-gray-400 text-xs max-w-[150px] truncate">{{ $record->medications ?: '—' }}</td>
                                            <td class="px-5 py-3 text-gray-400 text-xs max-w-[150px] truncate">{{ $record->vet_notes ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-clipboard2 text-3xl text-gray-300 mb-2 block"></i>
                                <p class="text-gray-400 text-sm">No medical records yet.</p>
                                <p class="text-gray-300 text-xs mt-1">Records are added by clinic staff.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-calendar-check text-emerald-600"></i> Next Appointment
                    </h4>
                    @if($nextAppointment)
                        <p class="font-semibold text-gray-900">{{ $nextAppointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-gray-400 text-xs mt-0.5">{{ $nextAppointment->appointment_date->format('g:i A') }} · {{ $nextAppointment->service_label }}</p>
                    @else
                        <p class="text-gray-400 text-sm">No upcoming appointments.</p>
                        <a href="{{ route('request.appointment') }}" class="mt-2 inline-block text-xs text-emerald-600 hover:underline">Book one now →</a>
                    @endif
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle text-emerald-600"></i> About {{ $pet->name }}
                    </h4>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $pet->special_notes ?: 'No special notes added.' }}</p>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="font-bold text-gray-900 text-sm mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="{{ route('request.appointment') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-sm transition-all">
                            <i class="bi bi-calendar-plus"></i> Book Appointment
                        </a>
                        <form method="POST" action="{{ route('pets.destroy', $pet) }}" onsubmit="return confirm('Remove {{ $pet->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 text-sm transition-all">
                                <i class="bi bi-trash"></i> Remove Pet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Pet Modal -->
    <div id="edit-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
         onclick="if(event.target===this) closeModal('edit-pet-modal','edit-pet-modal-content')">
        <div id="edit-pet-modal-content" class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 w-full max-w-lg shadow-xl transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-bold text-gray-900 mb-5">Edit {{ $pet->name }}</h2>
            <form action="{{ route('pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pet Name</label>
                        <input type="text" name="name" value="{{ $pet->name }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pet Type</label>
                        <select name="type" id="edit-pet-type" onchange="populateBreedOptions('edit', this.value, '')" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                            <option value="dog"   {{ $pet->type==='dog'  ?'selected':'' }}>🐶 Dog</option>
                            <option value="cat"   {{ $pet->type==='cat'  ?'selected':'' }}>🐱 Cat</option>
                            <option value="other" {{ $pet->type==='other'?'selected':'' }}>🐾 Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Breed</label>
                        <select id="edit-breed-select" onchange="onBreedSelectChange('edit')" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm"></select>
                        <input type="text" id="edit-breed-other" oninput="onBreedOtherInput('edit')" placeholder="Enter breed" class="hidden mt-2 w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                        <input type="hidden" name="breed" id="edit-breed-hidden" value="{{ $pet->breed }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Age</label>
                        <input type="number" name="age" value="{{ $pet->age }}" min="0" max="100" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm [appearance:textfield]">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Profile Picture</label>
                    <label class="relative flex items-center justify-center w-full h-28 border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden {{ $pet->photo_url ? 'border-emerald-300' : 'border-gray-200 hover:border-emerald-400' }} bg-gray-50 hover:bg-emerald-50">
                        <div id="edit-upload-placeholder" class="flex flex-col items-center gap-1 {{ $pet->photo_url ? 'hidden' : '' }}">
                            <i class="bi bi-cloud-upload text-2xl text-emerald-500"></i>
                            <p class="text-xs text-gray-400">Click to upload or replace photo</p>
                        </div>
                        @if($pet->photo_url)
                            <img id="edit-photo-preview" src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="absolute inset-0 w-full h-full object-cover rounded-xl">
                            <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-all rounded-xl">
                                <p class="text-xs text-white font-semibold"><i class="bi bi-pencil mr-1"></i>Change</p>
                            </div>
                        @else
                            <img id="edit-photo-preview" src="" alt="{{ $pet->name }}" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                        @endif
                        <input type="file" id="edit-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Size</label>
                    <select name="size" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                        @foreach(\App\Models\Pet::SIZE_LABELS as $key => $label)
                            <option value="{{ $key }}" {{ $pet->size === $key ? 'selected' : '' }}>{{ $key }} — {{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-gray-400 text-xs mt-1">Used to calculate grooming prices for this pet.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Special Notes</label>
                    <textarea name="special_notes" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm">{{ $pet->special_notes }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Vaccination Record</label>
                    <textarea name="vaccination_record" rows="2" required placeholder="e.g. Rabies - June 2026, 5-in-1 - March 2026" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm">{{ $pet->vaccination_record }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Existing Medical Conditions <span class="normal-case font-normal text-gray-300">(optional)</span></label>
                    <textarea name="medical_conditions" rows="2" placeholder="e.g. Skin allergies, joint issues, none" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm">{{ $pet->medical_conditions }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Grooming Triggers / Trauma <span class="normal-case font-normal text-gray-300">(optional)</span></label>
                    <textarea name="grooming_triggers" rows="2" placeholder="e.g. Scared of dryers, sensitive paws, had a bad experience with nail trims" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm">{{ $pet->grooming_triggers }}</textarea>
                </div>
                @if($errors->any())
                    <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('edit-pet-modal','edit-pet-modal-content')" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold transition-all text-sm">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all text-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>