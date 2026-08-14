@php
    // $notifRoutePrefix should be set by the including view: '' for owner, 'staff.' or 'admin.' otherwise.
    $notifRoutePrefix = $notifRoutePrefix ?? '';
@endphp
<div class="relative" id="notif-bell-wrapper">
    <button type="button" onclick="toggleNotifDropdown()" class="relative w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
        <i class="bi bi-bell text-lg"></i>
        <span id="notif-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">0</span>
    </button>

    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto bg-white border border-gray-200 rounded-2xl shadow-xl z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 sticky top-0 bg-white">
            <p class="font-bold text-gray-900 text-sm">Notifications</p>
            <button type="button" onclick="markAllNotifsRead()" class="text-xs text-emerald-600 hover:text-emerald-700 transition-all">Mark all read</button>
        </div>
        <div id="notif-list" class="divide-y divide-gray-100">
            <p class="text-center text-gray-400 text-sm py-8">Loading...</p>
        </div>
    </div>
</div>

<script>
    const notifIndexUrl    = "{{ route($notifRoutePrefix . 'notifications.index') }}";
    const notifReadUrlTemplate = "{{ route($notifRoutePrefix . 'notifications.read', ['notification' => '__ID__']) }}";
    const notifReadAllUrl  = "{{ route($notifRoutePrefix . 'notifications.read-all') }}";

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        const wasHidden = dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden');
        if (wasHidden) loadNotifications();
    }

    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('notif-bell-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('notif-dropdown').classList.add('hidden');
        }
    });

    function timeIcon(type) {
        const icons = {
            appointment_approved:  'bi-check-circle-fill text-emerald-500',
            appointment_rejected:  'bi-x-circle-fill text-red-500',
            appointment_completed: 'bi-stars text-blue-500',
            almost_done:           'bi-hourglass-split text-amber-500',
            new_request:           'bi-calendar-plus text-violet-500',
        };
        return icons[type] || 'bi-bell text-gray-400';
    }

    function loadNotifications() {
        fetch(notifIndexUrl)
            .then(res => res.json())
            .then(data => {
                updateBadge(data.unread_count);
                const list = document.getElementById('notif-list');
                if (!data.notifications.length) {
                    list.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">No notifications yet.</p>';
                    return;
                }
                list.innerHTML = data.notifications.map(n => `
                    <div onclick="handleNotifClick(${n.id}, ${n.link ? `'${n.link}'` : 'null'})"
                         class="px-4 py-3 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition-all ${n.unread ? 'bg-emerald-50/40' : ''}">
                        <i class="bi ${timeIcon(n.type)} mt-0.5"></i>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">${n.title}</p>
                            <p class="text-xs text-gray-500 line-clamp-2">${n.message}</p>
                            <p class="text-[11px] text-gray-400 mt-1">${n.created_at}</p>
                        </div>
                        ${n.unread ? '<span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 mt-1.5"></span>' : ''}
                    </div>
                `).join('');
            })
            .catch(() => {
                document.getElementById('notif-list').innerHTML = '<p class="text-center text-red-400 text-sm py-8">Could not load notifications.</p>';
            });
    }

    function updateBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (count > 0) {
            badge.innerText = count > 9 ? '9+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function handleNotifClick(id, link) {
        fetch(notifReadUrlTemplate.replace('__ID__', id), {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        }).finally(() => {
            if (link) window.location.href = link;
        });
    }

    function markAllNotifsRead() {
        fetch(notifReadAllUrl, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
        }).then(() => loadNotifications());
    }

    // Poll every 30s for new notifications so the badge stays current without a full reload
    setInterval(() => {
        fetch(notifIndexUrl).then(res => res.json()).then(data => updateBadge(data.unread_count)).catch(() => {});
    }, 30000);

    document.addEventListener('DOMContentLoaded', () => {
        fetch(notifIndexUrl).then(res => res.json()).then(data => updateBadge(data.unread_count)).catch(() => {});
    });
</script>
