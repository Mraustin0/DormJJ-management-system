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

    @include('partials.sidebar', ['activePage' => 'home'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'ระบบจัดการหอพัก JJ Apartment'])

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

                <form action="{{ route('rooms.storeContract', $room->id) }}" method="POST" enctype="multipart/form-data">
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
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" maxlength="10" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
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
                                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" maxlength="10" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column - Accommodation Info --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลการเข้าพัก</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เลขห้อง</label>
                                <input type="text" value="ห้อง {{ $room->room_number }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500 cursor-not-allowed">
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
                                <input type="date" name="check_in_date" value="{{ old('check_in_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่ทำสัญญา <span class="text-red-500">*</span></label>
                                <input type="date" name="contract_date" value="{{ old('contract_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer" required>
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
                                        <input type="number" name="initial_electric_meter" value="{{ old('initial_electric_meter', 0) }}" min="0" placeholder="กรุณากรอกมิเตอร์ไฟเริ่มต้น" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เลขมิเตอร์น้ำ</label>
                                        <input type="number" name="initial_water_meter" value="{{ old('initial_water_meter', 0) }}" min="0" placeholder="กรุณากรอกมิเตอร์น้ำเริ่มต้น" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
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
                        <a href="{{ route('rooms.index') }}" class="bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold py-3 px-10 rounded-lg shadow-md transition-colors">
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
