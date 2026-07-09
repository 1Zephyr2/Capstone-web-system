@php
    $pet = App\Models\Pet::with([
        'user',
        'appointments' => fn($q) => $q->orderByDesc('appointment_date'),
        'records'      => fn($q) => $q->with('recorder')->orderByDesc('record_date'),
    ])->findOrFail($id);

    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $isStaff = auth()->check() && auth()->user()->role === 'staff';

    $nextAppointment       = $pet->appointments->where('status','approved')->where('appointment_date','>=',now())->sortBy('appointment_date')->first();
    $completedAppointments = $pet->appointments->where('status','completed')->sortByDesc('appointment_date');
    $activeAppts           = $pet->appointments->whereIn('status',['pending','approved'])->sortBy('appointment_date');

    if ($isAdmin) {
        $bgMain='bg-gray-50'; $bgNav='bg-white';
        $badgeBg='bg-rose-100'; $badgeBorder='border-rose-200'; $badgeText='text-rose-700';
        $accentText='text-gray-500'; $cardBg='bg-white';
        $cardBorder='border-gray-200'; $accentBorder='border-indigo-300';
        $accentBtn='bg-indigo-600 hover:bg-indigo-700';
        $inputBg='bg-white border-gray-300'; $portalLabel='ADMIN PORTAL';
    } else {
        $bgMain='bg-gray-50'; $bgNav='bg-white';
        $badgeBg='bg-violet-100'; $badgeBorder='border-violet-200'; $badgeText='text-violet-700';
        $accentText='text-violet-600'; $cardBg='bg-white';
        $cardBorder='border-gray-200'; $accentBorder='border-violet-300';
        $accentBtn='bg-violet-600 hover:bg-violet-700';
        $inputBg='bg-white border-gray-300'; $portalLabel='STAFF PORTAL';
    }
    $prefix = $isAdmin ? 'admin' : 'staff';
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
            switchTab('appointments');

            @if(session('active_tab') === 'records')
                switchTab('records');
            @endif
        });

        function switchTab(tab) {
            ['appointments','records'].forEach(t => {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('btn-' + t).classList.remove('border-violet-500','text-gray-900','border-indigo-500');
                document.getElementById('btn-' + t).classList.add('border-transparent','text-gray-400');
            });
            document.getElementById('tab-' + tab).classList.remove('hidden');
            document.getElementById('btn-' + tab).classList.add('{{ $isAdmin ? "border-indigo-500" : "border-violet-500" }}','text-gray-900');
            document.getElementById('btn-' + tab).classList.remove('border-transparent','text-gray-400');
        }

        function showModal(id, contentId) {
            const m = document.getElementById(id), c = document.getElementById(contentId);
            m.classList.remove('hidden'); m.classList.add('flex');
            setTimeout(() => { m.classList.add('opacity-100'); c.classList.remove('scale-95','opacity-0'); c.classList.add('scale-100','opacity-100'); }, 10);
        }

        function closeModal(id, contentId) {
            const m = document.getElementById(id), c = document.getElementById(contentId);
            m.classList.remove('opacity-100'); c.classList.remove('scale-100','opacity-100'); c.classList.add('scale-95','opacity-0');
            setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
        }

        function showVisitDetails(service, date, notes, status) {
            document.getElementById('visit-service').innerText = service;
            document.getElementById('visit-date').innerText    = date;
            document.getElementById('visit-notes').innerText   = notes || '—';
            document.getElementById('visit-status').innerText  = status;
            showModal('visit-modal','visit-modal-content');
        }

        function showOwnerProfile(name, email) {
            document.getElementById('owner-name').innerText  = name;
            document.getElementById('owner-email').innerText = email;
            showModal('profile-modal','profile-modal-content');
        }

        function openEditRecord(id, date, diagnosis, medications, vet_notes, action) {
            document.getElementById('edit-record-date').value        = date;
            document.getElementById('edit-record-diagnosis').value   = diagnosis;
            document.getElementById('edit-record-medications').value = medications;
            document.getElementById('edit-record-vet_notes').value   = vet_notes;
            document.getElementById('edit-record-form').action       = action;
            showModal('edit-record-modal','edit-record-modal-content');
        }
    </script>
