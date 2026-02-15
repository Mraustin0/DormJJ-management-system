<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการบิล - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
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

        {{-- Content --}}
        <div class="p-6 relative">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">รายการบิลทั้งหมด</h2>
                <div class="flex items-center gap-3">
                    {{-- Search --}}
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="ค้นหา..." class="border border-gray-300 rounded-lg px-4 py-2 pl-9 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent w-48" oninput="filterBills()">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    {{-- Filter Toggle --}}
                    <button onclick="toggleFilter()" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors" id="filterBtn">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filter Dropdown --}}
            <div id="filterDropdown" class="hidden absolute right-6 z-20 bg-white rounded-xl shadow-lg border border-gray-200 p-5" style="min-width: 400px;">
                <div class="flex gap-8">
                    {{-- Status --}}
                    <div>
                        <h4 class="font-bold text-sm text-gray-700 mb-3">สถานะ</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" class="status-filter rounded border-gray-300 text-red-600 focus:ring-red-500" value="pending" onchange="filterBills()" checked>
                                <span>ค้างชำระ</span>
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" class="status-filter rounded border-gray-300 text-red-600 focus:ring-red-500" value="paid" onchange="filterBills()">
                                <span>ชำระแล้ว</span>
                            </label>
                        </div>
                    </div>
                    {{-- Month --}}
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <h4 class="font-bold text-sm text-gray-700">เดือน</h4>
                            <select id="yearFilter" class="border border-gray-300 rounded px-2 py-1 text-xs" onchange="filterBills()">
                                @php $currentYear = now()->year; @endphp
                                @for($y = $currentYear; $y >= $currentYear - 2; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5">
                            @php $thaiMonths = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม']; @endphp
                            @foreach($thaiMonths as $idx => $mName)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" class="month-filter rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ $idx + 1 }}" onchange="filterBills()">
                                <span>{{ $mName }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bills List --}}
            <div class="space-y-2" id="billsList">
                @forelse($bills as $bill)
                <a href="{{ route('tenant.bills.view', $bill->id) }}"
                   class="bill-item flex items-center justify-between bg-white rounded-xl border border-gray-200 px-5 py-4 hover:bg-gray-50 transition-colors group"
                   data-status="{{ $bill->status }}"
                   data-month="{{ \Carbon\Carbon::parse($bill->billing_month)->format('n') }}"
                   data-year="{{ \Carbon\Carbon::parse($bill->billing_month)->format('Y') }}"
                   data-text="บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }}">
                    <div class="flex items-center gap-4">
                        <span class="text-gray-800 font-medium">บิลประจำเดือน{{ thaiMonth(\Carbon\Carbon::parse($bill->billing_month)->format('m')) }}</span>
                        @if($bill->status == 'paid')
                            <span class="px-3 py-0.5 bg-green-100 text-green-600 rounded-full text-xs font-semibold">ชำระแล้ว</span>
                        @else
                            <span class="px-3 py-0.5 bg-red-100 text-red-600 rounded-full text-xs font-semibold">ค้างชำระ</span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @empty
                <div class="text-center py-12 text-gray-400">ยังไม่มีรายการบิล</div>
                @endforelse
            </div>

            @if($bills->hasPages())
            <div class="mt-6">{{ $bills->links() }}</div>
            @endif
        </div>
    </main>

    <script>
        function toggleFilter() {
            document.getElementById('filterDropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('filterDropdown');
            const btn = document.getElementById('filterBtn');
            if (!dd.contains(e.target) && !btn.contains(e.target)) dd.classList.add('hidden');
        });
        function filterBills() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const statuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(c => c.value);
            const months = Array.from(document.querySelectorAll('.month-filter:checked')).map(c => c.value);
            const yearVal = document.getElementById('yearFilter').value;

            document.querySelectorAll('.bill-item').forEach(item => {
                const s = item.dataset.status === 'overdue' ? 'pending' : item.dataset.status;
                let show = true;
                if (search && !item.dataset.text.toLowerCase().includes(search)) show = false;
                if (statuses.length > 0 && !statuses.includes(s)) show = false;
                if (months.length > 0 && !months.includes(item.dataset.month)) show = false;
                if (yearVal && item.dataset.year !== yearVal) show = false;
                item.style.display = show ? '' : 'none';
            });
        }
        // Apply default filter on load
        filterBills();
    </script>
</body>
</html>
