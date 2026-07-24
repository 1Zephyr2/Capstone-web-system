<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Appointments</title>
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
        function openPhotoLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            const lightbox = document.getElementById('photo-lightbox');
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }
        function closePhotoLightbox() {
            const lightbox = document.getElementById('photo-lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }
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
                    <i class="bi bi-plus-circle mr-1"></i> New Appointment
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 transition-all">Logout</button>
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
        @if(session('error'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 flex items-center gap-3 text-sm">
                <i class="bi bi-exclamation-circle-fill shrink-0"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">My Appointments</h1>
                <p class="text-gray-400 text-sm">Track your pending, confirmed, and past appointments.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all shrink-0">← Back</a>
        </header>

        @php
            $statusFilters = [
                ''          => 'All',
                'pending'   => 'Pending',
                'approved'  => 'Confirmed',
                'completed' => 'Completed',
                'rejected'  => 'Rejected',
                'cancelled' => 'Cancelled',
            ];
            $currentStatus = request('status', '');
        @endphp
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach($statusFilters as $value => $label)
                <a href="{{ route('appointments.index', $value ? ['status' => $value] : []) }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold transition-all {{ $currentStatus === $value ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if($appointments->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-16 text-center shadow-sm">
                <i class="bi bi-calendar-x text-5xl text-gray-300 mb-4 block"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $currentStatus ? 'No ' . $statusFilters[$currentStatus] . ' Appointments' : 'No Appointments Yet' }}</h3>
                <p class="text-gray-400 mb-6">{{ $currentStatus ? 'Try a different filter above.' : "You haven't booked any appointments." }}</p>
                @if(!$currentStatus)
                    <a href="{{ route('request.appointment') }}" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all inline-block">
                        <i class="bi bi-plus-circle mr-2"></i>Request Appointment
                    </a>
                @endif
            </div>
        @else
            <div class="space-y-3">
                @foreach($appointments as $appt)
                    @php
                        $borderMap = ['pending'=>'border-l-amber-400','approved'=>'border-l-emerald-500','rejected'=>'border-l-red-400','completed'=>'border-l-blue-400','cancelled'=>'border-l-gray-300'];
                        $border = $borderMap[$appt->status] ?? 'border-l-gray-300';
                        $badgeClass = match($appt->status) {
                            'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                            'approved'  => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                            'rejected'  => 'bg-red-100 text-red-600 border border-red-200',
                            'completed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                            'cancelled' => 'bg-gray-100 text-gray-500 border border-gray-200',
                            default     => 'bg-gray-100 text-gray-500',
                        };
                        $canEdit = $appt->isPending() || $appt->isApproved();
                    @endphp
                    <div class="bg-white border border-gray-200 border-l-4 {{ $border }} rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="font-bold text-gray-900">{{ $appt->pet->name }}</h5>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ ucfirst($appt->status) }}</span>
                                    </div>
                                    <p class="text-gray-500 text-sm">
                                        <i class="bi bi-scissors mr-1 text-emerald-500"></i>{{ $appt->service_label }}
                                        &bull;
                                        <i class="bi bi-clock mr-1 text-emerald-500"></i>{{ $appt->appointment_date->format('M d, Y — g:i A') }}
                                    </p>
                                    @if($appt->notes)
                                        <p class="text-gray-400 text-xs mt-1"><i class="bi bi-chat-left-text mr-1"></i>{{ $appt->notes }}</p>
                                    @endif
                                    @if($appt->isRejected() && $appt->rejection_reason)
                                        <p class="text-red-500 text-xs mt-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                            <i class="bi bi-info-circle mr-1"></i><span class="font-semibold">Reason:</span> {{ $appt->rejection_reason }}
                                        </p>
                                    @endif
                                    @if($appt->isCompleted())
                                        @if($appt->result_photo_url)
                                            <div class="mt-2">
                                                <img src="{{ $appt->result_photo_url }}" alt="Result photo"
                                                     onclick="openPhotoLightbox(this.src)"
                                                     class="w-24 h-24 object-cover rounded-lg border border-gray-200 cursor-zoom-in hover:opacity-90 transition-all">
                                            </div>
                                        @endif
                                        @if($appt->different_pickup)
                                            <p class="text-gray-500 text-xs mt-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                                <i class="bi bi-person-check mr-1"></i><span class="font-semibold">Picked up by:</span> {{ $appt->picked_up_by }}
                                                @if($appt->pickup_note) — {{ $appt->pickup_note }} @endif
                                            </p>
                                        @endif
                                    @endif
                                    @if($appt->isApproved())
                                        <p class="text-amber-500 text-xs mt-1"><i class="bi bi-exclamation-circle mr-1"></i>Editing will reset status to pending.</p>
                                    @endif
                                </div>
                            </div>
                            @if($canEdit)
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('appointments.edit', $appt) }}"
                                       class="px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold transition-all">
                                        <i class="bi bi-pencil mr-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Cancel this appointment?')"
                                                class="px-3 py-2 rounded-xl bg-red-50 border border-red-100 text-red-500 text-sm font-semibold transition-all hover:bg-red-100">
                                            <i class="bi bi-x-circle mr-1"></i>Cancel
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('appointments.history') }}" class="text-sm text-emerald-600 hover:text-emerald-700 transition-all flex items-center gap-1">
                    <i class="bi bi-clock-history"></i> View full appointment history
                </a>
                @if($appointments->hasPages())
                    <div>{{ $appointments->links() }}</div>
                @endif
            </div>
        @endif
    </main>

    <!-- Result Photo Lightbox -->
    <div id="photo-lightbox" class="fixed inset-0 z-[60] hidden items-center justify-center p-6 bg-black/80 backdrop-blur-sm"
         onclick="if(event.target===this) closePhotoLightbox()">
        <button type="button" onclick="closePhotoLightbox()"
                class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all">
            <i class="bi bi-x-lg"></i>
        </button>
        <img id="lightbox-img" src="" alt="Result photo full size" class="max-w-full max-h-full rounded-xl shadow-2xl">
    </div>
</body>
</html>