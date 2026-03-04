<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการบิล - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .status-badge {
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'bills'])

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
        <div class="p-6">

            {{-- Header & Filters --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">รายการบิลทั้งหมด</h2>

                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="ค้นหา..." onkeyup="applyFilters()"
                               class="border border-gray-300 rounded-lg px-4 py-2 pl-9 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent w-44">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    
                    {{-- Filter Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-100 transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V19l-4 2v-5.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            <span>ตัวกรอง</span>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl z-20 border"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">

                            <div class="p-4 border-b border-gray-100">
                                <h4 class="font-semibold text-sm text-gray-700 mb-3">เลือกปี</h4>
                                <div class="space-y-2">
                                    @php
                                        $years = $bills->map(function($bill) {
                                            return \Carbon\Carbon::parse($bill->billing_month)->year;
                                        })->unique()->sortDesc();
                                    @endphp
                                    @foreach($years as $year)
                                        <label class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                                            <input type="checkbox" name="year" value="{{ $year }}" onchange="applyFilters()" class="rounded text-red-500 focus:ring-red-400">
                                            <span>{{ $year + 543 }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-4">
                                <h4 class="font-semibold text-sm text-gray-700 mb-3">เลือกเดือน</h4>
                                <div class="grid grid-cols-2 gap-x-4">
                                    {{-- Months 1-6 --}}
                                    <div class="space-y-2">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <label class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                                                <input type="checkbox" name="month" value="{{ $i }}" onchange="applyFilters()" class="rounded text-red-500 focus:ring-red-400">
                                                <span>{{ thaiMonth($i) }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                    {{-- Months 7-12 --}}
                                    <div class="space-y-2">
                                        @for ($i = 7; $i <= 12; $i++)
                                            <label class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                                                <input type="checkbox" name="month" value="{{ $i }}" onchange="applyFilters()" class="rounded text-red-500 focus:ring-red-400">
                                                <span>{{ thaiMonth($i) }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-2 border-t border-gray-100">
                                <button onclick="resetFilters()" class="text-sm text-red-600 hover:underline">ล้างตัวกรอง</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bills List --}}
            <div class="space-y-2" id="billsGrid">
                @forelse($bills as $bill)
                @php
                    $billingDate = \Carbon\Carbon::parse($bill->billing_month);
                    $month = $billingDate->format('m');
                    $monthName   = thaiMonth($month);
                    $yearAD      = $billingDate->format('Y');
                @endphp
                <a href="{{ route('tenant.bills.view', $bill->id) }}"
                   class="bill-item flex items-center bg-white rounded-xl border border-gray-200 px-5 py-4 hover:bg-gray-50 transition-colors group"
                   data-text="{{ $monthName }} {{ $yearAD }}" data-month="{{ (int)$month }}" data-year="{{ $yearAD }}">

                    {{-- Bill Info --}}
                    <div class="flex-grow">
                        <p class="text-gray-800 font-medium">บิลประจำเดือน{{ $monthName }} {{ $yearAD }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">฿{{ number_format($bill->total_amount, 0) }}</p>
                    </div>

                    {{-- Status --}}
                    <div class="w-32 text-center">
                        @if($bill->status == 'paid')
                            <span class="status-badge px-3 py-0.5 bg-green-100 text-green-600 rounded-full text-xs font-semibold">ชำระแล้ว</span>
                        @elseif($bill->status == 'reviewing')
                            <span class="status-badge px-3 py-0.5 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold">รอการอนุมัติ</span>
                        @else
                            <span class="status-badge px-3 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-semibold">ค้างชำระ</span>
                        @endif
                    </div>

                    {{-- Arrow Icon --}}
                    <div class="pl-4">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @empty
                <div class="text-center py-12 text-gray-400">ยังไม่มีรายการบิล</div>
                @endforelse
            </div>

            {{-- No search result --}}
            <div id="noResult" class="hidden text-center py-10 text-gray-400 text-sm">ไม่พบบิลที่ค้นหา</div>

            @if($bills->hasPages())
            <div class="mt-6">{{ $bills->links() }}</div>
            @endif
        </div>
    </main>

    <script>
        function applyFilters() {
            const q = document.getElementById('searchInput').value.trim().toLowerCase();
            
            const selectedMonths = Array.from(document.querySelectorAll('input[name="month"]:checked')).map(el => el.value);
            const selectedYears = Array.from(document.querySelectorAll('input[name="year"]:checked')).map(el => el.value);

            let visible = 0;
            
            document.querySelectorAll('.bill-item').forEach(el => {
                const text = el.dataset.text.toLowerCase();
                const month = el.dataset.month;
                const year = el.dataset.year;

                const textMatch = !q || text.includes(q);
                const monthMatch = selectedMonths.length === 0 || selectedMonths.includes(month);
                const yearMatch = selectedYears.length === 0 || selectedYears.includes(year);

                const isVisible = textMatch && monthMatch && yearMatch;
                el.style.display = isVisible ? 'flex' : 'none'; // Use flex to be consistent with the <a> tag style
                if (isVisible) visible++;
            });

            document.getElementById('noResult').classList.toggle('hidden', visible > 0);
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.querySelectorAll('input[name="month"]:checked').forEach(el => el.checked = false);
            document.querySelectorAll('input[name="year"]:checked').forEach(el => el.checked = false);
            applyFilters();
        }
    </script>
</body>
</html>
