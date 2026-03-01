{{-- Sidebar Overlay (Mobile) --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col border-r border-gray-100">
    <div class="p-8 pb-4">
        <h1 class="text-[#4A90E2] font-bold text-2xl leading-tight tracking-wide">
            DORMITORY<br>MANAGEMENT<br>SYSTEM
        </h1>
    </div>
    <nav class="flex-1 px-6 space-y-6 overflow-y-auto py-4">
        {{-- Home Button --}}
        <a href="{{ route('rooms.index') }}" class="block px-6 py-3 {{ ($activePage ?? '') == 'home' ? 'bg-[#A0A0A0] text-white' : 'text-gray-800 hover:bg-gray-100' }} text-lg font-bold rounded-lg text-center transition-colors">
            หน้าหลัก
        </a>

        {{-- Main Data Section --}}
        <div>
            <h3 class="text-gray-500 font-bold text-sm mb-3">จัดการข้อมูลหลัก</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('meters.water') }}" class="block px-2 {{ ($activePage ?? '') == 'meters.water' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        บันทึกมิเตอร์น้ำ
                    </a>
                </li>
                <li>
                    <a href="{{ route('meters.electric') }}" class="block px-2 {{ ($activePage ?? '') == 'meters.electric' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        บันทึกมิเตอร์ไฟฟ้า
                    </a>
                </li>
                <li>
                    <a href="{{ route('bills.create') }}" class="block px-2 {{ ($activePage ?? '') == 'bills.create' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        สร้างบิล
                    </a>
                </li>
                <li>
                    <a href="{{ route('rooms.bills') }}" class="block px-2 {{ ($activePage ?? '') == 'bills' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        รายการบิล
                    </a>
                </li>

                </li>   
            </ul>
        </div>

        {{-- User Section --}}
        <div>
            <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">USER</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('rooms.customers') }}" class="block px-2 {{ ($activePage ?? '') == 'customers' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        ข้อมูลลูกค้า
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenants.create') }}" class="block px-2 {{ ($activePage ?? '') == 'tenants.create' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        สร้างบัญชีผู้ใช้
                    </a>
                </li>
                <li>
                    <a href="{{ route('rooms.accommodation') }}" class="block px-2 {{ ($activePage ?? '') == 'accommodation' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        ข้อมูลการเข้าพัก
                    </a>
                </li>
            </ul>
        </div>

        {{-- Settings Section --}}
        <div>
            <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">SETTING</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('settings.index') }}" class="block px-2 {{ ($activePage ?? '') == 'settings' ? 'py-2 bg-[#A0A0A0] text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-[#4A90E2]' }} font-medium text-lg">
                        ตั้งค่าระบบ
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2] w-full text-left">
                            ออกจากระบบ
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</aside>

{{-- Sidebar Toggle Script --}}
<script>
    let sidebarOpen = window.innerWidth >= 768;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('sidebarOverlay');

        sidebarOpen = !sidebarOpen;

        if (sidebarOpen) {
            if (window.innerWidth >= 768) {
                sidebar.classList.add('md:translate-x-0');
                if (mainContent) mainContent.classList.add('md:ml-72');
            } else {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) {
                    overlay.classList.remove('hidden');
                    requestAnimationFrame(() => overlay.classList.remove('opacity-0'));
                }
            }
        } else {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('md:translate-x-0');
                if (mainContent) mainContent.classList.remove('md:ml-72');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (overlay) {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }

        // Dispatch event for map resize (used in settings page)
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('sidebarToggled'));
        }, 350);
    }
</script>
