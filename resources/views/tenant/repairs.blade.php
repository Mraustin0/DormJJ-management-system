<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติแจ้งซ่อม - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'repairs'])

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
                    @include('tenant.partials.notification-bell')
                    @include('tenant.partials.user-dropdown')
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6 sm:p-8">
            {{-- Back button --}}
            <div class="mb-4">
                <a href="{{ route('tenant.repairs.create') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            </div>

            {{-- Title centered --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">ประวัติแจ้งซ่อม ห้อง {{ $contract->room->room_number }}</h2>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 max-w-xl mx-auto">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Repair List --}}
            <div class="space-y-4 max-w-xl mx-auto">
                @forelse($repairs as $repair)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden cursor-pointer hover:shadow-md transition-shadow"
                     onclick="toggleRepairDetail({{ $repair->id }})">
                    {{-- Card Summary --}}
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-800">{{ $repair->category ?? $repair->title }}</h3>
                                <p class="text-gray-500 text-sm mt-1">{{ $repair->description }}</p>
                                <p class="text-gray-400 text-xs mt-2">{{ $repair->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex-shrink-0 mt-1">
                                @if($repair->status == 'pending')
                                    <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full whitespace-nowrap">รอดำเนินการ</span>
                                @elseif($repair->status == 'in_progress')
                                    <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full whitespace-nowrap">กำลังดำเนินการ</span>
                                @elseif($repair->status == 'completed')
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full whitespace-nowrap">เสร็จสิ้น</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-400 text-white text-xs font-bold rounded-full whitespace-nowrap">ยกเลิก</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Expandable Detail --}}
                    <div id="repairDetail-{{ $repair->id }}" class="hidden border-t border-gray-100 px-5 py-4 bg-gray-50">
                        <div class="space-y-3 text-sm">
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">หมวดปัญหา</span>
                                <span class="text-gray-800">{{ $repair->category ?? '-' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">รายละเอียด</span>
                                <span class="text-gray-800">{{ $repair->description }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">วันที่แจ้ง</span>
                                <span class="text-gray-800">{{ $repair->created_at->format('d/m/Y H:i') }} น.</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">สถานะ</span>
                                <span class="text-gray-800">
                                    @if($repair->status == 'pending') รอดำเนินการ
                                    @elseif($repair->status == 'in_progress') กำลังดำเนินการ
                                    @elseif($repair->status == 'completed') เสร็จสิ้น
                                    @else ยกเลิก @endif
                                </span>
                            </div>
                            @if($repair->remark)
                            <div class="flex gap-2">
                                <span class="text-gray-400 w-28 flex-shrink-0">หมายเหตุ Admin</span>
                                <span class="text-gray-800">{{ $repair->remark }}</span>
                            </div>
                            @endif
                            @if($repair->image)
                            <div>
                                <span class="text-gray-400 text-sm">รูปถ่ายแนบ</span>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $repair->image) }}" alt="Repair Image" class="max-w-xs rounded-lg border border-gray-200">
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <p class="text-gray-400 text-lg font-medium">ยังไม่มีรายการแจ้งซ่อม</p>
                    <p class="text-gray-400 text-sm mt-1">กดปุ่ม "แจ้งซ่อมใหม่" เพื่อแจ้งปัญหา</p>
                </div>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        function toggleRepairDetail(id) {
            const detail = document.getElementById('repairDetail-' + id);
            if (detail) {
                detail.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>
