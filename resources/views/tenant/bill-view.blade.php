<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
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
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'bills'])

    <main id="mainContent" class="md:ml-72 min-h-screen transition-all duration-300">
        {{-- Top Bar --}}
        <header class="bg-red-600 sticky top-0 z-30 no-print">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="text-white font-semibold text-lg">ระบบจัดการหอพัก {{ $setting->apartment_name ?? 'JJ Apartment' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <button class="text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></button>
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="text-white text-sm hidden sm:block">
                            <p class="font-semibold leading-tight">{{ $contract->tenant_name }}</p>
                            <p class="text-white/80 text-xs">ห้อง {{ $contract->room->room_number }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Sub Header --}}
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('tenant.bills') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800">บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}</h2>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Room Info Card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 flex items-start justify-between">
                <div>
                    <p class="text-lg font-bold text-gray-800">ห้อง {{ $contract->room->room_number }}</p>
                    <p class="text-gray-600">{{ $contract->tenant_name }}</p>
                    <p class="text-gray-500 text-sm">เบอร์โทร {{ $contract->phone ?? '-' }}</p>
                </div>
                @if($bill->status == 'paid')
                    <span class="px-4 py-1 bg-green-100 text-green-600 rounded-full text-sm font-semibold">ชำระแล้ว</span>
                @else
                    <span class="px-4 py-1 bg-red-100 text-red-600 rounded-full text-sm font-semibold">ค้างชำระ</span>
                @endif
            </div>

            {{-- Bill Document --}}
            <div class="print-area bg-white rounded-xl border border-gray-200 p-8 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">รายการชำระเงิน</h3>
                        <p class="text-red-500 text-sm">กำหนดชำระ: {{ \Carbon\Carbon::parse($bill->due_date)->format('j M Y') }}</p>
                    </div>
                    <div class="flex gap-2 no-print">
                        @if($bill->status == 'paid' && $bill->receipt)
                            <a href="{{ route('tenant.receipt', $bill->id) }}" class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold hover:bg-red-600 transition-colors">
                                ดาวน์โหลดใบเสร็จ
                            </a>
                        @endif
                        <button onclick="window.print()" class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold hover:bg-red-600 transition-colors">
                            ดาวน์โหลดบิล
                        </button>
                    </div>
                </div>

                @php
                    $apartmentAddress = ($setting->address ?? '') . ' ถ.' . ($setting->subdistrict ?? '') . ' ต.' . ($setting->district ?? '') . ' อ.เมือง จ.' . ($setting->province ?? '') . ' ' . ($setting->postal_code ?? '');
                    $meterReading = $contract->room->meterReadings()->where('billing_month', $bill->billing_month)->first();
                @endphp

                {{-- Bill Table --}}
                <table class="bill-table text-sm mb-4">
                    <thead>
                        <tr>
                            <th class="text-left">รายการ</th>
                            <th class="text-center w-28">จำนวนหน่วยที่ใช้</th>
                            <th class="text-right w-28">จำนวนเงิน(บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                ค่าน้ำ เดือน {{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }}
                                @if($meterReading)
                                    ({{ $meterReading->water_prev }}-{{ $meterReading->water_curr }})
                                @endif
                            </td>
                            <td class="text-center">{{ $bill->water_units ?? 0 }}</td>
                            <td class="text-right">{{ number_format($bill->water_amount ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td>
                                ค่าไฟ เดือน {{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }}
                                @if($meterReading)
                                    ({{ $meterReading->elec_prev }}-{{ $meterReading->elec_curr }})
                                @endif
                            </td>
                            <td class="text-center">{{ $bill->electric_units ?? 0 }}</td>
                            <td class="text-right">{{ number_format($bill->electric_amount ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td>ค่าเช่าห้อง</td>
                            <td class="text-center"></td>
                            <td class="text-right">{{ number_format($bill->room_rate ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td>ค่าปรับจ่ายบิลล่าช้า</td>
                            <td class="text-center"></td>
                            <td class="text-right">{{ number_format($bill->other_fees ?? 0) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-bold">
                            <td>รวมสุทธิ</td>
                            <td class="text-center"></td>
                            <td class="text-right">{{ number_format($bill->total_amount ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Payment Slip Upload Section (for unpaid) --}}
                @if($bill->status != 'paid')
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-3">หลักฐานการชำระเงิน</h4>
                    <div class="flex items-center gap-3">
                        <input type="text" placeholder="กรุณาแนบสลิป" class="border border-gray-300 rounded-lg px-4 py-2 text-sm flex-1" disabled>
                        <button class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg text-sm font-medium">อัปโหลด</button>
                    </div>
                </div>
                @endif

                {{-- Notes --}}
                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                    <h4 class="font-bold text-gray-800 mb-2">หมายเหตุ</h4>
                    <ol class="list-decimal list-inside space-y-1 ml-2">
                        <li>กรุณาโอนเงินเข้าบัญชี "{{ $setting->apartment_name ?? 'นาย เจเจ อพาร์ท' }}" และนำสลิปโอนเงิน แจ้งที่สำนักงานทราบ
                            <ul class="ml-6 mt-1 space-y-0.5">
                                <li>ธนาคารกสิกรไทย จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</li>
                                <li>ธนาคารไทยพาณิชย์ จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</li>
                                <li>ธนาคารกรุงเทพ จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</li>
                            </ul>
                        </li>
                        <li>ค่าเช่าชำระไม่เกินวันที่ 5 ของเดือน เกินกำหนดจะปรับวันละ {{ number_format($setting->late_fee_per_day ?? 100) }} บาท</li>
                    </ol>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
