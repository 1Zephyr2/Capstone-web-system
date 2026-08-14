<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Appointments</title>
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

        function openDetailModal(id, pet, owner, service, datetime, status, notes, rejectRoute, approveRoute, completeRoute, cancelRoute, editNotesRoute, resultPhotoUrl, notifyRoute) {
            document.getElementById('modal-pet').innerText      = pet;
            document.getElementById('modal-owner').innerText    = owner;
            document.getElementById('modal-service').innerText  = service;
            document.getElementById('modal-datetime').innerText = datetime;
            document.getElementById('modal-notes-display').innerText = notes || '—';
            document.getElementById('modal-notes-input').value = notes || '';

            const photoWrap = document.getElementById('modal-result-photo-wrap');
            const photoImg  = document.getElementById('modal-result-photo');
            if (resultPhotoUrl) {
                photoImg.src = resultPhotoUrl;
                photoWrap.classList.remove('hidden');
            } else {
                photoWrap.classList.add('hidden');
            }

            const statusEl = document.getElementById('modal-status');
            statusEl.innerText = status.charAt(0).toUpperCase() + status.slice(1);
            statusEl.className = 'px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide ' + statusBadgeClass(status);

            document.getElementById('btn-approve').classList.toggle('hidden', status !== 'pending');
            document.getElementById('btn-reject-wrap').classList.toggle('hidden', status !== 'pending');
            document.getElementById('btn-complete').classList.toggle('hidden', status !== 'approved');
            document.getElementById('btn-notify-wrap').classList.toggle('hidden', status !== 'approved');
            document.getElementById('btn-cancel').classList.toggle('hidden', status !== 'approved');

            document.getElementById('form-approve').action  = approveRoute;
            document.getElementById('form-complete').action = completeRoute;
            document.getElementById('form-cancel').action   = cancelRoute;
            document.getElementById('form-reject').action   = rejectRoute;
            document.getElementById('form-edit-notes').action = editNotesRoute;
            document.getElementById('form-notify').action    = notifyRoute;

            // Reset notes edit mode
            showNotesView();

            // Reset the complete panel (photo/pickup fields) for the newly opened appointment
            document.getElementById('complete-panel').classList.add('hidden');
            const completeForm = document.getElementById('form-complete');
            completeForm.reset();
            document.getElementById('pickup-fields').classList.add('hidden');
            document.getElementById('result-photo-filename').innerText = 'Click to upload a photo';

            showModal('detail-modal', 'detail-modal-content');
        }

        function toggleCompletePanel() {
            document.getElementById('complete-panel').classList.toggle('hidden');
        }

        function togglePickupFields(checkbox) {
            const fields = document.getElementById('pickup-fields');
            fields.classList.toggle('hidden', !checkbox.checked);
            fields.querySelectorAll('input,textarea').forEach(el => el.required = checkbox.checked);
        }

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

        function updateResultPhotoFilename(input) {
            const label = document.getElementById('result-photo-filename');
            label.innerText = input.files.length ? input.files[0].name : 'Click to upload a photo';
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
                pending:   'bg-amber-100 text-amber-600 border border-amber-200',
                approved:  'bg-emerald-100 text-emerald-600 border border-emerald-200',
                rejected:  'bg-red-100 text-red-600 border border-red-200',
                completed: 'bg-blue-100 text-blue-600 border border-blue-200',
                cancelled: 'bg-gray-100 text-gray-500 border border-gray-200',
            };
            return map[status] || 'bg-gray-100 text-gray-600';
        }
    </script>
    <script>function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }</script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    @php $isAdmin = auth()->user()->role === 'admin'; @endphp
    @php $prefix  = $isAdmin ? 'admin' : 'staff'; @endphp

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route($prefix . '.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="{{ $isAdmin ? 'text-rose-700 bg-rose-100 border-rose-200' : 'text-violet-700 bg-violet-100 border-violet-200' }} font-normal text-xs ml-2 px-2 py-0.5 rounded-md border">
                    {{ $isAdmin ? 'ADMIN PORTAL' : 'STAFF PORTAL' }}
                </span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route($prefix . '.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route($prefix . '.directory') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Pets</a>
                <a href="{{ route($prefix . '.appointments') }}" class="text-gray-900 font-semibold transition-all hover:scale-105">Appointments</a>
                <a href="{{ route($prefix . '.insights') }}"     class="hover:text-gray-900 transition-all hover:scale-105">Insights</a>
                @if($isAdmin)
                    <a href="{{ route('admin.panel') }}" class="text-rose-700 font-semibold transition-all bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 hover:bg-rose-100 hover:scale-105">Admin Panel</a>
                @endif
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => ($isAdmin ?? false) ? 'admin.' : 'staff.'])
            <form action="{{ route($isAdmin ? 'admin.logout' : 'staff.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route($prefix . '.dashboard') }}"    class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
        <a href="{{ route($prefix . '.directory') }}"    class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><svg class="inline w-[1em] h-[1em]" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg> Pets</a>
        <a href="{{ route($prefix . '.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold"><i class="bi bi-calendar-event"></i> Appointments</a>
        <a href="{{ route($prefix . '.insights') }}"     class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
        @if($isAdmin)
            <a href="{{ route('admin.panel') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-rose-50 text-rose-700 text-sm font-semibold"><i class="bi bi-gear"></i> Admin Panel</a>
        @endif
        <form action="{{ route($isAdmin ? 'admin.logout' : 'staff.logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </div>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start gap-3">
                <i class="bi bi-exclamation-circle-fill mt-0.5"></i>
                <div>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
            </div>
        @endif

        <header class="mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
    <h1 class="text-2xl font-bold text-gray-900">Appointment Management</h1>
    <p class="text-gray-500 text-sm">Review, approve, and manage appointments by day.</p>
