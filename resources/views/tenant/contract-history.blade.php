<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการทำรายการสัญญาเช่า - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'contract'])

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
                {{-- Back + Title --}}
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('tenant.contract') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h2 class="text-xl font-bold text-gray-800">ประวัติการทำรายการสัญญาเช่า</h2>
                </div>

                {{-- Contract Summary --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">ห้องพัก:</span>
                            <span class="text-gray-800 font-semibold ml-1">{{ $contract->room->room_number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">ผู้เช่า:</span>
                            <span class="text-gray-800 font-semibold ml-1">{{ $contract->tenant_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">วันเริ่มสัญญา:</span>
                            <span class="text-gray-800 font-semibold ml-1">
                                @if($contract->start_date)
                                    {{ \Carbon\Carbon::parse($contract->start_date)->format('j') }} {{ thaiMonth(\Carbon\Carbon::parse($contract->start_date)->format('m')) }} {{ \Carbon\Carbon::parse($contract->start_date)->format('Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">ระยะสัญญา:</span>
                            <span class="text-gray-800 font-semibold ml-1">{{ $contract->contract_duration ?? 12 }} เดือน</span>
                        </div>
                    </div>
                </div>

                {{-- History Timeline --}}
                <div class="space-y-4">
                    @forelse($histories as $index => $history)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-5">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $history['type'] == 'signed' ? 'bg-green-100' : '' }}
                                {{ $history['type'] == 'checkin' ? 'bg-blue-100' : '' }}
                                {{ $history['type'] == 'sent' ? 'bg-orange-100' : '' }}
                                {{ $history['type'] == 'created' ? 'bg-purple-100' : '' }}
                                {{ $history['type'] == 'viewed' ? 'bg-yellow-100' : '' }}">
                                @if($history['type'] == 'signed')
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($history['type'] == 'checkin')
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                @elseif($history['type'] == 'sent')
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                @elseif($history['type'] == 'created')
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($history['type'] == 'viewed')
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @endif
                            </div>
                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2.5 h-2.5 rounded-full inline-block flex-shrink-0
                                        {{ $history['type'] == 'signed' ? 'bg-green-500' : '' }}
                                        {{ $history['type'] == 'checkin' ? 'bg-blue-500' : '' }}
                                        {{ $history['type'] == 'sent' ? 'bg-orange-400' : '' }}
                                        {{ $history['type'] == 'created' ? 'bg-purple-500' : '' }}
                                        {{ $history['type'] == 'viewed' ? 'bg-yellow-500' : '' }}"></span>
                                    <span class="font-bold text-gray-800">
                                        @if($history['type'] == 'signed')
                                            เริ่มต้นสัญญาเช่า
                                        @elseif($history['type'] == 'checkin')
                                            เข้าพักอาศัย
                                        @elseif($history['type'] == 'sent')
                                            ส่งเอกสารสัญญา
                                        @elseif($history['type'] == 'created')
                                            สร้างสัญญาเช่า
                                        @elseif($history['type'] == 'viewed')
                                            ผู้เช่าเปิดอ่านสัญญาแล้ว
                                        @endif
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <div class="flex gap-2">
                                        <span class="text-gray-400 w-12 flex-shrink-0">วันที่</span>
                                        <span>{{ $history['date'] }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-400 w-12 flex-shrink-0">เวลา</span>
                                        <span>{{ $history['time'] }} น.</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="text-gray-400 w-12 flex-shrink-0">โดย</span>
                                        <span>{{ $history['by'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 text-center">
                        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-400 text-lg font-medium">ยังไม่มีประวัติการทำรายการ</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
