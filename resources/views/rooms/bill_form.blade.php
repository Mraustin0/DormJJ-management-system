<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>สร้างบิล - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'bills.create'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin1' }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-6">
            @php
                $monthName = \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('F Y');
                $meter = $room->meterReadings->first();
            @endphp

            <h3 class="text-xl font-bold text-gray-800 mb-6">สร้างบิล ห้อง {{ $room->room_number }} ประจำเดือน {{ $monthName }}</h3>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                {{-- Tenant Info --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">{{ $room->contract->tenant_name ?? '-' }}</h4>
                        <p class="text-sm text-[#4A90E2]">กำลังเข้าพัก</p>
                    </div>
                </div>

                <form id="billForm" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="billing_month" value="{{ $selectedMonth }}">

                    {{-- Left Column --}}
                    <div class="space-y-6">
                        {{-- Room Number --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ห้อง <span class="text-red-500">*</span></label>
                            <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500">
                        </div>

                        {{-- Water Section --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าน้ำ <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์เริ่มต้น</label>
                                    <input type="number" id="water_prev" value="{{ $meter->water_prev ?? 0 }}" oninput="calculateWater()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์ล่าสุด</label>
                                    <input type="number" id="water_curr" value="{{ $meter->water_curr ?? '' }}" oninput="calculateWater()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยที่ใช้</label>
                                    <div class="flex">
                                        <input type="number" id="water_units" name="water_units" value="{{ $meter->water_unit ?? 0 }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยละ</label>
                                    <div class="flex">
                                        <input type="number" id="water_rate" value="18" oninput="calculateWater()" class="w-full border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500 mb-1">ค่าน้ำ</label>
                                <div class="flex">
                                    <input type="number" id="water_amount" name="water_amount" value="0" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm font-bold">
                                    <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                </div>
                            </div>
                        </div>

                        {{-- Electric Section --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าไฟ <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์เริ่มต้น</label>
                                    <input type="number" id="elec_prev" value="{{ $meter->elec_prev ?? 0 }}" oninput="calculateElec()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์ล่าสุด</label>
                                    <input type="number" id="elec_curr" value="{{ $meter->elec_curr ?? '' }}" oninput="calculateElec()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยที่ใช้</label>
                                    <div class="flex">
                                        <input type="number" id="elec_units" name="electric_units" value="{{ $meter->elec_unit ?? 0 }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยละ</label>
                                    <div class="flex">
                                        <input type="number" id="elec_rate" value="8" oninput="calculateElec()" class="w-full border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500 mb-1">ค่าไฟ</label>
                                <div class="flex">
                                    <input type="number" id="elec_amount" name="electric_amount" value="0" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm font-bold">
                                    <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">
                        {{-- Room Rate --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ค่าห้อง <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" id="room_rate" name="room_rate" value="{{ $room->price ?? 3500 }}" oninput="calculateTotal()" class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        {{-- Due Date --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">วันกำหนดชำระ <span class="text-red-500">*</span></label>
                            <input type="date" id="due_date" name="due_date" value="{{ \Carbon\Carbon::parse($selectedMonth)->endOfMonth()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                        </div>

                        {{-- Other Fees --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ค่าปรับ</label>
                            <input type="text" id="fine_desc" placeholder="กรุณากรอกรายละเอียดค่าปรับ" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 mb-2 focus:border-[#4A90E2] outline-none">
                            <div class="flex">
                                <input type="number" id="other_fees" name="other_fees" value="0" oninput="calculateTotal()" placeholder="กรุณากรอกค่าปรับ" class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ยอดรวม <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" id="total_amount" name="total_amount" value="0" readonly class="w-full bg-blue-50 border-2 border-[#4A90E2] rounded-l-lg px-4 py-2.5 text-lg font-bold text-[#4A90E2]">
                                <span class="bg-[#4A90E2] text-white rounded-r-lg px-4 py-2.5 font-bold">บาท</span>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4">
                            <button type="button" onclick="saveBill()" class="w-full bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-lg transition-colors shadow-md">
                                บันทึกข้อมูล
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function calculateWater() {
            const prev = parseFloat(document.getElementById('water_prev').value) || 0;
            const curr = parseFloat(document.getElementById('water_curr').value) || 0;
            const rate = parseFloat(document.getElementById('water_rate').value) || 0;
            const units = Math.max(0, curr - prev);
            const amount = units * rate;

            document.getElementById('water_units').value = units;
            document.getElementById('water_amount').value = amount;
            calculateTotal();
        }

        function calculateElec() {
            const prev = parseFloat(document.getElementById('elec_prev').value) || 0;
            const curr = parseFloat(document.getElementById('elec_curr').value) || 0;
            const rate = parseFloat(document.getElementById('elec_rate').value) || 0;
            const units = Math.max(0, curr - prev);
            const amount = units * rate;

            document.getElementById('elec_units').value = units;
            document.getElementById('elec_amount').value = amount;
            calculateTotal();
        }

        function calculateTotal() {
            const waterAmount = parseFloat(document.getElementById('water_amount').value) || 0;
            const elecAmount = parseFloat(document.getElementById('elec_amount').value) || 0;
            const roomRate = parseFloat(document.getElementById('room_rate').value) || 0;
            const otherFees = parseFloat(document.getElementById('other_fees').value) || 0;

            const total = waterAmount + elecAmount + roomRate + otherFees;
            document.getElementById('total_amount').value = total;
        }

        function saveBill() {
            const formData = new FormData(document.getElementById('billForm'));

            fetch("{{ route('bills.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกบิลเรียบร้อย!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // ไปหน้าดูบิล
                        window.location.href = '/bills/view/{{ $room->id }}?month={{ $selectedMonth }}';
                    });
                } else {
                    Swal.fire('Error', data.message || 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            });
        }

        // Calculate on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateWater();
            calculateElec();
        });
    </script>
</body>
</html>