</header>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <i class="bi bi-hourglass-split text-amber-600 text-xl mb-2 block"></i>
        <p class="text-gray-500 text-xs mb-1">Pending</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
        <i class="bi bi-calendar-check text-emerald-600 text-xl mb-2 block"></i>
        <p class="text-gray-500 text-xs mb-1">Approved</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <i class="bi bi-sun text-blue-600 text-xl mb-2 block"></i>
        <p class="text-gray-500 text-xs mb-1">Today's Schedule</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] }}</p>
    </div>
</div>

@php
    $prevDate  = $date->copy()->subDay()->toDateString();
    $nextDate  = $date->copy()->addDay()->toDateString();
    $todayDate = today()->toDateString();
    $viewMode  = $viewMode ?? 'day';
@endphp

<!-- Day / Week toggle -->
<div class="flex items-center gap-2 mb-4 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
        <a href="{{ route($prefix . '.appointments', array_merge(request()->except('view'), ['view' => 'day'])) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $viewMode === 'day' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Day</a>
        <a href="{{ route($prefix . '.appointments', array_merge(request()->except('view'), ['view' => 'week'])) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ $viewMode === 'week' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Week</a>
    </div>
</div>

<!-- Date nav + Filters -->
<form method="GET" action="{{ route($prefix . '.appointments') }}"
      class="flex flex-wrap items-center gap-3 mb-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
    <input type="hidden" name="view" value="{{ $viewMode }}">

    @if($viewMode === 'day')
        <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-gray-200">
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $prevDate])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-medium transition-all">&lt; Prev</a>
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $todayDate])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-gray-900 hover:bg-gray-800 text-white font-medium transition-all">Today</a>
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $nextDate])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-medium transition-all">Next &gt;</a>
        </div>
        <input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()"
               class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-gray-900 text-sm outline-none focus:border-violet-500 transition-all shadow-sm">
    @else
        @php
            $prevWeekDate = $weekStart->copy()->subWeek()->toDateString();
            $nextWeekDate = $weekStart->copy()->addWeek()->toDateString();
        @endphp
        <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-gray-200">
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $prevWeekDate, 'view' => 'week'])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-medium transition-all">&lt; Prev Week</a>
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $todayDate, 'view' => 'week'])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-gray-900 hover:bg-gray-800 text-white font-medium transition-all">This Week</a>
            <a href="{{ route($prefix . '.appointments', array_merge(request()->except('date'), ['date' => $nextWeekDate, 'view' => 'week'])) }}"
               class="px-3 py-2 rounded-lg text-sm bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-medium transition-all">Next Week &gt;</a>
        </div>
        <p class="text-gray-500 text-sm font-medium">{{ $weekStart->format('M j') }} – {{ $weekEnd->format('M j, Y') }}</p>
    @endif

    <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-gray-900 text-sm outline-none focus:border-violet-500 transition-all appearance-none shadow-sm">
        <option value="">All Statuses</option>
        @foreach($statusOptions as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>

    <select name="service" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-xl px-4 py-2 text-gray-900 text-sm outline-none focus:border-violet-500 transition-all appearance-none shadow-sm">
        <option value="">All Services</option>
        @foreach($serviceTypes as $svc)
            <option value="{{ $svc->id }}" {{ (string) request('service') === (string) $svc->id ? 'selected' : '' }}>{{ $svc->name }}</option>
        @endforeach
    </select>

    @if(request()->hasAny(['status','service']))
        <a href="{{ route($prefix . '.appointments', ['date' => $date->toDateString(), 'view' => $viewMode]) }}"
           class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold transition-all">Clear</a>
    @endif
</form>

@if($viewMode === 'week')
    <!-- Week Grid -->
    <div class="bg-white border-gray-200 border rounded-2xl overflow-hidden shadow-sm reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead class="bg-gray-50/50 border-gray-200 border-b">
                <tr class="text-xs uppercase text-gray-500 tracking-wider">
                    <th class="px-4 py-3 w-20">Time</th>
                    @foreach($weekDays as $day)
                        <th class="px-3 py-3 text-center {{ $day['is_today'] ? 'bg-violet-50 text-violet-700' : '' }} {{ $day['is_weekend'] ? 'text-gray-300' : '' }}">
                            <a href="{{ route($prefix . '.appointments', ['date' => $day['date']->toDateString(), 'view' => 'day']) }}" class="hover:underline">
                                {{ $day['label'] }} <span class="block text-sm font-bold normal-case">{{ $day['day_num'] }}</span>
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($timeSlots as $slotIndex => $referenceSlot)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-xs font-bold text-violet-600 whitespace-nowrap">{{ $referenceSlot['label'] }}</td>
                        @foreach($weekDays as $day)
                            @php $slotAppointments = $day['slots'][$slotIndex]['appointments'] ?? collect(); @endphp
                            <td class="px-3 py-3 text-center {{ $day['is_weekend'] ? 'bg-gray-50/50' : '' }}">
                                @if($day['is_weekend'])
                                    <span class="text-gray-300 text-xs">Closed</span>
                                @elseif($slotAppointments->isEmpty())
                                    <a href="{{ route($prefix . '.appointments', ['date' => $day['date']->toDateString(), 'view' => 'day']) }}" class="text-gray-300 text-xs hover:text-emerald-500 transition-all">—</a>
                                @else
                                    <a href="{{ route($prefix . '.appointments', ['date' => $day['date']->toDateString(), 'view' => 'day']) }}"
                                       class="inline-flex flex-col items-center gap-0.5 hover:opacity-75 transition-all">
                                        @foreach($slotAppointments->take(2) as $appt)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $appt->status_badge_class }} whitespace-nowrap">{{ $appt->pet->name }}</span>
                                        @endforeach
                                        @if($slotAppointments->count() > 2)
                                            <span class="text-[10px] text-gray-400">+{{ $slotAppointments->count() - 2 }} more</span>
                                        @endif
                                    </a>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else

