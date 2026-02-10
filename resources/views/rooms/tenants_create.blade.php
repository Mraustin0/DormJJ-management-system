<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างบัญชีผู้ใช้ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'tenants.create'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>
        </nav>

        <main class="p-8">
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-[#4A90E2] mb-8">สร้างบัญชีผู้ใช้</h2>

                <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                        {{-- Left Column - Personal Info --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลส่วนตัว</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="tenant_name" value="{{ old('tenant_name') }}" placeholder="กรุณากรอกชื่อ นาม สกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์ <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="กรุณากรอกอีเมล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เลขบัตรประชาชน <span class="text-red-500">*</span></label>
                                <input type="text" name="nid" value="{{ old('nid') }}" placeholder="กรุณากรอกเลขบัตรประชาชน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            {{-- Login Information --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">Log In Information</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input type="text" name="username" value="{{ old('username') }}" placeholder="กรุณากรอกชื่อบัญชีผู้ใช้" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input type="password" name="password" placeholder="กรุณากรอกรหัสผ่าน" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- Emergency Contact --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">ผู้ติดต่อฉุกเฉิน</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="กรุณากรอกชื่อ นาม สกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์ติดต่อฉุกเฉิน <span class="text-red-500">*</span></label>
                                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - Accommodation Info --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลการเข้าพัก</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เลขห้อง</label>
                                <div class="relative">
                                    <select name="room_id" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none cursor-pointer" required>
                                        <option value="" disabled selected>เลือกห้อง</option>
                                        @foreach($vacantRooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                ห้อง {{ $room->room_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ระยะสัญญา <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="contract_duration" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none cursor-pointer" required>
                                        <option value="" disabled selected>-</option>
                                        <option value="6" {{ old('contract_duration') == '6' ? 'selected' : '' }}>6 เดือน</option>
                                        <option value="12" {{ old('contract_duration') == '12' ? 'selected' : '' }}>12 เดือน</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เข้าพัก <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" placeholder="วว/ดด/ปปปป" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer" required>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่ทำสัญญา <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="date" name="contract_date" value="{{ old('contract_date') }}" placeholder="วว/ดด/ปปปป" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer" required>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สถานะผู้เช่า</label>
                                <div class="relative">
                                    <select name="tenant_status" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none cursor-pointer">
                                        <option value="" disabled selected>- ผู้เช่า / Admin</option>
                                        <option value="active" {{ old('tenant_status') == 'active' ? 'selected' : '' }}>ผู้เช่า (กำลังเข้าพัก)</option>
                                        <option value="reserved" {{ old('tenant_status') == 'reserved' ? 'selected' : '' }}>จอง</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Initial Meter Settings --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">ตั้งค่ามิเตอร์เริ่มต้น</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เลขมิเตอร์ไฟ</label>
                                        <input type="number" name="initial_electric_meter" value="{{ old('initial_electric_meter') }}" placeholder="กรุณากรอกมิเตอร์ไฟเริ่มต้น" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เลขมิเตอร์น้ำ</label>
                                        <input type="number" name="initial_water_meter" value="{{ old('initial_water_meter') }}" placeholder="กรุณากรอกมิเตอร์น้ำเริ่มต้น" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                    </div>
                                </div>
                            </div>

                            {{-- Documents --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">เอกสาร</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">สัญญาเช่า *</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <span id="contract-file-name" class="flex-1 px-4 py-2.5 text-gray-400 text-sm truncate bg-white">เลือกไฟล์</span>
                                            <label for="contract_file" class="px-6 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium cursor-pointer hover:bg-gray-400 transition-colors">
                                                อัปโหลด
                                            </label>
                                            <input type="file" id="contract_file" name="contract_file" class="hidden" onchange="updateFileName(this, 'contract-file-name')">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">สำเนาบัตรประชาชน *</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <span id="idcard-file-name" class="flex-1 px-4 py-2.5 text-gray-400 text-sm truncate bg-white">เลือกไฟล์</span>
                                            <label for="idcard_file" class="px-6 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium cursor-pointer hover:bg-gray-400 transition-colors">
                                                อัปโหลด
                                            </label>
                                            <input type="file" id="idcard_file" name="idcard_file" class="hidden" onchange="updateFileName(this, 'idcard-file-name')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-gray-200">
                        <a href="{{ route('rooms.customers') }}" class="bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold py-3 px-10 rounded-lg shadow-md transition-colors">
                            ยกเลิก
                        </a>
                        <button type="submit" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-10 rounded-lg shadow-md transition-colors">
                            บันทึก
                        </button>
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
                display.classList.remove('text-gray-400');
                display.classList.add('text-gray-800');
            } else {
                display.textContent = 'เลือกไฟล์';
                display.classList.add('text-gray-400');
                display.classList.remove('text-gray-800');
            }
        }
    </script>
</body>
</html>
