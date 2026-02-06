<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>บิลค่าเช่า - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            #mainContent { margin-left: 0 !important; }
        }
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
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-4">
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin1' }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-6">
            @php
                $monthName = \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('F Y');
            @endphp

            <h3 class="text-xl font-bold text-gray-800 mb-6">บิลค่าเช่า ประจำเดือน {{ $monthName }}</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Bill Details Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="font-bold text-gray-700">รายละเอียดหัวบิล</h4>
                        <a href="{{ route('bills.createForRoom', $room->id) }}?month={{ $selectedMonth }}" class="text-gray-400 hover:text-[#4A90E2]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p class="font-bold text-gray-800">{{ $room->contract->tenant_name ?? '-' }}</p>
                        <p class="text-gray-500">เบอร์โทร {{ $room->contract->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Recipient Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="font-bold text-gray-700 mb-4">บิลจะถูกส่งไปให้ผู้เช่า</h4>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $room->contract->tenant_name ?? '-' }}</p>
                            <p class="text-sm text-[#4A90E2]">ห้อง {{ $room->room_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-bold text-gray-800 text-lg">รายการชำระเงิน</h4>
                    <button class="text-sm text-gray-500 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50">
                        + เพิ่มรายการ
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-bold text-gray-600">รายการ</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-600">จำนวนหน่วยที่ใช้</th>
                                <th class="text-right py-3 px-4 font-bold text-gray-600">จำนวนเงิน(บาท)</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @if($bill)
                            {{-- Water --}}
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-700">
                                    ค่าน้ำ เดือน {{ \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('M') }} {{ \Carbon\Carbon::parse($selectedMonth)->format('y') }}
                                    @if($meter)
                                    ({{ $meter->water_prev ?? 0 }}-{{ $meter->water_curr ?? 0 }})
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center text-gray-700">{{ $bill->water_units ?? 0 }}</td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->water_amount ?? 0) }}</td>
                            </tr>
                            {{-- Electric --}}
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-700">
                                    ค่าไฟ เดือน {{ \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('M') }} {{ \Carbon\Carbon::parse($selectedMonth)->format('y') }}
                                    @if($meter)
                                    ({{ $meter->elec_prev ?? 0 }}-{{ $meter->elec_curr ?? 0 }})
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center text-gray-700">{{ $bill->electric_units ?? 0 }}</td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->electric_amount ?? 0) }}</td>
                            </tr>
                            {{-- Room Rate --}}
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-700">ค่าเช่าห้อง</td>
                                <td class="py-4 px-4 text-center text-gray-700"></td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->room_rate ?? 0) }}</td>
                            </tr>
                            {{-- Other Fees --}}
                            @if(($bill->other_fees ?? 0) > 0)
                            <tr class="border-b border-gray-100">
                                <td class="py-4 px-4 text-gray-700">ค่าปรับจ่ายบิลล่าช้า</td>
                                <td class="py-4 px-4 text-center text-gray-700"></td>
                                <td class="py-4 px-4 text-right font-bold text-gray-800">{{ number_format($bill->other_fees ?? 0) }}</td>
                            </tr>
                            @endif
                            @else
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-400">ยังไม่มีข้อมูลบิล</td>
                            </tr>
                            @endif
                        </tbody>
                        @if($bill)
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td class="py-4 px-4 font-bold text-gray-800 text-lg">รวมสุทธิ</td>
                                <td class="py-4 px-4"></td>
                                <td class="py-4 px-4 text-right font-bold text-[#4A90E2] text-xl">{{ number_format($bill->total_amount ?? 0) }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-4 mt-8 no-print">
                    <button onclick="window.print()" class="flex items-center gap-2 bg-[#f27b6d] hover:bg-[#e06a5c] text-white font-bold py-3 px-8 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        พิมพ์
                    </button>
                    <button onclick="sendBill()" class="flex items-center gap-2 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-8 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        ส่งบิล
                    </button>
                </div>
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
                cancelButtonColor: '#d33',
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
                        // กลับไปหน้าสร้างบิล
                        window.location.href = '{{ route("bills.create") }}?month={{ $selectedMonth }}';
                    });
                }
            });
        }
    </script>
</body>
</html>
