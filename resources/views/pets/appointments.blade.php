<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Appointments</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-10');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
        });

        function toggleModal() {
            const modal   = document.getElementById('profile-modal');
            const content = document.getElementById('profile-modal-content');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex','opacity-100'); }, 300);
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('request.appointment') }}" class="px-4 py-2 rounded-full text-sm bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">
                    <i class="bi bi-plus-circle mr-1"></i> New Appointment
                </a>
                <button onclick="toggleModal()" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all hover:scale-105 active:scale-95">
                    <i class="bi bi-person-circle text-xl"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3 text-sm">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-10 flex items-start justify-between reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div>
                <h1 class="text-2xl font-bold text-white mb-1">My Appointments</h1>
                <p class="text-slate-400 text-sm">Track your pending, confirmed, and past appointments.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">
                ← Back
            </a>
        </header>

        @if($appointments->isEmpty())
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-16 text-center">
                <i class="bi bi-calendar-x text-5xl text-slate-600 mb-4 block"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Appointments Yet</h3>
                <p class="text-slate-400 mb-6">You haven't booked any appointments.</p>
                <a href="{{ route('request.appointment') }}" class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-105">
                    <i class="bi bi-plus-circle mr-2"></i>Request Appointment
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($appointments as $appt)
                    @php
                        $borderMap = ['pending'=>'border-l-amber-500','approved'=>'border-l-emerald-500','rejected'=>'border-l-red-500','completed'=>'border-l-blue-500','cancelled'=>'border-l-slate-600'];
                        $border = $borderMap[$appt->status] ?? 'border-l-slate-600';
                        $canEdit = $appt->isPending() || $appt->isApproved();
                    @endphp

                    <div class="bg-slate-900/40 border border-slate-800/80 border-l-4 {{ $border }} rounded-2xl p-5 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400 shrink-0">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="font-bold text-white">{{ $appt->pet->name }}</h5>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                                    </div>
                                    <p class="text-slate-400 text-sm">
                                        <i class="bi bi-scissors mr-1 text-teal-500"></i>{{ $appt->service_label }}
                                        &bull;
                                        <i class="bi bi-clock mr-1 text-teal-500"></i>{{ $appt->appointment_date->format('M d, Y — g:i A') }}
                                    </p>
                                    @if($appt->notes)
                                        <p class="text-slate-500 text-xs mt-1"><i class="bi bi-chat-left-text mr-1"></i>{{ $appt->notes }}</p>
                                    @endif
                                    @if($appt->isRejected() && $appt->rejection_reason)
                                        <p class="text-red-400 text-xs mt-2 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                            <i class="bi bi-info-circle mr-1"></i><span class="font-semibold">Reason:</span> {{ $appt->rejection_reason }}
                                        </p>
                                    @endif
                                    @if($appt->isApproved())
                                        <p class="text-amber-400 text-xs mt-1"><i class="bi bi-exclamation-circle mr-1"></i>Editing will reset status to pending.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Action buttons -->
                            @if($canEdit)
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('appointments.edit', $appt) }}"
                                       class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold transition-all">
                                        <i class="bi bi-pencil mr-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Cancel this appointment?')"
                                                class="px-3 py-2 rounded-xl bg-red-900/30 hover:bg-red-900/50 text-red-400 border border-red-500/20 text-sm font-semibold transition-all">
                                            <i class="bi bi-x-circle mr-1"></i>Cancel
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination + View all history -->
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('appointments.history') }}"
                   class="text-sm text-teal-400 hover:text-teal-300 transition-all flex items-center gap-1">
                    <i class="bi bi-clock-history"></i> View full appointment history
                </a>
                @if($appointments->hasPages())
                    <div>{{ $appointments->links() }}</div>
                @endif
            </div>
        @endif
    </main>

    <!-- Profile Modal -->
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
                <div class="flex gap-3 mt-4">
                    <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-center text-sm font-semibold transition-all">Edit Profile</a>
                    <button onclick="toggleModal()" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>