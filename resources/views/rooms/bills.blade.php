<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติบิล - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col border-r border-gray-100">
        <div class="p-8 pb-4">
            <h1 class="text-[#4A90E2] font-bold text-2xl leading-tight tracking-wide">
                DORMITORY<br>MANAGEMENT<br>SYSTEM
            </h1>
        </div>
        <nav class="flex-1 px-6 space-y-6 overflow-y-auto py-4">
            <a href="{{ route('rooms.index') }}" class="block px-6 py-3 text-gray-800 text-lg font-bold rounded-lg text-center hover:bg-gray-100 transition-colors">หน้าหลัก</a>
            <div>
                <h3 class="text-gray-500 font-bold text-sm mb-3">จัดการข้อมูลหลัก</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('meters.water') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์น้ำ</a></li>
                    <li><a href="{{ route('meters.electric') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์ไฟฟ้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบิล</a></li>
                    <li><a href="{{ route('rooms.accommodation') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ข้อมูลการเข้าพัก</a></li>
                    <li><a href="{{ route('rooms.bills') }}" class="block px-2 py-2 bg-[#A0A0A0] text-white rounded-lg font-medium text-lg shadow-sm">ประวัติบิล</a></li>
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

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800">ประวัติบิล</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">ผู้ดูแลระบบ</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">
                
                <form action="{{ route('rooms.bills') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-xl font-bold text-gray-800">ประวัติการชำระเงิน</h3>
                    
                    <div class="flex gap-3 w-full md:w-auto">
                        <div class="relative flex-1 md:flex-none">
                            <select name="month" onchange="this.form.submit()" class="appearance-none pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-48 cursor-pointer bg-white font-medium text-gray-700">
                                <option value="all">ทั้งหมด (All History)</option>
                                @foreach($months as $key => $label)
                                    <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-gray-500 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        <div class="relative flex-1 md:flex-none">
                            <input type="text" name="search" value="{{ request('search') }}" onchange="this.form.submit()" placeholder="ค้นหาห้อง..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-64">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </form>

                <h4 class="font-bold text-lg mb-4 text-gray-700 border-l-4 border-[#4A90E2] pl-3">
                    {{ $selectedMonth && $selectedMonth != 'all' ? 'รายการประจำเดือน ' . \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') : 'รายการทั้งหมด' }}
                </h4>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tl-lg text-center w-16">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600 w-24">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ-สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">เดือน</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-right">ยอดรวม</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">สถานะ</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">หลักฐาน</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center rounded-tr-lg">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($bills as $index => $bill)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-gray-500 text-center">{{ $bills->firstItem() + $index }}</td>
                                <td class="py-4 px-4 font-bold text-[#4A90E2]">{{ $bill->room->room_number ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $bill->room->contract->tenant_name ?? 'ไม่พบผู้เช่า' }}</td>
                                <td class="py-4 px-4 text-center text-gray-500">{{ \Carbon\Carbon::parse($bill->billing_month)->translatedFormat('M Y') }}</td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->total_amount, 2) }}</td>
                                
                                <td class="py-4 px-4 text-center">
                                    @if($bill->status == 'paid')
                                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg> ชำระแล้ว
                                        </span>
                                    @elseif($bill->status == 'pending')
                                        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> รอชำระ
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg> ค้างชำระ
                                        </span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <button disabled class="text-gray-300 cursor-not-allowed flex items-center gap-1 mx-auto text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        ไม่มีสลิป
                                    </button>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <a href="#" class="bg-blue-50 text-[#4A90E2] text-xs px-2.5 py-1.5 rounded-lg flex items-center gap-1 mx-auto hover:bg-[#4A90E2] hover:text-white transition-all font-bold w-fit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        ใบแจ้งหนี้
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">ไม่พบข้อมูลบิลในเดือนนี้</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $bills->links('pagination::tailwind') }}
                </div>

            </div>
        </main>
    </div>

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
        }
    </script>
</body>
</html>