<p class="text-gray-500 mb-4 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
    {{ $date->format('l, F j, Y') }}
</p>
@if($timeSlots->every(fn($slot) => $slot['full']))
    <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-semibold flex items-center gap-2 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">
        <i class="bi bi-calendar-x"></i> This date is fully booked — no open slots remain.
    </div>
@endif

<!-- Time Slot Grid -->
<div class="bg-white border-gray-200 border rounded-2xl overflow-hidden shadow-sm reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out">

    <!-- Mobile cards -->
    <div class="block sm:hidden divide-y divide-gray-200">
        @foreach($timeSlots as $slot)
            @forelse($slot['appointments'] as $appt)
                @php
                    $approveRouteM   = route($prefix.'.appointments.approve',  $appt);
                    $rejectRouteM    = route($prefix.'.appointments.reject',   $appt);
                    $completeRouteM  = route($prefix.'.appointments.complete', $appt);
                    $cancelRouteM    = route($prefix.'.appointments.cancel',   $appt);
                    $editNotesRouteM = route($prefix.'.appointments.notes',    $appt);
                    $notifyRouteM    = route($prefix.'.appointments.notify-almost-done', $appt);
                @endphp
                <div onclick="openDetailModal(
                        {{ $appt->id }},
                        '{{ addslashes($appt->pet->name . ' (' . $appt->pet->breed . ')') }}',
                        '{{ addslashes($appt->user->name) }}',
                        '{{ addslashes($appt->service_label) }}',
                        '{{ $appt->appointment_date->format('M d, Y — g:i A') }}',
                        '{{ $appt->status }}',
                        '{{ addslashes($appt->notes ?? '') }}',
                        '{{ $rejectRouteM }}',
                        '{{ $approveRouteM }}',
                        '{{ $completeRouteM }}',
                        '{{ $cancelRouteM }}',
                        '{{ $editNotesRouteM }}',
                        {{ $appt->result_photo_url ? "'".addslashes($appt->result_photo_url)."'" : 'null' }},
                        '{{ $notifyRouteM }}'
                     )"
                     class="p-4 active:bg-gray-50">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-violet-600 text-sm">{{ $slot['label'] }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $appt->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $appt->service_label }} &bull; {{ $appt->pet->name }}</p>
                </div>
            @empty
                <div class="p-4 flex items-center justify-between">
                    <span class="font-bold text-violet-600 text-sm">{{ $slot['label'] }}</span>
                    @if($slot['datetime']->isPast())
                        <span class="text-gray-300 text-sm italic">Past</span>
                    @else
                        <button type="button"
                                onclick="openBookModal('{{ $slot['datetime']->toDateTimeString() }}', '{{ $slot['label'] }}')"
                                class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all text-sm">
                            + Book
                        </button>
                    @endif
                </div>
            @endforelse
            @if($slot['appointments']->isNotEmpty() && !$slot['full'] && !$slot['datetime']->isPast())
                <div class="p-4 flex items-center justify-between bg-gray-50/50">
                    <span class="text-gray-400 text-xs">{{ $slot['appointments']->count() }}/{{ \App\Models\Appointment::MAX_PER_SLOT }} slots used</span>
                    <button type="button"
                            onclick="openBookModal('{{ $slot['datetime']->toDateTimeString() }}', '{{ $slot['label'] }}')"
                            class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all text-xs">
                        + Add another pet
                    </button>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50 border-gray-200 border-b">
            <tr class="text-xs uppercase text-gray-500 tracking-wider">
                <th class="px-6 py-4">Time</th>
                <th class="px-6 py-4">Owner</th>
                <th class="px-6 py-4">Package / Service</th>
                <th class="px-6 py-4">Notes</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($timeSlots as $slot)
                @forelse($slot['appointments'] as $appt)
                    @php
                        $approveRoute   = route($prefix.'.appointments.approve',  $appt);
                        $rejectRoute    = route($prefix.'.appointments.reject',   $appt);
                        $completeRoute  = route($prefix.'.appointments.complete', $appt);
                        $cancelRoute    = route($prefix.'.appointments.cancel',   $appt);
                        $editNotesRoute = route($prefix.'.appointments.notes',    $appt);
                        $notifyRoute    = route($prefix.'.appointments.notify-almost-done', $appt);
                    @endphp
                    <tr onclick="openDetailModal(
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
                            '{{ $editNotesRoute }}',
                            {{ $appt->result_photo_url ? "'".addslashes($appt->result_photo_url)."'" : 'null' }},
                            '{{ $notifyRoute }}'
                         )"
                        class="cursor-pointer hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold text-violet-600">{{ $slot['label'] }}</td>
                        <td class="px-6 py-5 font-medium">{{ $appt->user->name }}</td>
                        <td class="px-6 py-5">{{ $appt->service_label }} <span class="text-gray-400 text-xs">({{ $appt->pet->name }})</span></td>
                        <td class="px-6 py-5 text-gray-500 text-sm truncate max-w-xs">{{ $appt->notes ?? '—' }}</td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide {{ $appt->status_badge_class }}">{{ ucfirst($appt->status) }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs text-violet-600 underline">View / Manage</span>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 font-bold text-violet-600">{{ $slot['label'] }}</td>
                        <td colspan="4" class="px-6 py-5 text-center text-gray-400 italic">— Available —</td>
                        <td class="px-6 py-5">
                            @if($slot['datetime']->isPast())
                                <span class="text-gray-300 text-sm italic">Past</span>
                            @else
                                <button type="button"
                                        onclick="openBookModal('{{ $slot['datetime']->toDateTimeString() }}', '{{ $slot['label'] }}')"
                                        class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all text-sm">
                                    + Book
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforelse
                @if($slot['appointments']->isNotEmpty() && !$slot['full'] && !$slot['datetime']->isPast())
                    <tr class="bg-gray-50/50 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-xs text-gray-400" colspan="4">{{ $slot['appointments']->count() }}/{{ \App\Models\Appointment::MAX_PER_SLOT }} slots used for {{ $slot['label'] }}</td>
                        <td class="px-6 py-3">
                            <button type="button"
                                    onclick="openBookModal('{{ $slot['datetime']->toDateTimeString() }}', '{{ $slot['label'] }}')"
                                    class="text-emerald-600 font-semibold hover:text-emerald-700 transition-all text-xs">
                                + Add another pet
                            </button>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif
    </main>

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeModal('detail-modal','detail-modal-content')">
        <div id="detail-modal-content" class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Appointment Details</h2>

            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Pet</label>
                    <p id="modal-pet" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Owner</label>
                    <p id="modal-owner" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Service</label>
                    <p id="modal-service" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Date & Time</label>
                    <p id="modal-datetime" class="text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-200"></p>
                </div>

                <!-- Notes — view + inline edit -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Notes</label>
                        <button type="button" onclick="showNotesEdit()" id="notes-edit-btn"
                                class="text-xs text-violet-600 hover:text-violet-700 transition-all">
                            <i class="bi bi-pencil mr-1"></i>Edit
                        </button>
                    </div>
                    <!-- View mode -->
                    <div id="notes-view">
                        <p id="modal-notes-display" class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200 text-sm min-h-[44px]"></p>
                    </div>
                    <!-- Edit mode -->
                    <div id="notes-edit" class="hidden">
                        <form id="form-edit-notes" method="POST">
                            @csrf @method('PATCH')
                            <textarea id="modal-notes-input" name="notes" rows="3"
                                      class="w-full bg-gray-50 border border-violet-500 rounded-lg px-3 py-2.5 text-gray-900 outline-none text-sm resize-none mb-2"></textarea>
                            <div class="flex gap-2">
                                <button type="button" onclick="showNotesView()"
                                        class="flex-1 px-3 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-semibold transition-all">Cancel</button>
                                <button type="submit"
                                        class="flex-1 px-3 py-2 rounded-lg bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold transition-all">Save Notes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Status</label>
                    <span id="modal-status" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide"></span>
                </div>

                <div id="modal-result-photo-wrap" class="hidden">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Result Photo <span class="normal-case font-normal text-gray-300">(click to enlarge)</span></label>
                    <img id="modal-result-photo" src="" alt="Result photo"
                         onclick="openPhotoLightbox(this.src)"
                         class="w-full h-40 object-cover rounded-lg border border-gray-200 cursor-zoom-in hover:opacity-90 transition-all">
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
                    <button type="button" onclick="openRejectModal()" class="w-full px-4 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-semibold transition-all">
                        <i class="bi bi-x-circle mr-2"></i>Reject Appointment
                    </button>
                </div>
                <div id="btn-notify-wrap">
                    <form id="form-notify" method="POST" onsubmit="return confirm('Send an \'almost done\' notice to the owner?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full px-4 py-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold transition-all">
                            <i class="bi bi-bell mr-2"></i>Notify Owner — Almost Done
                        </button>
                    </form>
                </div>
                <form id="form-complete" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')
                    <button id="btn-complete" type="button" onclick="toggleCompletePanel()" class="w-full px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold transition-all hover:scale-[1.02]">
                        <i class="bi bi-check2-all mr-2"></i>Mark as Completed
                    </button>
                    <div id="complete-panel" class="hidden mt-3 p-4 rounded-xl bg-blue-50 border border-blue-200 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Result Photo <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                            <label for="result-photo-input"
                                   class="flex items-center gap-3 w-full bg-white border border-dashed border-gray-300 rounded-xl px-3 py-2.5 text-sm cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition-all">
                                <span class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="bi bi-cloud-upload"></i>
                                </span>
                                <span class="min-w-0">
                                    <span id="result-photo-filename" class="block text-gray-600 truncate">Click to upload a photo</span>
                                    <span class="block text-gray-400 text-xs">PNG, JPG, or WEBP — up to 4MB</span>
                                </span>
                            </label>
                            <input type="file" name="result_photo" id="result-photo-input" accept="image/*" class="hidden"
                                   onchange="updateResultPhotoFilename(this)">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="different_pickup" value="1" onchange="togglePickupFields(this)" class="rounded border-gray-300">
                            Picked up by someone other than the owner?
                        </label>
                        <div id="pickup-fields" class="hidden space-y-2">
                            <input type="text" name="picked_up_by" placeholder="Name of person who picked up"
                                   class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500">
                            <textarea name="pickup_note" rows="2" placeholder="Note (e.g. ID shown, relation to owner...)"
                                      class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-all">Confirm Completion</button>
                    </div>
                </form>
                <form id="form-cancel" method="POST">
                    @csrf @method('PATCH')
                    <button id="btn-cancel" type="submit" onclick="return confirm('Cancel this appointment?')"
                            class="w-full px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">
                        <i class="bi bi-slash-circle mr-2"></i>Cancel Appointment
                    </button>
                </form>
                <button type="button" onclick="closeModal('detail-modal','detail-modal-content')"
                        class="w-full px-4 py-2 rounded-xl bg-transparent text-gray-400 hover:text-gray-700 text-sm transition-all">Close</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) closeRejectModal()">
        <div id="reject-modal-content" class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Reject Appointment</h2>
            <p class="text-gray-500 text-sm mb-6">Optionally provide a reason for the owner.</p>
            <form id="form-reject" method="POST">
                @csrf @method('PATCH')
                <textarea name="rejection_reason" rows="3" placeholder="e.g. Slot unavailable, please reschedule..."
                          class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-red-500 transition-all resize-none text-sm mb-4"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-3 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all font-semibold">Back</button>
                    <button type="submit" class="px-4 py-3 rounded-xl bg-red-600 hover:bg-red-500 text-white font-semibold transition-all hover:scale-[1.02]">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Book Walk-in Modal -->
