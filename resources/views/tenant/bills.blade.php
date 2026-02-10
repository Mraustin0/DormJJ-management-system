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

    {{-- Main Content --}}
    <main id="mainContent" class="md:ml-72 min-h-screen transition-all duration-300">
        {{-- Top Bar --}}
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">รายการบิล</h2>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">ห้อง {{ $contract->room->room_number }}</span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-center">
                    <label class="text-gray-600">สถานะ:</label>
                    <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอชำระ</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>เกินกำหนด</option>
                    </select>
                </form>
            </div>

            {{-- Bills Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">เดือน</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ค่าน้ำ</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ค่าไฟ</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ค่าห้อง</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">รวม</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">สถานะ</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">ดูบิล</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($bills as $bill)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($bill->billing_month)->format('m/Y') }}</td>
                                    <td class="px-6 py-4">{{ number_format($bill->water_amount, 2) }}</td>
                                    <td class="px-6 py-4">{{ number_format($bill->electric_amount, 2) }}</td>
                                    <td class="px-6 py-4">{{ number_format($bill->room_rate, 2) }}</td>
                                    <td class="px-6 py-4 font-bold">{{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($bill->status == 'paid')
                                            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-medium">ชำระแล้ว</span>
                                        @elseif($bill->status == 'overdue')
                                            <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-medium">เกินกำหนด</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full text-sm font-medium">รอชำระ</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('tenant.bills.view', $bill->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#4A90E2] text-white rounded-lg hover:bg-[#3a7bc8] transition-colors text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            ดูบิล
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        ยังไม่มีรายการบิล
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($bills->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $bills->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
