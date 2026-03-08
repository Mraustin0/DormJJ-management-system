<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>แจ้งย้ายออก - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> body { font-family: 'Sarabun', 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'home'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 md:hidden"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h2 class="text-xl font-bold text-gray-800">แจ้งย้ายออก</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block"><p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p><p class="text-xs text-gray-500">ผู้ดูแลระบบ</p></div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8 max-w-5xl mx-auto w-full">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-4">แจ้งย้ายออกห้อง <span class="text-[#4A90E2]">{{ $room->room_number }}</span></h2>

                <div class="mb-6">
                    <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#4A90E2] transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        กลับหน้าหลัก
                    </a>
                </div>

                <form id="moveOutForm" class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <input type="hidden" id="contractId" value="{{ $room->contract->id ?? '' }}">
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-4">ข้อมูลผู้เช่า</h3>
                            <div class="space-y-3 text-sm">
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">ชื่อ-สกุล:</span> <span class="font-bold text-gray-900">{{ $room->contract->tenant_name ?? '-' }}</span></div>
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">เลขบัตรประชาชน:</span> <span class="font-medium text-gray-800">{{ $room->contract->nid ?? '-' }}</span></div>
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">เบอร์โทรศัพท์:</span> <span class="font-medium text-gray-800">{{ $room->contract->phone ?? '-' }}</span></div>
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">อีเมล:</span> <span class="font-medium text-gray-800">{{ $room->contract->email ?? '-' }}</span></div>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg text-gray-800 mb-4">ข้อมูลสัญญา</h3>
                            <div class="space-y-3 text-sm mb-6">
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">สัญญาเช่า:</span> <span class="font-medium text-gray-800">สัญญาห้อง {{ $room->room_number }}</span></div>
                                <div class="grid grid-cols-[140px_1fr]"><span class="text-gray-500">วันที่เริ่มเช่า:</span> <span class="font-medium text-gray-800">{{ $room->contract && $room->contract->start_date ? $room->contract->start_date->format('d/m/Y') : '-' }}</span></div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">วันที่แจ้งย้ายออก <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="date" id="notifyDate" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none text-gray-700">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-600 mb-1">วันที่ย้ายออกจริง <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="date" id="moveOutDate" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none text-gray-700">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="font-bold text-lg text-gray-800 mb-4">ข้อมูลการชำระเงิน</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ยอดค้างชำระ (ถ้ามี)</label>
                                <input type="number" id="debt" oninput="calculate()" placeholder="ระบุยอดค้างชำระ" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ค่าเสียหาย (ถ้ามี)</label>
                                <input type="number" id="fine" oninput="calculate()" placeholder="ระบุค่าเสียหาย" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">เงินประกัน/เงินมัดจำ</label>
                                <input type="text" value="5,000" readonly class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-gray-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">ยอดรวม (คืน/หัก)</label>
                                <input type="text" id="total" value="5,000" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-gray-50 text-[#4A90E2] font-bold text-lg cursor-not-allowed">
                            </div>
                        </div>

                        <div class="pt-8 text-right">
                            <button type="button" onclick="confirmMoveOut()" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-12 rounded-xl shadow-lg transition-all transform hover:scale-105">
                                บันทึก
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        function calculate() {
            const deposit = 5000;
            const debt = parseFloat(document.getElementById('debt').value) || 0;
            const fine = parseFloat(document.getElementById('fine').value) || 0;
            const total = deposit - debt - fine;
            document.getElementById('total').value = total.toLocaleString();
        }

        function confirmMoveOut() {
            const moveOutDate = document.getElementById('moveOutDate').value;
            if (!moveOutDate) {
                Swal.fire('กรุณาระบุวันที่', 'กรุณาระบุวันที่ย้ายออกจริง', 'warning');
                return;
            }

            Swal.fire({
                title: 'ยืนยันการย้ายออก?',
                text: "สถานะห้องจะถูกเปลี่ยนเป็น 'ว่าง' และสิ้นสุดสัญญา",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4A90E2',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('contracts.moveout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            contract_id: document.getElementById('contractId').value,
                            move_out_date: moveOutDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', 'บันทึกข้อมูลเรียบร้อย', 'success').then(() => {
                                window.location.href = "{{ route('rooms.index') }}";
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
                    });
                }
            })
        }
    </script>
</body>
</html>