</head>
<body class="{{ $bgMain }} text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full {{ $bgNav }} border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix.'.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $badgeText }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md {{ $badgeBg }} border {{ $badgeBorder }}">{{ $portalLabel }}</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route($prefix.'.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix.'.directory') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix.'.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                <a href="{{ route($prefix.'.insights') }}"     class="hover:text-gray-900 transition-all hover:scale-105">Insights</a>
            </div>
            <form action="{{ route($isAdmin ? 'admin.logout' : 'staff.logout') }}" method="POST" class="m-0">
                @csrf
                <button class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-10">

        @if(session('success'))
            <div class="mb-5 px-5 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Header -->
        <header class="mb-8 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="flex items-center gap-5">
                @if($pet->photo_url)
                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 {{ $isAdmin ? 'border-indigo-200' : 'border-violet-200' }} shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl {{ $isAdmin ? 'bg-indigo-100 border-indigo-200' : 'bg-violet-100 border-violet-200' }} flex items-center justify-center text-3xl border">
                        {{ $pet->type==='cat' ? '🐱' : ($pet->type==='dog' ? '🐶' : '🐾') }}
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $pet->name }}</h1>
                    <p class="{{ $accentText }} font-medium text-sm">{{ $pet->breed }} · {{ $pet->age }} {{ Str::plural('yr',$pet->age) }} · {{ ucfirst($pet->type) }}</p>
                    @if($pet->special_notes)
                        <p class="text-gray-400 text-xs mt-1 max-w-sm truncate">{{ $pet->special_notes }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route($prefix.'.directory') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold transition-all">← Back</a>
        </header>

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Main content -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Tab Buttons -->
                <div class="flex gap-1 {{ $cardBg }} border {{ $cardBorder }} rounded-xl shadow-sm p-1">
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
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm p-5 mb-4">
                        <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                            <i class="bi bi-calendar-event {{ $accentText }}"></i> Pending / Upcoming
                        </h3>
                        <div class="space-y-2">
                            @foreach($activeAppts as $appt)
                                <div class="flex items-center justify-between p-3 border-l-2 border-amber-500/50 bg-amber-50 rounded-r-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $appt->service_label }}</p>
                                        <p class="text-xs text-gray-500">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Completed — TABLE -->
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b {{ $cardBorder }} flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <i class="bi bi-clock-history {{ $accentText }}"></i> Appointment History
                            </h3>
                            <span class="text-xs text-gray-400">{{ $completedAppointments->count() }} completed</span>
                        </div>
                        @if($completedAppointments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/40 border-b {{ $cardBorder }}">
                                    <tr class="text-xs uppercase text-gray-400 tracking-wider">
                                        <th class="px-5 py-3 text-left">Date</th>
                                        <th class="px-5 py-3 text-left">Service</th>
                                        <th class="px-5 py-3 text-left">Notes</th>
                                        <th class="px-5 py-3 text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200/40">
                                    @foreach($completedAppointments as $appt)
                                        <tr class="hover:bg-gray-50 transition-all cursor-pointer"
                                            onclick="showVisitDetails('{{ addslashes($appt->service_label) }}','{{ $appt->appointment_date->format('F d, Y — g:i A') }}','{{ addslashes($appt->notes ?? '') }}','{{ ucfirst($appt->status) }}')">
                                            <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ $appt->appointment_date->format('M d, Y') }}</td>
                                            <td class="px-5 py-3 text-gray-900 font-medium">{{ $appt->service_label }}</td>
                                            <td class="px-5 py-3 text-gray-500 text-xs max-w-[150px] truncate">{{ $appt->notes ?: '—' }}</td>
                                            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-calendar-x text-3xl text-gray-300 mb-2 block"></i>
                                <p class="text-gray-400 text-sm italic">No completed appointments yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Medical Records Tab -->
                <div id="tab-records" class="hidden space-y-4">

                    <!-- Add Record Form -->
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
                            <i class="bi bi-plus-circle {{ $accentText }}"></i> Add Medical Record
                        </h3>
                        <form method="POST" action="{{ route($prefix . '.records.store', $pet) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="redirect_tab" value="records">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Date</label>
                                    <input type="date" name="record_date" value="{{ date('Y-m-d') }}" required
                                           class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Diagnosis</label>
                                    <input type="text" name="diagnosis" required placeholder="e.g. Skin infection"
                                           class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Medications</label>
                                <input type="text" name="medications" placeholder="e.g. Amoxicillin 250mg"
                                       class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Vet Notes</label>
                                <textarea name="vet_notes" rows="2" placeholder="Follow-up instructions, observations..."
                                          class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all resize-none text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 rounded-xl {{ $accentBtn }} text-white font-semibold text-sm transition-all hover:scale-[1.02]">
                                <i class="bi bi-plus-circle mr-1"></i> Save Record
                            </button>
                        </form>
                    </div>

                    <!-- Records Table -->
                    <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b {{ $cardBorder }} flex items-center justify-between">
                            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                                <i class="bi bi-clipboard2-pulse {{ $accentText }}"></i> Record History
                            </h3>
                            <span class="text-xs text-gray-400">{{ $pet->records->count() }} {{ Str::plural('record',$pet->records->count()) }}</span>
                        </div>
                        @if($pet->records->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/40 border-b {{ $cardBorder }}">
                                    <tr class="text-xs uppercase text-gray-400 tracking-wider">
                                        <th class="px-5 py-3 text-left">Date</th>
                                        <th class="px-5 py-3 text-left">Diagnosis</th>
                                        <th class="px-5 py-3 text-left">Medications</th>
                                        <th class="px-5 py-3 text-left">Recorded By</th>
                                        <th class="px-5 py-3 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200/40">
                                    @foreach($pet->records as $record)
                                        <tr class="hover:bg-gray-50 transition-all">
                                            <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ $record->record_date->format('M d, Y') }}</td>
                                            <td class="px-5 py-3 text-gray-900 font-medium">{{ $record->diagnosis }}</td>
                                            <td class="px-5 py-3 text-gray-500 text-xs max-w-[140px] truncate">{{ $record->medications ?: '—' }}</td>
                                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $record->recorder->name ?? '—' }}</td>
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="openEditRecord(
                                                        {{ $record->id }},
                                                        '{{ $record->record_date->format('Y-m-d') }}',
                                                        '{{ addslashes($record->diagnosis) }}',
                                                        '{{ addslashes($record->medications ?? '') }}',
                                                        '{{ addslashes($record->vet_notes ?? '') }}',
                                                        '{{ route($prefix . '.records.update', $record) }}'
                                                    )" class="p-1.5 rounded-lg {{ $isAdmin ? 'text-gray-500 hover:bg-indigo-100' : 'text-violet-600 hover:bg-violet-100' }} transition-all">
                                                        <i class="bi bi-pencil text-xs"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route($prefix . '.records.destroy', $record) }}"
                                                          onsubmit="return confirm('Delete this record?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-100 transition-all">
                                                            <i class="bi bi-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="text-center py-10">
                                <i class="bi bi-clipboard2 text-3xl text-gray-300 mb-2 block"></i>
                                <p class="text-gray-400 text-sm italic">No medical records yet. Add one above.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm p-5">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-calendar-check {{ $accentText }}"></i> Next Appointment
                    </h4>
                    @if($nextAppointment)
                        <p class="text-gray-900 font-semibold">{{ $nextAppointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $nextAppointment->appointment_date->format('g:i A') }} · {{ $nextAppointment->service_label }}</p>
                    @else
                        <p class="text-gray-400 text-sm italic">No upcoming appointments.</p>
                    @endif
                </div>

                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm p-5">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle {{ $accentText }}"></i> About {{ $pet->name }}
                    </h4>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $pet->special_notes ?: 'No special notes.' }}</p>
                </div>

                <div class="{{ $cardBg }} border {{ $cardBorder }} rounded-2xl shadow-sm p-5">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <i class="bi bi-person {{ $accentText }}"></i> Owner
                    </h4>
                    <p class="text-gray-900 font-semibold text-sm">{{ $pet->user->name }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $pet->user->email }}</p>
                    <button onclick="showOwnerProfile('{{ addslashes($pet->user->name) }}','{{ addslashes($pet->user->email) }}')"
                            class="w-full mt-3 px-4 py-2 border {{ $cardBorder }} rounded-lg text-xs hover:bg-gray-50 transition-all text-gray-600">
                        View Profile
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Visit Details Modal -->
    <div id="visit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('visit-modal','visit-modal-content')">
        <div id="visit-modal-content" class="bg-white border-gray-200 border rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-5">Appointment Details</h2>
            <div class="space-y-3">
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Service</label>
                    <p id="visit-service" class="text-gray-900 p-3 rounded-lg border bg-gray-50 border-gray-200"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Date & Time</label>
                    <p id="visit-date" class="text-gray-900 p-3 rounded-lg border bg-gray-50 border-gray-200"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Status</label>
                    <p id="visit-status" class="text-gray-900 p-3 rounded-lg border bg-gray-50 border-gray-200"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Notes</label>
                    <p id="visit-notes" class="text-gray-700 p-3 rounded-lg border text-sm bg-gray-50 border-gray-200"></p></div>
                <button onclick="closeModal('visit-modal','visit-modal-content')" class="w-full mt-2 px-4 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Record Modal -->
    <div id="edit-record-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('edit-record-modal','edit-record-modal-content')">
        <div id="edit-record-modal-content" class="bg-white border-gray-200 border rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-5">Edit Medical Record</h2>
            <form id="edit-record-form" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Date</label>
                    <input type="date" id="edit-record-date" name="record_date" required
                           class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Diagnosis</label>
                    <input type="text" id="edit-record-diagnosis" name="diagnosis" required
                           class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Medications</label>
                    <input type="text" id="edit-record-medications" name="medications"
                           class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Vet Notes</label>
                    <textarea id="edit-record-vet_notes" name="vet_notes" rows="3"
                              class="w-full {{ $inputBg }} border rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-violet-500 transition-all resize-none text-sm"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('edit-record-modal','edit-record-modal-content')"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all font-semibold">Cancel</button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-xl {{ $accentBtn }} text-white font-semibold transition-all hover:scale-[1.02]">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Owner Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('profile-modal','profile-modal-content')">
        <div id="profile-modal-content" class="bg-white border-gray-200 border rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-5">Owner Profile</h2>
            <div class="space-y-3">
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Name</label>
                    <p id="owner-name" class="text-gray-900 p-3 rounded-lg border bg-gray-50 border-gray-200"></p></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider {{ $accentText }} mb-1">Email</label>
                    <p id="owner-email" class="text-gray-900 p-3 rounded-lg border bg-gray-50 border-gray-200"></p></div>
                <button onclick="closeModal('profile-modal','profile-modal-content')" class="w-full mt-2 px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">Close</button>
            </div>
        </div>
    </div>
</body>
</html>