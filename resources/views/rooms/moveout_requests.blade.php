<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งย้ายออก - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'moveout'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'แจ้งย้ายออก'])

        <main class="p-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 min-h-[80vh]">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-gray-800">รายการแจ้งย้ายออก</h3>
                        <span class="bg-blue-100 text-[#4A90E2] text-sm px-3 py-1 rounded-full font-bold">
                            {{ $requests->count() }} รายการ
                        </span>
                    </div>

                    {{-- Status Filter --}}
                    <form action="{{ route('moveout.requests') }}" method="GET">
                        <select name="status" onchange="this.form.submit()"
                                class="border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer">
                            <option value="" {{ $status == '' ? 'selected' : '' }}>สถานะทั้งหมด</option>
                            <option value="pending"  {{ $status == 'pending'  ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>ปฏิเสธแล้ว</option>
                        </select>
                    </form>
                </div>

                {{-- List --}}
                <div class="space-y-3">
                    @forelse($requests as $req)
                    @php
                        $roomNum    = $req->room->room_number ?? $req->contract->room->room_number ?? '-';
                        $tenantName = $req->contract->tenant_name ?? '-';
                        $moveoutDate = $req->moveout_date ? $req->moveout_date->format('d/m/Y') : '-';
                        $notifyDate  = $req->notify_date  ? $req->notify_date->format('d/m/Y')  : '-';
                    @endphp
                    <a href="{{ route('moveout.show', $req->id) }}"
                       class="flex items-center justify-between px-5 py-4 border border-gray-200 rounded-xl hover:border-[#4A90E2] hover:bg-blue-50 transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-[#4A90E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-base">ห้อง {{ $roomNum }} แจ้งย้ายออก</p>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $tenantName }} • แจ้ง {{ $notifyDate }} • ย้ายออก {{ $moveoutDate }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($req->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">รอดำเนินการ</span>
                            @elseif($req->status == 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">อนุมัติแล้ว</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">ปฏิเสธแล้ว</span>
                            @endif
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-[#4A90E2] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-20 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <p class="text-lg font-medium">ไม่พบรายการแจ้งย้ายออก</p>
                        <p class="text-sm mt-1">เมื่อผู้เช่าแจ้งย้ายออก รายการจะปรากฏที่นี่</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

</body>
</html>
