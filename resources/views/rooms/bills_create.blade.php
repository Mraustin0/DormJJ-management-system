<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>สร้างบิลประจำเดือน - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .room-card { height: 9rem; } /* fixed 144px */
        input[type=number]::-webkit-inner-spin-button { opacity: 1; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'bills.create'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        {{-- Navbar --}}
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-6">

            {{-- Header: Title + Month picker + Search (left) | Create All button (right) --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 gap-4">
                {{-- Left: Back + Title + Month + Search --}}
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-800">สร้างบิลประจำเดือน</h3>

                    <form action="{{ route('bills.create') }}" method="GET" class="flex items-center gap-3 flex-wrap">
                        <input type="hidden" name="floor" value="{{ $currentFloor }}">
                        {{-- Month Picker --}}
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                               class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer">
                        {{-- Search --}}
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหาห้อง..." value="{{ request('search') }}"
                                   class="border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#4A90E2] w-44">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>
                </div>

                {{-- Right: Create All button --}}
                <button onclick="openCreateAllModal()"
                        class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition-colors flex items-center gap-2 text-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    สร้างบิลทั้งหมด
                </button>
            </div>

            {{-- Floor navigation --}}
            @if(!($search ?? false))
            <div class="flex items-center justify-between mb-5">
                {{-- Prev / Next --}}
                <div class="flex items-center gap-2">
                    @if($currentFloor > 1)
                        <a href="{{ route('bills.create') }}?floor={{ $currentFloor - 1 }}&month={{ $selectedMonth }}"
                           class="flex items-center gap-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            ชั้นก่อนหน้า
                        </a>
                    @else
                        <span class="flex items-center gap-1 px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            ชั้นก่อนหน้า
                        </span>
                    @endif

                    {{-- Floor tabs --}}
                    <div class="flex items-center gap-1 mx-2">
                        @for($i = 1; $i <= 4; $i++)
                            <a href="{{ route('bills.create') }}?floor={{ $i }}&month={{ $selectedMonth }}"
                               class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-bold transition
                                      {{ $currentFloor == $i ? 'bg-[#4A90E2] text-white shadow-md' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>

                    @if($currentFloor < 4)
                        <a href="{{ route('bills.create') }}?floor={{ $currentFloor + 1 }}&month={{ $selectedMonth }}"
                           class="flex items-center gap-1 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                            ชั้นถัดไป
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="flex items-center gap-1 px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed">
                            ชั้นถัดไป
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-500">ชั้น {{ $currentFloor }} • {{ $rooms->count() }} ห้อง</p>
            </div>
            @else
            <div class="mb-4 p-3 bg-blue-50 rounded-lg flex justify-between items-center">
                <p class="text-sm text-blue-700">ผลการค้นหา "{{ $search }}" พบ {{ $rooms->count() }} ห้อง</p>
                <a href="{{ route('bills.create') }}?month={{ $selectedMonth }}&floor={{ $currentFloor }}" class="text-sm text-blue-600 hover:underline">ล้างการค้นหา</a>
            </div>
            @endif

            {{-- Room Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($rooms as $room)
                @php
                    $hasContract = $room->contract && $room->status == 'ไม่ว่าง';
                    $hasBill = $hasContract && in_array($room->id, $existingBillRoomIds ?? []);
                @endphp
                <div onclick="selectRoom({{ $room->id }}, '{{ $room->room_number }}', {{ $hasContract ? 'true' : 'false' }})"
                     class="room-card p-3 rounded-2xl shadow-sm border-2 flex flex-col items-center justify-center text-center relative cursor-pointer hover:shadow-lg hover:scale-105 transition-all
                            {{ $hasContract ? 'bg-red-50 border-red-200 hover:border-red-400' : 'bg-green-50 border-green-200 hover:border-green-400' }}">

                    {{-- Bill checkmark --}}
                    @if($hasBill)
                        <div class="absolute top-2 left-2 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Status dot --}}
                    <div class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full {{ $hasContract ? 'bg-red-400' : 'bg-green-400' }}"></div>

                    {{-- Room Number --}}
                    <h3 class="text-3xl font-bold {{ $hasContract ? 'text-red-700' : 'text-green-700' }} leading-none">
                        {{ $room->room_number }}
                    </h3>

                    {{-- Status --}}
                    <p class="text-xs mt-1 {{ $hasContract ? 'text-red-500' : 'text-green-500' }} font-medium">
                        {{ $hasContract ? 'มีผู้เช่า' : 'ว่าง' }}
                    </p>

                    @if($hasContract)
                        <p class="text-xs text-gray-500 mt-1 truncate w-full px-2">{{ $room->contract->tenant_name }}</p>
                    @endif

                    @if($hasBill)
                        <span class="text-xs text-green-600 font-bold mt-1">✓ ส่งบิลแล้ว</span>
                    @endif
                </div>
                @endforeach
            </div>

            @if($rooms->isEmpty())
            <div class="text-center py-16">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <p class="text-gray-400 text-lg">ไม่พบห้องในชั้นนี้</p>
            </div>
            @endif

        </main>
    </div>

    {{-- ===== CREATE ALL MODAL ===== --}}
    <div id="createAllModal" class="fixed inset-0 bg-black/50 z-50 hidden items-start justify-center p-4 pt-10 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col mb-10">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl z-10">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">สร้างบิลทั้งหมด</h3>
                    <p class="text-sm text-gray-500 mt-0.5">
                        เดือน {{ \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('F Y') }}
                        • แก้ไขค่าได้ก่อนยืนยัน
                    </p>
                </div>
                <button onclick="closeCreateAllModal()" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Legend --}}
            <div class="px-6 pt-3 pb-2 flex items-center gap-4 text-xs text-gray-500 bg-gray-50 border-b border-gray-100">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> ส่งบิลแล้ว (จะ overwrite)
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> ไม่มีข้อมูลมิเตอร์
                </span>
                <span class="ml-auto text-gray-400">* แก้ไขค่าอื่นๆ ได้โดยตรงในตาราง</span>
            </div>

            {{-- Modal Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                            <th class="px-4 py-3 text-left w-20">ห้อง</th>
                            <th class="px-4 py-3 text-left">ผู้เช่า</th>
                            <th class="px-4 py-3 text-center w-32">ค่าห้อง (฿)</th>
                            <th class="px-4 py-3 text-center w-40">ค่าไฟ (฿)<br><span class="font-normal normal-case text-gray-400">(หน่วย)</span></th>
                            <th class="px-4 py-3 text-center w-40">ค่าน้ำ (฿)<br><span class="font-normal normal-case text-gray-400">(หน่วย)</span></th>
                            <th class="px-4 py-3 text-center w-32">ค่าอื่นๆ (฿)</th>
                            <th class="px-4 py-3 text-right w-32 text-[#4A90E2]">รวม (฿)</th>
                        </tr>
                    </thead>
                    <tbody id="modalRoomsBody" class="divide-y divide-gray-100">
                        {{-- Populated by JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Empty state --}}
            <div id="modalEmpty" class="hidden text-center py-12 text-gray-400">
                <p class="text-lg">ไม่มีห้องที่มีผู้เช่า</p>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl sticky bottom-0">
                <div class="text-sm">
                    <span class="text-gray-600">จำนวนห้อง: </span>
                    <span id="modalRoomCount" class="font-bold text-gray-800">0</span>
                    <span class="ml-4 text-gray-600">รวมทั้งหมด: </span>
                    <span id="modalGrandTotal" class="font-bold text-lg text-[#4A90E2]">฿0</span>
                </div>
                <div class="flex gap-3">
                    <button onclick="closeCreateAllModal()"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition text-sm">
                        ยกเลิก
                    </button>
                    <button onclick="submitAllBills()"
                            class="px-6 py-2.5 bg-[#4A90E2] hover:bg-[#357abd] text-white rounded-lg font-bold transition text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        ยืนยันสร้างบิลทั้งหมด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ข้อมูลห้องทั้งหมดจาก PHP
        const allRoomsData = @json($allRoomsData);
        const selectedMonth = '{{ $selectedMonth }}';

        // clone เพื่อให้ผู้ใช้แก้ไขได้
        let editableRooms = allRoomsData.map(r => ({ ...r }));

        // ===== Room card click =====
        function selectRoom(roomId, roomNumber, hasContract) {
            if (!hasContract) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ห้องว่าง',
                    text: 'ห้อง ' + roomNumber + ' ยังไม่มีผู้เช่า ไม่สามารถสร้างบิลได้',
                    confirmButtonColor: '#4A90E2'
                });
                return;
            }
            window.location.href = '/bills/create/' + roomId + '?month=' + selectedMonth;
        }

        // ===== Modal open/close =====
        function openCreateAllModal() {
            editableRooms = allRoomsData.map(r => ({ ...r }));
            renderModalTable();
            const modal = document.getElementById('createAllModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateAllModal() {
            const modal = document.getElementById('createAllModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close on backdrop click
        document.getElementById('createAllModal').addEventListener('click', function(e) {
            if (e.target === this) closeCreateAllModal();
        });

        // ===== Render table rows =====
        function renderModalTable() {
            const tbody = document.getElementById('modalRoomsBody');
            const emptyEl = document.getElementById('modalEmpty');
            tbody.innerHTML = '';

            if (editableRooms.length === 0) {
                emptyEl.classList.remove('hidden');
                updateGrandTotal();
                return;
            }
            emptyEl.classList.add('hidden');

            editableRooms.forEach((room, idx) => {
                const hasBillClass = room.has_bill ? 'bg-green-50' : '';
                const noMeterBadge = !room.has_meter
                    ? `<span class="ml-1 text-yellow-500" title="ไม่มีข้อมูลมิเตอร์">⚠</span>` : '';

                const tr = document.createElement('tr');
                tr.className = `hover:bg-gray-50 transition ${hasBillClass}`;
                tr.innerHTML = `
                    <td class="px-4 py-3 align-middle">
                        <div class="flex items-center gap-1">
                            <span class="font-bold text-gray-800">ห้อง ${room.room_number}</span>
                            ${room.has_bill ? '<span class="text-green-500 text-xs">✓</span>' : ''}
                        </div>
                        <div class="text-xs text-gray-400">ชั้น ${room.floor}</div>
                    </td>
                    <td class="px-4 py-3 align-middle">
                        <span class="text-gray-700 text-sm">${room.tenant_name}</span>
                        ${noMeterBadge}
                    </td>
                    <td class="px-4 py-3 text-center align-middle">
                        <span class="font-medium text-gray-800 text-sm">${Number(room.room_rate).toLocaleString('th-TH')}</span>
                        <div class="text-xs text-transparent mt-0.5">-</div>
                    </td>
                    <td class="px-4 py-3 text-center align-middle">
                        <span class="font-medium text-gray-800 text-sm">${Number(room.electric_amount).toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 2})}</span>
                        <div class="text-xs text-gray-400 mt-0.5">${room.electric_units} หน่วย</div>
                    </td>
                    <td class="px-4 py-3 text-center align-middle">
                        <span class="font-medium text-gray-800 text-sm">${Number(room.water_amount).toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 2})}</span>
                        <div class="text-xs text-gray-400 mt-0.5">${room.water_units} หน่วย</div>
                    </td>
                    <td class="px-4 py-3 text-center align-middle">
                        <input type="number" min="0" step="0.01"
                               class="w-28 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:border-[#4A90E2] focus:outline-none"
                               value="${room.other_fees}"
                               oninput="updateField(${idx}, 'other_fees', this.value)">
                        <div class="text-xs text-transparent mt-0.5">-</div>
                    </td>
                    <td class="px-4 py-3 text-right align-middle">
                        <span id="row-total-${idx}" class="font-bold text-[#4A90E2]">
                            ฿${calcTotal(room).toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 2})}
                        </span>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            updateGrandTotal();
        }

        // ===== Field update & totals =====
        function calcTotal(room) {
            return (parseFloat(room.room_rate) || 0)
                 + (parseFloat(room.electric_amount) || 0)
                 + (parseFloat(room.water_amount) || 0)
                 + (parseFloat(room.other_fees) || 0);
        }

        function updateField(idx, field, val) {
            editableRooms[idx][field] = parseFloat(val) || 0;
            const total = calcTotal(editableRooms[idx]);
            const totalEl = document.getElementById(`row-total-${idx}`);
            if (totalEl) {
                totalEl.textContent = '฿' + total.toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            }
            updateGrandTotal();
        }

        function updateGrandTotal() {
            const grand = editableRooms.reduce((sum, r) => sum + calcTotal(r), 0);
            document.getElementById('modalGrandTotal').textContent =
                '฿' + grand.toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('modalRoomCount').textContent = editableRooms.length;
        }

        // ===== Submit =====
        function submitAllBills() {
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg> กำลังสร้างบิล...`;

            const roomsMissingMeters = editableRooms.filter(room => !room.has_meter);

            if (roomsMissingMeters.length > 0) {
                const roomList = roomsMissingMeters.map(room => `ห้อง ${room.room_number}`).join(', ');
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถสร้างบิลได้',
                    html: `ไม่สามารถสร้างบิลได้เนื่องจากยังไม่มีการบันทึกค่ามิเตอร์สำหรับห้องเหล่านี้:<br><b>${roomList}</b><br>กรุณากรอกค่ามิเตอร์ให้ครบถ้วนก่อนดำเนินการ`,
                    showCancelButton: true,
                    confirmButtonText: 'ตกลง',
                    cancelButtonText: 'กรอกมิเตอร์',
                    confirmButtonColor: '#6b7280', // Gray for "Ok"
                    cancelButtonColor: '#4A90E2' // Blue for "Enter Meter"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User clicked "ตกลง" (OK), do nothing or close alert
                    } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                        // User clicked "กรอกมิเตอร์" (Enter Meter)
                        window.location.href = '{{ route("rooms.meters") }}?month=' + selectedMonth;
                    }
                });
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg> ยืนยันสร้างบิลทั้งหมด`;
                return;
            }

            const payload = {
                billing_month: selectedMonth,
                rooms: editableRooms.map(r => ({
                    room_id:          r.room_id,
                    electric_units:   r.electric_units,
                    electric_amount:  r.electric_amount,
                    water_units:      r.water_units,
                    water_amount:     r.water_amount,
                    room_rate:        r.room_rate,
                    other_fees:       r.other_fees,
                }))
            };

            fetch('{{ route("bills.storeAll") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                closeCreateAllModal();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: `สร้างบิลสำเร็จ!`,
                        text: `สร้างบิลทั้งหมด ${data.count} ห้องเรียบร้อยแล้ว`,
                        confirmButtonColor: '#4A90E2',
                        confirmButtonText: 'ตกลง'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'warning', title: data.message ?? 'ไม่มีห้องที่สามารถสร้างบิลได้', confirmButtonColor: '#4A90E2' });
                }
            })
            .catch(() => {
                closeCreateAllModal();
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', confirmButtonColor: '#4A90E2' });
            });
        }
    </script>
</body>
</html>
