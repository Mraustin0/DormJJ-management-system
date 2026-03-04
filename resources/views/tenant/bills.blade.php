<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการบิล - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
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

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">รายการบิลทั้งหมด</h2>

                {{-- Search --}}
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="ค้นหาเดือน/ปี..." onkeyup="filterBills()"
                           class="border border-gray-300 rounded-lg px-4 py-2 pl-9 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent w-44">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Bills List --}}
            <div class="space-y-2" id="billsGrid">
                @forelse($bills as $bill)
                @php
                    $billingDate = \Carbon\Carbon::parse($bill->billing_month);
                    $monthName   = thaiMonth($billingDate->format('m'));
                    $yearAD      = $billingDate->format('Y');
                @endphp
                <a href="{{ route('tenant.bills.view', $bill->id) }}"
                   class="bill-item flex items-center justify-between bg-white rounded-xl border border-gray-200 px-5 py-4 hover:bg-gray-50 transition-colors group"
                   data-text="{{ $monthName }} {{ $yearAD }}">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-gray-800 font-medium">บิลประจำเดือน{{ $monthName }} {{ $yearAD }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">฿{{ number_format($bill->total_amount, 0) }}</p>
                        </div>
                        @if($bill->status == 'paid')
                            <span class="px-3 py-0.5 bg-green-100 text-green-600 rounded-full text-xs font-semibold">ชำระแล้ว</span>
                        @elseif($bill->status == 'reviewing')
                            <span class="px-3 py-0.5 bg-orange-100 text-orange-600 rounded-full text-xs font-semibold">รอการอนุมัติ</span>
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

            {{-- No search result --}}
            <div id="noResult" class="hidden text-center py-10 text-gray-400 text-sm">ไม่พบบิลที่ค้นหา</div>

            @if($bills->hasPages())
            <div class="mt-6">{{ $bills->links() }}</div>
            @endif
        </div>
    </main>

    <script>
        function filterBills() {
            const q = document.getElementById('searchInput').value.trim().toLowerCase();
            let visible = 0;
            document.querySelectorAll('.bill-item').forEach(el => {
                const match = !q || el.dataset.text.toLowerCase().includes(q);
                el.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('noResult').classList.toggle('hidden', visible > 0);
        }
    </script>
</body>
</html>
