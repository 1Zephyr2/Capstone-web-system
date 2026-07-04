<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Appointments</title>
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

        function openDetailModal(id, pet, owner, service, datetime, status, notes, rejectRoute, approveRoute, completeRoute, cancelRoute, editNotesRoute) {
            document.getElementById('modal-pet').innerText      = pet;
            document.getElementById('modal-owner').innerText    = owner;
            document.getElementById('modal-service').innerText  = service;
            document.getElementById('modal-datetime').innerText = datetime;
            document.getElementById('modal-notes-display').innerText = notes || '—';
            document.getElementById('modal-notes-input').value = notes || '';

            const statusEl = document.getElementById('modal-status');
            statusEl.innerText = status.charAt(0).toUpperCase() + status.slice(1);
            statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide ' + statusBadgeClass(status);

            document.getElementById('btn-approve').classList.toggle('hidden', status !== 'pending');
            document.getElementById('btn-reject-wrap').classList.toggle('hidden', status !== 'pending');
            document.getElementById('btn-complete').classList.toggle('hidden', status !== 'approved');
            document.getElementById('btn-cancel').classList.toggle('hidden', status !== 'approved');

            document.getElementById('form-approve').action  = approveRoute;
            document.getElementById('form-complete').action = completeRoute;
            document.getElementById('form-cancel').action   = cancelRoute;
            document.getElementById('form-reject').action   = rejectRoute;
            document.getElementById('form-edit-notes').action = editNotesRoute;

            // Reset notes edit mode
            showNotesView();

            showModal('detail-modal', 'detail-modal-content');
        }

        function showNotesView() {
            document.getElementById('notes-view').classList.remove('hidden');
            document.getElementById('notes-edit').classList.add('hidden');
        }

        function showNotesEdit() {
            document.getElementById('notes-view').classList.add('hidden');
            document.getElementById('notes-edit').classList.remove('hidden');
            document.getElementById('modal-notes-input').focus();
        }

        function openRejectModal() {
            closeModal('detail-modal', 'detail-modal-content');
            setTimeout(() => showModal('reject-modal', 'reject-modal-content'), 200);
        }

        function closeRejectModal() {
            closeModal('reject-modal', 'reject-modal-content');
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

        function statusBadgeClass(status) {
            const map = {
                pending:   'bg-amber-500/20 text-amber-400 border border-amber-500/30',
                approved:  'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                rejected:  'bg-red-500/20 text-red-400 border border-red-500/30',
                completed: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                cancelled: 'bg-slate-600/40 text-slate-400 border border-slate-600/40',
            };
            return map[status] || 'bg-slate-700 text-slate-300';
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    @php $isAdmin = auth()->user()->role === 'admin'; @endphp
    @php $prefix  = $isAdmin ? 'admin' : 'staff'; @endphp

    <nav class="relative z-50 w-full bg-[#0c1220] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix . '.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $isAdmin ? 'text-rose-300 bg-rose-500/20 border-rose-500/30' : 'text-violet-300 bg-violet-500/20 border-violet-500/30' }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md border">
                    {{ $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL' }}
                </span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="{{ route($prefix . '.dashboard') }}"    class="hover:text-white transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix . '.directory') }}"    class="hover:text-white transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix . '.appointments') }}" class="text-white font-semibold transition-all hover:scale-105">Appointments</a>
                <a href="{{ route($prefix . '.insights') }}"     class="hover:text-white transition-all hover:scale-105">Insights</a>
            </div>
            <form action="{{ route($isAdmin ? 'admin.logout' : 'staff.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 transition-all text-white">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center gap-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <h1 class="text-2xl font-bold text-white">Appointment Management</h1>
            <p class="text-slate-400 text-sm">Review, approve, and manage all appointment requests.</p>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-3 gap-4 mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5">
                <i class="bi bi-hourglass-split text-amber-400 text-xl mb-2 block"></i>
                <p class="text-slate-400 text-xs mb-1">Pending</p>
                <p class="text-2xl font-bold text-white">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5">
                <i class="bi bi-calendar-check text-emerald-400 text-xl mb-2 block"></i>
                <p class="text-slate-400 text-xs mb-1">Approved</p>
                <p class="text-2xl font-bold text-white">{{ $stats['approved'] }}</p>
            </div>
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-5">
                <i class="bi bi-sun text-blue-400 text-xl mb-2 block"></i>
                <p class="text-slate-400 text-xs mb-1">Today's Schedule</p>
                <p class="text-2xl font-bold text-white">{{ $stats['today'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route($prefix . '.appointments') }}"
              class="flex flex-wrap gap-3 mb-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm outline-none focus:border-violet-500 transition-all">
            <select name="status" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm outline-none focus:border-violet-500 transition-all appearance-none">
                <option value="">All Statuses</option>
                @foreach($statusOptions as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="service" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm outline-none focus:border-violet-500 transition-all appearance-none">
                <option value="">All Services</option>
                @foreach($serviceTypes as $key => $label)
                    <option value="{{ $key }}" {{ request('service') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold transition-all hover:scale-105">
                <i class="bi bi-funnel mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['date','status','service']))
                <a href="{{ route($prefix . '.appointments') }}" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold transition-all">Clear</a>
            @endif
        </form>

        <!-- Appointments List -->
        <div class="space-y-3">
            @forelse($appointments as $appt)
                @php
                    $accentMap = ['pending'=>'border-l-amber-500','approved'=>'border-l-emerald-500','rejected'=>'border-l-red-500','completed'=>'border-l-blue-500','cancelled'=>'border-l-slate-600'];
                    $accent = $accentMap[$appt->status] ?? 'border-l-slate-600';
                    $approveRoute  = route($prefix.'.appointments.approve',  $appt);
                    $rejectRoute   = route($prefix.'.appointments.reject',   $appt);
                    $completeRoute = route($prefix.'.appointments.complete', $appt);
                    $cancelRoute   = route($prefix.'.appointments.cancel',   $appt);
                    $editNotesRoute = route($prefix.'.appointments.notes',   $appt);
                @endphp
                <div onclick="openDetailModal(
                        {{ $appt->id }},
                        '{{ addslashes($appt->pet->name . ' (' . $appt->pet->breed . ')') }}',
                        '{{ addslashes($appt->user->name) }}',
                        '{{ addslashes($appt->service_label) }}',
                        '{{ $appt->appointment_date->format('M d, Y — g:i A') }}',
                        '{{ $appt->status }}',
                        '{{ addslashes($appt->notes ?? '') }}',
                        '{{ $rejectRoute }}',
                        '{{ $approveRoute }}',
                        '{{ $completeRoute }}',
                        '{{ $cancelRoute }}',
                        '{{ $editNotesRoute }}'
                     )"
                     class="bg-slate-900/40 border border-slate-800/80 border-l-4 {{ $accent }} rounded-xl p-5 hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(139,92,246,0.08)] reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out flex items-center justify-between cursor-pointer">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-full bg-violet-500/20 flex items-center justify-center text-violet-400 shrink-0">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-white">{{ $appt->pet->name }} <span class="text-slate-500 font-normal text-sm">({{ $appt->pet->breed }})</span></h5>
                            <p class="text-sm text-slate-400">
                                <span class="text-slate-300">{{ $appt->user->name }}</span>
                                &bull; {{ $appt->service_label }}
                                &bull; {{ $appt->appointment_date->format('M d, Y — g:i A') }}
                            </p>
                            @if($appt->notes)
                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-sm"><i class="bi bi-chat-left-text mr-1"></i>{{ $appt->notes }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                        <i class="bi bi-chevron-right text-slate-600"></i>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-12 text-center">
                    <i class="bi bi-calendar-x text-4xl text-slate-600 mb-3 block"></i>
                    <p class="text-slate-400">No appointments found.</p>
                </div>
            @endforelse
        </div>

        @if($appointments->hasPages())
            <div class="mt-8 flex justify-center">{{ $appointments->withQueryString()->links() }}</div>
        @endif
    </main>

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('detail-modal','detail-modal-content')">
        <div id="detail-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-white mb-6">Appointment Details</h2>

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Pet</label>
                    <p id="modal-pet" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Owner</label>
                    <p id="modal-owner" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Service</label>
                    <p id="modal-service" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Date & Time</label>
                    <p id="modal-datetime" class="text-white bg-slate-950 p-3 rounded-lg border border-slate-800"></p>
                </div>

                <!-- Notes — view + inline edit -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Notes</label>
                        <button type="button" onclick="showNotesEdit()" id="notes-edit-btn"
                                class="text-xs text-violet-400 hover:text-violet-300 transition-all">
                            <i class="bi bi-pencil mr-1"></i>Edit
                        </button>
                    </div>
                    <!-- View mode -->
                    <div id="notes-view">
                        <p id="modal-notes-display" class="text-slate-300 bg-slate-950 p-3 rounded-lg border border-slate-800 text-sm min-h-[44px]"></p>
                    </div>
                    <!-- Edit mode -->
                    <div id="notes-edit" class="hidden">
                        <form id="form-edit-notes" method="POST">
                            @csrf @method('PATCH')
                            <textarea id="modal-notes-input" name="notes" rows="3"
                                      class="w-full bg-slate-950 border border-violet-500 rounded-lg px-3 py-2.5 text-white outline-none text-sm resize-none mb-2"></textarea>
                            <div class="flex gap-2">
                                <button type="button" onclick="showNotesView()"
                                        class="flex-1 px-3 py-2 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-semibold transition-all">Cancel</button>
                                <button type="submit"
                                        class="flex-1 px-3 py-2 rounded-lg bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold transition-all">Save Notes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                    <span id="modal-status" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide"></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <form id="form-approve" method="POST">
                    @csrf @method('PATCH')
                    <button id="btn-approve" type="submit" class="w-full px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition-all hover:scale-[1.02]">
                        <i class="bi bi-check-circle mr-2"></i>Approve Appointment
                    </button>
                </form>
                <div id="btn-reject-wrap">
                    <button type="button" onclick="openRejectModal()" class="w-full px-4 py-3 rounded-xl bg-red-900/40 hover:bg-red-900/60 text-red-400 border border-red-500/30 font-semibold transition-all">
                        <i class="bi bi-x-circle mr-2"></i>Reject Appointment
                    </button>
                </div>
                <form id="form-complete" method="POST">
                    @csrf @method('PATCH')
                    <button id="btn-complete" type="submit" class="w-full px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold transition-all hover:scale-[1.02]">
                        <i class="bi bi-check2-all mr-2"></i>Mark as Completed
                    </button>
                </form>
                <form id="form-cancel" method="POST">
                    @csrf @method('PATCH')
                    <button id="btn-cancel" type="submit" onclick="return confirm('Cancel this appointment?')"
                            class="w-full px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition-all">
                        <i class="bi bi-slash-circle mr-2"></i>Cancel Appointment
                    </button>
                </form>
                <button type="button" onclick="closeModal('detail-modal','detail-modal-content')"
                        class="w-full px-4 py-2 rounded-xl bg-transparent text-slate-500 hover:text-slate-300 text-sm transition-all">Close</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeRejectModal()">
        <div id="reject-modal-content" class="bg-slate-900 border border-slate-800 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-2">Reject Appointment</h2>
            <p class="text-slate-400 text-sm mb-6">Optionally provide a reason for the owner.</p>
            <form id="form-reject" method="POST">
                @csrf @method('PATCH')
                <textarea name="rejection_reason" rows="3" placeholder="e.g. Slot unavailable, please reschedule..."
                          class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-red-500 transition-all resize-none text-sm mb-4"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-3 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all font-semibold">Back</button>
                    <button type="submit" class="px-4 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-semibold transition-all hover:scale-[1.02]">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>