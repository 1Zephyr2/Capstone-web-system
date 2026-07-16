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
        let currentYear, currentMonth, selectedDate = null;
        let fullyBookedDates = new Set();
        const clinicHours = @json($clinicHours);
        const availabilityUrl = "{{ route('appointments.availability') }}";
        const monthAvailabilityUrl = "{{ route('appointments.availability.month') }}";

        document.addEventListener('DOMContentLoaded', () => {
            const now = new Date();
            currentYear = now.getFullYear(); currentMonth = now.getMonth();
            loadMonthAvailability(currentYear, currentMonth + 1);
        });

        function loadMonthAvailability(year, month) {
            fetch(`${monthAvailabilityUrl}?year=${year}&month=${month}`)
                .then(res => res.json())
                .then(data => {
                    fullyBookedDates = new Set(data.fully_booked_dates || []);
                    renderCalendar(currentYear, currentMonth);
                })
                .catch(() => {
                    fullyBookedDates = new Set();
                    renderCalendar(currentYear, currentMonth);
                });
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
            document.getElementById('booking-sheet').classList.remove('hidden');
            document.getElementById('booking-placeholder').classList.add('hidden');
            document.getElementById('selected-date-label').innerText = monthName + ' ' + day + ', ' + year;
            document.getElementById('appointment_date_hidden').value = dateStr;
            loadDayAvailability(dateStr);
            setTimeout(() => document.getElementById('booking-sheet').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
        }

        function loadDayAvailability(dateStr) {
            const mobileContainer = document.getElementById('slots-mobile');
            const desktopBody     = document.getElementById('slots-desktop-body');
            const fullyBookedBanner = document.getElementById('fully-booked-banner');
            const slotsWrapper    = document.getElementById('slots-wrapper');

            mobileContainer.innerHTML = '<p class="text-gray-400 text-sm text-center py-6">Loading slots...</p>';
            desktopBody.innerHTML = '';
            fullyBookedBanner.classList.add('hidden');
            slotsWrapper.classList.remove('hidden');

            fetch(`${availabilityUrl}?date=${dateStr}`)
                .then(res => res.json())
                .then(data => {
                    if (data.fully_booked) {
                        slotsWrapper.classList.add('hidden');
                        fullyBookedBanner.classList.remove('hidden');
                        return;
                    }

                    const booked = new Set(data.booked || []);
                    mobileContainer.innerHTML = '';
                    desktopBody.innerHTML = '';

                    Object.entries(clinicHours).forEach(([value, label]) => {
                        const isBooked = booked.has(value);

                        const card = document.createElement('div');
                        card.className = 'flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3';
                        card.innerHTML = `
                            <span class="font-bold ${isBooked ? 'text-gray-400 line-through' : 'text-emerald-700'} text-sm">${label}</span>
                            ${isBooked
                                ? `<span class="px-3 py-1.5 rounded-lg text-xs bg-gray-100 text-gray-400 font-semibold">Booked</span>`
                                : `<button type="button" onclick="openServiceModal('${value}', '${label}')" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">+ Request</button>`
                            }`;
                        mobileContainer.appendChild(card);

                        const row = document.createElement('tr');
                        row.className = isBooked ? 'bg-gray-50/60' : 'hover:bg-emerald-50 transition-all';
                        row.innerHTML = `
                            <td class="px-6 py-4 font-bold ${isBooked ? 'text-gray-400 line-through' : 'text-emerald-700'}">${label}</td>
                            <td class="px-6 py-4">
                                ${isBooked
                                    ? `<span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200 uppercase">Booked</span>`
                                    : `<span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Open Slot</span>`
                                }
                            </td>
                            <td class="px-6 py-4 text-right">
                                ${isBooked
                                    ? `<span class="text-gray-300 text-xs font-semibold">Unavailable</span>`
                                    : `<button type="button" onclick="openServiceModal('${value}', '${label}')" class="px-5 py-2 rounded-xl text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all hover:shadow-md">+ Request</button>`
                                }
                            </td>`;
                        desktopBody.appendChild(row);
                    });
                })
                .catch(() => {
                    mobileContainer.innerHTML = '<p class="text-red-400 text-sm text-center py-6">Could not load availability. Please try again.</p>';
                });
        }

        function openServiceModal(timeValue, timeLabel) {
            if (!selectedDate) { alert('Please select a date first.'); return; }
            const petSelect = document.getElementById('pet_select');
            const serviceSelect = document.getElementById('service_select');
            document.getElementById('appointment_time').value    = timeValue;
            document.getElementById('hidden_date').value         = selectedDate;
            document.getElementById('hidden_pet_id').value       = petSelect.value;
            document.getElementById('hidden_service_id').value   = serviceSelect.value;
            document.getElementById('selected_time_display').innerText   = timeLabel;
            document.getElementById('confirm_date_display').innerText    = document.getElementById('selected-date-label').innerText;
            document.getElementById('confirm_pet_display').innerText     = petSelect.options[petSelect.selectedIndex].text;
            document.getElementById('confirm_service_display').innerText = serviceSelect.options[serviceSelect.selectedIndex].text;
            showModal('service-modal','service-modal-content');
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
                <p class="text-gray-400 text-sm">Pick a date on the calendar, then choose a time slot.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all shrink-0">← Back</a>
        </header>

        @if($pets->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <svg class="inline w-[1em] h-[1em] text-5xl text-gray-300 mb-4 block" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No Pets Registered</h3>
                <p class="text-gray-400 mb-5 text-sm">Add a pet from your dashboard before booking.</p>
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold transition-all hover:bg-emerald-700">Go to Dashboard</a>
            </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Settings + Calendar -->
            <div class="space-y-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Which Pet?</label>
                        <div class="relative">
                            <svg class="inline w-[1em] h-[1em] absolute left-3 top-3 text-emerald-500 pointer-events-none text-sm" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg>
                            <select id="pet_select" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-9 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}">{{ $pet->name }} ({{ $pet->breed }})</option>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none text-sm"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Service Type</label>
                        <div class="relative">
                            <i class="bi bi-scissors absolute left-3 top-3 text-emerald-500 pointer-events-none text-sm"></i>
                            <select id="service_select" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-9 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                                @foreach($services as $category => $items)
                                    <optgroup label="{{ $category }}">
                                        @foreach($items as $svc)
                                            <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <i class="bi bi-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <button onclick="prevMonth()" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                            <i class="bi bi-chevron-left text-sm"></i>
                        </button>
                        <span id="cal-month-label" class="text-gray-900 font-bold text-sm"></span>
                        <button onclick="nextMonth()" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                            <i class="bi bi-chevron-right text-sm"></i>
                        </button>
                    </div>
                    <div id="cal-grid" class="grid grid-cols-7 gap-1 text-center"></div>
                    <p class="text-gray-400 text-xs text-center mt-3">Click a date to see available slots</p>
                </div>

                <!-- Legend -->
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 space-y-2 text-xs text-gray-500">
                    <p class="font-bold uppercase tracking-widest text-emerald-700 mb-2">Legend</p>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600 inline-block"></span> Open — click to request</div>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-300 inline-block"></span> Fully booked</div>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-gray-300 inline-block"></span> Past date</div>
                </div>
            </div>

            <!-- Booking Sheet -->
            <div class="lg:col-span-2">
                <input type="hidden" id="appointment_date_hidden">

                <div id="booking-placeholder" class="flex items-center justify-center py-20 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="text-center">
                        <i class="bi bi-calendar3 text-5xl text-gray-300 mb-4 block"></i>
                        <p class="text-gray-500 font-medium">Select a date on the calendar</p>
                        <p class="text-gray-400 text-sm mt-1">Available time slots will appear here</p>
                    </div>
                </div>

                <div id="booking-sheet" class="hidden bg-white border border-emerald-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3 bg-emerald-50">
                        <i class="bi bi-calendar-check text-emerald-600"></i>
                        <div>
                            <p class="text-gray-900 font-bold text-sm">Available Slots</p>
                            <p class="text-emerald-600 text-xs font-medium" id="selected-date-label"></p>
                        </div>
                    </div>

                    <!-- Fully booked message -->
                    <div id="fully-booked-banner" class="hidden p-10 text-center">
                        <i class="bi bi-calendar-x text-4xl text-red-300 mb-3 block"></i>
                        <p class="text-gray-900 font-bold">This date is fully booked</p>
                        <p class="text-gray-400 text-sm mt-1">Please choose another date on the calendar.</p>
                    </div>

                    <div id="slots-wrapper">
                        <!-- Mobile cards -->
                        <div id="slots-mobile" class="block sm:hidden p-4 space-y-2"></div>
                        <!-- Desktop table -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr class="text-xs uppercase text-gray-400 tracking-widest">
                                        <th class="px-6 py-4">Time</th>
                                        <th class="px-6 py-4">Availability</th>
                                        <th class="px-6 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="slots-desktop-body" class="divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- Confirm Modal -->
    <div id="service-modal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center sm:p-6 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
         onclick="if(event.target===this) closeModal('service-modal','service-modal-content')">
        <div id="service-modal-content" class="bg-white border border-gray-200 rounded-t-2xl sm:rounded-2xl p-6 sm:p-8 w-full sm:max-w-md shadow-xl transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Confirm Appointment</h2>
            <p class="text-gray-400 text-xs mb-5">Review your booking details before submitting.</p>
            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <input type="hidden" name="appointment_time" id="appointment_time">
                <input type="hidden" name="appointment_date" id="hidden_date">
                <input type="hidden" name="pet_id"           id="hidden_pet_id">
                <input type="hidden" name="service_id"       id="hidden_service_id">
                <div class="space-y-3 mb-5">
                    <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Time</label>
                        <p id="selected_time_display" class="text-emerald-700 font-bold bg-emerald-50 p-3 rounded-lg border border-emerald-200 text-sm"></p></div>
                    <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Date</label>
                        <p id="confirm_date_display" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm"></p></div>
                    <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Pet</label>
                        <p id="confirm_pet_display" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm"></p></div>
                    <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Service</label>
                        <p id="confirm_service_display" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm"></p></div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                            Additional Info <span class="normal-case font-normal text-gray-300">(optional)</span>
                        </label>
                        <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm"></textarea>
                    </div>
                </div>
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeModal('service-modal','service-modal-content')"
                            class="px-4 py-3 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm transition-all hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>