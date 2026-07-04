@php
    $pet = App\Models\Pet::with([
        'appointments' => fn($q) => $q->orderByDesc('appointment_date'),
        'records'      => fn($q) => $q->with('recorder')->orderByDesc('record_date'),
    ])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

    $nextAppointment       = $pet->appointments->where('status','approved')->where('appointment_date','>=',now())->sortBy('appointment_date')->first();
    $completedAppointments = $pet->appointments->where('status','completed')->sortByDesc('appointment_date');
    $activeAppts           = $pet->appointments->whereIn('status',['pending','approved'])->sortBy('appointment_date');
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
            const obs = new IntersectionObserver(entries => entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('opacity-100','translate-y-0'); e.target.classList.remove('opacity-0','translate-y-10'); }
            }), { threshold: 0.1 });
            document.querySelectorAll('.reveal-on-scroll').forEach(el => obs.observe(el));

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

            // Default tab
            switchTab('appointments');
        });

        function switchTab(tab) {
            ['appointments','records'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('btn-' + t).classList.remove('border-teal-500','text-white');
                document.getElementById('btn-' + t).classList.add('border-transparent','text-slate-400');
            });
            document.getElementById('tab-' + tab).classList.remove('hidden');
            document.getElementById('btn-' + tab).classList.add('border-teal-500','text-white');
            document.getElementById('btn-' + tab).classList.remove('border-transparent','text-slate-400');
        }

        function toggleModal(id, contentId) {
            const modal = document.getElementById(id);
            const content = document.getElementById(contentId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-3">
                <button onclick="toggleModal('profile-modal','profile-modal-content')" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 hover:text-white transition-all">
                    <i class="bi bi-person-circle text-lg"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="px-4 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-10">

        @if(session('success'))
            <div class="mb-5 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <header class="mb-8 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="flex items-center gap-5">
                @if($pet->photo_url)
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-teal-500/30 shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-teal-500/20 flex items-center justify-center text-3xl border border-teal-500/30">
                        {{ $pet->type === 'cat' ? '🐱' : ($pet->type === 'dog' ? '🐶' : '🐾') }}
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $pet->name }}</h1>
                    <p class="{{ $accentText }} text-sm font-medium">{{ $pet->breed }} · {{ $pet->age }} {{ Str::plural('yr', $pet->age) }} · {{ ucfirst($pet->type) }}</p>
                    @if($pet->special_notes)
                        <p class="text-slate-500 text-xs mt-1 max-w-sm truncate">{{ $pet->special_notes }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">← Back</a>
                <button onclick="toggleModal('edit-pet-modal','edit-pet-modal-content')" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold transition-all">
                    <i class="bi bi-pencil mr-1"></i> Edit
                </button>
            </div>
        </header>

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Main content with tabs -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Tab Buttons -->
                <div class="flex gap-1 bg-slate-900/60 border border-slate-800/80 rounded-xl p-1">
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
                    <!-- Active appointments -->
                    @if($activeAppts->count() > 0)
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-6 mb-4">
                        <h3 class="font-bold text-white text-sm mb-4 flex items-center gap-2">
                            <i class="bi bi-calendar-event {{ $accentText }}"></i> Upcoming & Pending
                        </h3>
                        <div class="space-y-2">
                            @foreach($activeAppts as $appt)
                                <div class="flex items-center justify-between p-3 border-l-2 border-amber-500/50 bg-amber-500/5 rounded-r-lg">
                                    <div>
                                        <p class="font-medium text-white text-sm">{{ $appt->service_label }}</p>
                                        <p class="text-xs text-slate-400">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Completed appointments — TABLE format (item 9) -->
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
                            <h3 class="font-bold text-white text-sm flex items-center gap-2">
                                <i class="bi bi-clock-history {{ $accentText }}"></i> Appointment History
                            </h3>
                            <span class="text-xs text-slate-500">{{ $completedAppointments->count() }} completed</span>
                        </div>
                        @if($completedAppointments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-950/40 border-b border-slate-800/60">
                                    <tr class="text-xs uppercase text-slate-500 tracking-wider">
                                        <th class="px-6 py-3 text-left">Date</th>
                                        <th class="px-6 py-3 text-left">Service</th>
                                        <th class="px-6 py-3 text-left">Notes</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40">
                                    @foreach($completedAppointments as $appt)
                                        <tr class="hover:bg-slate-800/20 transition-all">
                                            <td class="px-6 py-3 text-slate-300 whitespace-nowrap">{{ $appt->appointment_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-3 text-white font-medium">{{ $appt->service_label }}</td>
                                            <td class="px-6 py-3 text-slate-400 text-xs max-w-[180px] truncate">{{ $appt->notes ?: '—' }}</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-calendar-x text-3xl text-slate-700 mb-2 block"></i>
                                <p class="text-slate-500 text-sm italic">No completed appointments yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Medical Records Tab (item 10) -->
                <div id="tab-records" class="hidden">
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
                            <h3 class="font-bold text-white text-sm flex items-center gap-2">
                                <i class="bi bi-clipboard2-pulse {{ $accentText }}"></i> Medical Records
                            </h3>
                            <span class="text-xs text-slate-500">{{ $pet->records->count() }} {{ Str::plural('record', $pet->records->count()) }}</span>
                        </div>
                        @if($pet->records->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-950/40 border-b border-slate-800/60">
                                    <tr class="text-xs uppercase text-slate-500 tracking-wider">
                                        <th class="px-6 py-3 text-left">Date</th>
                                        <th class="px-6 py-3 text-left">Diagnosis</th>
                                        <th class="px-6 py-3 text-left">Medications</th>
                                        <th class="px-6 py-3 text-left">Vet Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/40">
                                    @foreach($pet->records as $record)
                                        <tr class="hover:bg-slate-800/20 transition-all">
                                            <td class="px-6 py-3 text-slate-300 whitespace-nowrap">{{ $record->record_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-3 text-white font-medium">{{ $record->diagnosis }}</td>
                                            <td class="px-6 py-3 text-slate-400 text-xs max-w-[160px] truncate">{{ $record->medications ?: '—' }}</td>
                                            <td class="px-6 py-3 text-slate-400 text-xs max-w-[160px] truncate">{{ $record->vet_notes ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-clipboard2 text-3xl text-slate-700 mb-2 block"></i>
                                <p class="text-slate-500 text-sm italic">No medical records yet.</p>
                                <p class="text-slate-600 text-xs mt-1">Records are added by clinic staff.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-5">
                    <h4 class="font-bold text-white text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Next Appointment
                    </h4>
                    @if($nextAppointment)
                        <p class="text-white font-semibold">{{ $nextAppointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-slate-400 text-xs mt-0.5">{{ $nextAppointment->appointment_date->format('g:i A') }} · {{ $nextAppointment->service_label }}</p>
                    @else
                        <p class="text-slate-500 text-sm italic">No upcoming appointments.</p>
                        <a href="{{ route('request.appointment') }}" class="mt-2 inline-block text-xs text-teal-400 hover:text-teal-300">Book one now →</a>
                    @endif
                </div>

                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl p-5">
                    <h4 class="font-bold text-white text-sm mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <a href="{{ route('request.appointment') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-teal-600/20 hover:bg-teal-600/40 border border-teal-500/20 text-teal-300 text-sm transition-all hover:translate-x-1">
                            <i class="bi bi-calendar-plus"></i> Book Appointment
                        </a>
                        <form method="POST" action="{{ route('pets.destroy', $pet) }}"
                              onsubmit="return confirm('Remove {{ $pet->name }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 text-sm transition-all hover:translate-x-1">
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
         onclick="if(event.target===this) toggleModal('edit-pet-modal','edit-pet-modal-content')">
        <div id="edit-pet-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-lg shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-6">Edit {{ $pet->name }}</h2>
            <form action="{{ route('pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Name</label>
                        <input type="text" name="name" value="{{ $pet->name }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Pet Type</label>
                        <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            <option value="dog"   {{ $pet->type==='dog'  ?'selected':'' }}>🐶 Dog</option>
                            <option value="cat"   {{ $pet->type==='cat'  ?'selected':'' }}>🐱 Cat</option>
                            <option value="other" {{ $pet->type==='other'?'selected':'' }}>🐾 Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Breed</label>
                        <input type="text" name="breed" value="{{ $pet->breed }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Age</label>
                        <input type="number" name="age" value="{{ $pet->age }}" min="0" max="100" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Profile Picture</label>
                    <label class="relative flex items-center justify-center w-full h-28 border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden {{ $pet->photo_url ? 'border-teal-500/40' : 'border-slate-700 hover:border-teal-500' }} bg-slate-950 hover:bg-slate-900">
                        <div id="edit-upload-placeholder" class="flex flex-col items-center gap-1 {{ $pet->photo_url ? 'hidden' : '' }}">
                            <i class="bi bi-cloud-upload text-2xl text-teal-500"></i>
                            <p class="text-xs text-slate-400">Click to upload or replace photo</p>
                        </div>
                        @if($pet->photo_url)
                            <img id="edit-photo-preview" src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="absolute inset-0 w-full h-full object-cover rounded-xl">
                            <div class="absolute inset-0 bg-slate-950/60 flex items-center justify-center opacity-0 hover:opacity-100 transition-all rounded-xl">
                                <p class="text-xs text-white font-semibold"><i class="bi bi-pencil mr-1"></i>Change Photo</p>
                            </div>
                        @else
                            <img id="edit-photo-preview" src="" alt="{{ $pet->name }}" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                        @endif
                        <input type="file" id="edit-photo-input" name="photo" accept="image/*" class="hidden">
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Special Notes</label>
                    <textarea name="special_notes" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm">{{ $pet->special_notes }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('edit-pet-modal','edit-pet-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02]">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('profile-modal','profile-modal-content')">
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
                <button type="button" onclick="toggleModal('profile-modal','profile-modal-content')" class="w-full mt-2 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>
</body>
</html>