<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>สร้างบิลประจำเดือน - JJ Apartment</title>
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
                {{-- Notification Bell --}}
                <a href="{{ route('rooms.bills') }}?status=pending" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin1' }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
                </div>
            </div>
        </nav>

        <main class="p-6">
            {{-- Header with filters --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h3 class="text-2xl font-bold text-gray-800">สร้างบิลประจำเดือน</h3>

                <form action="{{ route('bills.create') }}" method="GET" class="flex items-center gap-3">
                    {{-- Month Picker --}}
                    <div class="relative">
                        <input type="month" name="month" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                               class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer min-w-[180px]">
                    </div>

                    {{-- Floor Selector --}}
                    <select name="floor" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer appearance-none bg-white bg-[url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M6%208L1%203h10z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px] bg-[right_12px_center] bg-no-repeat pr-9 min-w-[100px]">
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ $currentFloor == $i ? 'selected' : '' }}>ชั้น {{ $i }}</option>
                        @endfor
                    </select>

                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" name="search" placeholder="ค้นหา..." value="{{ request('search') }}"
                               class="border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#4A90E2] w-48">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>
            </div>

            {{-- Search Results Info --}}
            @if($search ?? false)
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
                    $meter = $room->meterReadings->first();
                    $hasMeterData = $meter && ($meter->water_curr || $meter->elec_curr);
                @endphp
                <div onclick="selectRoom({{ $room->id }}, '{{ $room->room_number }}', {{ $hasContract ? 'true' : 'false' }})"
                     class="p-6 rounded-2xl shadow-sm border-2 text-center relative transition-all cursor-pointer hover:shadow-lg hover:scale-105 group
                            {{ $hasContract ? 'bg-red-50 border-red-200 hover:border-red-400' : 'bg-green-50 border-green-200 hover:border-green-400' }}">

                    {{-- Status Indicator --}}
                    <div class="absolute top-3 right-3 w-3 h-3 rounded-full {{ $hasContract ? 'bg-red-500' : 'bg-green-500' }}"></div>

                    {{-- Room Number --}}
                    <h3 class="text-3xl font-bold {{ $hasContract ? 'text-red-700' : 'text-green-700' }} mb-1">{{ $room->room_number }}</h3>

                    {{-- Status Text --}}
                    <p class="text-sm {{ $hasContract ? 'text-red-500' : 'text-green-500' }}">
                        {{ $hasContract ? 'มีผู้เช่า' : 'ว่าง' }}
                    </p>

                    {{-- Tenant Name (if occupied) --}}
                    @if($hasContract)
                        <p class="text-xs text-red-400 mt-2 truncate">{{ $room->contract->tenant_name }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            @if($rooms->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-400 text-lg">ไม่พบห้องในชั้นนี้</p>
            </div>
            @endif

        </main>
    </div>

    <script>
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

            // ไปหน้าสร้างบิลสำหรับห้องนั้น
            const month = '{{ $selectedMonth }}';
            window.location.href = '/bills/create/' + roomId + '?month=' + month;
        }
    </script>
</body>
</html>
