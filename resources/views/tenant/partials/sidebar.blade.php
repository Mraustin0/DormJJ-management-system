{{-- Sidebar Overlay (Mobile) --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col border-r border-gray-100">
    <div class="p-8 pb-4">
        <h1 class="text-red-600 font-bold text-xl leading-tight tracking-wide uppercase">
            Dormitory<br>Management<br>System
        </h1>
    </div>
    <nav class="flex-1 px-6 space-y-6 overflow-y-auto py-4">
        {{-- Home Button --}}
        <a href="{{ route('tenant.dashboard') }}" class="block px-6 py-3 {{ ($activePage ?? '') == 'dashboard' ? 'bg-gray-500 text-white' : 'text-gray-800 hover:bg-gray-100' }} text-lg font-bold rounded-lg text-center transition-colors">
            หน้าหลัก
        </a>

        {{-- Bills & Receipts Section --}}
        <div>
            <h3 class="text-gray-400 font-bold text-xs mb-3">รายการบิลและใบเสร็จ</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('tenant.bills') }}" class="block px-2 {{ ($activePage ?? '') == 'bills' ? 'py-2 bg-gray-500 text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-red-600' }} font-medium text-base">
                        รายการบิลทั้งหมด
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenant.contract') }}" class="block px-2 {{ ($activePage ?? '') == 'contract' ? 'py-2 bg-gray-500 text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-red-600' }} font-medium text-base">
                        เอกสารสัญญาเช่า
                    </a>
                </li>
            </ul>
        </div>

        {{-- Utilities Section --}}
        <div>
            <h3 class="text-gray-400 font-bold text-xs uppercase mb-3">UTILITIES</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('tenant.meters') }}" class="block px-2 {{ ($activePage ?? '') == 'meters' ? 'py-2 bg-gray-500 text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-red-600' }} font-medium text-base">
                        ข้อมูลมิเตอร์น้ำ/ไฟ
                    </a>
                </li>
            </ul>
        </div>

        {{-- Room Info Section --}}
        <div>
            <h3 class="text-gray-400 font-bold text-xs mb-3">ข้อมูลหอพัก</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('tenant.dashboard') }}" class="block px-2 py-1.5 text-gray-800 hover:text-red-600 font-medium text-base">
                        รายละเอียดห้องพัก
                    </a>
                </li>
            </ul>
        </div>

        {{-- Settings Section --}}
        <div>
            <h3 class="text-gray-400 font-bold text-xs mb-3">การตั้งค่า</h3>
            <ul class="space-y-2">
                <li>
                    <a href="#" class="block px-2 py-1.5 text-gray-800 hover:text-red-600 font-medium text-base">
                        แจ้งย้ายออก
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenant.profile') }}" class="block px-2 {{ ($activePage ?? '') == 'profile' ? 'py-2 bg-gray-500 text-white rounded-lg shadow-sm' : 'py-1.5 text-gray-800 hover:text-red-600' }} font-medium text-base">
                        ตั้งค่าระบบ
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block px-2 py-1.5 text-gray-800 font-medium text-base hover:text-red-600 w-full text-left">
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
    let sidebarCollapsed = false;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('sidebarOverlay');

        if (window.innerWidth >= 768) {
            sidebarCollapsed = !sidebarCollapsed;
            if (sidebarCollapsed) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('md:translate-x-0');
                if (mainContent) mainContent.classList.remove('md:ml-72');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('md:translate-x-0');
                if (mainContent) mainContent.classList.add('md:ml-72');
            }
        } else {
            sidebarOpen = !sidebarOpen;
            if (sidebarOpen) {
                sidebar.classList.remove('-translate-x-full');
                if (overlay) {
                    overlay.classList.remove('hidden');
                    requestAnimationFrame(() => overlay.classList.remove('opacity-0'));
                }
            } else {
                sidebar.classList.add('-translate-x-full');
                if (overlay) {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }
    }
</script>
