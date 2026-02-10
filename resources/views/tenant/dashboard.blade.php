<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'dashboard'])

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
                    <h2 class="text-xl font-bold text-gray-800">หน้าหลัก</h2>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ Auth::user()->username }}</span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            {{-- Room Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-[#4A90E2] rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">ห้อง {{ $room->room_number }}</h3>
                        <p class="text-gray-500">ชั้น {{ $room->floor }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm">ผู้เช่า</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $contract->tenant_name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm">ค่าเช่า/เดือน</p>
                        <p class="text-lg font-semibold text-gray-800">{{ number_format($room->room_rate) }} บาท</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm">วันเข้าพัก</p>
                        <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($contract->check_in_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm">สัญญาสิ้นสุด</p>
                        <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Unpaid Amount --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">ยอดค้างชำระ</h3>
                        @if($totalUnpaid > 0)
                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-medium">{{ $unpaidBills->count() }} รายการ</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">ชำระครบแล้ว</span>
                        @endif
                    </div>
                    <p class="text-3xl font-bold {{ $totalUnpaid > 0 ? 'text-red-500' : 'text-green-500' }}">
                        {{ number_format($totalUnpaid, 2) }} บาท
                    </p>
                    @if($totalUnpaid > 0)
                        <a href="{{ route('tenant.bills') }}?status=pending" class="mt-4 inline-block text-[#4A90E2] hover:underline">
                            ดูรายการค้างชำระ →
                        </a>
                    @endif
                </div>

                {{-- Latest Bill --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">บิลล่าสุด</h3>
                    @if($latestBill)
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500">เดือน</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($latestBill->billing_month)->format('m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">ยอดรวม</span>
                                <span class="font-bold text-lg">{{ number_format($latestBill->total_amount, 2) }} บาท</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">สถานะ</span>
                                @if($latestBill->status == 'paid')
                                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">ชำระแล้ว</span>
                                @elseif($latestBill->status == 'overdue')
                                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-medium">เกินกำหนด</span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-sm font-medium">รอชำระ</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('tenant.bills.view', $latestBill->id) }}" class="mt-4 inline-block w-full text-center px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#3a7bc8] transition-colors">
                            ดูบิล
                        </a>
                    @else
                        <p class="text-gray-500 text-center py-4">ยังไม่มีบิล</p>
                    @endif
                </div>
            </div>

            {{-- Contract Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">สัญญาเช่า</h3>
                    <a href="{{ route('tenant.contract') }}" class="text-[#4A90E2] hover:underline">ดูรายละเอียด →</a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">ระยะเวลาสัญญา</p>
                        <p class="font-semibold">{{ $contract->contract_duration ?? 12 }} เดือน</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">เริ่มสัญญา</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">สิ้นสุดสัญญา</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">เงินมัดจำ</p>
                        <p class="font-semibold">{{ number_format($contract->deposit ?? 0, 2) }} บาท</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
