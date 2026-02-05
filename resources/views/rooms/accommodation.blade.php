<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการเข้าพัก - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            <a href="{{ route('rooms.index') }}" class="block px-6 py-3 text-gray-800 text-lg font-bold rounded-lg text-center hover:bg-gray-100 transition-colors">
                หน้าหลัก
            </a>
            <div>
                <h3 class="text-gray-500 font-bold text-sm mb-3">จัดการข้อมูลหลัก</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('meters.water') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์น้ำ</a></li>
                    <li><a href="{{ route('meters.electric') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์ไฟฟ้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบิล</a></li>
                    <li><a href="{{ route('rooms.accommodation') }}" class="block px-2 py-2 bg-[#A0A0A0] text-white rounded-lg font-medium text-lg shadow-sm">ข้อมูลการเข้าพัก</a></li>
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

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800">ข้อมูลการเข้าพัก</h2>
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
                
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-xl font-bold text-gray-800">รายชื่อผู้เข้าพัก ({{ $contracts->total() }} รายการ)</h3>
                    <div class="relative w-full md:w-auto">
                        <input type="text" placeholder="ค้นหาชื่อ หรือ ห้อง..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-64">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="py-4 px-4 font-bold text-gray-600 w-16 rounded-tl-lg">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600 w-24">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ-สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600">วันที่เข้าพัก</th>
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tr-lg">ระยะสัญญา</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($contracts as $index => $contract)
                            @php
                                $startDate = $contract->start_date ?? $contract->created_at;
                                $endDate = $contract->end_date;
                                if ($startDate && $endDate) {
                                    $diff = $startDate->diff($endDate);
                                    $parts = [];
                                    if ($diff->y > 0) $parts[] = $diff->y . ' ปี';
                                    if ($diff->m > 0) $parts[] = $diff->m . ' เดือน';
                                    if (empty($parts) && $diff->d > 0) $parts[] = $diff->d . ' วัน';
                                    $duration = implode(' ', $parts) ?: '-';
                                } elseif ($startDate) {
                                    $diff = $startDate->diff(now());
                                    $parts = [];
                                    if ($diff->y > 0) $parts[] = $diff->y . ' ปี';
                                    if ($diff->m > 0) $parts[] = $diff->m . ' เดือน';
                                    if (empty($parts) && $diff->d > 0) $parts[] = $diff->d . ' วัน';
                                    $duration = (implode(' ', $parts) ?: '< 1 เดือน') . ' (ต่อเนื่อง)';
                                } else {
                                    $duration = '-';
                                }
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-gray-500">{{ $contracts->firstItem() + $index }}</td>
                                <td class="py-4 px-4 font-bold text-[#4A90E2]">{{ $contract->room->room_number ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $contract->tenant_name }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $startDate ? $startDate->format('d/m/Y') : '-' }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $duration }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">ไม่พบข้อมูลผู้เข้าพัก</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $contracts->links('pagination::tailwind') }}
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