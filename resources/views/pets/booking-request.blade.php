<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Request Appointment</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        // ── Calendar State ──────────────────────────────────────────────
        let currentYear, currentMonth, selectedDate = null;

        document.addEventListener('DOMContentLoaded', () => {
            const now = new Date();
            currentYear  = now.getFullYear();
            currentMonth = now.getMonth();
            renderCalendar(currentYear, currentMonth);
        });

        function renderCalendar(year, month) {
            const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            document.getElementById('cal-month-label').innerText = monthNames[month] + ' ' + year;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            today.setHours(0,0,0,0);

            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '';

            // Day headers
            ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
                const el = document.createElement('div');
                el.className = 'text-center text-xs text-slate-500 font-semibold py-1';
                el.innerText = d;
                grid.appendChild(el);
            });

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                grid.appendChild(document.createElement('div'));
            }

            // Day cells
            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(year, month, d);
                cellDate.setHours(0,0,0,0);
                const isPast = cellDate <= today;
                const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                const isSelected = selectedDate === dateStr;

                const el = document.createElement('button');
                el.type = 'button';
                el.innerText = d;
                el.className = [
                    'rounded-lg py-1.5 text-sm font-medium transition-all',
                    isPast
                        ? 'text-slate-700 cursor-not-allowed'
                        : isSelected
                            ? 'bg-teal-500 text-white font-bold shadow-lg shadow-teal-900/30'
                            : 'text-slate-200 hover:bg-teal-500/20 hover:text-teal-300',
                ].join(' ');

                if (!isPast) {
                    el.onclick = () => selectDate(dateStr, d, monthNames[month], year);
                }
                grid.appendChild(el);
            }
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar(currentYear, currentMonth);
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            renderCalendar(currentYear, currentMonth);
        }

        function selectDate(dateStr, day, monthName, year) {
            selectedDate = dateStr;
            renderCalendar(currentYear, currentMonth);

            // Show booking sheet
            document.getElementById('booking-sheet').classList.remove('hidden');
            document.getElementById('selected-date-label').innerText = monthName + ' ' + day + ', ' + year;
            document.getElementById('appointment_date_hidden').value = dateStr;

            // Smooth scroll to booking sheet
            setTimeout(() => document.getElementById('booking-sheet').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
        }

        // ── Confirm Modal ───────────────────────────────────────────────
        function openServiceModal(timeValue, timeLabel) {
            if (!selectedDate) { alert('Please select a date first.'); return; }

            const petSelect     = document.getElementById('pet_select');
            const serviceSelect = document.getElementById('service_select');

            document.getElementById('appointment_time').value    = timeValue;
            document.getElementById('hidden_date').value         = selectedDate;
            document.getElementById('hidden_pet_id').value       = petSelect.value;
            document.getElementById('hidden_service_type').value = serviceSelect.value;

            document.getElementById('selected_time_display').innerText   = timeLabel;
            document.getElementById('confirm_date_display').innerText    = document.getElementById('selected-date-label').innerText;
            document.getElementById('confirm_pet_display').innerText     = petSelect.options[petSelect.selectedIndex].text;
            document.getElementById('confirm_service_display').innerText = serviceSelect.options[serviceSelect.selectedIndex].text;

            showModal('service-modal', 'service-modal-content');
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

        function toggleProfileModal() {
            const modal = document.getElementById('profile-modal');
            if (modal.classList.contains('hidden')) showModal('profile-modal','profile-modal-content');
            else closeModal('profile-modal','profile-modal-content');
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
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all">
                    <i class="bi bi-calendar-check mr-1"></i> My Appointments
                </a>
                <button onclick="toggleProfileModal()" class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 hover:text-white transition-all hover:scale-105">
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
            <div class="mb-5 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Request an Appointment</h1>
                <p class="text-slate-400 text-sm">Pick a date on the calendar, then choose a time slot.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">← Back</a>
        </header>

        @if($pets->isEmpty())
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-12 text-center">
                <i class="bi bi-paw text-5xl text-slate600 mb-4 block"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Pets Registered</h3>
                <p class="text-slate-400 mb-6">Add a pet from your dashboard before booking.</p>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">Go to Dashboard</a>
            </div>
        @else

        <div class="grid lg:grid-cols-3 gap-8">

            <!-- LEFT: Settings + Calendar -->
            <div class="space-y-5">

                <!-- Pet & Service selectors -->
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Which Pet?</label>
                        <div class="relative">
                            <i class="bi bi-paw absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                            <select id="pet_select" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none text-sm">
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}">{{ $pet->name }} ({{ $pet->breed }})</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-3 text-slate-500 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Service Type</label>
                        <div class="relative">
                            <i class="bi bi-scissors absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                            <select id="service_select" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none text-sm">
                                @foreach($serviceTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-3 text-slate-500 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <button type="button" onclick="prevMonth()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 transition-all">
                            <i class="bi bi-chevron-left text-sm"></i>
                        </button>
                        <span id="cal-month-label" class="text-white font-semibold text-sm"></span>
                        <button type="button" onclick="nextMonth()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 transition-all">
                            <i class="bi bi-chevron-right text-sm"></i>
                        </button>
                    </div>
                    <div id="cal-grid" class="grid grid-cols-7 gap-1 text-center"></div>
                    <p class="text-slate-600 text-xs text-center mt-3">Click a date to see available slots</p>
                </div>

                <!-- Legend -->
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-4 space-y-2 text-xs text-slate-400">
                    <p class="font-bold uppercase tracking-widest text-slate-500 mb-2">Legend</p>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block"></span> Open — click to request</div>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-slate-600 inline-block"></span> Past date</div>
                </div>
            </div>

            <!-- RIGHT: Booking Sheet (hidden until date selected) -->
            <div class="lg:col-span-2" id="booking-sheet" style="display:none;" class="hidden">
                <input type="hidden" id="appointment_date_hidden">

                <div class="bg-slate-900/40 border border-teal-500/30 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="px-6 py-4 border-b border-slate-800/80 flex items-center gap-3">
                        <i class="bi bi-calendar-check text-teal-400"></i>
                        <div>
                            <p class="text-white font-semibold text-sm">Available Slots</p>
                            <p class="text-teal-400 text-xs" id="selected-date-label"></p>
                        </div>
                    </div>
                    <table class="w-full text-left">
                        <thead class="bg-slate-950/30 border-b border-slate-800">
                            <tr class="text-xs uppercase text-slate-500 tracking-widest">
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4">Availability</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($clinicHours as $value => $label)
                                <tr>
                                    <td class="px-6 py-5 font-bold text-teal-400">{{ $label }}</td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-teal-500/10 text-teal-400 border border-teal-500/20 uppercase tracking-wide">Open Slot</span>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <button type="button" onclick="openServiceModal('{{ $value }}', '{{ $label }}')"
                                                class="px-5 py-2 rounded-xl text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105 active:scale-95">
                                            + Request
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Placeholder when no date selected -->
            <div class="lg:col-span-2 flex items-center justify-center" id="booking-placeholder">
                <div class="text-center py-16">
                    <i class="bi bi-calendar3 text-5xl text-slate-700 mb-4 block"></i>
                    <p class="text-slate-500 font-medium">Select a date on the calendar</p>
                    <p class="text-slate-600 text-sm mt-1">Available time slots will appear here</p>
                </div>
            </div>
        </div>

        <script>
            // Override selectDate to also show/hide placeholder
            const origSelectDate = selectDate;
            selectDate = function(dateStr, day, monthName, year) {
                origSelectDate(dateStr, day, monthName, year);
                document.getElementById('booking-sheet').style.display = 'block';
                document.getElementById('booking-placeholder').style.display = 'none';
            }
        </script>

        @endif
    </main>

    <!-- Confirm Booking Modal -->
    <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('service-modal','service-modal-content')">
        <div id="service-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-2">Confirm Appointment</h2>
            <p class="text-slate-400 text-sm mb-6">Review your booking details before submitting.</p>
            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <input type="hidden" name="appointment_time" id="appointment_time">
                <input type="hidden" name="appointment_date" id="hidden_date">
                <input type="hidden" name="pet_id"           id="hidden_pet_id">
                <input type="hidden" name="service_type"     id="hidden_service_type">
                <div class="space-y-3 mb-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Time</label>
                        <p id="selected_time_display" class="text-teal-400 font-bold bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Date</label>
                        <p id="confirm_date_display" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet</label>
                        <p id="confirm_pet_display" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Service</label>
                        <p id="confirm_service_display" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">
                            Additional Info <span class="normal-case font-normal text-slate-600">(optional)</span>
                        </label>
                        <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                  class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm"></textarea>
                    </div>
                </div>
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeModal('service-modal','service-modal-content')"
                            class="px-4 py-3 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleProfileModal()">
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
                <button onclick="toggleProfileModal()" class="w-full mt-2 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>
</body>
</html>