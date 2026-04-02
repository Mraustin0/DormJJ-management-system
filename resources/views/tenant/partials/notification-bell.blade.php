{{-- Notification Bell + Dropdown --}}
<div class="relative" id="notificationWrapper">
    <button onclick="toggleNotificationDropdown()" class="text-white relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if(($unreadCount ?? 0) > 0)
        <span class="absolute -top-1.5 -right-1.5 bg-yellow-400 text-gray-900 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div id="notificationDropdown" class="hidden absolute right-0 top-full mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 max-h-[480px] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">การแจ้งเตือน</h3>
            @if(($unreadCount ?? 0) > 0)
            <form action="{{ route('tenant.notifications.markAllRead') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-blue-500 hover:text-blue-700 font-medium">อ่านทั้งหมด</button>
            </form>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="overflow-y-auto flex-1">
            @forelse(($notifications ?? collect()) as $notif)
            <a href="{{ route('tenant.notifications.read', $notif->id) }}"
               class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors {{ !$notif->is_read ? 'bg-red-50/50' : '' }}">
                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @if($notif->type == 'bill_created')
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @elseif($notif->type == 'payment_confirmed')
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @elseif($notif->type == 'contract_created')
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @elseif($notif->type == 'moveout_processed')
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                        @elseif(str_starts_with($notif->type, 'repair'))
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 {{ !$notif->is_read ? '' : 'text-gray-500' }}">{{ $notif->title }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    {{-- Unread dot --}}
                    @if(!$notif->is_read)
                    <div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0 mt-2"></div>
                    @endif
                </div>
            </a>
            @empty
            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                ไม่มีการแจ้งเตือน
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        dropdown.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notificationWrapper');
        const dropdown = document.getElementById('notificationDropdown');
        if (wrapper && dropdown && !wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // ─── Notification Polling ────────────────────────────────────────────────
    // หลักการ: JS ส่ง HTTP GET ไปถาม server ทุก 30 วินาที (Polling)
    // Server ตอบ JSON {count, notifications[]} → ถ้า count เปลี่ยน อัปเดต badge + dropdown
    // ไม่ต้อง reload หน้า, ไม่ต้องใช้ WebSocket
    ;(function () {
        const POLL_INTERVAL = 30000; // 30 วินาที
        let lastCount = {{ $unreadCount ?? 0 }};

        // ─ อัปเดต badge ตัวเลข ─
        function updateBadge(count) {
            const wrapper = document.getElementById('notificationWrapper');
            if (!wrapper) return;
            const btn   = wrapper.querySelector('button');
            let   badge = wrapper.querySelector('.notif-badge');

            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'notif-badge absolute -top-1.5 -right-1.5 bg-yellow-400 text-gray-900 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center pointer-events-none';
                    btn.style.position = 'relative';
                    btn.appendChild(badge);
                }
                badge.textContent = count > 9 ? '9+' : count;
            } else if (badge) {
                badge.remove();
            }
        }

        // ─ สร้าง HTML ของ notification item 1 รายการ ─
        function buildItem(n) {
            const iconMap = {
                bill_created:      { bg: 'bg-red-100',    color: 'text-red-500',   path: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
                payment_confirmed: { bg: 'bg-green-100',  color: 'text-green-500', path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
                payment_rejected:  { bg: 'bg-red-100',    color: 'text-red-500',   path: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
                moveout_approved:  { bg: 'bg-orange-100', color: 'text-orange-500',path: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1' },
            };
            const def  = { bg: 'bg-gray-100', color: 'text-gray-500', path: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' };
            let   icon = iconMap[n.type];
            if (!icon && n.type && n.type.startsWith('repair')) icon = { bg: 'bg-yellow-100', color: 'text-yellow-600', path: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' };
            if (!icon) icon = def;

            const href   = n.link ? `/tenant/notifications/${n.id}/read` : '#';
            const unread = !n.is_read;
            return `
            <a href="${href}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors ${unread ? 'bg-red-50/50' : ''}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 ${icon.bg} rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 ${icon.color}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon.path}"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold ${unread ? 'text-gray-800' : 'text-gray-500'}">${n.title}</p>
                        <p class="text-xs text-gray-500 truncate">${n.message}</p>
                        <p class="text-xs text-gray-400 mt-0.5">${n.time}</p>
                    </div>
                    ${unread ? '<div class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0 mt-2"></div>' : ''}
                </div>
            </a>`;
        }

        // ─ อัปเดต dropdown list ─
        function updateDropdown(notifications) {
            const list = document.querySelector('#notificationDropdown .overflow-y-auto');
            if (!list) return;
            if (!notifications.length) {
                list.innerHTML = '<div class="px-4 py-8 text-center text-gray-400 text-sm">ไม่มีการแจ้งเตือน</div>';
                return;
            }
            list.innerHTML = notifications.map(buildItem).join('');
        }

        // ─ แจ้งเตือนด้วย bell pulse เมื่อมีแจ้งเตือนใหม่ ─
        function ringBell() {
            const btn = document.querySelector('#notificationWrapper button');
            if (!btn) return;
            btn.classList.add('animate-bounce');
            setTimeout(() => btn.classList.remove('animate-bounce'), 1500);
        }

        // ─ ฟังก์ชัน poll หลัก ─
        function poll() {
            fetch('{{ route("tenant.notifications.poll") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                if (!data) return;
                if (data.count !== lastCount) {
                    const isNew = data.count > lastCount;
                    lastCount = data.count;
                    updateBadge(data.count);
                    updateDropdown(data.notifications);
                    if (isNew) ringBell();
                }
            })
            .catch(() => {}); // silent fail — ถ้า network ขาดก็รอรอบหน้า
        }

        // เริ่ม polling
        setInterval(poll, POLL_INTERVAL);
    })();
</script>
