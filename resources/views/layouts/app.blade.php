<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JJ Apartment - Admin Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 transform sidebar-transition shadow-2xl flex flex-col border-r border-gray-100">
        <div class="p-8 pb-4">
            <h1 class="text-[#4A90E2] font-bold text-2xl leading-tight tracking-wide">
                DORMITORY<br>MANAGEMENT<br>SYSTEM
            </h1>
        </div>
        <nav class="flex-1 px-6 space-y-6 overflow-y-auto py-4">
            <a href="{{ route('rooms.index') }}" class="block px-6 py-3 bg-[#A0A0A0] text-white text-lg font-bold rounded-lg text-center shadow-sm hover:bg-gray-500 transition-colors">
                หน้าหลัก
            </a>
            <div>
                <h3 class="text-gray-500 font-bold text-sm mb-3">จัดการข้อมูลหลัก</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('meters.water') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์น้ำ</a></li>
                    <li><a href="{{ route('meters.electric') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์ไฟฟ้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบิล</a></li>
                    <li><a href="{{ route('rooms.accommodation') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ข้อมูลการเข้าพัก</a></li>
                    <li><a href="{{ route('rooms.bills') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ประวัติบิล</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">USER</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('rooms.customers') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ข้อมูลลูกค้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบัญชีผู้ใช้</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">SETTING</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ตั้งค่าระบบ</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2] w-full text-left">ออกจากระบบ</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>
    {{-- End Sidebar --}}

    <div id="mainContent" class="ml-0 md:ml-72 flex-1 min-h-screen flex flex-col transition-all duration-300">
        {{-- Top Navigation --}}
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800">Dashboard</h2> {{-- This will eventually be dynamic --}}
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">ผู้ดูแลระบบ</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>
        {{-- End Top Navigation --}}

        <main class="p-6 max-w-7xl mx-auto w-full">
            @yield('content')
        </main>
    </div>

    {{-- Room Modal --}}
    <div id="roomModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden transform scale-95 transition-transform duration-200 transition-all" id="modalPanel">
            
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">ข้อมูลห้องพัก</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-8 overflow-y-auto max-h-[85vh]">
                
                <div id="vacantView" class="hidden flex-col items-center text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-2">ห้องว่าง</h4>
                    <p class="text-gray-500 mb-8">ห้องนี้พร้อมให้เข้าพัก สามารถทำสัญญาเช่าได้ทันที</p>
                    <button onclick="switchToEditMode(true)" class="w-full bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-xl transition-colors shadow-md shadow-blue-200">
                        แก้ไขข้อมูล / ทำสัญญา
                    </button>
                </div>

                <div id="occupiedView" class="hidden">
                    <div class="flex items-start gap-6 mb-8">
                        <div class="w-20 h-20 bg-gray-200 rounded-full flex-shrink-0 flex items-center justify-center text-gray-400">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div>
                                <h4 class="text-xl font-bold text-gray-800 leading-tight" id="tenantName">-</h4>
                                <p class="text-xs text-red-500 font-bold bg-red-50 inline-block px-2 py-0.5 rounded mt-1">ผู้เช่าปัจจุบัน</p>
                            </div>
                            <div class="grid grid-cols-[80px_1fr] gap-y-1 text-sm text-gray-600 mt-2">
                                <span class="text-gray-400">เลขบัตร:</span> <span class="font-medium" id="tenantId">-</span>
                                <span class="text-gray-400">เบอร์โทร:</span> <span class="font-medium" id="tenantPhone">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button onclick="switchToEditMode(false)" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">
                            แก้ไขข้อมูล
                        </button>
                        
                        <a id="btnMoveOut" href="#" class="bg-[#f27b6d] hover:bg-[#d65f51] text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm flex items-center justify-center">
                            แจ้งย้ายออก
                        </a>
                        
                        <a id="btnMeter" href="#" class="bg-[#f2b45c] hover:bg-[#e0a653] text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm flex items-center justify-center">
                            จดมิเตอร์น้ำ/ไฟ
                        </a>
                        
                        <a id="btnBill" href="#" class="bg-[#56ab91] hover:bg-[#469d82] text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm flex items-center justify-center">
                            ดูบิลค่าเช่า
                        </a>
                    </div>
                </div>

                <div id="editView" class="hidden">
                    <form action="#" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ห้อง <span class="text-red-500">*</span></label>
                            <input type="text" id="editRoomNumber" readonly class="w-full bg-gray-100 border border-gray-300 text-gray-500 rounded-lg px-3 py-2 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                            <input type="text" id="editName" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                        <div class="pt-4 text-center">
                            <button type="button" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-colors w-40">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/common.js') }}"></script>
    @yield('scripts')
</body>
</html>
