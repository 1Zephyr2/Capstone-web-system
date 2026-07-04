<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Appointment History</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('request.appointment') }}" class="px-4 py-2 rounded-full text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">
                    <i class="bi bi-plus-circle mr-1"></i> New
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-5 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">Appointment History</h1>
                <p class="text-slate-400 text-sm">All your appointments — past, present, and upcoming.</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">
                ← Back
            </a>
        </header>

        <!-- Filter tabs -->
        @php
            $statusCounts = $appointments->groupBy('status');
        @endphp

        @if($appointments->isEmpty())
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-12 text-center">
                <i class="bi bi-calendar-x text-4xl text-slate-600 mb-3 block"></i>
                <p class="text-slate-400">No appointment history yet.</p>
            </div>
        @else
            <!-- Table view -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between">
                    <h3 class="font-bold text-white text-sm">All Appointments</h3>
                    <span class="text-xs text-slate-500">{{ $appointments->total() }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-950/40 border-b border-slate-800/60">
                            <tr class="text-xs uppercase text-slate-500 tracking-wider">
                                <th class="px-6 py-3 text-left">Pet</th>
                                <th class="px-6 py-3 text-left">Service</th>
                                <th class="px-6 py-3 text-left">Date & Time</th>
                                <th class="px-6 py-3 text-left">Notes</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40">
                            @foreach($appointments as $appt)
                                <tr class="hover:bg-slate-800/20 transition-all">
                                    <td class="px-6 py-4 text-white font-medium">{{ $appt->pet->name }}</td>
                                    <td class="px-6 py-4 text-slate-300">{{ $appt->service_label }}</td>
                                    <td class="px-6 py-4 text-slate-300 whitespace-nowrap">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</td>
                                    <td class="px-6 py-4 text-slate-400 text-xs max-w-[160px] truncate">{{ $appt->notes ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                                        @if($appt->isRejected() && $appt->rejection_reason)
                                            <p class="text-red-400 text-xs mt-1">{{ $appt->rejection_reason }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($appt->isPending() || $appt->isApproved())
                                                <a href="{{ route('appointments.edit', $appt) }}"
                                                   class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                                                    <i class="bi bi-pencil mr-1"></i>Edit
                                                </a>
                                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" onclick="return confirm('Cancel this appointment?')"
                                                            class="px-3 py-1 rounded-lg bg-red-900/30 hover:bg-red-900/50 text-red-400 text-xs font-semibold transition-all">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-slate-600 text-xs">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($appointments->hasPages())
                <div class="mt-6 flex justify-center">{{ $appointments->links() }}</div>
            @endif
        @endif
    </main>
</body>
</html>