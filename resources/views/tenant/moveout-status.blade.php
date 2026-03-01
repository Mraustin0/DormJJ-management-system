<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการแจ้งย้ายออก - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'moveout'])

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
        <div class="px-6 py-8">
            <div class="max-w-2xl mx-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('tenant.moveout') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h2 class="text-xl font-bold text-gray-800">รายการแจ้งย้ายออก</h2>
                    </div>
                </div>

                {{-- Success/Info Message (SweetAlert2) --}}
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: @json(session('success')),
                                icon: 'success',
                                confirmButtonColor: '#f87171',
                                confirmButtonText: 'ตกลง'
                            });
                        });
                    </script>
                @endif
                @if(session('info'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'แจ้งเตือน',
                                text: @json(session('info')),
                                icon: 'info',
                                confirmButtonColor: '#f87171',
                                confirmButtonText: 'ตกลง'
                            });
                        });
                    </script>
                @endif

                {{-- Moveout Request Cards --}}
                <div class="space-y-6">
                    @forelse($moveoutRequests as $request)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        {{-- Card Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-800">แจ้งย้ายออก ห้อง {{ $contract->room->room_number }}</h3>
                                <p class="text-gray-500 text-sm mt-0.5">วันที่ย้ายออก: {{ $request->moveout_date->format('j') }} {{ thaiMonth($request->moveout_date->format('m')) }} {{ $request->moveout_date->format('Y') }}</p>
                            </div>
                            <div>
                                @if($request->status == 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">รอดำเนินการ</span>
                                @elseif($request->status == 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">อนุมัติแล้ว</span>
                                @elseif($request->status == 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">ปฏิเสธ</span>
                                @endif
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="px-6 py-5">
                            <div class="relative">
                                {{-- Vertical line --}}
                                <div class="absolute left-[17px] top-3 bottom-3 w-0.5 bg-gray-200"></div>

                                <div class="space-y-6">
                                    {{-- Step 1: Tenant submitted --}}
                                    <div class="flex items-start gap-4 relative">
                                        <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0 z-10 shadow-sm">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-bold text-gray-800 text-sm">ผู้เช่าแจ้งย้ายออก</p>
                                            <p class="text-gray-500 text-xs mt-1">
                                                {{ $request->created_at->format('j') }} {{ thaiMonth($request->created_at->format('m')) }} {{ $request->created_at->format('Y') }}
                                                เวลา {{ $request->created_at->format('H:i') }} น.
                                            </p>
                                            <div class="mt-2 bg-gray-50 rounded-lg p-3 text-sm text-gray-600 space-y-1">
                                                <div class="flex gap-2">
                                                    <span class="text-gray-400 w-28 flex-shrink-0">วันที่แจ้ง</span>
                                                    <span>{{ $request->notify_date->format('j') }} {{ thaiMonth($request->notify_date->format('m')) }} {{ $request->notify_date->format('Y') }}</span>
                                                </div>
                                                <div class="flex gap-2">
                                                    <span class="text-gray-400 w-28 flex-shrink-0">วันที่ย้ายออก</span>
                                                    <span>{{ $request->moveout_date->format('j') }} {{ thaiMonth($request->moveout_date->format('m')) }} {{ $request->moveout_date->format('Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Step 2: Admin response --}}
                                    @if($request->status == 'rejected')
                                    <div class="flex items-start gap-4 relative">
                                        <div class="w-9 h-9 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0 z-10 shadow-sm">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-bold text-red-600 text-sm">Admin ปฏิเสธการย้ายออก</p>
                                            @if($request->processed_at)
                                                <p class="text-gray-500 text-xs mt-1">
                                                    {{ $request->processed_at->format('j') }} {{ thaiMonth($request->processed_at->format('m')) }} {{ $request->processed_at->format('Y') }}
                                                    เวลา {{ $request->processed_at->format('H:i') }} น.
                                                </p>
                                            @endif
                                            @if($request->admin_remark)
                                                <div class="mt-2 bg-red-50 rounded-lg p-3 text-sm text-red-700">
                                                    <span class="font-medium">เหตุผล:</span> {{ $request->admin_remark }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @elseif($request->status == 'approved')
                                    <div class="flex items-start gap-4 relative">
                                        <div class="w-9 h-9 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 z-10 shadow-sm">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-bold text-green-600 text-sm">Admin อนุมัติการย้ายออก</p>
                                            @if($request->processed_at)
                                                <p class="text-gray-500 text-xs mt-1">
                                                    {{ $request->processed_at->format('j') }} {{ thaiMonth($request->processed_at->format('m')) }} {{ $request->processed_at->format('Y') }}
                                                    เวลา {{ $request->processed_at->format('H:i') }} น.
                                                </p>
                                            @endif
                                            <div class="mt-2 bg-green-50 rounded-lg p-3 text-sm text-green-700 space-y-1">
                                                @if($request->admin_remark)
                                                    <div>
                                                        <span class="font-medium">หมายเหตุ:</span> {{ $request->admin_remark }}
                                                    </div>
                                                @endif
                                                @if($request->deposit_return !== null)
                                                    <div>
                                                        <span class="font-medium">เงินประกันคืน:</span> {{ number_format($request->deposit_return, 2) }} บาท
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    {{-- Pending --}}
                                    <div class="flex items-start gap-4 relative">
                                        <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 z-10">
                                            <div class="w-3 h-3 rounded-full bg-gray-400 animate-pulse"></div>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-bold text-gray-400 text-sm">รอ Admin ดำเนินการ</p>
                                            <p class="text-gray-400 text-xs mt-1">กรุณารอแอดมินตรวจสอบและดำเนินการ</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <p class="text-gray-400 text-lg font-medium">ยังไม่มีรายการแจ้งย้ายออก</p>
                        <p class="text-gray-400 text-sm mt-1">กดปุ่ม "แจ้งย้ายออก" ที่หน้าก่อนหน้าเพื่อส่งคำขอ</p>
                        <a href="{{ route('tenant.moveout') }}" class="inline-block mt-4 px-6 py-2 bg-red-400 text-white rounded-lg text-sm font-semibold hover:bg-red-500 transition-colors">
                            แจ้งย้ายออก
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
