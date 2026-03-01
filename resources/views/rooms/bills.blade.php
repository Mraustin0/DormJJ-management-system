<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รายการบิล - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'bills'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        {{-- Navbar with Notification Bell --}}
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm no-print">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800">รายการบิล</h2>
            </div>
            <div class="flex items-center gap-4">
                {{-- Notification Bell --}}
                <div class="relative">
                    <button onclick="toggleNotifications()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $pendingCount = $bills->where('status', 'reviewing')->count();
                        @endphp
                        @if($pendingCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">
                            {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                        </span>
                        @endif
                    </button>

                    {{-- Notification Dropdown --}}
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h4 class="font-bold text-gray-800">รอตรวจสอบสลิป</h4>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @php
                                $pendingBills = $bills->where('status', 'reviewing')->take(5);
                            @endphp
                            @if($pendingBills->count() > 0)
                                @foreach($pendingBills as $pBill)
                                <div onclick="openSlipModal({{ $pBill->id }}, '{{ $pBill->room->room_number ?? '-' }}', {{ $pBill->total_amount ?? 0 }}, '{{ $pBill->payment_slip ? asset('storage/' . $pBill->payment_slip) : '' }}')" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800">ห้อง {{ $pBill->room->room_number ?? '-' }} รอตรวจสอบ</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ number_format($pBill->total_amount ?? 0, 2) }} บาท</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm">ไม่มีสลิปรอตรวจสอบ</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- User Info --}}
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">ผู้ดูแลระบบ</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">

                <!-- Header with filters -->
                <form id="filterForm" action="{{ route('rooms.bills') }}" method="GET" class="mb-6">
                    <!-- Row 1: Title and Controls -->
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800">รายการบิล</h3>
                            <span class="bg-blue-100 text-[#4A90E2] text-sm px-3 py-1 rounded-full font-bold">{{ $bills->total() }} รายการ</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Month Picker -->
                            <input type="month" name="month" value="{{ $selectedMonth != 'all' ? $selectedMonth : '' }}" onchange="this.form.submit()"
                                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer w-44">

                            <!-- Search -->
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาข้อมูล" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-48">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <!-- Filter Button -->
                            <button type="button" onclick="openFilterModal()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Row 2: Download Button -->
                    <div class="flex justify-start mt-4">
                        <button type="button" onclick="openDownloadModal()" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-blue-600 font-bold transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            ดาวน์โหลดบิล/ใบเสร็จ
                        </button>
                    </div>

                    <!-- Hidden inputs for checkbox filters -->
                    <input type="hidden" name="status" id="statusInput" value="{{ $selectedStatus ?? '' }}">
                    <input type="hidden" name="months" id="monthsInput" value="{{ request('months') }}">
                </form>

                <!-- Active Filters Display -->
                @if(($selectedStatus ?? '') || request('months'))
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($selectedStatus ?? '')
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                        สถานะ: {{ $statuses[$selectedStatus] ?? $selectedStatus }}
                        <a href="{{ route('rooms.bills', array_merge(request()->except('status'), [])) }}" class="hover:text-blue-900">&times;</a>
                    </span>
                    @endif
                    @if(request('months'))
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                        กรองตามเดือน
                        <a href="{{ route('rooms.bills', array_merge(request()->except('months'), [])) }}" class="hover:text-green-900">&times;</a>
                    </span>
                    @endif
                    <a href="{{ route('rooms.bills') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">ล้างตัวกรองทั้งหมด</a>
                </div>
                @endif

                <h4 class="font-bold text-lg mb-4 text-gray-700 border-l-4 border-[#4A90E2] pl-3">
                    {{ $selectedMonth && $selectedMonth != 'all' ? 'รายการประจำเดือน ' . \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') : 'รายการทั้งหมด' }}
                </h4>

                <!-- Bills table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tl-lg text-center w-16">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600 w-24">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ-สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">วันที่ชำระ</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-right">ยอด</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">สถานะ</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">สลิป</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center">ดูบิล</th>
                                <th class="py-4 px-4 font-bold text-gray-600 text-center rounded-tr-lg">ใบเสร็จ</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($bills as $index => $bill)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-gray-500 text-center">{{ $bills->firstItem() + $index }}</td>
                                <td class="py-4 px-4 font-bold text-[#4A90E2]">{{ $bill->room->room_number ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $bill->room->contract->tenant_name ?? 'ไม่พบผู้เช่า' }}</td>
                                <td class="py-4 px-4 text-center text-gray-500">
                                    @if($bill->receipt && $bill->receipt->receipt_date)
                                        {{ \Carbon\Carbon::parse($bill->receipt->receipt_date)->translatedFormat('j M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->total_amount ?? 0) }}</td>

                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if($bill->status == 'paid')
                                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold">ชำระแล้ว</span>
                                    @elseif($bill->status == 'reviewing')
                                        <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-bold">รอการอนุมัติ</span>
                                    @elseif($bill->status == 'pending')
                                        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-xs font-bold">รอชำระ</span>
                                    @else
                                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold">ค้างชำระ</span>
                                    @endif
                                </td>

                                {{-- Slip Column --}}
                                <td class="py-4 px-4 text-center">
                                    @if($bill->status != 'paid')
                                        <button onclick="openSlipModal({{ $bill->id }}, '{{ $bill->room->room_number ?? '-' }}', {{ $bill->total_amount ?? 0 }}, '{{ $bill->payment_slip ? asset('storage/' . $bill->payment_slip) : '' }}')" class="{{ $bill->payment_slip ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }} text-xs px-3 py-1.5 rounded-lg inline-flex items-center gap-1 hover:bg-gray-200 transition-all font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $bill->payment_slip ? 'ดูสลิป' : 'ตรวจสลิป' }}
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('bills.view', $bill->room_id) }}?month={{ $bill->billing_month }}" class="bg-blue-50 text-[#4A90E2] text-xs px-3 py-1.5 rounded-lg inline-flex items-center gap-1 hover:bg-[#4A90E2] hover:text-white transition-all font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        ดูบิล
                                    </a>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    @if($bill->receipt)
                                        <a href="{{ route('bills.receipt', $bill->id) }}" class="bg-green-50 text-green-600 text-xs px-3 py-1.5 rounded-lg inline-flex items-center gap-1 hover:bg-green-600 hover:text-white transition-all font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            ใบเสร็จ
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-lg font-medium">ไม่พบข้อมูลบิล</p>
                                    <p class="text-sm">ลองเปลี่ยนเงื่อนไขการค้นหา หรือสร้างบิลใหม่</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $bills->links('pagination::tailwind') }}
                </div>

            </div>
        </main>
    </div>

    <!-- Slip Verification Modal -->
    <div id="slipModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">หลักฐานการชำระเงิน</h3>
                <button onclick="closeSlipModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Slip Image Preview --}}
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 flex flex-col items-center justify-center bg-gray-50 min-h-[300px]">
                        {{-- Empty state --}}
                        <div id="slipPreviewEmpty" class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-400 text-sm">ผู้เช่ายังไม่ได้อัปโหลดสลิป</p>
                        </div>
                        {{-- Tenant uploaded slip image --}}
                        <img id="slipPreviewImage" src="" alt="Payment Slip" class="hidden max-w-full max-h-[250px] rounded-lg object-contain">
                        {{-- Admin can also upload/attach slip --}}
                        <div class="mt-3 w-full">
                            <label class="block cursor-pointer">
                                <div class="flex items-center justify-center gap-2 border border-gray-300 rounded-lg px-3 py-2 hover:border-[#4A90E2] transition-colors bg-white">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span id="adminSlipFileName" class="text-xs text-gray-500">แนบสลิปเพิ่ม (แอดมิน)</span>
                                </div>
                                <input type="file" id="adminSlipFile" accept="image/*" class="hidden" onchange="previewAdminSlip(this)">
                            </label>
                        </div>
                    </div>

                    {{-- Payment Form --}}
                    <div>
                        <form id="slipForm" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="bill_id" id="slipBillId">

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">เลือกวิธีการชำระเงิน <span class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="payment_method" value="เงินสด" class="w-4 h-4 text-[#4A90E2]">
                                        <span class="text-gray-700">เงินสด</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="payment_method" value="โอนเงิน" class="w-4 h-4 text-[#4A90E2]" checked>
                                        <span class="text-gray-700">โอนเงิน</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-2">วันที่ชำระเงิน</label>
                                <input type="date" name="payment_date" id="paymentDate" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-[#4A90E2]" value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">จำนวนเงิน</label>
                                <input type="text" name="amount" id="paymentAmount" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-[#4A90E2]" readonly>
                            </div>

                            <button type="submit" class="w-full bg-[#4A90E2] hover:bg-blue-600 text-white font-bold py-3 rounded-xl transition-colors">
                                อนุมัติ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">ตัวกรอง</h3>
                <button onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- Status Filter -->
                    <div>
                        <h4 class="font-bold text-gray-700 mb-3">สถานะ</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filter_status[]" value="overdue" class="status-checkbox w-4 h-4 text-[#4A90E2] rounded" {{ ($selectedStatus ?? '') == 'overdue' ? 'checked' : '' }}>
                                <span class="text-gray-700">ค้างชำระ</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filter_status[]" value="reviewing" class="status-checkbox w-4 h-4 text-[#4A90E2] rounded" {{ ($selectedStatus ?? '') == 'reviewing' ? 'checked' : '' }}>
                                <span class="text-gray-700">รอการอนุมัติ</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filter_status[]" value="paid" class="status-checkbox w-4 h-4 text-[#4A90E2] rounded" {{ ($selectedStatus ?? '') == 'paid' ? 'checked' : '' }}>
                                <span class="text-gray-700">ชำระแล้ว</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filter_status[]" value="pending" class="status-checkbox w-4 h-4 text-[#4A90E2] rounded" {{ ($selectedStatus ?? '') == 'pending' ? 'checked' : '' }}>
                                <span class="text-gray-700">รอชำระ</span>
                            </label>
                        </div>
                    </div>

                    <!-- Month Filter -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-gray-700">เดือน</h4>
                            <select id="yearSelect" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @php
                                $thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                            @endphp
                            @foreach($thaiMonths as $index => $monthName)
                            @php $monthNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT); @endphp
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="filter_months[]" value="{{ $monthNum }}" class="month-checkbox w-4 h-4 text-[#4A90E2] rounded">
                                <span class="text-gray-700">{{ $monthName }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors">
                    ล้างตัวกรอง
                </button>
                <button onclick="applyFilters()" class="px-6 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-blue-600 font-bold transition-colors">
                    ยืนยัน
                </button>
            </div>
        </div>
    </div>

    <!-- Download Bills Modal -->
    <div id="downloadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-[#4A90E2] to-blue-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    ดาวน์โหลดใบเสร็จ
                </h3>
                <button onclick="closeDownloadModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">เลือกห้องที่ต้องการดาวน์โหลดใบเสร็จ</p>

                <div class="flex items-center gap-2 mb-4">
                    <input type="checkbox" id="selectAllRooms" class="w-4 h-4 text-[#4A90E2] rounded" onchange="toggleSelectAll()">
                    <label for="selectAllRooms" class="text-sm font-bold text-gray-700">เลือกทั้งหมด</label>
                </div>

                <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                    @foreach($bills->filter(fn($b) => $b->receipt) as $bill)
                    <label class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                        <input type="checkbox" name="download_bills[]" value="{{ $bill->id }}" class="room-checkbox w-4 h-4 text-[#4A90E2] rounded">
                        <span class="font-bold text-[#4A90E2]">{{ $bill->room->room_number ?? '-' }}</span>
                        <span class="text-gray-600 text-sm">{{ $bill->room->contract->tenant_name ?? '-' }}</span>
                        <span class="ml-auto text-xs text-gray-400">{{ $bill->receipt->receipt_number ?? '-' }}</span>
                    </label>
                    @endforeach
                    @if($bills->filter(fn($b) => $b->receipt)->isEmpty())
                    <div class="p-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p>ไม่พบใบเสร็จที่สามารถดาวน์โหลดได้</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button onclick="closeDownloadModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-colors">
                    ยกเลิก
                </button>
                <button onclick="downloadSelected()" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    ดาวน์โหลด
                </button>
            </div>
        </div>
    </div>

    <script>
        // Notification Dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const button = e.target.closest('button[onclick="toggleNotifications()"]');
            if (!button && !e.target.closest('#notificationDropdown')) {
                dropdown?.classList.add('hidden');
            }
        });

        // Slip Modal Functions
        function openSlipModal(billId, roomNumber, amount, slipUrl) {
            document.getElementById('slipBillId').value = billId;
            document.getElementById('paymentAmount').value = amount.toLocaleString() + ' บาท';
            document.getElementById('slipForm').action = `/bills/${billId}/confirm-payment`;

            // Show/hide slip image
            const previewEmpty = document.getElementById('slipPreviewEmpty');
            const previewImage = document.getElementById('slipPreviewImage');

            if (slipUrl && slipUrl !== '') {
                previewEmpty.classList.add('hidden');
                previewImage.src = slipUrl;
                previewImage.classList.remove('hidden');
            } else {
                previewEmpty.classList.remove('hidden');
                previewImage.classList.add('hidden');
                previewImage.src = '';
            }

            // Reset admin file input
            document.getElementById('adminSlipFile').value = '';
            document.getElementById('adminSlipFileName').textContent = 'แนบสลิปเพิ่ม (แอดมิน)';

            document.getElementById('slipModal').classList.remove('hidden');
            document.getElementById('slipModal').classList.add('flex');
        }

        function closeSlipModal() {
            document.getElementById('slipModal').classList.add('hidden');
            document.getElementById('slipModal').classList.remove('flex');
        }

        // Preview admin-uploaded slip
        function previewAdminSlip(input) {
            if (input.files && input.files[0]) {
                document.getElementById('adminSlipFileName').textContent = input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('slipPreviewImage');
                    const previewEmpty = document.getElementById('slipPreviewEmpty');
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    previewEmpty.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Filter Modal Functions
        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
            document.getElementById('filterModal').classList.add('flex');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('filterModal').classList.remove('flex');
        }

        function resetFilters() {
            document.querySelectorAll('.status-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.month-checkbox').forEach(cb => cb.checked = false);
        }

        function applyFilters() {
            const selectedStatuses = [];
            document.querySelectorAll('.status-checkbox:checked').forEach(cb => {
                selectedStatuses.push(cb.value);
            });

            const selectedMonths = [];
            const year = document.getElementById('yearSelect').value;
            document.querySelectorAll('.month-checkbox:checked').forEach(cb => {
                selectedMonths.push(year + '-' + cb.value);
            });

            document.getElementById('statusInput').value = selectedStatuses.join(',');
            document.getElementById('monthsInput').value = selectedMonths.join(',');

            if (selectedStatuses.length === 1) {
                document.getElementById('statusInput').value = selectedStatuses[0];
            }

            closeFilterModal();
            document.getElementById('filterForm').submit();
        }

        // Download Modal Functions
        function openDownloadModal() {
            document.getElementById('downloadModal').classList.remove('hidden');
            document.getElementById('downloadModal').classList.add('flex');
        }

        function closeDownloadModal() {
            document.getElementById('downloadModal').classList.add('hidden');
            document.getElementById('downloadModal').classList.remove('flex');
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAllRooms');
            const checkboxes = document.querySelectorAll('.room-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        function downloadSelected() {
            const selected = document.querySelectorAll('.room-checkbox:checked');
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกห้อง',
                    text: 'กรุณาเลือกห้องที่ต้องการดาวน์โหลดใบเสร็จ',
                    confirmButtonColor: '#4A90E2'
                });
                return;
            }

            selected.forEach(cb => {
                window.open(`/bills/${cb.value}/receipt`, '_blank');
            });

            closeDownloadModal();
        }

        // Form submission (using FormData to support file upload)
        document.getElementById('slipForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const billId = document.getElementById('slipBillId').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const paymentDate = document.getElementById('paymentDate').value;

            const formData = new FormData();
            formData.append('payment_method', paymentMethod);
            formData.append('payment_date', paymentDate);

            // Attach admin's slip file if selected
            const adminSlipInput = document.getElementById('adminSlipFile');
            if (adminSlipInput.files.length > 0) {
                formData.append('payment_slip', adminSlipInput.files[0]);
            }

            fetch(`/bills/${billId}/confirm-payment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'อนุมัติสำเร็จ',
                        text: 'บันทึกการชำระเงินเรียบร้อยแล้ว',
                        confirmButtonColor: '#4A90E2'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถบันทึกได้',
                        confirmButtonColor: '#4A90E2'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'กรุณาลองใหม่อีกครั้ง',
                    confirmButtonColor: '#4A90E2'
                });
            });
        });
    </script>
</body>
</html>
