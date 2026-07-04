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
            // Set min date to tomorrow
            const dateInput = document.getElementById('appointment_date');
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dateInput.min = tomorrow.toISOString().split('T')[0];
        });
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 max-w-2xl">

        <header class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Edit Appointment</h1>
                <p class="text-slate-400 text-sm">
                    Update your appointment details.
                    @if($appointment->isApproved())
                        <span class="text-amber-400">Note: editing will reset status to pending.</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">
                ← Back
            </a>
        </header>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
        @endif

        <!-- Current appointment info -->
        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-5 mb-6 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400 shrink-0">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                <p class="text-white font-semibold">{{ $appointment->pet->name }}</p>
                <p class="text-slate-400 text-xs">Current: {{ $appointment->service_label }} · {{ $appointment->appointment_date->format('M d, Y — g:i A') }}</p>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appointment->status_badge_class }}">{{ ucfirst($appointment->status) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('appointments.update', $appointment) }}"
              class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8 space-y-5">
            @csrf @method('PATCH')

            <!-- Pet (read-only — can't change pet) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Pet</label>
                <div class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-slate-400 text-sm">
                    {{ $appointment->pet->name }} ({{ $appointment->pet->breed }})
                </div>
                <p class="text-slate-600 text-xs mt-1">Pet cannot be changed. Cancel and rebook to use a different pet.</p>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">New Date</label>
                <div class="relative">
                    <i class="bi bi-calendar3 absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                    <input type="date" id="appointment_date" name="appointment_date"
                           value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                           required
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all">
                </div>
            </div>

            <!-- Time -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">New Time</label>
                <div class="relative">
                    <i class="bi bi-clock absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                    <select name="appointment_time" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                        @foreach($clinicHours as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('appointment_time', $appointment->appointment_date->format('H:i')) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-4 top-3 text-slate-500 pointer-events-none"></i>
                </div>
            </div>

            <!-- Service Type -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Service Type</label>
                <div class="relative">
                    <i class="bi bi-scissors absolute left-4 top-3 text-teal-500 pointer-events-none"></i>
                    <select name="service_type" required
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-10 py-3 text-white outline-none focus:border-teal-500 transition-all appearance-none">
                        @foreach($serviceTypes as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('service_type', $appointment->service_type) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down absolute right-4 top-3 text-slate-500 pointer-events-none"></i>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                    Additional Info <span class="normal-case font-normal text-slate-600">(optional)</span>
                </label>
                <textarea name="notes" rows="3"
                          placeholder="Any special instructions or notes..."
                          class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all resize-none text-sm">{{ old('notes', $appointment->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('appointments.index') }}"
                   class="flex-1 px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-center font-semibold transition-all">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02]">
                    Save Changes
                </button>
            </div>
        </form>
    </main>
</body>
</html>