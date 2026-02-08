<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ใบแจ้งหนี้ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'bills'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm no-print">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a href="{{ route('bills.create') }}?month={{ $selectedMonth }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800">ใบแจ้งหนี้</h2>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="bg-[#f27b6d] hover:bg-[#e06a5c] text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    พิมพ์
                </button>
                <button onclick="sendBill()" class="bg-[#4A90E2] hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    ส่งบิล
                </button>
            </div>
        </nav>

        <main class="p-6 flex justify-center">
            @php
                $billNo = $bill ? 'INV-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) : '-';
                $billDate = $bill ? \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y') : now()->format('d/m/Y');
                $apartmentAddress = ($setting->address ?? '140/12,1/1') . ' ถ.' . ($setting->subdistrict ?? 'มิตรภาพ') . ' ต.' . ($setting->district ?? 'ในเมือง') . ' อ.เมือง จ.' . ($setting->province ?? 'ขอนแก่น') . ' ' . ($setting->postal_code ?? '40000');
            @endphp

            <div class="print-area bg-white shadow-lg border border-gray-200 p-10 w-full max-w-4xl" style="min-height: 800px;">

                {{-- Header --}}
                <div class="text-center mb-2">
                    <h1 class="text-2xl font-bold">{{ $setting->apartment_name ?? 'JJ Apartment' }}</h1>
                    <p class="text-sm">{{ $setting->address ?? '140/12,1/1' }} ถนน{{ $setting->subdistrict ?? 'มิตรภาพ' }} ตำบล{{ $setting->district ?? 'ในเมือง' }} อำเภอเมือง จังหวัด{{ $setting->province ?? 'ขอนแก่น' }} {{ $setting->postal_code ?? '40000' }}</p>
                </div>

                {{-- Title --}}
                <div class="text-center my-6">
                    <h2 class="text-2xl font-bold text-blue-600">ใบแจ้งหนี้</h2>
                </div>

                {{-- Info Section --}}
                <div class="flex justify-between mb-6">
                    {{-- Left: Tenant Info --}}
                    <div class="flex-1">
                        <p><span class="font-medium">ชื่อผู้เช่า</span> Name: <span class="font-bold">{{ $room->contract->tenant_name ?? '-' }}</span></p>
                        <p><span class="font-medium">ที่อยู่</span> Address: {{ $apartmentAddress }}</p>
                    </div>
                    {{-- Right: Bill Info Table --}}
                    <div>
                        <table class="info-table text-sm">
                            <tr>
                                <td class="font-medium">เลขที่ No.</td>
                                <td>{{ $billNo }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">วันที่ Date</td>
                                <td>{{ $billDate }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">ห้อง Room</td>
                                <td class="font-bold">{{ $room->room_number }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Main Table --}}
                @if($bill)
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

                {{-- Notes --}}
                <div class="text-sm mb-8">
                    <p class="font-medium">หมายเหตุ</p>
                    <p class="ml-4">1. กรุณาโอนเงินเข้าบัญชี "{{ $setting->apartment_name ?? 'นาย เจเจ อพาร์ท' }}" และนำสลิปโอนเงิน แจ้งที่สำนักงานทราบ</p>
                    <p class="ml-8">- ธนาคารกสิกรไทย จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
                    <p class="ml-8">- ธนาคารไทยพาณิชย์ จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
                    <p class="ml-8">- ธนาคารกรุงเทพ จำกัด สาขา {{ $setting->province ?? 'ขอนแก่น' }} เลขที่ xxx-xxxxxx-x</p>
                    <p class="ml-4">2. ค่าเช่าชำระไม่เกินวันที่ 5 ของเดือน เกินกำหนดจะปรับวันละ {{ number_format($setting->late_fee_per_day ?? 100) }} บาท</p>
                </div>

                {{-- Signature --}}
                <div class="flex justify-end mt-12">
                    <div class="text-center">
                        <p>ลงชื่อ _______________________</p>
                    </div>
                </div>

                @else
                <div class="text-center py-12 text-gray-400">
                    <p class="text-lg">ยังไม่มีข้อมูลบิลสำหรับเดือนนี้</p>
                    <a href="{{ route('bills.createForRoom', $room->id) }}?month={{ $selectedMonth }}" class="inline-block mt-4 bg-[#4A90E2] text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-600 transition-colors">
                        สร้างบิล
                    </a>
                </div>
                @endif

            </div>
        </main>
    </div>

    <script>
        function sendBill() {
            Swal.fire({
                title: 'ส่งบิลให้ผู้เช่า?',
                text: 'บิลจะถูกส่งไปยัง {{ $room->contract->tenant_name ?? "ผู้เช่า" }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4A90E2',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ส่งบิล',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ส่งบิลเรียบร้อย!',
                        text: 'บิลถูกส่งไปยังผู้เช่าแล้ว',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '{{ route("bills.create") }}?month={{ $selectedMonth }}';
                    });
                }
            });
        }
    </script>
</body>
</html>
