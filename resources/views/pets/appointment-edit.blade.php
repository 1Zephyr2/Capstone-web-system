<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Edit Appointment</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('appointment_date');
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dateInput.min = tomorrow.toISOString().split('T')[0];
        });
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
            </a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-4 sm:px-6 py-8 max-w-2xl">

        <header class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Edit Appointment</h1>
                <p class="text-gray-400 text-sm">
                    Update your appointment details.
                    @if($appointment->isApproved())
                        <span class="text-amber-500">Note: editing will reset status to pending.</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all shrink-0">← Back</a>
        </header>

        @if($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
        @endif

        <!-- Current info card -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mb-6 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                <p class="text-emerald-800 font-semibold text-sm">{{ $appointment->pet->name }}</p>
                <p class="text-emerald-600 text-xs">Current: {{ $appointment->service_label }} · {{ $appointment->appointment_date->format('M d, Y — g:i A') }}</p>
                @php
                    $badgeClass = match($appointment->status) {
                        'pending'  => 'bg-amber-100 text-amber-700 border border-amber-200',
                        'approved' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                        default    => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }} mt-1 inline-block">{{ ucfirst($appointment->status) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('appointments.update', $appointment) }}"
              class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
            @csrf @method('PATCH')

            <!-- Pet read-only -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Pet</label>
                <div class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-400 text-sm">
                    {{ $appointment->pet->name }} ({{ $appointment->pet->breed }})
                </div>
                <p class="text-gray-400 text-xs mt-1">Pet cannot be changed. Cancel and rebook to use a different pet.</p>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">New Date</label>
                <div class="relative">
                    <i class="bi bi-calendar3 absolute left-4 top-3 text-emerald-500 pointer-events-none"></i>
                    <input type="date" id="appointment_date" name="appointment_date"
                           value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                           required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-10 py-3 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                </div>
            </div>

            <!-- Time -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">New Time</label>
                <div class="relative">
                    <i class="bi bi-clock absolute left-4 top-3 text-emerald-500 pointer-events-none"></i>
                    <select name="appointment_time" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-10 py-3 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                        @foreach($clinicHours as $value => $label)
                            <option value="{{ $value }}" {{ old('appointment_time', $appointment->appointment_date->format('H:i')) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            <!-- Service -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Service Type</label>
                <div class="relative">
                    <i class="bi bi-scissors absolute left-4 top-3 text-emerald-500 pointer-events-none"></i>
                    <select name="service_id" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-10 py-3 text-gray-900 outline-none focus:border-emerald-500 transition-all appearance-none text-sm">
                        @foreach($services as $category => $items)
                            <optgroup label="{{ $category }}">
                                @foreach($items as $svc)
                                    <option value="{{ $svc->id }}" {{ (int) old('service_id', $appointment->service_id) === $svc->id ? 'selected' : '' }}>
                                        {{ $svc->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                </div>
                @if(!$appointment->service_id)
                    <p class="text-amber-500 text-xs mt-1">
                        <i class="bi bi-exclamation-circle mr-1"></i>This appointment used an older service type ("{{ $appointment->service_label }}"). Please pick a current service to continue.
                    </p>
                @endif
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">
                    Additional Info <span class="normal-case font-normal text-gray-300">(optional)</span>
                </label>
                <textarea name="notes" rows="3" placeholder="Any special instructions or notes..."
                          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-emerald-500 transition-all resize-none text-sm">{{ old('notes', $appointment->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('appointments.index') }}"
                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-600 text-center font-semibold transition-all hover:bg-gray-50 text-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all text-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </main>
</body>
</html>