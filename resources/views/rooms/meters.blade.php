<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดการมิเตอร์ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Sarabun', 'Inter', sans-serif; } 
        .sidebar-transition { transition: transform 0.3s ease-in-out, margin 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity opacity-0 md:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 transform -translate-x-full md:translate-x-0 sidebar-transition shadow-2xl flex flex-col border-r border-gray-100">
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
                    <li><a href="{{ route('meters.water') }}" class="block px-2 py-2 bg-[#A0A0A0] text-white rounded-lg font-medium text-lg shadow-sm">บันทึกมิเตอร์น้ำ</a></li>
                    <li><a href="{{ route('meters.electric') }}" class="block px-2 py-2 bg-[#A0A0A0] text-white rounded-lg font-medium text-lg shadow-sm">บันทึกมิเตอร์ไฟฟ้า</a></li>
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

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col sidebar-transition">
        
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-2xl font-bold text-[#4A90E2]">จัดการมิเตอร์น้ำ/ไฟฟ้า</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">ผู้ดูแลระบบ</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">
                
                <form action="{{ route('meters.water') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">ข้อมูลมิเตอร์น้ำ/ไฟฟ้า</h3>
                        <p class="text-gray-500 text-sm mt-1">ประจำรอบบิล: {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#4A90E2] cursor-pointer">
                        <div class="relative w-full md:w-auto">
                            <input type="text" placeholder="ค้นหาห้อง..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-64">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">   
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tl-lg">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">น้ำ (หน่วย)</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">ไฟ (หน่วย)</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">สถานะ</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center rounded-tr-lg">แก้ไข</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($rooms as $room)
                            @php
                                $meter = $room->meterReadings->first();
                                $status = $meter ? $meter->status : 'pending';
                                $water_used = $meter ? $meter->water_unit : '-';
                                $elec_used = $meter ? $meter->elec_unit : '-';
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-gray-500">{{ $loop->iteration }}</td>
                                <td class="py-4 px-4 font-bold text-[#4A90E2]">{{ $room->room_number }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">
                                    {{ $room->contract ? $room->contract->tenant_name : 'ห้องว่าง' }}
                                </td>
                                <td class="py-4 px-4 text-center text-gray-600">{{ $water_used }}</td>
                                <td class="py-4 px-4 text-center text-gray-600">{{ $elec_used }}</td>
                                <td class="py-4 px-4 text-center">
                                    @if($status == 'paid')
                                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold">ชำระแล้ว</span>
                                    @elseif($status == 'pending')
                                        <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">รอจด</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-bold">ค้างชำระ</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button onclick="openMeterModal({{ json_encode($room) }}, {{ json_encode($meter) }})" 
                                            class="text-gray-400 hover:text-[#4A90E2] transition-colors p-1 rounded-full hover:bg-blue-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="meterModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform scale-95 transition-transform duration-200" id="modalPanel">
            <div class="flex justify-between items-start px-8 py-6 border-b border-gray-100 bg-gray-50">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        ข้อมูลห้อง <span id="mRoomNum" class="text-[#4A90E2]">101</span>
                    </h3>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 overflow-hidden">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm" id="mTenantName">-</p>
                            <p class="text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded inline-block font-bold">กำลังเข้าพัก</p>
                        </div>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-8">
                <form id="meterForm">
                    <input type="hidden" id="mRoomId" name="room_id">
                    <input type="hidden" name="billing_month" value="{{ $selectedMonth }}">

                    <h4 class="text-lg font-bold text-gray-800 mb-4">บันทึกค่าน้ำ ค่าไฟ</h4>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">งวดเดือน</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">สถานะ</label>
                            <select id="mStatus" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                                <option value="pending">รอชำระ (Pending)</option>
                                <option value="paid">ชำระแล้ว (Paid)</option>
                                <option value="overdue">ค้างชำระ (Overdue)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <h5 class="font-bold text-[#4A90E2] mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                                ค่าน้ำ
                            </h5>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div><label class="text-xs text-gray-500">เริ่มต้น</label><input type="number" id="mWaterPrev" name="water_prev" class="w-full bg-white border border-gray-300 rounded px-2 py-1.5" oninput="calculateUnit()"></div>
                                <div><label class="text-xs text-gray-500">ล่าสุด</label><input type="number" id="mWaterCurr" name="water_curr" class="w-full bg-white border border-gray-300 rounded px-2 py-1.5 focus:border-blue-500 outline-none" oninput="calculateUnit()"></div>
                            </div>
                            <div class="flex justify-between items-center bg-white px-3 py-2 rounded border border-gray-200">
                                <span class="text-sm text-gray-500">หน่วยที่ใช้</span>
                                <span id="mWaterUnit" class="font-bold text-gray-800">-</span>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
                            <h5 class="font-bold text-yellow-600 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                                ค่าไฟ
                            </h5>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div><label class="text-xs text-gray-500">เริ่มต้น</label><input type="number" id="mElecPrev" name="elec_prev" class="w-full bg-white border border-gray-300 rounded px-2 py-1.5" oninput="calculateUnit()"></div>
                                <div><label class="text-xs text-gray-500">ล่าสุด</label><input type="number" id="mElecCurr" name="elec_curr" class="w-full bg-white border border-gray-300 rounded px-2 py-1.5 focus:border-yellow-500 outline-none" oninput="calculateUnit()"></div>
                            </div>
                            <div class="flex justify-between items-center bg-white px-3 py-2 rounded border border-gray-200">
                                <span class="text-sm text-gray-500">หน่วยที่ใช้</span>
                                <span id="mElecUnit" class="font-bold text-gray-800">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 text-center">
                        <button type="button" onclick="saveMeter()" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:scale-105">
                            บันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar Logic
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 768) {
                // Desktop
                sidebar.classList.toggle('md:translate-x-0');
                if (mainContent) mainContent.classList.toggle('md:ml-72');
            } else {
                // Mobile
                sidebar.classList.toggle('-translate-x-full');
                if (overlay.classList.contains('hidden')) {
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }
        }

        // Modal Logic
        function openMeterModal(room, meter) {
            const modal = document.getElementById('meterModal');
            const panel = document.getElementById('modalPanel');
            modal.classList.remove('hidden');
            setTimeout(() => { panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }, 10);

            document.getElementById('mRoomNum').innerText = room.room_number;
            document.getElementById('mTenantName').innerText = room.contract ? room.contract.tenant_name : 'ไม่พบผู้เช่า';
            document.getElementById('mRoomId').value = room.id;

            if (meter) {
                document.getElementById('mWaterPrev').value = meter.water_prev;
                document.getElementById('mWaterCurr').value = meter.water_curr;
                document.getElementById('mElecPrev').value = meter.elec_prev;
                document.getElementById('mElecCurr').value = meter.elec_curr;
                document.getElementById('mStatus').value = meter.status;
            } else {
                document.getElementById('mWaterPrev').value = 0;
                document.getElementById('mWaterCurr').value = '';
                document.getElementById('mElecPrev').value = 0;
                document.getElementById('mElecCurr').value = '';
                document.getElementById('mStatus').value = 'pending';
            }
            calculateUnit();
        }

        function calculateUnit() {
            const wPrev = parseFloat(document.getElementById('mWaterPrev').value) || 0;
            const wCurr = parseFloat(document.getElementById('mWaterCurr').value) || 0;
            const ePrev = parseFloat(document.getElementById('mElecPrev').value) || 0;
            const eCurr = parseFloat(document.getElementById('mElecCurr').value) || 0;

            document.getElementById('mWaterUnit').innerText = (wCurr - wPrev > 0) ? (wCurr - wPrev) + ' หน่วย' : '-';
            document.getElementById('mElecUnit').innerText = (eCurr - ePrev > 0) ? (eCurr - ePrev) + ' หน่วย' : '-';
        }

        function closeModal() {
            const modal = document.getElementById('meterModal');
            const panel = document.getElementById('modalPanel');
            panel.classList.remove('scale-100'); panel.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        function saveMeter() {
            const formData = new FormData(document.getElementById('meterForm'));
            
            fetch("{{ route('meters.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({ icon: 'success', title: 'บันทึกสำเร็จ!', showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Oops...', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            });
        }
    </script>
</body>
</html>