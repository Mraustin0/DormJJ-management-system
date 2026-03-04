<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            #mainContent { margin-left: 0 !important; }
            @page { margin: 10mm; }
        }
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

        {{-- Content --}}
        <div class="p-5 max-w-2xl mx-auto">

            {{-- Back + Title --}}
            <div class="flex items-center gap-3 mb-5 no-print">
                <a href="{{ route('tenant.bills') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800">
                    บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}
                </h2>
            </div>

            {{-- ===== Room Info Card ===== --}}
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex items-start justify-between">
                <div>
                    <p class="font-bold text-gray-800">ห้อง {{ $contract->room->room_number }}</p>
                    <p class="text-gray-600 text-sm">{{ $contract->tenant_name }}</p>
                    <p class="text-gray-500 text-sm">เบอร์โทร {{ $contract->phone ?? '-' }}</p>
                </div>
                @if($bill->status == 'paid')
                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-semibold">ชำระแล้ว</span>
                @elseif($bill->status == 'reviewing')
                    <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold">รอการอนุมัติ</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">ค้างชำระ</span>
                @endif
            </div>

            {{-- ===== Bill Table ===== --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 print-area">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800">รายการชำระเงิน</h3>
                        @if($bill->due_date)
                            <p class="text-red-500 text-sm mt-0.5">
                                กำหนดชำระ: {{ \Carbon\Carbon::parse($bill->due_date)->format('j') }}
                                {{ thaiMonth(\Carbon\Carbon::parse($bill->due_date)->format('m')) }}
                                {{ \Carbon\Carbon::parse($bill->due_date)->format('Y') + 543 }}
                            </p>
                        @endif
                    </div>
                    <button onclick="window.print()"
                            class="no-print px-4 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-full text-sm font-semibold transition-colors">
                        ดาวน์โหลดใบเสร็จ
                    </button>
                </div>

                @php
                    $meterReading = $contract->room->meterReadings()
                        ->where('billing_month', $bill->billing_month)->first();
                    $billMonth = thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m'));
                    $billYearBE = \Carbon\Carbon::parse($bill->billing_month)->format('Y') + 543;
                    $shortYearBE = substr((string)$billYearBE, 2); // e.g. "68"
                @endphp

                {{-- Table --}}
                <table class="w-full text-base border-collapse">
                    <thead>
                        <tr class="border border-gray-300">
                            <th class="border border-gray-300 px-4 py-3 text-left font-semibold bg-gray-50">รายการ</th>
                            <th class="border border-gray-300 px-4 py-3 text-center font-semibold bg-gray-50 w-32">จำนวนหน่วยที่ใช้</th>
                            <th class="border border-gray-300 px-4 py-3 text-right font-semibold bg-gray-50 w-36">จำนวนเงิน(บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Water --}}
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 px-4 py-3">
                                ค่าน้ำ เดือน {{ $billMonth }} {{ $shortYearBE }}
                                @if($meterReading)
                                    ({{ $meterReading->water_prev }}-{{ $meterReading->water_curr }})
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">{{ $bill->water_units ?? 0 }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($bill->water_amount ?? 0) }}</td>
                        </tr>
                        {{-- Electric --}}
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 px-4 py-3">
                                ค่าไฟ เดือน {{ $billMonth }} {{ $shortYearBE }}
                                @if($meterReading)
                                    ({{ $meterReading->elec_prev }}-{{ $meterReading->elec_curr }})
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-3 text-center">{{ $bill->electric_units ?? 0 }}</td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($bill->electric_amount ?? 0) }}</td>
                        </tr>
                        {{-- Room rate --}}
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 px-4 py-3">ค่าเช่าห้อง</td>
                            <td class="border border-gray-300 px-4 py-3 text-center">1</td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($bill->room_rate ?? 0) }}</td>
                        </tr>
                        {{-- Late fee --}}
                        @if(($bill->other_fees ?? 0) > 0)
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 px-4 py-3">ค่าปรับจ่ายบิลล่าช้า</td>
                            <td class="border border-gray-300 px-4 py-3"></td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($bill->other_fees ?? 0) }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="border border-gray-300 font-bold bg-gray-50">
                            <td class="border border-gray-300 px-4 py-3">รวมสุทธิ</td>
                            <td class="border border-gray-300 px-4 py-3"></td>
                            <td class="border border-gray-300 px-4 py-3 text-right">{{ number_format($bill->total_amount ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- ===== ช่องทางการชำระเงิน ===== --}}
            @if($bill->status != 'paid')
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">

                <h3 class="font-bold text-gray-800 mb-4">ช่องทางการชำระเงิน</h3>

                @php
                    $payBankName    = config('payment.bank_name');
                    $payBankAccount = config('payment.bank_account');
                    $payBankHolder  = config('payment.bank_account_name');
                    $payPromptpay   = config('payment.promptpay_id');
                @endphp

                {{-- Bank Account Details --}}
                @if($payBankAccount)
                <div class="border border-gray-200 rounded-xl px-4 py-3 flex items-center gap-4 mb-4">
                    {{-- Bank logo --}}
                    <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center flex-shrink-0 overflow-hidden bg-white">
                        <img src="{{ asset('images/logoKasikorm.png') }}" alt="bank"
                             class="w-12 h-12 object-contain rounded-full">
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ $payBankName ?: 'ธนาคาร' }}</p>
                        <p class="text-lg font-bold text-gray-800 tracking-wide">{{ $payBankAccount }}</p>
                        <p class="text-sm text-gray-500">ชื่อบัญชี : {{ $payBankHolder ?: $setting->apartment_name }}</p>
                    </div>
                </div>
                @endif

                {{-- PromptPay QR Code --}}
                @if($payPromptpay)
                <div class="border border-gray-200 rounded-xl py-6 flex flex-col items-center">

                    {{-- QR Code Image --}}
                    <img src="{{ asset('images/image_11.png') }}"
                         alt="PromptPay QR Code"
                         class="w-64 h-64 object-contain mb-4">

                    {{-- Info below QR --}}
                    <div class="text-center space-y-0.5">
                        <p class="text-base font-bold text-blue-600 tracking-wide">{{ $payPromptpay }}</p>
                        <p class="text-sm text-gray-600">ชื่อบัญชี : {{ $payBankHolder ?: $setting->apartment_name }}</p>
                        <p class="text-base font-bold text-blue-600">จำนวน  {{ number_format($bill->total_amount, 0) }} บาท</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- ===== หลักฐานการชำระเงิน ===== --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 no-print">
                <h3 class="font-bold text-gray-800 mb-3">หลักฐานการชำระเงิน</h3>

                @if($bill->payment_slip)
                {{-- Already uploaded --}}
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-700 text-sm font-medium">อัปโหลดสลิปแล้ว — รอแอดมินตรวจสอบ</span>
                </div>
                <img src="{{ asset('storage/' . $bill->payment_slip) }}" alt="slip" class="max-w-[200px] rounded-lg border border-gray-200 mb-4 shadow-sm">
                <p class="text-xs text-gray-400 mb-2">ต้องการเปลี่ยนสลิป?</p>
                @endif

                <form action="{{ route('tenant.bills.uploadSlip', $bill->id) }}" method="POST"
                      enctype="multipart/form-data" id="slipForm">
                    @csrf
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <span id="slipFileName" class="flex-1 px-4 py-2.5 text-sm text-gray-400 bg-white truncate">
                                    กรุณาแนบสลิป
                                </span>
                                <span class="px-4 py-2.5 bg-gray-200 text-gray-700 text-sm border-l border-gray-300">
                                    เลือกไฟล์
                                </span>
                            </div>
                            <input type="file" name="payment_slip" accept="image/*" class="hidden"
                                   required
                                   onchange="document.getElementById('slipFileName').textContent = this.files[0]?.name || 'กรุณาแนบสลิป'">
                        </label>
                        <button type="submit"
                                class="px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-semibold transition-colors whitespace-nowrap">
                            อัปโหลด
                        </button>
                    </div>
                    @error('payment_slip')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            {{-- ยืนยัน button --}}
            <div class="flex justify-end mb-4 no-print">
                <button type="submit" form="slipForm"
                        class="px-8 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold transition-colors">
                    ยืนยัน
                </button>
            </div>

            @elseif($bill->payment_slip)
            {{-- Paid - show slip --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
                <h3 class="font-bold text-gray-800 mb-3">หลักฐานการชำระเงิน</h3>
                <img src="{{ asset('storage/' . $bill->payment_slip) }}" alt="slip" class="max-w-[200px] rounded-lg border border-gray-200 shadow-sm">
                @if($bill->status == 'paid' && $bill->receipt)
                <div class="mt-3">
                    <a href="{{ route('tenant.receipt', $bill->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        ดาวน์โหลดใบเสร็จ
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- ===== หมายเหตุ ===== --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
                <h3 class="font-bold text-gray-800 mb-3">หมายเหตุ</h3>
                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 ml-1">
                    <li>
                        กรุณาโอนเงินเข้าบัญชี "{{ config('payment.bank_account_name') ?: ($setting->apartment_name ?? 'หอพัก') }}" และนำสลิปโอนเงินแจ้งให้สำนักงานทราบ
                        @if(config('payment.bank_name') && config('payment.bank_account'))
                        <ul class="ml-5 mt-1 space-y-0.5 text-gray-500">
                            <li>{{ config('payment.bank_name') }} เลขที่ {{ config('payment.bank_account') }}</li>
                        </ul>
                        @endif
                    </li>
                    <li>ค่าเช่าชำระไม่เกินวันที่ {{ $setting->payment_due_day ?? '5' }} ของเดือน เกินกำหนดจะปรับวันละ {{ number_format($setting->late_fee_per_day ?? 100) }} บาท</li>
                </ol>
            </div>

        </div>
    </main>
</body>
</html>