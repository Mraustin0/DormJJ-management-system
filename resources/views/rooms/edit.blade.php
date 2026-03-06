<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Prompt', sans-serif; } 
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

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-all duration-300">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="openSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 md:hidden"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <a href="{{ route('rooms.customers') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-[#4A90E2] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block"><p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p><p class="text-xs text-gray-500">ผู้ดูแลระบบ</p></div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8 max-w-7xl mx-auto w-full">
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
                                <input type="date" name="check_in_date" value="{{ $room->contract->check_in_date ?? $room->contract->created_at?->format('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">สถานะผู้เช่า</label>
                                <div class="relative">
                                    <select name="tenant_status" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 appearance-none focus:border-[#4A90E2] outline-none">
                                        <option value="active">กำลังเข้าพัก</option>
                                        <option value="moving_out">แจ้งย้ายออก</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-8 flex justify-end gap-4">
                                <button type="button" onclick="confirmDelete()" class="bg-[#ef4444] hover:bg-[#dc2626] text-white font-bold py-2.5 px-8 rounded-lg shadow-sm transition-colors text-sm">
                                    ลบข้อมูล
                                </button>
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

        // Delete Confirmation
        function confirmDelete() {
            Swal.fire({
                title: 'ยืนยันการลบข้อมูล?',
                text: "ข้อมูลผู้เช่าและสัญญาจะถูกลบออกจากระบบ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#d1d5db',
                confirmButtonText: 'ลบข้อมูล',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('ลบสำเร็จ', 'ข้อมูลถูกลบเรียบร้อยแล้ว', 'success');
                    // ใส่ Logic ส่ง Form Delete หรือ Redirect ไป Route Delete ที่นี่
                }
            })
        }
    </script>
</body>
</html>