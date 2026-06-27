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
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll reveal
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-10');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));

            // Set min date to tomorrow
            const dateInput = document.getElementById('appointment_date');
            const tomorrow  = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dateInput.min = tomorrow.toISOString().split('T')[0];
        });

        // ── Open confirm modal and populate all fields ──────────────────
        function openServiceModal(timeValue, timeLabel) {
            const dateInput     = document.getElementById('appointment_date');
            const petSelect     = document.getElementById('pet_select');
            const serviceSelect = document.getElementById('service_select');

            // Guard: make sure a date is selected
            if (!dateInput.value) {
                alert('Please select a date first.');
                return;
            }

            // Populate hidden form inputs
            document.getElementById('appointment_time').value       = timeValue;
            document.getElementById('hidden_date').value            = dateInput.value;
            document.getElementById('hidden_pet_id').value          = petSelect.value;
            document.getElementById('hidden_service_type').value    = serviceSelect.value;

            // Populate display labels in the modal
            document.getElementById('selected_time_display').innerText   = timeLabel;
            document.getElementById('confirm_date_display').innerText    = formatDate(dateInput.value);
            document.getElementById('confirm_pet_display').innerText     = petSelect.options[petSelect.selectedIndex].text;
            document.getElementById('confirm_service_display').innerText = serviceSelect.options[serviceSelect.selectedIndex].text;

            showModal('service-modal', 'service-modal-content');
        }

        function closeServiceModal() {
            closeModal('service-modal', 'service-modal-content');
        }

        function toggleModal() {
            const modal = document.getElementById('profile-modal');
            if (modal.classList.contains('hidden')) {
                showModal('profile-modal', 'profile-modal-content');
            } else {
                closeModal('profile-modal', 'profile-modal-content');
            }
        }

        function showModal(id, contentId) {
            const modal   = document.getElementById(id);
            const content = document.getElementById(contentId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal(id, contentId) {
            const modal   = document.getElementById(id);
            const content = document.getElementById(contentId);
            modal.classList.remove('opacity-100');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
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
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all duration-300">
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

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-lg"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill text-lg"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-12 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Request an Appointment</h1>
                <p class="text-slate-400">Select a date, your pet, service, and an available time slot.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all hover:scale-105">
                ← Back to Dashboard
            </a>
        </header>

        @if($pets->isEmpty())
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-3xl p-12 text-center reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
                <i class="bi bi-paw text-5xl text-slate-600 mb-4 block"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Pets Registered</h3>
                <p class="text-slate-400 mb-6">You need to add a pet before booking an appointment.</p>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">
                    Go to Dashboard
                </a>
            </div>
        @else
        <div class="grid lg:grid-cols-4 gap-10">

            <!-- Settings Panel -->
            <div class="lg:col-span-1 space-y-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-100">
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-3xl p-8 shadow-xl">

                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Select Date</label>
                    <div class="relative mb-6">
                        <i class="bi bi-calendar3 absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                        <input type="date" id="appointment_date"
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all"
                               value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>

                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Which Pet?</label>
                    <div class="relative mb-6">
                        <i class="bi bi-paw absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                        <select id="pet_select" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}">{{ $pet->name }} ({{ $pet->breed }})</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-3 text-slate-500 pointer-events-none"></i>
                    </div>

                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Service Type</label>
                    <div class="relative">
                        <i class="bi bi-scissors absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                        <select id="service_select" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                            @foreach($serviceTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-3 text-slate-500 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Legend -->
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 space-y-2 text-xs text-slate-400">
                    <p class="font-bold uppercase tracking-widest text-slate-500 mb-3">Legend</p>
                    <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block"></span> Open — click to book</div>
                </div>
            </div>

            <!-- Time Slot Sheet -->
            <div class="lg:col-span-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out delay-200">
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-3xl overflow-hidden shadow-2xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-950/30 border-b border-slate-800">
                            <tr class="text-xs uppercase text-slate-400 tracking-widest">
                                <th class="px-8 py-6">Time</th>
                                <th class="px-8 py-6">Availability</th>
                                <th class="px-8 py-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach($clinicHours as $value => $label)
                                <tr class="hover:bg-teal-900/5 transition-colors">
                                    <td class="px-8 py-7 font-bold text-teal-400 text-lg">{{ $label }}</td>
                                    <td class="px-8 py-7">
                                        <span class="px-4 py-1.5 rounded-full text-xs font-medium bg-teal-500/10 text-teal-400 border border-teal-500/20 uppercase tracking-wide">
                                            Open Slot
                                        </span>
                                    </td>
                                    <td class="px-8 py-7 text-right">
                                        <button type="button"
                                                onclick="openServiceModal('{{ $value }}', '{{ $label }}')"
                                                class="px-6 py-2.5 rounded-xl text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105 active:scale-95 shadow-lg shadow-teal-900/20">
                                            + Request
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- ── Confirm Booking Modal ──────────────────────────────────────── -->
    <div id="service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeServiceModal()">
        <div id="service-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-2">Confirm Appointment</h2>
            <p class="text-slate-400 text-sm mb-6">Review your booking details before submitting.</p>

            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                {{-- Hidden inputs populated by JS --}}
                <input type="hidden" name="appointment_time" id="appointment_time">
                <input type="hidden" name="appointment_date" id="hidden_date">
                <input type="hidden" name="pet_id"          id="hidden_pet_id">
                <input type="hidden" name="service_type"    id="hidden_service_type">

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Selected Time</label>
                        <p id="selected_time_display" class="text-teal-400 font-bold text-lg bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
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
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Notes <span class="normal-case font-normal">(optional)</span></label>
                        <textarea name="notes" rows="2" placeholder="Any special instructions..."
                                  class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm"></textarea>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="closeServiceModal()"
                            class="px-4 py-3 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all font-semibold">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105 active:scale-95">
                        Submit Request
                    </button>
                </div>
            </form>
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
                <button type="button" onclick="toggleModal()"
                        class="w-full mt-2 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
            </div>
        </div>
    </div>

</body>
</html>