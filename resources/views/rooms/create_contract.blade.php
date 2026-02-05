<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลผู้เช่า - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Prompt', sans-serif; } 
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity opacity-0 md:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white z-50 transform -translate-x-full md:translate-x-0 sidebar-transition shadow-xl flex flex-col border-r border-gray-100 overflow-y-auto">
        <div class="p-6 pb-2">
            <h1 class="text-[#4A90E2] font-bold text-xl leading-tight tracking-wide">DORMITORY<br>MANAGEMENT<br>SYSTEM</h1>
        </div>
        <nav class="flex-1 px-6 py-4 space-y-6">
            <a href="{{ route('rooms.index') }}" class="block px-4 py-3 bg-[#A0A0A0] text-white text-lg font-bold rounded-lg text-center shadow-sm hover:bg-gray-500 transition-colors">หน้าหลัก</a>
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

    <div id="mainContent" class="md:ml-64 flex-1 min-h-screen flex flex-col transition-all duration-300">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 md:hidden"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block"><p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Admin' }}</p><p class="text-xs text-gray-500">ผู้ดูแลระบบ</p></div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8 max-w-7xl mx-auto w-full">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10">
                
                <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b pb-4">
                    เพิ่มข้อมูลผู้เช่า ห้อง {{ $room->room_number }}
                </h2>

                <form action="{{ route('rooms.storeContract', $room->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-16 gap-y-8">
                        
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลผู้เช่า</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ห้อง <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="tenant_name" placeholder="กรุณากรอกชื่อ-สกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">เบอร์โทร <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" placeholder="กรุณากรอกเบอร์โทร" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">เลขบัตรประจำตัวประชาชน <span class="text-red-500">*</span></label>
                                <input type="text" name="nid" placeholder="กรุณากรอกเลขประจำตัวประชาชน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">อีเมล <span class="text-red-500">*</span></label>
                                <input type="email" name="email" placeholder="กรุณากรอกอีเมล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div class="pt-2 space-y-4">
                                <h4 class="text-base font-bold text-gray-800">เอกสาร</h4>
                                
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">สัญญาเช่า <span class="text-red-500">*</span></label>
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden h-[42px]">
                                        <span id="name-contract" class="flex-1 px-4 text-gray-400 text-sm truncate bg-white">เลือกไฟล์</span>
                                        <label for="file-contract" class="h-full px-6 bg-[#d1d5db] text-gray-700 text-sm font-bold flex items-center justify-center cursor-pointer hover:bg-gray-400 transition-colors">
                                            อัปโหลด
                                        </label>
                                        <input type="file" id="file-contract" name="contract_file" class="hidden" onchange="updateFileName(this, 'name-contract')">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">สำเนาบัตรประชาชน <span class="text-red-500">*</span></label>
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden h-[42px]">
                                        <span id="name-idcard" class="flex-1 px-4 text-gray-400 text-sm truncate bg-white">เลือกไฟล์</span>
                                        <label for="file-idcard" class="h-full px-6 bg-[#d1d5db] text-gray-700 text-sm font-bold flex items-center justify-center cursor-pointer hover:bg-gray-400 transition-colors">
                                            อัปโหลด
                                        </label>
                                        <input type="file" id="file-idcard" name="idcard_file" class="hidden" onchange="updateFileName(this, 'name-idcard')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลการเข้าพัก</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">เลขห้อง <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-400 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ค่ามัดจำ/ค่าประกันห้องพัก <span class="text-red-500">*</span></label>
                                <input type="number" name="deposit" placeholder="ระบุยอดเงิน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ระยะสัญญา <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="contract_duration" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none text-gray-500 cursor-pointer">
                                        <option value="" disabled selected>ระยะสัญญา</option>
                                        <option value="6">6 เดือน</option>
                                        <option value="12">12 เดือน</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">วันที่ทำสัญญา <span class="text-red-500">*</span></label>
                                <input type="date" name="contract_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none text-gray-500 uppercase cursor-pointer">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">วันที่เข้าพัก <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none text-gray-500 uppercase cursor-pointer" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">สถานะผู้เช่า <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="tenant_status" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none text-gray-500 cursor-pointer">
                                        <option value="" disabled selected>สถานะผู้เช่า</option>
                                        <option value="active">กำลังเข้าพัก</option>
                                        <option value="reserved">จอง</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-8 flex justify-end">
                                <button type="submit" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-16 rounded-lg shadow-md transition-colors text-base">
                                    บันทึก
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        function openSidebar() { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); setTimeout(() => overlay.classList.remove('opacity-0'), 10); }
        function closeSidebar() { sidebar.classList.add('-translate-x-full'); overlay.classList.add('opacity-0'); setTimeout(() => overlay.classList.add('hidden'), 300); }
        function toggleSidebar() { if(sidebar.classList.contains('-translate-x-full')) openSidebar(); else closeSidebar(); }
        window.addEventListener('resize', () => { if (window.innerWidth >= 768) { overlay.classList.add('hidden'); sidebar.classList.remove('-translate-x-full'); } else if(overlay.classList.contains('hidden')) { sidebar.classList.add('-translate-x-full'); } });

        // Update File Name
        function updateFileName(input, displayId) {
            const display = document.getElementById(displayId);
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
                display.classList.remove('text-gray-400');
                display.classList.add('text-gray-800');
            } else {
                display.textContent = 'เลือกไฟล์';
                display.classList.add('text-gray-400');
            }
        }
    </script>
</body>
</html>