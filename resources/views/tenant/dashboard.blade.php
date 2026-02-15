<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <header class="bg-red-600 sticky top-0 z-30">
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
                    {{-- Notification Bell --}}
                    <button class="text-white relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    {{-- User Avatar --}}
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="text-white text-sm hidden sm:block">
                            <p class="font-semibold leading-tight">{{ $contract->tenant_name }}</p>
                            <p class="text-white/80 text-xs">ห้อง {{ $room->room_number }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            {{-- Greeting --}}
            <h2 class="text-xl font-bold text-gray-800 mb-6">สวัสดีคุณ {{ $contract->tenant_name }}</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column: Outstanding Bill --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">บิลค้างชำระ:</h3>

                    @if($latestUnpaidBill)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-sm text-gray-500">ยอดค้างชำระ:</p>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($latestUnpaidBill->total_amount, 2) }} บาท</p>
                                <p class="text-xs text-red-500 mt-1">กำหนดชำระ: {{ \Carbon\Carbon::parse($latestUnpaidBill->due_date)->format('j M Y') }}</p>
                            </div>
                            <a href="{{ route('tenant.bills.view', $latestUnpaidBill->id) }}" class="px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-lg hover:bg-red-600 transition-colors whitespace-nowrap">
                                ดูและชำระบิล
                            </a>
                        </div>

                        <hr class="my-3 border-gray-100">

                        <p class="text-sm font-semibold text-gray-600 mb-2">ข้อมูลการใช้งาน</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ค่าเช่าห้อง</span>
                                <span class="font-medium">{{ number_format($latestUnpaidBill->room_rate, 0) }} บาท</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">ค่าไฟฟ้า</span>
                                <span class="font-medium">
                                    @if($latestUnpaidBill->electric_units)
                                        {{ $latestUnpaidBill->electric_units }} หน่วย = {{ number_format($latestUnpaidBill->electric_amount, 0) }} บาท
                                    @else
                                        {{ number_format($latestUnpaidBill->electric_amount, 0) }} บาท
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">ค่าน้ำ</span>
                                <span class="font-medium">{{ number_format($latestUnpaidBill->water_amount, 0) }} บาท</span>
                            </div>
                            @if($latestUnpaidBill->other_fees > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">เพิ่มเติม</span>
                                <span class="font-medium">{{ number_format($latestUnpaidBill->other_fees, 0) }} บาท</span>
                            </div>
                            @endif
                            <hr class="border-gray-100">
                            <div class="flex justify-between font-bold">
                                <span>รวม</span>
                                <span class="text-red-600">{{ number_format($latestUnpaidBill->total_amount, 0) }} บาท</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                        <div class="text-green-500 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-green-600 font-semibold">ชำระบิลครบแล้ว</p>
                        <p class="text-gray-400 text-sm mt-1">ไม่มียอดค้างชำระ</p>
                    </div>
                    @endif

                    {{-- Expense Chart --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mt-6">
                        <h3 class="text-base font-bold text-gray-800 mb-3">ค่าใช้จ่ายรายเดือน</h3>
                        <div style="height: 220px;">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Bill History --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">บิลย้อนหลัง</h3>

                    <div class="space-y-3">
                        @forelse($billHistory as $bill)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ open: $loop->first }">
                            {{-- Accordion Header --}}
                            <button class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors" onclick="toggleBill(this)">
                                <span class="font-semibold text-gray-800">
                                    บิลประจำเดือน {{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 bill-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Accordion Content --}}
                            <div class="bill-content hidden border-t border-gray-100">
                                <div class="px-5 py-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="text-xs text-gray-500">ยอดค่าบริการ เดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }} {{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}</p>
                                            <p class="text-xl font-bold text-gray-800">{{ number_format($bill->total_amount, 2) }} บาท</p>
                                            @if($bill->status == 'paid')
                                                <p class="text-xs text-green-600 mt-1">เก็บค่าเสร็จ: {{ $bill->receipt ? \Carbon\Carbon::parse($bill->receipt->payment_date ?? $bill->receipt->receipt_date ?? '')->format('j M Y') : '' }}</p>
                                            @else
                                                <p class="text-xs text-red-500 mt-1">กำหนดชำระ: {{ \Carbon\Carbon::parse($bill->due_date)->format('j M Y') }}</p>
                                            @endif
                                        </div>
                                        @if($bill->status == 'paid')
                                            <span class="px-3 py-1 bg-green-600 text-white rounded-full text-xs font-semibold">ชำระแล้ว</span>
                                        @elseif($bill->status == 'overdue')
                                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">เกินกำหนด</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-semibold">รอชำระ</span>
                                        @endif
                                    </div>

                                    <p class="text-sm font-semibold text-gray-600 mb-2">ข้อมูลการใช้งาน</p>
                                    <div class="space-y-1.5 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">ค่าเช่าห้อง</span>
                                            <span>{{ number_format($bill->room_rate, 0) }} บาท</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">ค่าไฟฟ้า</span>
                                            <span>
                                                @if($bill->electric_units)
                                                    {{ $bill->electric_units }} หน่วย = {{ number_format($bill->electric_amount, 0) }} บาท
                                                @else
                                                    {{ number_format($bill->electric_amount, 0) }} บาท
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">ค่าน้ำ</span>
                                            <span>{{ number_format($bill->water_amount, 0) }} บาท</span>
                                        </div>
                                        @if($bill->other_fees > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">เพิ่มเติม</span>
                                            <span>{{ number_format($bill->other_fees, 0) }} บาท</span>
                                        </div>
                                        @endif
                                        <hr class="border-gray-100">
                                        <div class="flex justify-between font-bold">
                                            <span>รวม</span>
                                            <span class="text-red-600">{{ number_format($bill->total_amount, 0) }} บาท</span>
                                        </div>
                                    </div>

                                    {{-- Download Receipt --}}
                                    @if($bill->status == 'paid')
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <a href="{{ route('tenant.bills.view', $bill->id) }}" class="flex items-center justify-between text-sm text-gray-600 hover:text-red-600 transition-colors">
                                            <span>ดาวน์โหลดใบเสร็จ</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center text-gray-400">
                            ยังไม่มีบิลย้อนหลัง
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleBill(btn) {
            const content = btn.nextElementSibling;
            const chevron = btn.querySelector('.bill-chevron');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Auto-open first bill
        document.addEventListener('DOMContentLoaded', function() {
            const firstBtn = document.querySelector('.bill-content');
            if (firstBtn) {
                firstBtn.classList.remove('hidden');
                const chevron = firstBtn.previousElementSibling.querySelector('.bill-chevron');
                if (chevron) chevron.classList.add('rotate-180');
            }

            // Expense Chart
            const ctx = document.getElementById('expenseChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'ค่าไฟฟ้า',
                                data: @json($chartElectric),
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245,158,11,0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 4,
                            },
                            {
                                label: 'ค่าน้ำ',
                                data: @json($chartWater),
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59,130,246,0.1)',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 4,
                            },
                            {
                                label: 'ค่าห้อง',
                                data: @json($chartRoom),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16,185,129,0.05)',
                                tension: 0.3,
                                fill: false,
                                borderDash: [5, 5],
                                pointRadius: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { family: 'Sarabun', size: 12 }, usePointStyle: true, padding: 15 }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Sarabun', size: 11 } } },
                            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { family: 'Sarabun', size: 11 } } }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
