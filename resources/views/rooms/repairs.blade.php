<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อม - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'repairs'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'แจ้งซ่อม'])

        <main class="p-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 min-h-[80vh]">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-gray-800">รายการแจ้งซ่อม</h3>
                        <span class="bg-blue-100 text-[#4A90E2] text-sm px-3 py-1 rounded-full font-bold">
                            {{ $repairs->count() }} รายการ
                        </span>
                    </div>

                    {{-- Status Filter --}}
                    <form action="{{ route('repairs.index') }}" method="GET">
                        <select name="status" onchange="this.form.submit()"
                                class="border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-700 focus:outline-none focus:border-[#4A90E2] cursor-pointer">
                            <option value="" {{ $status == '' ? 'selected' : '' }}>สถานะทั้งหมด</option>
                            <option value="pending"     {{ $status == 'pending'     ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="completed"   {{ $status == 'completed'   ? 'selected' : '' }}>เสร็จสิ้น</option>
                            <option value="cancelled"   {{ $status == 'cancelled'   ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </form>
                </div>

                {{-- List --}}
                <div class="space-y-3">
                    @forelse($repairs as $repair)
                    @php
                        $roomNum = $repair->room->room_number ?? '-';
                        $createdAt = $repair->created_at ? $repair->created_at->format('d/m/Y') : '-';
                        $descPreview = $repair->description
                            ? (mb_strlen($repair->description) > 60
                                ? mb_substr($repair->description, 0, 60) . '...'
                                : $repair->description)
                            : '-';
                    @endphp
                    <a href="{{ route('repairs.show', $repair->id) }}"
                       class="flex items-center justify-between px-5 py-4 border border-gray-200 rounded-xl hover:border-[#4A90E2] hover:bg-blue-50 transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            {{-- Icon --}}
                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-[#4A90E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>

                            {{-- Info --}}
                            <div>
                                <p class="font-bold text-gray-800 text-base">
                                    ห้อง {{ $roomNum }}
                                    @if($repair->category)
                                        <span class="text-gray-400 font-normal text-sm ml-1">— {{ $repair->category }}</span>
                                    @endif
                                </p>
                                <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $repair->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $descPreview }} • {{ $createdAt }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($repair->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">รอดำเนินการ</span>
                            @elseif($repair->status == 'in_progress')
                                <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">กำลังดำเนินการ</span>
                            @elseif($repair->status == 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">เสร็จสิ้น</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">ยกเลิก</span>
                            @endif
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-[#4A90E2] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-20 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-lg font-medium">ไม่พบรายการแจ้งซ่อม</p>
                        <p class="text-sm mt-1">เมื่อผู้เช่าแจ้งซ่อม รายการจะปรากฏที่นี่</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </main>
    </div>

</body>
</html>
