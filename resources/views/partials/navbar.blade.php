{{-- Navbar with Profile Dropdown and Notifications --}}
<nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm no-print">
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h2 class="text-xl font-bold text-[#4A90E2]">{{ $pageTitle ?? 'ระบบจัดการหอพัก JJ Apartment' }}</h2>
    </div>
    <div class="flex items-center gap-4">
        {{-- Notification Bell with Dropdown --}}
        <div class="relative" id="notificationContainer">
            <button onclick="toggleNotifications()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if(($pendingSlipsCount ?? 0) > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                    {{ $pendingSlipsCount > 9 ? '9+' : $pendingSlipsCount }}
                </span>
                @endif
            </button>

            {{-- Notification Dropdown Panel (Design like image 2) --}}
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800">Notifications</h3>
                    <div class="flex gap-4 mt-3">
                        <button onclick="filterNotifications('all')" class="notification-tab text-sm font-bold text-[#4A90E2] pb-1 border-b-2 border-[#4A90E2]" data-tab="all">All</button>
                        <button onclick="filterNotifications('unread')" class="notification-tab text-sm font-medium text-gray-400 pb-1" data-tab="unread">Unread</button>
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto" id="notificationList">
                    @if(isset($pendingSlips) && $pendingSlips->count() > 0)
                        @foreach($pendingSlips->take(6) as $slip)
                        <a href="{{ route('rooms.bills') }}?status=pending" class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">ห้อง {{ $slip->room->room_number ?? '-' }}</p>
                                <p class="text-sm text-[#4A90E2]">แจ้งเตือนการโอนเงิน</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $slip->created_at?->diffForHumans() ?? '1 day' }}</p>
                            </div>
                        </a>
                        @endforeach
                    @else
                        {{-- Sample notifications for demo --}}
                        <div class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">ห้อง 101</p>
                                <p class="text-sm text-[#4A90E2]">แจ้งเตือนการโอนเงิน</p>
                                <p class="text-xs text-gray-400 mt-1">1 day</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">ห้อง 102</p>
                                <p class="text-sm text-[#4A90E2]">แจ้งเตือนสัญญาใกล้หมด</p>
                                <p class="text-xs text-gray-400 mt-1">4 days</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">ห้อง 110</p>
                                <p class="text-sm text-[#4A90E2]">แจ้งเตือนค้างชำระ</p>
                                <p class="text-xs text-gray-400 mt-1">1 day</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">ห้อง 201</p>
                                <p class="text-sm text-[#4A90E2]">แจ้งย้ายออก</p>
                                <p class="text-xs text-gray-400 mt-1">4 days</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-3 border-t border-gray-100 text-center">
                    <a href="{{ route('rooms.bills') }}?status=pending" class="text-[#4A90E2] text-sm font-bold hover:underline">View All</a>
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" id="profileContainer">
            <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 hover:bg-gray-50 rounded-lg p-1 pr-2 transition-colors">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </button>

            {{-- Profile Dropdown Menu --}}
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                <div class="py-2">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="font-medium">แก้ไขโปรไฟล์</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="font-medium">ตั้งค่าระบบ</span>
                    </a>
                    <hr class="my-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-red-500 hover:bg-red-50 transition-colors w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="font-medium">ออกจากระบบ</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Toggle Notification Dropdown
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown) profileDropdown.classList.add('hidden');
        dropdown.classList.toggle('hidden');
    }

    // Toggle Profile Dropdown
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) notificationDropdown.classList.add('hidden');
        dropdown.classList.toggle('hidden');
    }

    // Filter Notifications
    function filterNotifications(type) {
        const tabs = document.querySelectorAll('.notification-tab');
        tabs.forEach(tab => {
            if (tab.dataset.tab === type) {
                tab.classList.add('text-[#4A90E2]', 'border-b-2', 'border-[#4A90E2]');
                tab.classList.remove('text-gray-400');
            } else {
                tab.classList.remove('text-[#4A90E2]', 'border-b-2', 'border-[#4A90E2]');
                tab.classList.add('text-gray-400');
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const notificationContainer = document.getElementById('notificationContainer');
        const profileContainer = document.getElementById('profileContainer');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const profileDropdown = document.getElementById('profileDropdown');

        if (notificationContainer && !notificationContainer.contains(e.target)) {
            notificationDropdown?.classList.add('hidden');
        }
        if (profileContainer && !profileContainer.contains(e.target)) {
            profileDropdown?.classList.add('hidden');
        }
    });
</script>
