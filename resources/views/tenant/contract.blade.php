<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่า - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'contract'])

    {{-- Main Content --}}
    <main id="mainContent" class="md:ml-72 min-h-screen transition-all duration-300">
        {{-- Top Bar --}}
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">สัญญาเช่า</h2>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            <div class="max-w-4xl mx-auto">
                {{-- Contract Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">สถานะสัญญา</h3>
                            @php
                                $endDate = \Carbon\Carbon::parse($contract->end_date);
                                $now = \Carbon\Carbon::now();
                                $daysLeft = $now->diffInDays($endDate, false);
                            @endphp
                            @if($daysLeft < 0)
                                <p class="text-red-500 font-semibold mt-1">สัญญาหมดอายุแล้ว</p>
                            @elseif($daysLeft <= 30)
                                <p class="text-yellow-500 font-semibold mt-1">สัญญาจะหมดอายุใน {{ $daysLeft }} วัน</p>
                            @else
                                <p class="text-green-500 font-semibold mt-1">สัญญายังมีผลบังคับใช้</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">หมายเลขห้อง</p>
                            <p class="text-3xl font-bold text-[#4A90E2]">{{ $contract->room->room_number }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tenant Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">ข้อมูลผู้เช่า</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">ชื่อ-นามสกุล</p>
                            <p class="font-semibold text-lg">{{ $contract->tenant_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">เลขบัตรประชาชน</p>
                            <p class="font-semibold">{{ $contract->nid ? substr($contract->nid, 0, 1) . '-' . substr($contract->nid, 1, 4) . '-' . substr($contract->nid, 5, 5) . '-' . substr($contract->nid, 10, 2) . '-' . substr($contract->nid, 12) : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">เบอร์โทรศัพท์</p>
                            <p class="font-semibold">{{ $contract->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">อีเมล</p>
                            <p class="font-semibold">{{ $contract->email ?? '-' }}</p>
                        </div>
                        @if($contract->emergency_contact_name)
                        <div>
                            <p class="text-gray-500 text-sm">ผู้ติดต่อฉุกเฉิน</p>
                            <p class="font-semibold">{{ $contract->emergency_contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">เบอร์ผู้ติดต่อฉุกเฉิน</p>
                            <p class="font-semibold">{{ $contract->emergency_contact_phone ?? '-' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Contract Details --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">รายละเอียดสัญญา</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">ระยะเวลาสัญญา</p>
                            <p class="font-semibold text-lg">{{ $contract->contract_duration ?? 12 }} เดือน</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">วันที่ทำสัญญา</p>
                            <p class="font-semibold">{{ $contract->contract_date ? \Carbon\Carbon::parse($contract->contract_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">วันเริ่มสัญญา</p>
                            <p class="font-semibold">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">วันสิ้นสุดสัญญา</p>
                            <p class="font-semibold">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">วันเข้าพัก</p>
                            <p class="font-semibold">{{ $contract->check_in_date ? \Carbon\Carbon::parse($contract->check_in_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">เงินมัดจำ</p>
                            <p class="font-semibold text-lg text-[#4A90E2]">{{ number_format($contract->deposit ?? 0, 2) }} บาท</p>
                        </div>
                    </div>
                </div>

                {{-- Room Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">ข้อมูลห้องพัก</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">หมายเลขห้อง</p>
                            <p class="font-semibold text-lg">{{ $contract->room->room_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">ชั้น</p>
                            <p class="font-semibold">{{ $contract->room->floor }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">ค่าเช่า/เดือน</p>
                            <p class="font-semibold text-lg text-[#4A90E2]">{{ number_format($contract->room->room_rate ?? 0, 2) }} บาท</p>
                        </div>
                    </div>
                </div>

                {{-- Contract Files --}}
                @if($contract->contract_file || $contract->idcard_file)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">เอกสารแนบ</h3>
                    <div class="flex flex-wrap gap-4">
                        @if($contract->contract_file)
                        <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            ไฟล์สัญญาเช่า
                        </a>
                        @endif
                        @if($contract->idcard_file)
                        <a href="{{ asset('storage/' . $contract->idcard_file) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            สำเนาบัตรประชาชน
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
