<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JJ Apartment - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    @include('partials.sidebar', ['activePage' => 'home'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        
        @include('partials.navbar', ['pageTitle' => 'Dashboard', 'pendingSlipsCount' => $total_pending])

        <main class="p-6 max-w-7xl mx-auto w-full">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#56ab91] p-6 rounded-3xl text-white shadow-lg relative overflow-hidden group hover:-translate-y-2 transition-all cursor-pointer">
                    <div class="relative z-10">
                        <p class="text-xs opacity-90 font-bold uppercase tracking-wider">ห้องว่าง</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $total_vacant }} <span class="text-lg font-normal opacity-90">ห้อง</span></h3>
                    </div>
                    <div class="absolute -bottom-3 -right-3 text-white/20 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/></svg>
                    </div>
                </div>
                <div class="bg-[#f27b6d] p-6 rounded-3xl text-white shadow-lg relative overflow-hidden group hover:-translate-y-2 transition-all cursor-pointer">
                    <div class="relative z-10">
                        <p class="text-xs opacity-90 font-bold uppercase tracking-wider">ห้องไม่ว่าง</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $total_occupied }} <span class="text-lg font-normal opacity-90">ห้อง</span></h3>
                    </div>
                    <div class="absolute -bottom-3 -right-3 text-white/20 group-hover:scale-110 transition-transform duration-500">
                       <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                </div>
                <div class="bg-[#f59e0b] p-6 rounded-3xl text-white shadow-lg relative overflow-hidden group hover:-translate-y-2 transition-all cursor-pointer">
                    <div class="relative z-10">
                        <p class="text-xs opacity-90 font-bold uppercase tracking-wider">รอเข้าพัก</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $total_waiting }} <span class="text-lg font-normal opacity-90">ห้อง</span></h3>
                    </div>
                    <div class="absolute -bottom-3 -right-3 text-white/20 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
                    </div>
                </div>
                <div class="bg-[#4A90E2] p-6 rounded-3xl text-white shadow-lg relative overflow-hidden group hover:-translate-y-2 transition-all cursor-pointer">
                    <div class="relative z-10">
                        <p class="text-xs opacity-90 font-bold uppercase tracking-wider">ค้างค่าระ</p>
                        <h3 class="text-3xl font-bold mt-2">{{ $total_pending }} <span class="text-lg font-normal opacity-90">ห้อง</span></h3>
                    </div>
                    <div class="absolute -bottom-3 -right-3 text-white/20 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center justify-between gap-6 transition-all hover:shadow-md">
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-gray-800 text-lg font-bold">สัดส่วนห้องพัก (ชั้น {{ $currentFloor }})</h4>
                    <p class="text-gray-400 text-sm">ข้อมูลการใช้งานห้องพักชั้นที่เลือก</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4 text-[11px] font-bold uppercase">
                        <span class="flex items-center gap-2 text-[#56ab91] bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-[#56ab91]"></span> ว่าง {{ $floor_vacant }}
                        </span>
                        <span class="flex items-center gap-2 text-[#f27b6d] bg-red-50 px-3 py-1.5 rounded-lg border border-red-100">
                            <span class="w-2 h-2 rounded-full bg-[#f27b6d]"></span> ไม่ว่าง {{ $floor_occupied }}
                        </span>
                        <span class="flex items-center gap-2 text-[#f59e0b] bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100">
                            <span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span> รอเข้าพัก {{ $floor_waiting }}
                        </span>
                    </div>
                </div>
                <div class="w-36 h-36 relative">
                    <canvas id="roomChart"></canvas>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-black text-gray-800 uppercase italic underline decoration-[#4A90E2] decoration-4 underline-offset-4">สถานะห้องพัก</h2>
                <div class="flex gap-1.5 bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm overflow-x-auto max-w-full">
                    @foreach([1,2,3,4] as $floor)
                        <a href="?floor={{ $floor }}" class="whitespace-nowrap px-5 py-2 rounded-xl text-sm font-bold transition-all {{ ($currentFloor == $floor) ? 'bg-[#4A90E2] text-white shadow-md' : 'text-gray-400 hover:bg-gray-50' }}">
                            ชั้น {{ $floor }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($rooms as $room)
                @php
                    $cardColor = match($room->status) {
                        'ไม่ว่าง'                  => 'bg-[#f27b6d] border-[#f27b6d]',
                        'รอเข้าพัก', 'จอง'         => 'bg-[#f59e0b] border-[#f59e0b]',
                        'แจ้งย้ายออก'              => 'bg-[#f2b45c] border-[#f2b45c]',
                        default                    => 'bg-[#56ab91] border-[#56ab91]',
                    };
                @endphp
                <div onclick="openRoomModal({{ json_encode($room) }}, {{ json_encode($room->contract) }})"
                     class="p-6 rounded-3xl shadow-sm border text-center relative transition-all group cursor-pointer hover:shadow-xl hover:scale-105 {{ $cardColor }}">
                    <div class="absolute top-4 right-4 w-3 h-3 rounded-full bg-white/80"></div>
                    <h3 class="text-2xl font-bold text-white leading-none mt-1">
                        {{ $room->room_number }}
                    </h3>
                    <p class="text-[10px] text-white/90 font-bold uppercase mt-3 tracking-widest">
                        {{ $room->status }}
                    </p>
                    @if(in_array($room->status, ['ไม่ว่าง', 'รอเข้าพัก', 'จอง', 'แจ้งย้ายออก']) && $room->contract)
                        <p class="text-[10px] text-white/80 mt-2 truncate bg-white/20 rounded py-0.5 px-2">
                            {{ $room->contract->tenant_name }}
                        </p>
                    @endif
                </div>
                @endforeach
            </div>

        </main>
    </div>

    <div id="roomModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform duration-200 transition-all" id="modalPanel">
            
            <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800" id="modalTitle">ห้อง 103</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-8">
                
                <div id="vacantView" class="hidden flex-col items-center text-center">
                    <div class="w-20 h-20 bg-[#56ab91]/20 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-[#56ab91]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-6" id="vacantRoomTitle"></h4>
                    <div class="flex gap-3 w-full">
                        <a id="btnOldTenant" href="{{ route('rooms.customers') }}"
                           class="flex-1 border-2 border-[#4A90E2] text-[#4A90E2] hover:bg-blue-50 font-bold py-3 rounded-xl transition-colors flex justify-center items-center">
                            ผู้เช่าเก่า
                        </a>
                        <a id="btnAssign" href="#"
                           class="flex-1 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-xl transition-colors shadow-md shadow-blue-200 flex justify-center items-center">
                            ผู้เช่าใหม่
                        </a>
                    </div>
                </div>

                <div id="occupiedView" class="hidden">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex-shrink-0 flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800" id="tenantName">-</h4>
                            <p class="text-sm font-bold mt-1" id="roomStatusLabel">กำลังเข้าพัก</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-8 text-sm">
                        <div class="grid grid-cols-[140px_1fr]">
                            <span class="text-gray-400">เลขบัตรประชาชน:</span>
                            <span class="font-medium text-gray-800" id="tenantId">-</span>
                        </div>
                        <div class="grid grid-cols-[140px_1fr]">
                            <span class="text-gray-400">เบอร์โทร:</span>
                            <span class="font-medium text-gray-800" id="tenantPhone">-</span>
                        </div>
                        <div class="grid grid-cols-[140px_1fr]">
                            <span class="text-gray-400">อีเมล:</span>
                            <span class="font-medium text-gray-800" id="tenantEmail">-</span>
                        </div>
                        <div class="grid grid-cols-[140px_1fr]">
                            <span class="text-gray-400">วันที่เข้าพัก:</span>
                            <span class="font-medium text-gray-800" id="checkInDate">-</span>
                        </div>
                        <div class="grid grid-cols-[140px_1fr]">
                            <span class="text-gray-400">หนังสือสัญญาเช่า:</span>
                            <a id="btnContractLink" href="#" target="_blank"
                               class="font-medium text-[#4A90E2] underline cursor-pointer hover:text-[#357abd] transition-colors">
                               📄 ดูสัญญาเช่า
                            </a>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <a id="btnEdit" href="#" class="flex-1 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-xl transition-colors text-center shadow-sm">
                            แก้ไข
                        </a>
                        
                        <a id="btnMoveOut" href="#" class="flex-1 bg-[#f27b6d] hover:bg-[#d65f51] text-white font-bold py-3 rounded-xl transition-colors text-center shadow-sm">
                            แจ้งย้ายออก
                        </a>
                    </div>
                </div>

                <div id="editView" class="hidden">
                    <form action="#" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ห้อง</label>
                            <input type="text" id="editRoomNumber" readonly class="w-full bg-gray-100 border border-gray-300 text-gray-500 rounded-lg px-3 py-2 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ชื่อ-สกุล</label>
                            <input type="text" id="editName" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                        <div class="pt-4 text-center">
                            <button type="button" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-colors w-40">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        let currentRoom = null;
        let currentContract = null;

        // ฟังก์ชันเปิด Modal
        function openRoomModal(room, contract) {
            currentRoom = room;
            currentContract = contract;
            
            const rId = room.roomId ? room.roomId : room.id;
            console.log("Room Data:", room);

            const modal = document.getElementById('roomModal');
            const modalPanel = document.getElementById('modalPanel');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalPanel.classList.remove('scale-95');
                modalPanel.classList.add('scale-100');
            }, 10);

            resetModalViews();
            document.getElementById('modalTitle').innerText = 'ห้อง ' + room.room_number;
            
            if (room.status === 'ว่าง') {
                document.getElementById('vacantView').classList.remove('hidden');
                document.getElementById('vacantView').classList.add('flex');
                document.getElementById('vacantRoomTitle').innerText = 'ห้อง ' + room.room_number;

                const btnAssign = document.getElementById('btnAssign');
                if (btnAssign) {
                    btnAssign.href = "/rooms/" + rId + "/contract/create";
                }


            } else {
                document.getElementById('occupiedView').classList.remove('hidden');
                populateTenantInfo();

                // แสดง label ตามสถานะห้อง
                const statusLabel = document.getElementById('roomStatusLabel');
                if (room.status === 'รอเข้าพัก' || room.status === 'จอง') {
                    statusLabel.textContent = 'รอเข้าพัก';
                    statusLabel.className = 'text-sm font-bold mt-1 text-amber-500';
                } else if (room.status === 'แจ้งย้ายออก') {
                    statusLabel.textContent = 'แจ้งย้ายออก';
                    statusLabel.className = 'text-sm font-bold mt-1 text-orange-500';
                } else {
                    statusLabel.textContent = 'กำลังเข้าพัก';
                    statusLabel.className = 'text-sm font-bold mt-1 text-red-500';
                }
                
                
                // Link: แจ้งย้ายออก
                const btnMoveOut = document.getElementById('btnMoveOut');
                if (btnMoveOut) {
                    btnMoveOut.href = "/rooms/" + rId + "/moveout";
                }

                // Link: แก้ไข (ไปหน้าใหม่ตามที่ขอ)
                const btnEdit = document.getElementById('btnEdit');
                if (btnEdit) {
                    btnEdit.href = "/rooms/" + rId + "/edit";
                }

                // Link: ดูสัญญาเช่า
                const btnContractLink = document.getElementById('btnContractLink');
                if (btnContractLink) {
                    btnContractLink.href = "/rooms/" + rId + "/contract/view";
                }
            }
        }

        function resetModalViews() {
            document.getElementById('vacantView').classList.add('hidden');
            document.getElementById('vacantView').classList.remove('flex');
            document.getElementById('occupiedView').classList.add('hidden');
            document.getElementById('editView').classList.add('hidden');
        }

        

        function formatThaiDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d)) return dateStr;
            const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }

        function populateTenantInfo() {
            if (currentContract) {
                document.getElementById('tenantName').innerText  = currentContract.tenant_name || '-';
                document.getElementById('tenantId').innerText    = currentContract.nid   || '-';
                document.getElementById('tenantPhone').innerText = currentContract.phone || '-';
                document.getElementById('tenantEmail').innerText = currentContract.email || '-';
                document.getElementById('checkInDate').innerText = formatThaiDate(currentContract.check_in_date);
            } else {
                document.getElementById('tenantName').innerText  = 'ไม่พบข้อมูล';
                document.getElementById('tenantId').innerText    = '-';
                document.getElementById('tenantPhone').innerText = '-';
                document.getElementById('tenantEmail').innerText = '-';
                document.getElementById('checkInDate').innerText = '-';
            }
        }

    

        function closeModal() {
            const modal = document.getElementById('roomModal');
            const modalPanel = document.getElementById('modalPanel');
            modalPanel.classList.remove('scale-100');
            modalPanel.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        document.getElementById('roomModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // Chart - แสดงข้อมูลตามชั้นที่เลือก
        const ctx = document.getElementById('roomChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['ว่าง', 'ไม่ว่าง', 'รอเข้าพัก'],
                datasets: [{
                    data: [{{ $floor_vacant }}, {{ $floor_occupied }}, {{ $floor_waiting }}],
                    backgroundColor: ['#56ab91', '#f27b6d', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: { cutout: '75%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>