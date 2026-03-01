{{-- SweetAlert2 CDN (loaded once) --}}
@once
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce

{{-- User Dropdown --}}
<div class="relative" id="userDropdownWrapper">
    <button onclick="toggleUserDropdown()" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>
        <div class="text-white text-sm hidden sm:block">
            <p class="font-semibold leading-tight">{{ $contract->tenant_name ?? Auth::user()->username }}</p>
            <p class="text-white/80 text-xs">ห้อง {{ $contract->room->room_number ?? '-' }}</p>
        </div>
    </button>

    {{-- Dropdown Panel --}}
    <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
        <a href="{{ route('tenant.profile') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-sm font-medium">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            ตั้งค่า
        </a>
        <div class="border-t border-gray-100"></div>
        <form action="{{ route('logout') }}" method="POST" id="dropdownLogoutForm">
            @csrf
            <button type="button" onclick="confirmLogout('dropdownLogoutForm')" class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors text-sm font-medium w-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                ออกจากระบบ
            </button>
        </form>
    </div>
</div>

<script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('hidden');
        // Close notification dropdown if open
        const notiDropdown = document.getElementById('notificationDropdown');
        if (notiDropdown) notiDropdown.classList.add('hidden');
    }

    // Close user dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('userDropdownWrapper');
        const dropdown = document.getElementById('userDropdown');
        if (wrapper && dropdown && !wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Logout confirmation with SweetAlert2
    function confirmLogout(formId) {
        Swal.fire({
            title: 'ออกจากระบบ',
            text: 'คุณต้องการออกจากระบบหรือไม่?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ออกจากระบบ',
            cancelButtonText: 'ยกเลิก',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
