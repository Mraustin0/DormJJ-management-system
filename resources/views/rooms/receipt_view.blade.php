<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน - {{ $bill->receipt->receipt_number ?? 'N/A' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .no-print { display: none !important; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    @include('partials.sidebar', ['activePage' => 'bills'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        <!-- Navbar -->
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm no-print">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('rooms.bills') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800">ใบเสร็จรับเงิน</h2>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-[#4A90E2] hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    พิมพ์ใบเสร็จ
                </button>
            </div>
        </nav>

        <main class="p-8 flex justify-center">
            <div class="print-area bg-white rounded-xl shadow-lg border border-gray-200 p-8 w-full max-w-2xl">

                <!-- Header -->
                <div class="text-center border-b-2 border-gray-200 pb-6 mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-1">JJ APARTMENT</h1>
                    <p class="text-gray-500 text-sm">หอพัก เจเจ อพาร์ทเมนท์</p>
                    <p class="text-gray-500 text-sm">123 ถ.ตัวอย่าง ต.ตัวอย่าง อ.เมือง จ.ตัวอย่าง 12345</p>
                    <p class="text-gray-500 text-sm">โทร. 02-xxx-xxxx</p>
                </div>

                <!-- Receipt Title -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-green-600 inline-flex items-center gap-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ใบเสร็จรับเงิน
                    </h2>
                    <p class="text-gray-500 mt-1">RECEIPT</p>
                </div>

                <!-- Receipt Info -->
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 mb-1">เลขที่ใบเสร็จ</p>
                        <p class="font-bold text-lg text-[#4A90E2]">{{ $bill->receipt->receipt_number }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-right">
                        <p class="text-gray-500 mb-1">วันที่ออกใบเสร็จ</p>
                        <p class="font-bold text-lg">{{ $bill->receipt->receipt_date->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <!-- Tenant Info -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">ห้อง</p>
                            <p class="font-bold text-xl text-[#4A90E2]">{{ $bill->room->room_number ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500">ชื่อผู้เช่า</p>
                            <p class="font-bold text-lg">{{ $bill->room->contract->tenant_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200 text-sm">
                        <p class="text-gray-500">ประจำเดือน</p>
                        <p class="font-bold">{{ $bill->billing_month ? \Carbon\Carbon::parse($bill->billing_month)->translatedFormat('F Y') : '-' }}</p>
                    </div>
                </div>

                <!-- Payment Details -->
                <table class="w-full mb-6 text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="py-3 text-left font-bold text-gray-700">รายการ</th>
                            <th class="py-3 text-right font-bold text-gray-700">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">ค่าห้องพัก</td>
                            <td class="py-3 text-right font-medium">{{ number_format($bill->room_rate ?? 0, 2) }} บาท</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">
                                ค่าน้ำ
                                @if($bill->water_units)
                                    <span class="text-gray-400 text-xs">({{ $bill->water_units }} หน่วย)</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-medium">{{ number_format($bill->water_amount ?? 0, 2) }} บาท</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">
                                ค่าไฟฟ้า
                                @if($bill->electric_units)
                                    <span class="text-gray-400 text-xs">({{ $bill->electric_units }} หน่วย)</span>
                                @endif
                            </td>
                            <td class="py-3 text-right font-medium">{{ number_format($bill->electric_amount ?? 0, 2) }} บาท</td>
                        </tr>
                        @if($bill->other_fees > 0)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">ค่าบริการอื่นๆ</td>
                            <td class="py-3 text-right font-medium">{{ number_format($bill->other_fees, 2) }} บาท</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300">
                            <td class="py-4 font-bold text-lg text-gray-800">ยอดรวมทั้งสิ้น</td>
                            <td class="py-4 text-right font-bold text-2xl text-green-600">{{ number_format($bill->total_amount ?? 0, 2) }} บาท</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Payment Info -->
                <div class="bg-green-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-bold text-green-600">ชำระเงินแล้ว</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">ยอดที่ชำระ</p>
                            <p class="font-bold text-green-600">{{ number_format($bill->receipt->amount_paid, 2) }} บาท</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500">วิธีการชำระ</p>
                            <p class="font-bold">{{ $bill->receipt->payment_method }}</p>
                        </div>
                    </div>
                    @if($bill->receipt->notes)
                    <div class="mt-3 pt-3 border-t border-green-200">
                        <p class="text-gray-500 text-sm">หมายเหตุ: {{ $bill->receipt->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Payment Slip -->
                @if($bill->receipt->payment_slip)
                <div class="mb-6">
                    <p class="font-bold text-gray-700 mb-2">หลักฐานการชำระเงิน</p>
                    <img src="{{ asset('storage/' . $bill->receipt->payment_slip) }}" alt="Payment Slip" class="max-w-xs rounded-lg border border-gray-200 shadow-sm">
                </div>
                @endif

                <!-- Signature -->
                <div class="grid grid-cols-2 gap-8 mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <div class="h-16 border-b border-gray-300 mb-2"></div>
                        <p class="text-sm text-gray-600">ผู้ชำระเงิน</p>
                        <p class="text-xs text-gray-400">{{ $bill->room->contract->tenant_name ?? '-' }}</p>
                    </div>
                    <div class="text-center">
                        <div class="h-16 border-b border-gray-300 mb-2"></div>
                        <p class="text-sm text-gray-600">ผู้รับเงิน</p>
                        <p class="text-xs text-gray-400">{{ $bill->receipt->receiver->username ?? 'Admin' }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8 pt-4 border-t border-gray-200 text-xs text-gray-400">
                    <p>เอกสารฉบับนี้ออกโดยระบบคอมพิวเตอร์ ไม่จำเป็นต้องลงลายมือชื่อ</p>
                    <p class="mt-1">This document is computer-generated and does not require a signature.</p>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
