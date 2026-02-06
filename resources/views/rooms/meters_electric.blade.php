<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>บันทึกมิเตอร์ไฟฟ้า - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
        .sidebar-transition { transition: transform 0.3s ease-in-out, margin 0.3s ease-in-out; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'meters.electric'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col sidebar-transition">

        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-2xl font-bold text-[#f2b45c]">บันทึกมิเตอร์ไฟฟ้า</h2>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('meters.electric') }}" method="GET" class="flex items-center gap-3">
                    <select name="floor" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#f2b45c] cursor-pointer appearance-none bg-white bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L1%203h10z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[right_12px_center] bg-no-repeat pr-9 min-w-[120px]">
                        <option value="">ทุกชั้น</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ request('floor') == $i ? 'selected' : '' }}>ชั้น {{ $i }}</option>
                        @endfor
                    </select>
                    <div class="relative">
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#f2b45c] cursor-pointer min-w-[180px]">
                    </div>
                </form>
                {{-- Notification Bell --}}
                <a href="{{ route('rooms.bills') }}?status=pending" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>
            </div>
        </nav>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">


                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="meterTable">
                        <thead>
                            <tr class="border-b border-gray-200 bg-yellow-50">
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tl-lg">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">เลขมิเตอร์เดิม</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">เลขมิเตอร์ใหม่</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">หน่วยที่ใช้</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center rounded-tr-lg">บันทึก</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($rooms as $room)
                            @php
                                $meter = $room->meterReadings->first();
                                $elecPrev = $meter ? $meter->elec_prev : 0;
                                $elecCurr = $meter ? $meter->elec_curr : null;
                                $elecUnit = $meter ? $meter->elec_unit : null;
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-yellow-50/30 transition-colors" data-room="{{ $room->room_number }}">
                                <td class="py-4 px-4 text-gray-500">{{ $loop->iteration }}</td>
                                <td class="py-4 px-4 font-bold text-[#f2b45c]">{{ $room->room_number }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">
                                    {{ $room->contract ? $room->contract->tenant_name : 'ห้องว่าง' }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <input type="number"
                                           id="elecPrev_{{ $room->id }}"
                                           value="{{ $elecPrev }}"
                                           oninput="calcElecUnit({{ $room->id }})"
                                           class="w-24 text-center border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-yellow-300 outline-none">
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <input type="number"
                                           id="elecCurr_{{ $room->id }}"
                                           value="{{ $elecCurr }}"
                                           placeholder="--"
                                           oninput="calcElecUnit({{ $room->id }})"
                                           class="w-24 text-center border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-yellow-300 outline-none">
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span id="elecUnit_{{ $room->id }}" class="font-bold text-gray-800">
                                        {{ $elecUnit !== null ? $elecUnit . ' หน่วย' : '-' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <button onclick="saveElectric({{ $room->id }})"
                                            id="saveBtn_{{ $room->id }}"
                                            class="bg-[#f2b45c] hover:bg-[#e0a653] text-white font-bold py-1.5 px-4 rounded-lg transition-colors text-xs shadow-sm">
                                        บันทึก
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function calcElecUnit(roomId) {
            const prev = parseFloat(document.getElementById('elecPrev_' + roomId).value) || 0;
            const curr = parseFloat(document.getElementById('elecCurr_' + roomId).value) || 0;
            const unit = curr - prev;
            document.getElementById('elecUnit_' + roomId).innerText = unit > 0 ? unit + ' หน่วย' : '-';
        }

        function saveElectric(roomId) {
            const prev = document.getElementById('elecPrev_' + roomId).value;
            const curr = document.getElementById('elecCurr_' + roomId).value;
            const btn = document.getElementById('saveBtn_' + roomId);

            if (!curr || curr === '') {
                Swal.fire({ icon: 'warning', title: 'กรุณากรอกเลขมิเตอร์ใหม่', showConfirmButton: true });
                return;
            }

            btn.disabled = true;
            btn.innerText = '...';

            const formData = new FormData();
            formData.append('room_id', roomId);
            formData.append('billing_month', '{{ $selectedMonth }}');
            formData.append('elec_prev', prev);
            formData.append('elec_curr', curr);

            fetch("{{ route('meters.electric.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = 'บันทึก';
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'บันทึกค่าไฟสำเร็จ!', showConfirmButton: false, timer: 1200 });
                    btn.classList.remove('bg-[#f2b45c]', 'hover:bg-[#e0a653]');
                    btn.classList.add('bg-green-500', 'hover:bg-green-600');
                    btn.innerText = 'บันทึกแล้ว';
                    setTimeout(() => {
                        btn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        btn.classList.add('bg-[#f2b45c]', 'hover:bg-[#e0a653]');
                        btn.innerText = 'บันทึก';
                    }, 2000);
                }
            })
            .catch(error => {
                btn.disabled = false;
                btn.innerText = 'บันทึก';
                console.error('Error:', error);
                Swal.fire('Oops...', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            });
        }

    </script>
</body>
</html>
