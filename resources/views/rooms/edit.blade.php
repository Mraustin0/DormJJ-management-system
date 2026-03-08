<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล - JJ Apartment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Sarabun', sans-serif; }
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        /* Custom File Input Styling */
        .file-upload-wrapper { position: relative; display: flex; align-items: center; }
        .file-name-display { flex: 1; border: 1px solid #d1d5db; border-right: none; padding: 0.5rem 0.75rem; border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; background-color: #f9fafb; color: #374151; font-size: 0.875rem; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .file-upload-btn { background-color: #9ca3af; color: white; padding: 0.5rem 1rem; border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: background-color 0.2s; }
        .file-upload-btn:hover { background-color: #6b7280; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'home'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'ระบบจัดการหอพัก JJ Apartment'])

        <main class="p-8 max-w-7xl mx-auto w-full">
            {{-- ปุ่มกลับ --}}
            <div class="mb-4">
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#4A90E2] transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    กลับหน้าหลัก
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10">
                
                <h2 class="text-2xl font-bold text-gray-900 mb-8 border-b pb-4">
                    {{ $room->room_number }} {{ $room->contract->tenant_name ?? 'ไม่มีชื่อผู้เช่า' }}
                </h2>

                <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        
                        <div class="space-y-5">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลส่วนตัว</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                                <input type="text" name="tenant_name" value="{{ $room->contract->tenant_name ?? '' }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เบอร์ <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ $room->contract->phone ?? '' }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">อีเมล <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ $room->contract->email ?? '' }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เลขบัตรประชาชน <span class="text-red-500">*</span></label>
                                <input type="text" name="nid" value="{{ $room->contract->nid ?? '' }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div class="pt-4 space-y-4">
                                <h4 class="text-sm font-bold text-gray-800">เอกสาร</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">สัญญาเช่า</label>
                                    <div class="file-upload-wrapper">
                                        <div class="file-name-display" id="file-name-contract">หนังสือสัญญาเช่าห้อง{{ $room->room_number }}.pdf</div>
                                        <label for="contract_file" class="file-upload-btn">อัปโหลด</label>
                                        <input type="file" id="contract_file" name="contract_file" class="hidden" onchange="updateFileName(this, 'file-name-contract')">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">สำเนาบัตรประชาชน</label>
                                    <div class="file-upload-wrapper">
                                        <div class="file-name-display" id="file-name-idcard">สำเนาบัตรประชาชน{{ $room->contract->tenant_name ?? '' }}.pdf</div>
                                        <label for="idcard_file" class="file-upload-btn">อัปโหลด</label>
                                        <input type="file" id="idcard_file" name="idcard_file" class="hidden" onchange="updateFileName(this, 'file-name-idcard')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลการเข้าพัก</h3>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">เลขห้อง <span class="text-red-500">*</span></label>
                                <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-gray-100 border border-gray-300 text-gray-500 rounded-lg px-4 py-2.5 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">ระยะสัญญา <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select name="contract_duration" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none">
                                        <option value="6">6 เดือน</option>
                                        <option value="12">12 เดือน</option>
                                        <option value="24">24 เดือน</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">วันที่เข้าพัก <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" value="{{ $room->contract->check_in_date?->format('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">สถานะผู้เช่า</label>
                                <div class="relative">
                                    @if(in_array($room->status, ['ว่าง', 'จอง', 'รอเข้าพัก']))
                                        {{-- ห้องว่าง/จอง/รอเข้าพัก: แสดงแบบ read-only --}}
                                        <select class="w-full bg-gray-100 border border-gray-200 text-gray-500 rounded-lg px-4 py-2.5 appearance-none cursor-not-allowed outline-none" disabled>
                                            <option selected>
                                                {{ $room->status == 'ว่าง' ? 'ว่าง (ย้ายออกแล้ว)' : 'รอเข้าพัก' }}
                                            </option>
                                        </select>
                                    @else
                                        <select name="tenant_status" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none">
                                            <option value="active"      {{ $room->status == 'ไม่ว่าง'      ? 'selected' : '' }}>กำลังเข้าพัก</option>
                                            <option value="moving_out"  {{ $room->status == 'แจ้งย้ายออก' ? 'selected' : '' }}>แจ้งย้ายออก</option>
                                            <option value="moved_out">ย้ายออก</option>
                                        </select>
                                    @endif
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-8 flex justify-end gap-4">
                                @if($room->contract)
                                <button type="button"
                                        onclick="confirmDelete({{ $room->contract->id }}, '{{ addslashes($room->contract->tenant_name) }}')"
                                        class="bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold py-2.5 px-8 rounded-lg shadow-sm transition-colors text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    ลบข้อมูล
                                </button>
                                @endif
                                <button type="submit" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-colors text-sm">
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
        // Update file name when file is selected
        function updateFileName(input, displayId) {
            const display = document.getElementById(displayId);
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
            }
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'ลบข้อมูลผู้เช่า?',
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
                            }).then(() => window.location.href = '{{ route("rooms.index") }}');
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