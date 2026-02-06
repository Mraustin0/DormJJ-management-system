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
        /* Custom File Input */
        .file-upload-group { display: flex; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; }
        .file-name { flex: 1; padding: 0.5rem 0.75rem; background: #fff; color: #9ca3af; font-size: 0.875rem; display: flex; align-items: center; }
        .file-btn { background: #d1d5db; color: #374151; padding: 0.5rem 1rem; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: 0.2s; white-space: nowrap; }
        .file-btn:hover { background: #9ca3af; color: white; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'home'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-all duration-300">
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

                <form action="{{ route('rooms.storeAssignment', $room->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        
                        <div class="space-y-5">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลผู้เช่า</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ห้อง <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="tenant_name" placeholder="กรุณากรอกชื่อ-สกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เบอร์โทร <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" placeholder="กรุณากรอกเบอร์โทร" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เลขบัตรประจำตัวประชาชน <span class="text-red-500">*</span></label>
                                <input type="text" name="nid" placeholder="กรุณากรอกเลขประจำตัวประชาชน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                                <input type="email" name="email" placeholder="กรุณากรอกอีเมล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div class="pt-4 space-y-4">
                                <h4 class="text-sm font-bold text-gray-800">เอกสาร</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">สัญญาเช่า <span class="text-red-500">*</span></label>
                                    <div class="file-upload-group">
                                        <div class="file-name" id="name-contract">เลือกไฟล์</div>
                                        <label for="file-contract" class="file-btn">อัปโหลด</label>
                                        <input type="file" id="file-contract" name="contract_file" class="hidden" onchange="updateFileName(this, 'name-contract')">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">สำเนาบัตรประชาชน <span class="text-red-500">*</span></label>
                                    <div class="file-upload-group">
                                        <div class="file-name" id="name-idcard">เลือกไฟล์</div>
                                        <label for="file-idcard" class="file-btn">อัปโหลด</label>
                                        <input type="file" id="file-idcard" name="idcard_file" class="hidden" onchange="updateFileName(this, 'name-idcard')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลการเข้าพัก</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เลขห้อง <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ค่ามัดจำ/ค่าประกันห้องพัก <span class="text-red-500">*</span></label>
                                <input type="number" name="deposit" placeholder="ระบุยอดเงิน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ระยะสัญญา <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="contract_duration" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none text-gray-600">
                                        <option value="">ระยะสัญญา</option>
                                        <option value="6">6 เดือน</option>
                                        <option value="12">12 เดือน</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">วันที่ทำสัญญา <span class="text-red-500">*</span></label>
                                <input type="date" name="contract_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none text-gray-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">วันที่เข้าพัก <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none text-gray-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">สถานะผู้เช่า <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="tenant_status" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none text-gray-600">
                                        <option value="active">กำลังเข้าพัก</option>
                                        <option value="reserved">จอง</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 flex justify-end">
                                <button type="submit" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-12 rounded-lg shadow-sm transition-colors text-sm w-40">
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
        function updateFileName(input, displayId) {
            const display = document.getElementById(displayId);
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
                display.style.color = "#374151";
            }
        }
    </script>
</body>
</html>