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

                {{-- Notification Bell with Dropdown --}}
                <div class="relative" id="notificationContainer">
                    <button onclick="toggleNotifications()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="unreadBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold" style="display: none;">0</span>
                    </button>

                    {{-- Notification Dropdown Panel --}}
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-800">Notifications</h3>
                            <div class="flex gap-4 mt-3">
                                <button onclick="filterNotifications('all')" class="notification-tab text-sm font-bold text-[#4A90E2] pb-1 border-b-2 border-[#4A90E2]" data-tab="all">All</button>
                                <button onclick="filterNotifications('unread')" class="notification-tab text-sm font-medium text-gray-400 pb-1" data-tab="unread">Unread</button>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto" id="notificationList">
                            <a href="{{ route('rooms.bills') }}?status=pending" class="notification-item flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50" data-notif-id="demo-1" data-read="false" data-color="#4A90E2" onclick="markAsRead(this, event)">
                                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center flex-shrink-0 notif-icon">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 notif-title">ห้อง 101</p>
                                    <p class="text-sm text-[#4A90E2] notif-desc">แจ้งเตือนการโอนเงิน</p>
                                    <p class="text-xs text-gray-400 mt-1">1 วันที่แล้ว</p>
                                </div>
                                <div class="w-2 h-2 rounded-full bg-[#4A90E2] mt-2 unread-dot"></div>
                            </a>
                            <a href="{{ route('rooms.accommodation') }}" class="notification-item flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50" data-notif-id="demo-2" data-read="false" data-color="#f2b45c" onclick="markAsRead(this, event)">
                                <div class="w-10 h-10 rounded-full bg-[#f2b45c] flex items-center justify-center flex-shrink-0 notif-icon">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 notif-title">ห้อง 102</p>
                                    <p class="text-sm text-[#f2b45c] notif-desc">สัญญาใกล้หมดอายุ</p>
                                    <p class="text-xs text-gray-400 mt-1">4 วันที่แล้ว</p>
                                </div>
                                <div class="w-2 h-2 rounded-full bg-[#f2b45c] mt-2 unread-dot"></div>
                            </a>
                            <a href="{{ route('rooms.bills') }}?status=pending" class="notification-item flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50" data-notif-id="demo-3" data-read="false" data-color="#f27b6d" onclick="markAsRead(this, event)">
                                <div class="w-10 h-10 rounded-full bg-[#f27b6d] flex items-center justify-center flex-shrink-0 notif-icon">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 notif-title">ห้อง 110</p>
                                    <p class="text-sm text-[#f27b6d] notif-desc">ค้างชำระค่าเช่า</p>
                                    <p class="text-xs text-gray-400 mt-1">1 วันที่แล้ว</p>
                                </div>
                                <div class="w-2 h-2 rounded-full bg-[#f27b6d] mt-2 unread-dot"></div>
                            </a>
                            <a href="{{ route('rooms.customers') }}" class="notification-item flex items-start gap-3 p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50" data-notif-id="demo-4" data-read="false" data-color="#9ca3af" onclick="markAsRead(this, event)">
                                <div class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center flex-shrink-0 notif-icon">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800 notif-title">ห้อง 201</p>
                                    <p class="text-sm text-gray-500 notif-desc">แจ้งย้ายออก</p>
                                    <p class="text-xs text-gray-400 mt-1">4 วันที่แล้ว</p>
                                </div>
                                <div class="w-2 h-2 rounded-full bg-gray-400 mt-2 unread-dot"></div>
                            </a>
                        </div>
                        <div class="p-3 border-t border-gray-100 text-center">
                            <a href="{{ route('rooms.bills') }}?status=pending" class="text-[#4A90E2] text-sm font-bold hover:underline">View All</a>
                        </div>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 hover:bg-gray-50 rounded-lg p-1 pr-2 transition-colors">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">Admin</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-[#f2b45c] flex items-center justify-center text-white font-bold shadow-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="py-2">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="font-medium">แก้ไขโปรไฟล์</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-medium">ตั้งค่าระบบ</span>
                            </a>
                            <hr class="my-2">
                            <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmLogout()" class="flex items-center gap-3 px-4 py-2.5 text-red-500 hover:bg-red-50 transition-colors w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span class="font-medium">ออกจากระบบ</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-8">
            {{-- Filter bar ใต้ navbar --}}
            <div class="mb-4 flex items-center gap-3">
                <form action="{{ route('meters.electric') }}" method="GET" class="flex items-center gap-3">
                    <select name="floor" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#f2b45c] cursor-pointer appearance-none bg-white bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L1%203h10z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[right_12px_center] bg-no-repeat pr-9 min-w-[120px]">
                        <option value="">ทุกชั้น</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ request('floor') == $i ? 'selected' : '' }}>ชั้น {{ $i }}</option>
                        @endfor
                    </select>
                    <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#f2b45c] cursor-pointer min-w-[180px]">
                </form>
            </div>

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
                                <th class="py-4 px-4 font-bold text-gray-600 text-center rounded-tr-lg">หน่วยที่ใช้</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($rooms as $room)
                            @php
                                $meter = $room->meterReadings->first();
                                $elecPrev = $meter ? $meter->elec_prev : ($room->display_elec_prev ?? 0);
                                $elecCurr = $meter ? $meter->elec_curr : null;
                                $elecUnit = $meter ? $meter->elec_unit : null;
                                $isVacant = $room->status !== 'ไม่ว่าง';
                            @endphp
                            <tr class="border-b border-gray-100 transition-colors {{ $isVacant ? 'bg-gray-50' : 'hover:bg-yellow-50/30' }}"
                                data-room="{{ $room->room_number }}" data-room-id="{{ $room->id }}" data-vacant="{{ $isVacant ? 'true' : 'false' }}">
                                <td class="py-4 px-4 text-gray-400">{{ $loop->iteration }}</td>
                                <td class="py-4 px-4 font-bold {{ $isVacant ? 'text-gray-400' : 'text-[#f2b45c]' }}">{{ $room->room_number }}</td>
                                <td class="py-4 px-4 font-medium {{ $isVacant ? 'text-gray-400' : 'text-gray-700' }}">
                                    @if($isVacant)
                                        ห้องว่าง
                                        <span class="ml-1 text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full">ไม่สามารถกรอกได้</span>
                                    @else
                                        {{ $room->contract->tenant_name }}
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <input type="number"
                                           id="elecPrev_{{ $room->id }}"
                                           value="{{ $elecPrev }}"
                                           readonly
                                           class="w-24 text-center border rounded-lg px-2 py-1.5 outline-none border-gray-200 bg-gray-100 {{ $isVacant ? 'text-gray-400' : 'text-gray-600' }} cursor-not-allowed">
                                </td>
                                <td class="py-4 px-4 text-center">
                                    @if($isVacant)
                                        <span class="text-gray-300">—</span>
                                    @else
                                        <div>
                                            <input type="number"
                                                   id="elecCurr_{{ $room->id }}"
                                                   value="{{ $elecCurr }}"
                                                   placeholder="--"
                                                   oninput="calcElecUnit({{ $room->id }})"
                                                   class="w-24 text-center border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-yellow-300 outline-none">
                                            <span id="elecCurrError_{{ $room->id }}" class="hidden text-red-500 text-xs mt-1 block">ต้องมากกว่าค่าเดิม</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span id="elecUnit_{{ $room->id }}" class="{{ $isVacant ? 'text-gray-300' : 'font-bold text-gray-800' }}">
                                        {{ $isVacant ? '—' : ($elecUnit !== null ? $elecUnit . ' หน่วย' : '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ปุ่มบันทึกทั้งหมด --}}
                <div class="mt-6 flex justify-end">
                    <button onclick="saveAll()"
                            id="saveAllBtn"
                            class="bg-[#f2b45c] hover:bg-[#e0a653] text-white font-bold py-2.5 px-8 rounded-lg transition-colors shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        บันทึกทั้งหมด
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        function calcElecUnit(roomId) {
            const prevEl = document.getElementById('elecPrev_' + roomId);
            const currEl = document.getElementById('elecCurr_' + roomId);
            const errEl  = document.getElementById('elecCurrError_' + roomId);
            const unitEl = document.getElementById('elecUnit_' + roomId);

            const prev    = parseFloat(prevEl.value) || 0;
            const currVal = currEl.value;
            const curr    = parseFloat(currVal);
            const hasError = currVal !== '' && !isNaN(curr) && curr < prev;

            // แสดง/ซ่อน error ใต้ช่อง
            currEl.style.borderColor = hasError ? '#f87171' : '';
            currEl.style.boxShadow   = hasError ? '0 0 0 2px #fecaca' : '';
            if (errEl) errEl.classList.toggle('hidden', !hasError);

            // อัปเดตหน่วย
            if (!hasError && currVal !== '') {
                unitEl.innerText = Math.max(0, curr - prev) + ' หน่วย';
            } else {
                unitEl.innerText = '-';
            }
        }

        function saveElectric(roomId) {
            return saveElectricSilent(roomId, true);
        }

        function saveElectricSilent(roomId, showAlert = false) {
            const prev    = parseFloat(document.getElementById('elecPrev_' + roomId).value) || 0;
            const currEl  = document.getElementById('elecCurr_' + roomId);
            const currVal = currEl ? currEl.value : '';

            if (!currVal || currVal === '') {
                if (showAlert) Swal.fire({ icon: 'warning', title: 'กรุณากรอกเลขมิเตอร์ใหม่', showConfirmButton: true });
                return Promise.resolve(false);
            }

            // เลขใหม่ < เลขเดิม → error แสดงใต้ช่องแล้ว ไม่บันทึก
            if (parseFloat(currVal) < prev) {
                return Promise.resolve(false);
            }

            const formData = new FormData();
            formData.append('room_id', roomId);
            formData.append('billing_month', '{{ $selectedMonth }}');
            formData.append('elec_prev', prev);
            formData.append('elec_curr', currVal);

            return fetch("{{ route('meters.electric.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && showAlert) {
                    Swal.fire({ icon: 'success', title: 'บันทึกค่าไฟสำเร็จ!', showConfirmButton: false, timer: 1200 });
                }
                return data.success;
            })
            .catch(error => {
                console.error('Error:', error);
                return false;
            });
        }

        function saveAll() {
            const rows = document.querySelectorAll('tr[data-room-id]');
            const promises = [];
            const roomIds = [];

            rows.forEach(row => {
                const roomId = row.dataset.roomId;
                if (!roomId) return;
                if (row.dataset.vacant === 'true') return; // ข้ามห้องว่าง
                const curr   = document.getElementById('elecCurr_' + roomId)?.value;
                const errEl  = document.getElementById('elecCurrError_' + roomId);
                const hasErr = errEl && !errEl.classList.contains('hidden');
                if (curr && curr !== '' && !hasErr) {
                    roomIds.push(roomId);
                    promises.push(saveElectricSilent(roomId, false));
                }
            });

            if (promises.length === 0) {
                Swal.fire({ icon: 'warning', title: 'ไม่มีข้อมูลมิเตอร์ใหม่', text: 'กรุณากรอกเลขมิเตอร์ใหม่อย่างน้อย 1 ห้อง' });
                return;
            }

            const btn = document.getElementById('saveAllBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> กำลังบันทึก...';

            Promise.all(promises).then(results => {
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> บันทึกทั้งหมด';
                const success = results.filter(r => r).length;
                Swal.fire({ icon: 'success', title: `บันทึกสำเร็จ ${success}/${promises.length} ห้อง!`, showConfirmButton: false, timer: 1500 });
            });
        }

        // Toggle Notification Dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) profileDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // Toggle Profile Dropdown
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) notificationDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // Confirm Logout with SweetAlert2
        function confirmLogout() {
            Swal.fire({
                title: 'ออกจากระบบ?',
                text: 'คุณต้องการออกจากระบบใช่หรือไม่',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f27b6d',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ออกจากระบบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Get read notifications from localStorage
        function getReadNotifications() {
            const stored = localStorage.getItem('readNotifications');
            return stored ? JSON.parse(stored) : [];
        }

        // Save read notification to localStorage
        function saveReadNotification(notifId) {
            const readNotifs = getReadNotifications();
            if (!readNotifs.includes(notifId)) {
                readNotifs.push(notifId);
                localStorage.setItem('readNotifications', JSON.stringify(readNotifs));
            }
        }

        // Apply read state to notification element
        function applyReadState(element) {
            element.dataset.read = 'true';
            element.classList.remove('bg-blue-50');
            const dot = element.querySelector('.unread-dot');
            if (dot) dot.style.display = 'none';
            const iconBg = element.querySelector('.notif-icon');
            if (iconBg) {
                iconBg.className = 'w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center flex-shrink-0 notif-icon';
            }
            const title = element.querySelector('.notif-title');
            const desc = element.querySelector('.notif-desc');
            if (title) {
                title.classList.remove('font-bold', 'text-gray-800');
                title.classList.add('font-medium', 'text-gray-500');
            }
            if (desc) {
                desc.className = 'text-sm text-gray-400 notif-desc';
            }
        }

        // Mark notification as read
        function markAsRead(element, event) {
            const notifId = element.dataset.notifId;
            if (notifId) {
                saveReadNotification(notifId);
            }
            applyReadState(element);
            updateUnreadCount();
        }

        // Update unread count badge
        function updateUnreadCount() {
            const items = document.querySelectorAll('.notification-item');
            let unreadCount = 0;
            items.forEach(item => {
                if (item.dataset.read === 'false') unreadCount++;
            });

            const badge = document.getElementById('unreadBadge');
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        // Filter Notifications (All / Unread)
        function filterNotifications(type) {
            const tabs = document.querySelectorAll('.notification-tab');
            const items = document.querySelectorAll('.notification-item');

            tabs.forEach(tab => {
                if (tab.dataset.tab === type) {
                    tab.classList.add('text-[#4A90E2]', 'border-b-2', 'border-[#4A90E2]', 'font-bold');
                    tab.classList.remove('text-gray-400', 'font-medium');
                } else {
                    tab.classList.remove('text-[#4A90E2]', 'border-b-2', 'border-[#4A90E2]', 'font-bold');
                    tab.classList.add('text-gray-400', 'font-medium');
                }
            });

            items.forEach(item => {
                if (type === 'all') {
                    item.style.display = 'flex';
                } else if (type === 'unread') {
                    item.style.display = item.dataset.read === 'false' ? 'flex' : 'none';
                }
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const notificationContainer = document.getElementById('notificationContainer');
            const profileContainer = document.getElementById('profileContainer');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');

            if (notificationContainer && !notificationContainer.contains(e.target)) {
                notificationDropdown?.classList.add('hidden');
            }
            if (profileContainer && !profileContainer.contains(e.target)) {
                profileDropdown?.classList.add('hidden');
            }
        });

        // On page load, restore read states from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const readNotifs = getReadNotifications();
            const items = document.querySelectorAll('.notification-item');

            items.forEach(item => {
                const notifId = item.dataset.notifId;
                if (notifId && readNotifs.includes(notifId)) {
                    applyReadState(item);
                } else if (item.dataset.read === 'false') {
                    item.classList.add('bg-blue-50');
                }
            });

            updateUnreadCount();
        });
    </script>
</body>
</html>
