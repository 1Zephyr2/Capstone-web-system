<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Request Appointment</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        let currentYear, currentMonth, selectedDate = null;
        let fullyBookedDates = new Set();
        let slotCounts = {};
        let maxPerSlot = 3;
        const clinicHours = @json($clinicHours);
        const availabilityUrl = "{{ route('appointments.availability') }}";
        const monthAvailabilityUrl = "{{ route('appointments.availability.month') }}";

        document.addEventListener('DOMContentLoaded', () => {
            const now = new Date();
            currentYear = now.getFullYear(); currentMonth = now.getMonth();
            loadMonthAvailability(currentYear, currentMonth + 1);
            document.querySelectorAll('.pet-checkbox').forEach(cb => cb.addEventListener('change', onPetSelectionChange));
            document.querySelectorAll('input[name="time_mode_radio"]').forEach(r => r.addEventListener('change', renderSchedulingSection));
        });

        function loadMonthAvailability(year, month) {
            fetch(`${monthAvailabilityUrl}?year=${year}&month=${month}`)
                .then(res => res.json())
                .then(data => { fullyBookedDates = new Set(data.fully_booked_dates || []); renderCalendar(currentYear, currentMonth); })
                .catch(() => { fullyBookedDates = new Set(); renderCalendar(currentYear, currentMonth); });
        }

        function renderCalendar(year, month) {
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('cal-month-label').innerText = months[month] + ' ' + year;
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date(); today.setHours(0,0,0,0);
            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '';
            ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(d => {
                const el = document.createElement('div');
                el.className = 'text-center text-xs text-gray-400 font-semibold py-1';
                el.innerText = d; grid.appendChild(el);
            });
            for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));
            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(year, month, d); cellDate.setHours(0,0,0,0);
                const isPast = cellDate <= today;
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                const isSelected = selectedDate === dateStr;
                const isFull = fullyBookedDates.has(dateStr);
                const el = document.createElement('button');
                el.type = 'button'; el.innerText = d;
                el.title = isFull ? 'Fully booked' : '';
                el.className = ['rounded-lg py-1.5 text-sm font-medium transition-all w-full',
                    isPast ? 'text-gray-300 cursor-not-allowed' :
                    isSelected ? 'bg-emerald-600 text-white font-bold shadow-md' :
                    isFull ? 'bg-red-50 text-red-400 border border-red-200 line-through hover:bg-red-100' :
                    'text-gray-700 hover:bg-emerald-100 hover:text-emerald-700'].join(' ');
                if (!isPast) el.onclick = () => selectDate(dateStr, d, months[month], year);
                grid.appendChild(el);
            }
        }
        function prevMonth() { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } loadMonthAvailability(currentYear, currentMonth + 1); }
        function nextMonth() { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } loadMonthAvailability(currentYear, currentMonth + 1); }

        function selectDate(dateStr, day, monthName, year) {
            selectedDate = dateStr;
            renderCalendar(currentYear, currentMonth);
            document.getElementById('selected-date-label').innerText = monthName + ' ' + day + ', ' + year;
            document.getElementById('appointment_date_hidden').value = dateStr;
            document.getElementById('date-placeholder').classList.add('hidden');

            fetch(`${availabilityUrl}?date=${dateStr}`)
                .then(res => res.json())
                .then(data => {
                    slotCounts = data.counts || {};
                    maxPerSlot = data.max_per_slot || 3;
                    if (data.fully_booked) {
                        document.getElementById('fully-booked-banner').classList.remove('hidden');
                        document.getElementById('scheduling-section').classList.add('hidden');
                    } else {
                        document.getElementById('fully-booked-banner').classList.add('hidden');
                        renderSchedulingSection();
                    }
                });
        }

        function selectedPetIds() {
            return Array.from(document.querySelectorAll('.pet-checkbox:checked')).map(cb => cb.value);
        }

        function onPetSelectionChange() {
            document.querySelectorAll('.pet-service-row').forEach(row => {
                const cb = row.querySelector('.pet-checkbox');
                row.querySelector('.pet-service-select').classList.toggle('hidden', !cb.checked);
                row.querySelector('.pet-service-select').required = cb.checked;
            });
            const count = selectedPetIds().length;
            document.getElementById('time-mode-toggle').classList.toggle('hidden', count < 2);
            renderSchedulingSection();
        }

        function timeOptionsHtml(excludeCounts) {
            excludeCounts = excludeCounts || {};
            let html = '<option value="">Select a time</option>';
            Object.entries(clinicHours).forEach(([value, label]) => {
                const used = (slotCounts[value] || 0) + (excludeCounts[value] || 0);
                const full = used >= maxPerSlot;
                const remaining = Math.max(maxPerSlot - used, 0);
                html += `<option value="${value}" ${full ? 'disabled' : ''}>${label}${full ? ' — Full' : ` (${remaining} left)`}</option>`;
            });
            return html;
        }

        function renderSchedulingSection() {
            if (!selectedDate) return;
            const petIds = selectedPetIds();
            const section = document.getElementById('scheduling-section');
            if (petIds.length === 0) { section.classList.add('hidden'); return; }
            section.classList.remove('hidden');

            const mode = document.querySelector('input[name="time_mode_radio"]:checked')?.value || 'same';
            document.getElementById('time_mode_input').value = petIds.length > 1 ? mode : 'same';

            const sameWrap = document.getElementById('same-time-wrap');
            const separateWrap = document.getElementById('separate-time-wrap');

            if (petIds.length <= 1 || mode === 'same') {
                sameWrap.classList.remove('hidden');
                separateWrap.classList.add('hidden');
                document.getElementById('shared-time-select').innerHTML = timeOptionsHtml();
                document.getElementById('shared-time-select').required = true;
                separateWrap.querySelectorAll('select').forEach(s => s.required = false);
            } else {
                sameWrap.classList.add('hidden');
                separateWrap.classList.remove('hidden');
                document.getElementById('shared-time-select').required = false;

                const petNames = {};
                document.querySelectorAll('.pet-checkbox').forEach(cb => petNames[cb.value] = cb.dataset.name);

                separateWrap.innerHTML = petIds.map(petId => `
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-sm text-gray-700 w-32 shrink-0 truncate">${petNames[petId] || 'Pet'}</span>
                        <select name="times[${petId}]" class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500" required>
                            ${timeOptionsHtml()}
                        </select>
                    </div>
                `).join('');
            }
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
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition-all">
                    <i class="bi bi-calendar-check mr-1 text-emerald-600"></i> My Appointments
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 transition-all">Logout</button>
                </form>
            </div>
            <button onclick="toggleNav()" class="sm:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('appointments.index') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm"><i class="bi bi-calendar-check text-emerald-600"></i> My Appointments</a>
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

        <header class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Request Appointment</h1>
                <p class="text-gray-400 text-sm">Select one or more pets, pick a date, then choose your times.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all shrink-0">← Back</a>
        </header>

        @if($pets->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <i class="bi bi-heart text-5xl text-gray-300 mb-4 block"></i>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Pets Registered</h3>
                <p class="text-gray-400 mb-5 text-sm">Add a pet from your dashboard before booking.</p>
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold transition-all hover:bg-emerald-700">Go to Dashboard</a>
            </div>
        @else
        <form method="POST" action="{{ route('appointments.store') }}" id="booking-form">
            @csrf
            <input type="hidden" name="appointment_date" id="appointment_date_hidden">
            <input type="hidden" name="time_mode" id="time_mode_input" value="same">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Calendar -->
                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <button type="button" onclick="prevMonth()" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                                <i class="bi bi-chevron-left text-sm"></i>
                            </button>
                            <span id="cal-month-label" class="text-gray-900 font-bold text-sm"></span>
                            <button type="button" onclick="nextMonth()" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                                <i class="bi bi-chevron-right text-sm"></i>
                            </button>
                        </div>
                        <div id="cal-grid" class="grid grid-cols-7 gap-1 text-center"></div>
                        <p class="text-gray-400 text-xs text-center mt-3">Click a date to continue</p>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 space-y-2 text-xs text-gray-500">
                        <p class="font-bold uppercase tracking-widest text-emerald-700 mb-2">Legend</p>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600 inline-block"></span> Open — click to select</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-300 inline-block"></span> Fully booked</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-gray-300 inline-block"></span> Past date</div>
                    </div>
                </div>

                <!-- Pets, services, scheduling -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Pet + service selection -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Which Pet(s)?</p>
                        <div class="space-y-3">
                            @foreach($pets as $pet)
                                <div class="pet-service-row border border-gray-200 rounded-xl p-3">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" class="pet-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                               name="pets[]" value="{{ $pet->id }}" data-name="{{ $pet->name }}">
                                        <span class="text-sm font-semibold text-gray-900">{{ $pet->name }}</span>
                                        <span class="text-xs text-gray-400">({{ $pet->breed }})</span>
                                    </label>
                                    <select name="services[{{ $pet->id }}]" class="pet-service-select hidden mt-2 w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                        @foreach($services as $category => $items)
                                            <optgroup label="{{ $category }}">
                                                @foreach($items as $svc)
                                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date placeholder -->
                    <div id="date-placeholder" class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">
                        <i class="bi bi-calendar3 text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-gray-500 font-medium">Select a date on the calendar</p>
                    </div>

                    <!-- Fully booked banner -->
                    <div id="fully-booked-banner" class="hidden bg-white border border-red-200 rounded-2xl p-10 text-center shadow-sm">
                        <i class="bi bi-calendar-x text-4xl text-red-300 mb-3 block"></i>
                        <p class="text-gray-900 font-bold">This date is fully booked</p>
                        <p class="text-gray-400 text-sm mt-1">Please choose another date on the calendar.</p>
                    </div>

                    <!-- Scheduling -->
                    <div id="scheduling-section" class="hidden bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="bi bi-calendar-check text-emerald-600"></i>
                            <p class="text-gray-900 font-bold text-sm">Scheduling — <span id="selected-date-label" class="text-emerald-600"></span></p>
                        </div>

                        <div id="time-mode-toggle" class="hidden flex items-center gap-4 mb-4 text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="time_mode_radio" value="same" checked class="text-emerald-600 focus:ring-emerald-500">
                                Same time for all pets
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="time_mode_radio" value="separate" class="text-emerald-600 focus:ring-emerald-500">
                                Separate time per pet
                            </label>
                        </div>

                        <div id="same-time-wrap">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Time</label>
                            <select id="shared-time-select" name="appointment_time" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500"></select>
                        </div>

                        <div id="separate-time-wrap" class="hidden"></div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">
                                Additional Info <span class="normal-case font-normal text-gray-300">(optional)</span>
                            </label>
                            <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 resize-none"></textarea>
                        </div>

                        @if($errors->any())
                            <div class="mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                                @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                            </div>
                        @endif

                        <button type="submit" class="mt-5 w-full px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all">
                            Submit Request
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </main>
</body>
</html>
