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
        .bill-table { border-collapse: collapse; width: 100%; }
        .bill-table th, .bill-table td { border: 1px solid #000; padding: 8px 12px; }
        .bill-table th { background-color: #fff; font-weight: normal; }
        .info-table { border-collapse: collapse; }
        .info-table td { border: 1px solid #000; padding: 6px 12px; }
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
                <h2 class="text-xl font-bold text-gray-800">ใบเสร็จรับเงิน</h2>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-[#4A90E2] hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    พิมพ์ใบเสร็จ
                </button>
            </div>
        </nav>

        <main class="p-6 flex justify-center">
            <div class="flex items-start gap-3">
                {{-- ปุ่มซ้ายกระดาษ --}}
                <a href="{{ route('rooms.bills') }}" class="mt-2 p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-[#4A90E2] transition-colors no-print flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @php
                $receiptDate = $bill->receipt->receipt_date ? $bill->receipt->receipt_date->format('d/m/Y') : now()->format('d/m/Y');
                $receiptDateParts = $bill->receipt->receipt_date ? [
                    'day' => $bill->receipt->receipt_date->format('d'),
                    'month' => $bill->receipt->receipt_date->format('m'),
                    'year' => $bill->receipt->receipt_date->format('Y')
                ] : ['day' => '', 'month' => '', 'year' => ''];
                $apartmentAddress = ($setting->address ?? '140/12,1/1') . ' ถ.' . ($setting->subdistrict ?? 'มิตรภาพ') . ' ต.' . ($setting->district ?? 'ในเมือง') . ' อ.เมือง จ.' . ($setting->province ?? 'ขอนแก่น') . ' ' . ($setting->postal_code ?? '40000');
                $paymentMethod = $bill->receipt->payment_method ?? 'เงินสด';
                $isCash = str_contains(strtolower($paymentMethod), 'cash') || str_contains($paymentMethod, 'เงินสด');
                $isTransfer = str_contains(strtolower($paymentMethod), 'transfer') || str_contains($paymentMethod, 'โอน');
                $isCheck = str_contains(strtolower($paymentMethod), 'check') || str_contains($paymentMethod, 'เช็ค');
                $hasSlip = $bill->receipt->payment_slip ? true : false;
            @endphp

            <div class="print-area bg-white shadow-lg border border-gray-200 p-10 w-full max-w-4xl" style="min-height: 800px;">

                {{-- Header --}}
                <div class="text-center mb-2">
                    <h1 class="text-2xl font-bold">{{ $setting->apartment_name ?? 'JJ Apartment' }}</h1>
                    <p class="text-sm">{{ $setting->address ?? '140/12,1/1' }} ถนน{{ $setting->subdistrict ?? 'มิตรภาพ' }} ตำบล{{ $setting->district ?? 'ในเมือง' }} อำเภอเมือง จังหวัด{{ $setting->province ?? 'ขอนแก่น' }} {{ $setting->postal_code ?? '40000' }}</p>
                </div>

                {{-- Title --}}
                <div class="text-center my-6">
                    <h2 class="text-2xl font-bold text-blue-600">ใบเสร็จรับเงิน/Receipt</h2>
                </div>

                {{-- Info Section --}}
                <div class="flex justify-between mb-6">
                    {{-- Left: Tenant Info --}}
                    <div class="flex-1">
                        <p><span class="font-medium">ชื่อผู้เช่า</span> Name: <span class="font-bold">{{ $bill->room->contract->tenant_name ?? '-' }}</span></p>
                        <p><span class="font-medium">ที่อยู่</span> Address: {{ $apartmentAddress }}</p>
                    </div>
                    {{-- Right: Receipt Info Table --}}
                    <div>
                        <table class="info-table text-sm">
                            <tr>
                                <td class="font-medium">เลขที่ No.</td>
                                <td>{{ $bill->receipt->receipt_number }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">วันที่ Date</td>
                                <td>{{ $receiptDate }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">ห้อง Room</td>
                                <td class="font-bold">{{ $bill->room->room_number ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Main Table --}}
                <table class="bill-table text-sm mb-4">
                    <thead>
                        <tr>
                            <th class="text-center w-16">ลำดับ<br><span class="text-gray-500">Item</span></th>
                            <th class="text-left">รายการ<br><span class="text-gray-500">Description</span></th>
                            <th class="text-center w-24">จำนวนหน่วย<br><span class="text-gray-500">Qty</span></th>
                            <th class="text-center w-28">ราคาต่อหน่วย<br><span class="text-gray-500">Unit Price</span></th>
                            <th class="text-right w-28">จำนวนเงิน<br><span class="text-gray-500">Amount</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>ค่าน้ำประปา</td>
                            <td class="text-center">{{ $bill->water_units ?? 0 }}</td>
                            <td class="text-center">{{ number_format($setting->water_rate ?? 18, 2) }}</td>
                            <td class="text-right">{{ number_format($bill->water_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>ค่าไฟฟ้า</td>
                            <td class="text-center">{{ $bill->electric_units ?? 0 }}</td>
                            <td class="text-center">{{ number_format($setting->electric_rate ?? 8, 2) }}</td>
                            <td class="text-right">{{ number_format($bill->electric_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>ค่าเช่าห้องพัก</td>
                            <td class="text-center">1</td>
                            <td class="text-center">{{ number_format($bill->room_rate ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($bill->room_rate ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">4</td>
                            <td>ค่าปรับ</td>
                            <td class="text-center">1</td>
                            <td class="text-center">{{ number_format($bill->other_fees ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($bill->other_fees ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Total --}}
                <div class="flex justify-end mb-6">
                    <table class="info-table text-sm">
                        <tr>
                            <td class="font-bold">จำนวนเงินรวม</td>
                            <td class="font-bold text-right">{{ number_format($bill->total_amount ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Payment Method --}}
                <div class="text-sm mb-8">
                    <p class="font-medium mb-2">ชำระเงินโดย (Paid By)</p>
                    <div class="ml-4 space-y-1">
                        <p>( {{ $isCash ? 'X' : ' ' }} ) Cash</p>
                        <div class="flex gap-8">
                            <p>( {{ $isTransfer || $hasSlip ? 'X' : ' ' }} ) Transfer Bank</p>
                            <p>Account No <span class="border-b border-black px-2">{{ $isTransfer || $hasSlip ? '___________' : '___________' }}</span></p>
                            <p>Date <span class="border-b border-black px-2">{{ $isTransfer || $hasSlip ? $receiptDate : '___________' }}</span></p>
                        </div>
                        <div class="flex gap-8">
                            <p>( {{ $isCheck ? 'X' : ' ' }} ) Check Bank</p>
                            <p>Chq No _______________</p>
                            <p>Date _______________</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Slip (if exists) --}}
                @if($bill->receipt->payment_slip)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="font-medium text-gray-700 mb-2">หลักฐานการโอนเงิน:</p>
                    <img src="{{ asset('storage/' . $bill->receipt->payment_slip) }}" alt="Payment Slip" class="max-w-xs rounded-lg border border-gray-200 shadow-sm">
                </div>
                @endif

                {{-- Signature --}}
                <div class="flex justify-between items-end mt-12">
                    <div>
                        <p>ผู้รับเงิน <span class="border-b border-black px-4">{{ $bill->receipt->receiver->username ?? '_______________' }}</span></p>
                        <p class="text-gray-500 text-sm">Collector</p>
                    </div>
                    <div>
                        <p>วันที่ <span class="border-b border-black px-2">{{ $receiptDateParts['day'] ?: '____' }}</span>/<span class="border-b border-black px-2">{{ $receiptDateParts['month'] ?: '____' }}</span>/<span class="border-b border-black px-2">{{ $receiptDateParts['year'] ?: '____' }}</span></p>
                        <p class="text-gray-500 text-sm text-right">Date</p>
                    </div>
                </div>

            </div>
            </div>{{-- end flex wrapper --}}
        </main>
    </div>

</body>
</html>
