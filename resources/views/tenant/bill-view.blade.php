<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบแจ้งหนี้ - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            #mainContent { margin-left: 0 !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'bills'])

    {{-- Main Content --}}
    <main id="mainContent" class="md:ml-72 min-h-screen transition-all duration-300">
        {{-- Top Bar --}}
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30 no-print">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="{{ route('tenant.bills') }}" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h2 class="text-xl font-bold text-gray-800">ใบแจ้งหนี้</h2>
                </div>
                <button onclick="window.print()" class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg hover:bg-[#3a7bc8] transition-colors">
                    พิมพ์
                </button>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                {{-- Header --}}
                <div class="text-center mb-8 border-b border-gray-200 pb-6">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $setting->apartment_name ?? 'JJ APARTMENT' }}</h1>
                    <p class="text-gray-500 mt-2">
                        {{ $setting->address ?? '' }}
                        {{ $setting->subdistrict ? ', ' . $setting->subdistrict : '' }}
                        {{ $setting->district ? ', ' . $setting->district : '' }}
                        {{ $setting->province ?? '' }}
                        {{ $setting->postal_code ?? '' }}
                    </p>
                    <h2 class="text-xl font-bold text-[#4A90E2] mt-4">ใบแจ้งหนี้ค่าเช่าห้องพัก</h2>
                    <p class="text-gray-500">ประจำเดือน {{ \Carbon\Carbon::parse($bill->billing_month)->format('m/Y') }}</p>
                </div>

                {{-- Tenant Info --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-gray-500 text-sm">ห้องพัก</p>
                        <p class="font-semibold text-lg">{{ $contract->room->room_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">ผู้เช่า</p>
                        <p class="font-semibold">{{ $contract->tenant_name }}</p>
                    </div>
                </div>

                {{-- Bill Details --}}
                <table class="w-full mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">รายการ</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">หน่วย</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">ราคา/หน่วย</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="px-4 py-3">ค่าห้องพัก</td>
                            <td class="px-4 py-3 text-right">1</td>
                            <td class="px-4 py-3 text-right">{{ number_format($bill->room_rate, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($bill->room_rate, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">ค่าน้ำประปา</td>
                            <td class="px-4 py-3 text-right">{{ $bill->water_units ?? 0 }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($setting->water_rate ?? 18, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($bill->water_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3">ค่าไฟฟ้า</td>
                            <td class="px-4 py-3 text-right">{{ $bill->electric_units ?? 0 }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($setting->electric_rate ?? 8, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($bill->electric_amount, 2) }}</td>
                        </tr>
                        @if($bill->other_fees > 0)
                        <tr>
                            <td class="px-4 py-3">ค่าใช้จ่ายอื่นๆ</td>
                            <td class="px-4 py-3 text-right">-</td>
                            <td class="px-4 py-3 text-right">-</td>
                            <td class="px-4 py-3 text-right">{{ number_format($bill->other_fees, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-bold text-lg">รวมทั้งสิ้น</td>
                            <td class="px-4 py-3 text-right font-bold text-lg text-[#4A90E2]">{{ number_format($bill->total_amount, 2) }} บาท</td>
                        </tr>
                    </tfoot>
                </table>

                {{-- Status & Due Date --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-500 text-sm">กำหนดชำระ</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">สถานะ</p>
                            @if($bill->status == 'paid')
                                <span class="px-4 py-2 bg-green-100 text-green-600 rounded-full font-semibold">ชำระแล้ว</span>
                            @elseif($bill->status == 'overdue')
                                <span class="px-4 py-2 bg-red-100 text-red-600 rounded-full font-semibold">เกินกำหนด</span>
                            @else
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-600 rounded-full font-semibold">รอชำระ</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Receipt Info (if paid) --}}
                @if($bill->receipt)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-bold text-gray-800 mb-4">ข้อมูลการชำระเงิน</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">เลขที่ใบเสร็จ</p>
                            <p class="font-semibold">{{ $bill->receipt->receipt_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">วันที่ชำระ</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($bill->receipt->receipt_date)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">ยอดชำระ</p>
                            <p class="font-semibold">{{ number_format($bill->receipt->amount_paid, 2) }} บาท</p>
                        </div>
                        <div>
                            <p class="text-gray-500">วิธีชำระ</p>
                            <p class="font-semibold">{{ $bill->receipt->payment_method }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Bank Info (if unpaid) --}}
                @if($bill->status != 'paid' && $setting)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-bold text-gray-800 mb-4">ช่องทางการชำระเงิน</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-gray-600">{{ $setting->bank_name ?? 'กรุณาติดต่อเจ้าหน้าที่' }}</p>
                        @if($setting->bank_account)
                        <p class="font-semibold text-lg">{{ $setting->bank_account }}</p>
                        <p class="text-gray-500 text-sm">ชื่อบัญชี: {{ $setting->account_name ?? '-' }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