<div id="book-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/80 backdrop-blur-sm transition-opacity duration-300 ease-out"
     onclick="if(event.target===this) closeModal('book-modal','book-modal-content')">
    <div id="book-modal-content" class="bg-white border border-gray-200 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-bold text-gray-900 mb-1">Book Appointment</h2>
        <p id="book-modal-time" class="text-sm text-gray-500 mb-6"></p>

        <!-- Mode toggle -->
        <div class="flex gap-2 mb-5 bg-gray-100 p-1 rounded-xl">
            <button type="button" id="mode-btn-existing" onclick="setBookingMode('existing')"
                    class="flex-1 px-3 py-2 rounded-lg text-sm font-semibold transition-all bg-white shadow text-gray-900">
                Existing Owner
            </button>
            <button type="button" id="mode-btn-walkin" onclick="setBookingMode('walkin')"
                    class="flex-1 px-3 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500">
                Walk-in
            </button>
        </div>

        <form id="book-form" method="POST" action="{{ route($prefix . '.appointments.store') }}" onsubmit="return validateBookForm()">
            @csrf
            <input type="hidden" name="appointment_date" id="book-appointment-date">
            <input type="hidden" name="booking_mode" id="book-mode" value="existing">
            <input type="hidden" name="pet_id" id="book-pet-id">

            <!-- Existing owner search -->
            <div id="existing-fields" class="space-y-3 mb-4">
                <div class="relative">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Search Owner or Pet</label>
                    <input type="text" id="pet-search-input" oninput="searchPets(this.value)" autocomplete="off"
                           placeholder="Start typing a name..."
                           class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                    <div id="pet-search-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto"></div>
                </div>
                <div id="pet-selected-display" class="hidden bg-violet-50 border border-violet-200 rounded-lg p-3 text-sm">
                    <span class="font-semibold text-gray-900" id="pet-selected-text"></span>
                    <button type="button" onclick="clearSelectedPet()" class="text-red-500 text-xs ml-2">Change</button>
                </div>
            </div>

            <!-- Walk-in fields -->
            <div id="walkin-fields" class="space-y-3 mb-4 hidden">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Owner Name</label>
                    <input type="text" name="owner_name" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Pet Name</label>
                        <input type="text" name="pet_name" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Type</label>
                        <select name="pet_type" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Breed (optional)</label>
                        <input type="text" name="pet_breed" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Size</label>
                        <select name="pet_size" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500">
                            <option value="">—</option>
                            @foreach(\App\Models\Pet::SIZES as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Shared fields -->
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Service</label>
                <input type="hidden" name="service_id" id="book-service-id">
                <button type="button" id="service-picker-btn" onclick="toggleServiceOptions()"
                        class="w-full flex items-center justify-between bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-left outline-none focus:border-violet-500">
                    <span id="service-picker-label" class="text-gray-400">Select a service</span>
                    <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                </button>
                <p id="service-picker-error" class="hidden text-red-500 text-xs mt-1">Please select a service.</p>

                <div id="service-options-panel" class="hidden mt-2 border border-gray-300 rounded-lg max-h-56 overflow-y-auto divide-y divide-gray-100">
                    @foreach($serviceTypes->groupBy('category') as $category => $items)
                        <div class="px-3 py-1.5 bg-gray-50 text-[11px] font-bold uppercase tracking-wider text-gray-400 sticky top-0">{{ $category }}</div>
                        @foreach($items as $svc)
                            <div onclick="selectService({{ $svc->id }}, '{{ addslashes($svc->name) }}')"
                                 class="px-3 py-2.5 text-sm text-gray-700 hover:bg-violet-50 cursor-pointer transition-all flex items-center justify-between gap-2">
                                <span>{{ $svc->name }}</span>
                                <span class="text-gray-400 text-xs shrink-0">{{ $svc->price_range }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-sm outline-none focus:border-violet-500 resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeModal('book-modal','book-modal-content')"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all font-semibold">Cancel</button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition-all">Book & Approve</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBookModal(datetime, label) {
        document.getElementById('book-appointment-date').value = datetime;
        document.getElementById('book-modal-time').innerText = label;
        setBookingMode('existing');
        clearSelectedPet();
        document.getElementById('pet-search-input').value = '';
        document.getElementById('pet-search-results').classList.add('hidden');
        resetServicePicker();
        showModal('book-modal', 'book-modal-content');
    }

    function toggleServiceOptions() {
        document.getElementById('service-options-panel').classList.toggle('hidden');
    }

    function selectService(id, name) {
        document.getElementById('book-service-id').value = id;
        const label = document.getElementById('service-picker-label');
        label.innerText = name;
        label.classList.remove('text-gray-400');
        label.classList.add('text-gray-900');
        document.getElementById('service-options-panel').classList.add('hidden');
        document.getElementById('service-picker-error').classList.add('hidden');
    }

    function resetServicePicker() {
        document.getElementById('book-service-id').value = '';
        const label = document.getElementById('service-picker-label');
        label.innerText = 'Select a service';
        label.classList.add('text-gray-400');
        label.classList.remove('text-gray-900');
        document.getElementById('service-options-panel').classList.add('hidden');
        document.getElementById('service-picker-error').classList.add('hidden');
    }

    function validateBookForm() {
        const serviceId = document.getElementById('book-service-id').value;
        if (!serviceId) {
            document.getElementById('service-picker-error').classList.remove('hidden');
            document.getElementById('service-picker-btn').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        return true;
    }

    function setBookingMode(mode) {
        document.getElementById('book-mode').value = mode;
        const isExisting = mode === 'existing';

        document.getElementById('existing-fields').classList.toggle('hidden', !isExisting);
        document.getElementById('walkin-fields').classList.toggle('hidden', isExisting);

        document.getElementById('mode-btn-existing').classList.toggle('bg-white', isExisting);
        document.getElementById('mode-btn-existing').classList.toggle('shadow', isExisting);
        document.getElementById('mode-btn-existing').classList.toggle('text-gray-900', isExisting);
        document.getElementById('mode-btn-existing').classList.toggle('text-gray-500', !isExisting);

        document.getElementById('mode-btn-walkin').classList.toggle('bg-white', !isExisting);
        document.getElementById('mode-btn-walkin').classList.toggle('shadow', !isExisting);
        document.getElementById('mode-btn-walkin').classList.toggle('text-gray-900', !isExisting);
        document.getElementById('mode-btn-walkin').classList.toggle('text-gray-500', isExisting);
    }

    let searchTimeout;
    function searchPets(q) {
        clearTimeout(searchTimeout);
        const resultsBox = document.getElementById('pet-search-results');

        if (q.length < 2) {
            resultsBox.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route($prefix . '.appointments.search-pets') }}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(pets => {
                    resultsBox.innerHTML = '';
                    if (pets.length === 0) {
                        resultsBox.innerHTML = '<div class="p-3 text-sm text-gray-400">No matches found.</div>';
                    } else {
                        pets.forEach(pet => {
                            const item = document.createElement('div');
                            item.className = 'p-3 text-sm hover:bg-violet-50 cursor-pointer border-b border-gray-100 last:border-0';
                            item.innerHTML = `<span class="font-semibold">${pet.pet_name}</span> <span class="text-gray-400">(${pet.breed})</span> — ${pet.owner_name}`;
                            item.onclick = () => selectPet(pet);
                            resultsBox.appendChild(item);
                        });
                    }
                    resultsBox.classList.remove('hidden');
                });
        }, 250);
    }

    function selectPet(pet) {
        document.getElementById('book-pet-id').value = pet.pet_id;
        document.getElementById('pet-selected-text').innerText = `${pet.pet_name} (${pet.breed}) — ${pet.owner_name}`;
        document.getElementById('pet-selected-display').classList.remove('hidden');
        document.getElementById('pet-search-input').classList.add('hidden');
        document.getElementById('pet-search-results').classList.add('hidden');
    }

    function clearSelectedPet() {
        document.getElementById('book-pet-id').value = '';
        document.getElementById('pet-selected-display').classList.add('hidden');
        document.getElementById('pet-search-input').classList.remove('hidden');
        document.getElementById('pet-search-input').value = '';
    }
</script>

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