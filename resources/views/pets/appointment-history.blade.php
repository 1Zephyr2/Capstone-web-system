<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Appointment History</title>
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
                <a href="{{ route('request.appointment') }}" class="px-4 py-2 rounded-full text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">
                    <i class="bi bi-plus-circle mr-1"></i> New
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
            <a href="{{ route('request.appointment') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold"><i class="bi bi-plus-circle"></i> New Appointment</a>
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

        <header class="mb-8 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Appointment History</h1>
                <p class="text-gray-400 text-sm">All your appointments — past, present, and upcoming.</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all shrink-0">← Back</a>
        </header>

        @if($appointments->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <i class="bi bi-calendar-x text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-400">No appointment history yet.</p>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <h3 class="font-bold text-gray-900 text-sm">All Appointments</h3>
                    <span class="text-xs text-gray-400">{{ $appointments->total() }} total</span>
                </div>
                <!-- Mobile cards -->
                <div class="block sm:hidden divide-y divide-gray-100">
                    @foreach($appointments as $appt)
                        @php
                            $badgeClass = match($appt->status) {
                                'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                                'approved'  => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                'rejected'  => 'bg-red-100 text-red-600 border border-red-200',
                                'completed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                'cancelled' => 'bg-gray-100 text-gray-500 border border-gray-200',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $appt->pet->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $appt->service_label }} · {{ $appt->appointment_date->format('M d, Y g:i A') }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }} shrink-0">{{ ucfirst($appt->status) }}</span>
                            </div>
                            @if($appt->isPending() || $appt->isApproved())
                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('appointments.edit', $appt) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs font-semibold transition-all hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                                        @csrf @method('PATCH')
                                        <button onclick="return confirm('Cancel?')" class="px-3 py-1.5 rounded-lg bg-red-50 border border-red-100 text-red-500 text-xs font-semibold transition-all">Cancel</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <!-- Desktop table -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-xs uppercase text-gray-400 tracking-wider">
                                <th class="px-5 py-3 text-left">Pet</th>
                                <th class="px-5 py-3 text-left">Service</th>
                                <th class="px-5 py-3 text-left">Date & Time</th>
                                <th class="px-5 py-3 text-left">Notes</th>
                                <th class="px-5 py-3 text-left">Status</th>
                                <th class="px-5 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($appointments as $appt)
                                @php
                                    $badgeClass = match($appt->status) {
                                        'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                                        'approved'  => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                        'rejected'  => 'bg-red-100 text-red-600 border border-red-200',
                                        'completed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                        'cancelled' => 'bg-gray-100 text-gray-500 border border-gray-200',
                                        default     => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-5 py-4 text-gray-900 font-medium">{{ $appt->pet->name }}</td>
                                    <td class="px-5 py-4 text-gray-600 text-sm">{{ $appt->service_label }}</td>
                                    <td class="px-5 py-4 text-gray-600 text-xs whitespace-nowrap">{{ $appt->appointment_date->format('M d, Y — g:i A') }}</td>
                                    <td class="px-5 py-4 text-gray-400 text-xs max-w-[140px] truncate">{{ $appt->notes ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ ucfirst($appt->status) }}</span>
                                        @if($appt->isRejected() && $appt->rejection_reason)
                                            <p class="text-red-400 text-xs mt-1">{{ $appt->rejection_reason }}</p>
                                        @endif
                                        @if($appt->isCompleted())
                                            @if($appt->result_photo_url)
                                                <img src="{{ $appt->result_photo_url }}" alt="Result" class="w-12 h-12 object-cover rounded-lg border border-gray-200 mt-1">
                                            @endif
                                            @if($appt->different_pickup)
                                                <p class="text-gray-400 text-xs mt-1">Picked up by: {{ $appt->picked_up_by }}</p>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($appt->isPending() || $appt->isApproved())
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('appointments.edit', $appt) }}" class="px-3 py-1 rounded-lg border border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-all">Edit</a>
                                                <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                                                    @csrf @method('PATCH')
                                                    <button onclick="return confirm('Cancel?')" class="px-3 py-1 rounded-lg bg-red-50 border border-red-100 text-red-500 text-xs font-semibold transition-all">Cancel</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
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