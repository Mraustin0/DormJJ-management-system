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
                    @include('tenant.partials.notification-bell')
                    @include('tenant.partials.user-dropdown')
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
                @elseif($bill->status == 'reviewing')
                    <span class="px-4 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold">รอการอนุมัติ</span>
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

                {{-- Payment Slip Upload Section --}}
                @if($bill->status != 'paid')
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-3">หลักฐานการชำระเงิน</h4>

                    @if($bill->payment_slip)
                    {{-- Already uploaded --}}
                    <div class="mb-4">
                        <div class="border border-green-200 bg-green-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-green-700 font-semibold text-sm">อัปโหลดสลิปแล้ว - รอตรวจสอบจากแอดมิน</span>
                            </div>
                            <img src="{{ asset('storage/' . $bill->payment_slip) }}" alt="Payment Slip" class="max-w-xs rounded-lg border border-gray-200 shadow-sm">
                        </div>
                    </div>
                    {{-- Allow re-upload --}}
                    <form action="{{ route('tenant.bills.uploadSlip', $bill->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <p class="text-gray-500 text-xs mb-2">ต้องการเปลี่ยนสลิป? อัปโหลดใหม่ด้านล่าง</p>
                        <div class="flex items-center gap-3">
                            <label class="flex-1 cursor-pointer">
                                <div class="flex items-center gap-3 border border-gray-300 rounded-lg px-4 py-2 hover:border-red-400 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span id="reuploadFileName" class="text-sm text-gray-500">เลือกไฟล์สลิปใหม่...</span>
                                </div>
                                <input type="file" name="payment_slip" accept="image/*" class="hidden" onchange="document.getElementById('reuploadFileName').textContent = this.files[0]?.name || 'เลือกไฟล์สลิปใหม่...'">
                            </label>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition-colors whitespace-nowrap">
                                อัปโหลดใหม่
                            </button>
                        </div>
                    </form>
                    @else
                    {{-- No slip yet --}}
                    <form action="{{ route('tenant.bills.uploadSlip', $bill->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center gap-3">
                            <label class="flex-1 cursor-pointer">
                                <div class="flex items-center gap-3 border-2 border-dashed border-gray-300 rounded-lg px-4 py-3 hover:border-red-400 transition-colors">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span id="uploadFileName" class="text-sm text-gray-500">กรุณาแนบสลิปการโอนเงิน...</span>
                                </div>
                                <input type="file" name="payment_slip" accept="image/*" class="hidden" required onchange="document.getElementById('uploadFileName').textContent = this.files[0]?.name || 'กรุณาแนบสลิปการโอนเงิน...'">
                            </label>
                            <button type="submit" class="px-5 py-3 bg-red-500 text-white rounded-lg text-sm font-semibold hover:bg-red-600 transition-colors whitespace-nowrap">
                                อัปโหลด
                            </button>
                        </div>
                    </form>
                    @endif

                    @if(session('slip_success'))
                    <div class="mt-3 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg text-sm">
                        {{ session('slip_success') }}
                    </div>
                    @endif
                    @if($errors->has('payment_slip'))
                    <div class="mt-3 bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm">
                        {{ $errors->first('payment_slip') }}
                    </div>
                    @endif
                </div>
                @elseif($bill->payment_slip)
                {{-- Bill is paid but show the slip --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-3">หลักฐานการชำระเงิน</h4>
                    <img src="{{ asset('storage/' . $bill->payment_slip) }}" alt="Payment Slip" class="max-w-xs rounded-lg border border-gray-200 shadow-sm">
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
