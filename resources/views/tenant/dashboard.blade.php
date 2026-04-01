<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'dashboard'])

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
                    @include('tenant.partials.notification-bell')
                    @include('tenant.partials.user-dropdown')
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6 sm:p-8">

            {{-- Greeting --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-6">สวัสดีคุณ {{ $contract->tenant_name ?? Auth::user()->username }}</h2>

            {{-- 2-column layout --}}
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- ===== LEFT COLUMN ===== --}}
                <div class="lg:w-[42%] flex flex-col gap-6">

                    {{-- บิลค้างชำระ --}}
                    @php
                        $pendingBill = $recentBills->whereIn('status', ['pending', 'overdue', 'reviewing'])->first();
                    @endphp

                    <h3 class="text-lg font-bold text-gray-800">บิลค้างชำระ</h3>

                    @if($pendingBill)
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm -mt-2">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-gray-800">ยอดค้างชำระ</span>
                            <a href="{{ route('tenant.bills.view', $pendingBill->id) }}"
                               class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">
                                ดูและจ่ายบิล
                            </a>
                        </div>

                        <p class="text-3xl font-bold text-red-500 mb-1">{{ number_format($pendingBill->total_amount, 2) }} บาท</p>
                        <p class="text-sm text-red-500 mb-4">
                            กำหนดชำระ: {{ $pendingBill->due_date?->format('j') }} {{ thaiMonthShort($pendingBill->due_date?->format('m')) }} {{ $pendingBill->due_date?->format('Y') }}
                        </p>

                        <hr class="mb-4">
                        <p class="text-sm font-semibold text-gray-700 mb-3">ข้อมูลการใช้งาน</p>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>ค่าเช่าห้อง</span>
                                <span>{{ number_format($pendingBill->room_rate, 0, '.', ',') }} บาท</span>
                            </div>
                            @php
                                $elecMeter = \App\Models\MeterReading::where('room_id', $room->id)
                                    ->where('billing_month', $pendingBill->billing_month)
                                    ->first();
                            @endphp
                            <div class="flex justify-between">
                                <span>ค่าไฟฟ้า
                                    @if($elecMeter)
                                        <span class="text-gray-400 ml-4">{{ $elecMeter->elec_prev }} - {{ $elecMeter->elec_curr }} = {{ $elecMeter->elec_unit }} หน่วย</span>
                                    @endif
                                </span>
                                <span>{{ number_format($pendingBill->electric_amount, 0, '.', ',') }} บาท</span>
                            </div>
                            <div class="flex justify-between">
                                <span>ค่าน้ำ
                                    @if($elecMeter)
                                        <span class="text-gray-400 ml-4">{{ $elecMeter->water_prev }} - {{ $elecMeter->water_curr }} = {{ $elecMeter->water_unit }} หน่วย</span>
                                    @endif
                                </span>
                                <span>{{ number_format($pendingBill->water_amount, 0, '.', ',') }} บาท</span>
                            </div>
                            @if($pendingBill->other_fees > 0)
                            <div class="flex justify-between">
                                <span>เพิ่มเติม</span>
                                <span>{{ number_format($pendingBill->other_fees, 0, '.', ',') }} บาท</span>
                            </div>
                            @endif
                            <hr>
                            <div class="flex justify-between font-bold text-gray-800">
                                <span>รวม</span>
                                <span class="text-red-500">{{ number_format($pendingBill->total_amount, 0) }} บาท</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm -mt-2">
                        <div class="flex items-center gap-3 text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-bold text-base">ไม่มีบิลค้างชำระ</p>
                                <p class="text-sm text-gray-400">ชำระครบทุกเดือนแล้ว</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- กราฟค่าใช้จ่าย --}}
                    @if(count($chartLabels) > 0)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-700 mb-4">การใช้งาน 6 เดือนย้อนหลัง</h3>
                        <canvas id="meterChart" height="160"></canvas>
                    </div>
                    @endif

                </div>

                {{-- ===== RIGHT COLUMN ===== --}}
                <div class="lg:flex-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">บิลย้อนหลัง</h3>

                    <div class="space-y-3">
                        @forelse($recentBills as $bill)
                        @php
                            $bm = \Carbon\Carbon::parse($bill->billing_month . '-01');
                        @endphp
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            {{-- Accordion Header --}}
                            <button onclick="toggleBill({{ $bill->id }})"
                                    class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors">
                                <span class="font-semibold text-gray-800">
                                    บิลประจำเดือน {{ thaiMonth($bm->format('m')) }} {{ $bm->format('Y') }}
                                </span>
                                <svg id="chevron-{{ $bill->id }}" class="w-5 h-5 text-gray-400 transition-transform duration-200 {{ $loop->first ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Accordion Body --}}
                            <div id="bill-{{ $bill->id }}" class="{{ $loop->first ? '' : 'hidden' }} border-t border-gray-100">
                                <div class="px-5 pt-4 pb-4">

                                    {{-- ยอดค่าบริการ + badge --}}
                                    <div class="flex items-start justify-between mb-1">
                                        <p class="text-xs text-gray-400">ยอดค่าบริการ เดือน{{ thaiMonth($bm->format('m')) }} {{ $bm->format('Y') }}</p>
                                        @if($bill->status === 'paid')
                                            <span class="bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">จ่ายแล้ว</span>
                                        @elseif($bill->status === 'reviewing')
                                            <span class="bg-orange-100 text-orange-600 text-xs font-semibold px-3 py-1 rounded-full">รอการอนุมัติ</span>
                                        @elseif($bill->status === 'overdue')
                                            <span class="bg-red-100 text-red-600 text-xs font-semibold px-3 py-1 rounded-full">เกินกำหนด</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-3 py-1 rounded-full">รอชำระ</span>
                                        @endif
                                    </div>

                                    <p class="text-2xl font-bold text-red-500 mb-1">{{ number_format($bill->total_amount, 2) }} บาท</p>

                                    <p class="text-xs mb-4 @if($bill->status === 'paid') text-gray-400 @elseif($bill->status === 'reviewing') text-orange-500 @else text-red-500 @endif">
                                        @if($bill->status === 'overdue')
                                            เกินกำหนดชำระ {{ $bill->due_date?->format('j') }} {{ thaiMonthShort($bill->due_date?->format('m')) }} {{ $bill->due_date?->format('Y') }}
                                        @elseif($bill->status === 'paid')
                                            ชำระแล้ว
                                        @elseif($bill->status === 'reviewing')
                                            อัปโหลดสลิปแล้ว - รอแอดมินตรวจสอบ
                                        @else
                                            กำหนดชำระ {{ $bill->due_date?->format('j') }} {{ thaiMonthShort($bill->due_date?->format('m')) }} {{ $bill->due_date?->format('Y') }}
                                        @endif
                                    </p>

                                    <p class="text-sm font-semibold text-gray-700 mb-2">ข้อมูลการใช้งาน</p>
                                    <div class="space-y-1.5 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span>ค่าเช่าห้อง</span>
                                            <span>{{ number_format($bill->room_rate, 0, '.', ',') }} บาท</span>
                                        </div>
                                        @php
                                            $m = \App\Models\MeterReading::where('room_id', $room->id)
                                                ->where('billing_month', $bill->billing_month)->first();
                                        @endphp
                                        <div class="flex justify-between">
                                            <span>ค่าไฟฟ้า
                                                @if($m)
                                                    <span class="text-gray-400 text-xs ml-4">{{ $m->elec_prev }} - {{ $m->elec_curr }} = {{ $m->elec_unit }} หน่วย</span>
                                                @endif
                                            </span>
                                            <span>{{ number_format($bill->electric_amount, 0, '.', ',') }} บาท</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>ค่าน้ำ
                                                @if($m)
                                                    <span class="text-gray-400 text-xs ml-4">{{ $m->water_prev }} - {{ $m->water_curr }} = {{ $m->water_unit }} หน่วย</span>
                                                @endif
                                            </span>
                                            <span>{{ number_format($bill->water_amount, 0, '.', ',') }} บาท</span>
                                        </div>
                                        @if($bill->other_fees > 0)
                                        <div class="flex justify-between">
                                            <span>เพิ่มเติม</span>
                                            <span>{{ number_format($bill->other_fees, 0, '.', ',') }} บาท</span>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="flex justify-between font-bold text-gray-800">
                                            <span>รวม</span>
                                            <span class="text-red-500">{{ number_format($bill->total_amount, 0) }} บาท</span>
                                        </div>
                                    </div>

                                    {{-- ดาวน์โหลดใบเสร็จ / จ่ายบิล --}}
                                    @if($bill->status === 'paid' && $bill->receipt)
                                    <div class="mt-4 pt-3 border-t border-gray-100">
                                        <a href="{{ route('tenant.bills.downloadReceipt', $bill->id) }}" target="_blank"
                                           class="flex items-center justify-between text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                            <span>ดาวน์โหลดใบเสร็จ</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @elseif(in_array($bill->status, ['pending', 'overdue', 'reviewing']))
                                    <div class="mt-4 pt-3 border-t border-gray-100">
                                        <a href="{{ route('tenant.bills.view', $bill->id) }}"
                                           class="flex items-center justify-between text-sm text-gray-600 hover:text-gray-800 transition-colors">
                                            <span>ดูและจ่ายบิล</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400">
                            ยังไม่มีรายการบิล
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('tenant.bills') }}" class="text-sm text-red-500 hover:text-red-700 font-medium">
                            ดูบิลทั้งหมด →
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        function toggleBill(id) {
            const body = document.getElementById('bill-' + id);
            const chevron = document.getElementById('chevron-' + id);
            body.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        @if(count($chartLabels) > 0)
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('meterChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'ไฟฟ้า (หน่วย)',
                            data: @json($electricData),
                            backgroundColor: 'rgba(239,68,68,0.7)',
                            borderRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: 'น้ำ (หน่วย)',
                            data: @json($waterData),
                            backgroundColor: 'rgba(59,130,246,0.7)',
                            borderRadius: 4,
                            yAxisID: 'y1',
                        },
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { font: { family: 'Sarabun', size: 11 }, boxWidth: 10 } }
                    },
                    scales: {
                        x: { ticks: { font: { family: 'Sarabun', size: 10 } } },
                        y: {
                            type: 'linear',
                            position: 'left',
                            ticks: { font: { family: 'Sarabun', size: 10 } },
                            title: { display: true, text: 'ไฟฟ้า (หน่วย)', font: { size: 10 } }
                        },
                        y1: {
                            type: 'linear',
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { font: { family: 'Sarabun', size: 10 } },
                            title: { display: true, text: 'น้ำ (หน่วย)', font: { size: 10 } }
                        }
                    }
                }
            });
        });
        @endif
    </script>
</body>
</html>
