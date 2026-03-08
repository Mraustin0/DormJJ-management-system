<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้เช่า - JJ Apartment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'customers'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'แก้ไขข้อมูลผู้เช่า'])

        <main class="p-8">

            {{-- Back button --}}
            <div class="mb-4">
                <a href="{{ route('rooms.customers') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#4A90E2] transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    กลับหน้าจัดการบัญชี
                </a>
            </div>

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-[#4A90E2] mb-8">แก้ไขข้อมูลผู้เช่า</h2>

                <form action="{{ route('customers.updateFull', $contract->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">

                        {{-- ===== LEFT COLUMN - ข้อมูลส่วนตัว ===== --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลส่วนตัว</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="tenant_name" value="{{ old('tenant_name', $contract->tenant_name) }}" placeholder="กรุณากรอกชื่อ นามสกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทร</label>
                                <input type="text" name="phone" value="{{ old('phone', $contract->phone) }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" maxlength="10" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                                <input type="email" name="email" value="{{ old('email', $contract->email) }}" placeholder="กรุณากรอกอีเมล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">เลขบัตรประชาชน</label>
                                <input type="text" name="nid" value="{{ old('nid', $contract->nid) }}" placeholder="กรุณากรอกเลขบัตรประชาชน" inputmode="numeric" maxlength="13" oninput="validateNumericField(this, 'nid-error')" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                <p id="nid-error" class="hidden text-red-500 text-xs mt-1">⚠️ กรุณากรอกเฉพาะตัวเลขเท่านั้น</p>
                            </div>

                            {{-- Login Account --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-1">ข้อมูลการเข้าสู่ระบบ</h4>
                                @if($contract->user)
                                    <p class="text-sm text-gray-500 mb-4">
                                        ชื่อบัญชี: <span class="font-semibold text-gray-700">{{ $contract->user->username }}</span>
                                    </p>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่ <span class="text-xs text-gray-400">(เว้นว่างถ้าไม่ต้องการเปลี่ยน)</span></label>
                                        <input type="password" name="password" placeholder="กรอกรหัสผ่านใหม่" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                @else
                                    <p class="text-sm text-gray-400 italic">ผู้เช่ารายนี้ยังไม่มีบัญชีเข้าสู่ระบบ</p>
                                @endif
                            </div>

                            {{-- Emergency Contact --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">ผู้ติดต่อฉุกเฉิน</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-สกุล</label>
                                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $contract->emergency_contact_name) }}" placeholder="กรุณากรอกชื่อ นามสกุล" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เบอร์ติดต่อฉุกเฉิน</label>
                                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $contract->emergency_contact_phone) }}" placeholder="กรุณากรอกเบอร์โทรศัพท์" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'')" maxlength="10" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== RIGHT COLUMN - ข้อมูลการเข้าพัก ===== --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800">ข้อมูลการเข้าพัก</h3>

                            {{-- ห้อง (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ห้อง</label>
                                <div class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600 font-semibold">
                                    ห้อง {{ $contract->room->room_number ?? '-' }}
                                    @if($contract->room)
                                        @php
                                            $statusColor = match($contract->room->status) {
                                                'ไม่ว่าง'                => 'bg-red-100 text-red-600',
                                                'รอเข้าพัก', 'จอง'       => 'bg-amber-100 text-amber-600',
                                                'แจ้งย้ายออก'            => 'bg-orange-100 text-orange-600',
                                                default                  => 'bg-green-100 text-green-600',
                                            };
                                        @endphp
                                        <span class="ml-2 text-xs font-bold px-2 py-0.5 rounded-full {{ $statusColor }}">{{ $contract->room->status }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- ระยะสัญญา (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ระยะสัญญา</label>
                                <div class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600">
                                    {{ $contract->contract_duration ? $contract->contract_duration . ' เดือน' : '-' }}
                                </div>
                            </div>

                            {{-- วันที่เข้าพัก (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เข้าพัก</label>
                                <div class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600">
                                    {{ $contract->check_in_date ? \Carbon\Carbon::parse($contract->check_in_date)->locale('th')->isoFormat('D MMM YYYY') : '-' }}
                                </div>
                            </div>

                            {{-- วันที่ทำสัญญา (editable) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันที่ทำสัญญา</label>
                                <input type="date" name="contract_date" value="{{ old('contract_date', $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') : '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer">
                            </div>

                            {{-- วันหมดสัญญา (read-only) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">วันหมดสัญญา</label>
                                <div class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-600">
                                    {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->locale('th')->isoFormat('D MMM YYYY') : '-' }}
                                </div>
                            </div>

                            {{-- เข้าพักใหม่ (แสดงเฉพาะเมื่อสัญญาหมดอายุ) --}}
                            @if($contract->status === 'expired')
                            <div class="pt-4 border-t-2 border-dashed border-[#4A90E2]">
                                <h4 class="text-base font-bold text-[#4A90E2] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    เข้าพักใหม่
                                </h4>
                                <div class="space-y-4">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">เลือกห้อง <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <select name="new_room_id" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none cursor-pointer">
                                                <option value="">-- เลือกห้องว่าง --</option>
                                                @foreach($vacantRooms as $vr)
                                                    <option value="{{ $vr->id }}" {{ old('new_room_id') == $vr->id ? 'selected' : '' }}>ห้อง {{ $vr->room_number }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เข้าพัก <span class="text-red-500">*</span></label>
                                        <input type="date" name="new_check_in_date" value="{{ old('new_check_in_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ระยะสัญญา <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <select name="new_contract_duration" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none cursor-pointer">
                                                <option value="">-</option>
                                                <option value="6"  {{ old('new_contract_duration') == '6'  ? 'selected' : '' }}>6 เดือน</option>
                                                <option value="12" {{ old('new_contract_duration') == '12' ? 'selected' : '' }}>12 เดือน</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">วันที่ทำสัญญา</label>
                                        <input type="date" name="new_contract_date" value="{{ old('new_contract_date') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none cursor-pointer">
                                    </div>

                                </div>
                            </div>
                            @endif

                            {{-- เอกสาร --}}
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-base font-bold text-gray-800 mb-4">เอกสาร</h4>
                                <div class="space-y-4">

                                    {{-- สัญญาเช่า --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">สัญญาเช่า</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <span id="contract-file-name" class="flex-1 px-4 py-2.5 text-sm truncate bg-white {{ $contract->contract_file ? 'text-gray-800' : 'text-gray-400' }}">
                                                {{ $contract->contract_file ? basename($contract->contract_file) : 'เลือกไฟล์' }}
                                            </span>
                                            <label for="contract_file" class="px-6 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium cursor-pointer hover:bg-gray-400 transition-colors whitespace-nowrap">
                                                อัปโหลด
                                            </label>
                                            <input type="file" id="contract_file" name="contract_file" class="hidden" onchange="updateFileName(this, 'contract-file-name')">
                                        </div>
                                    </div>

                                    {{-- สำเนาบัตรประชาชน --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">สำเนาบัตรประชาชน</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                            <span id="idcard-file-name" class="flex-1 px-4 py-2.5 text-sm truncate bg-white {{ $contract->idcard_file ? 'text-gray-800' : 'text-gray-400' }}">
                                                {{ $contract->idcard_file ? basename($contract->idcard_file) : 'เลือกไฟล์' }}
                                            </span>
                                            <label for="idcard_file" class="px-6 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium cursor-pointer hover:bg-gray-400 transition-colors whitespace-nowrap">
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
                        <button type="button"
                                onclick="deleteContract({{ $contract->id }}, '{{ addslashes($contract->tenant_name) }}')"
                                class="w-40 bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold py-3 rounded-lg shadow-md transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            ลบข้อมูล
                        </button>
                        <button type="submit" class="w-40 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-lg shadow-md transition-colors">
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

        function validateNumericField(input, errorId) {
            const before = input.value;
            input.value = input.value.replace(/[^0-9]/g, '');
            const errEl = document.getElementById(errorId);
            if (before !== input.value) {
                errEl.classList.remove('hidden');
                setTimeout(() => errEl.classList.add('hidden'), 2500);
            }
        }

        function deleteContract(id, name) {
            Swal.fire({
                title: 'ลบข้อมูลผู้เข้าพัก?',
                html: `คุณต้องการลบข้อมูลของ <b>${name}</b> ใช่หรือไม่?<br><span class="text-sm text-red-500">การลบจะไม่สามารถกู้คืนได้</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/contracts/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบเรียบร้อย',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500,
                            }).then(() => window.location.href = '{{ route("rooms.customers") }}');
                        } else {
                            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: data.message ?? 'กรุณาลองใหม่อีกครั้ง' });
                        }
                    })
                    .catch(() => Swal.fire({ icon: 'error', title: 'ไม่สามารถเชื่อมต่อได้' }));
                }
            });
        }
    </script>
</body>
</html>
