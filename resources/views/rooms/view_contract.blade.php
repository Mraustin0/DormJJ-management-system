<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สัญญาเช่า ห้อง {{ $room->room_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'rooms'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'สัญญาเช่า ห้อง ' . $room->room_number])

        <main class="p-6 max-w-3xl">

            {{-- Back --}}
            <a href="{{ route('rooms.index') }}"
               class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            {{-- Header Card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-9 h-9 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $contract->tenant_name }}</h2>
                    <p class="text-sm text-[#4A90E2] font-medium mt-0.5">ห้อง {{ $room->room_number }} — กำลังเข้าพัก</p>
                </div>
            </div>

            {{-- Contract Details --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">ข้อมูลผู้เช่า</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">เลขบัตรประชาชน</p>
                        <p class="font-semibold text-gray-800">{{ $contract->nid ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">เบอร์โทร</p>
                        <p class="font-semibold text-gray-800">{{ $contract->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">อีเมล</p>
                        <p class="font-semibold text-gray-800">{{ $contract->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">ผู้ติดต่อฉุกเฉิน</p>
                        <p class="font-semibold text-gray-800">
                            {{ $contract->emergency_contact_name ?? '—' }}
                            @if($contract->emergency_contact_phone)
                                ({{ $contract->emergency_contact_phone }})
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">ข้อมูลสัญญา</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">วันที่เข้าพัก</p>
                        <p class="font-semibold text-gray-800">
                            @if($contract->check_in_date)
                                {{ \Carbon\Carbon::parse($contract->check_in_date)->locale('th')->translatedFormat('j F Y') }}
                            @else —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">วันที่ทำสัญญา</p>
                        <p class="font-semibold text-gray-800">
                            @if($contract->contract_date)
                                {{ \Carbon\Carbon::parse($contract->contract_date)->locale('th')->translatedFormat('j F Y') }}
                            @else —
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">ระยะสัญญา</p>
                        <p class="font-semibold text-gray-800">{{ $contract->contract_duration ?? '—' }} เดือน</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">เงินมัดจำ</p>
                        <p class="font-semibold text-gray-800">
                            {{ $contract->deposit ? '฿' . number_format($contract->deposit, 0) : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs mb-0.5">วันสิ้นสุดสัญญา</p>
                        <p class="font-semibold text-gray-800">
                            @if($contract->end_date)
                                {{ \Carbon\Carbon::parse($contract->end_date)->locale('th')->translatedFormat('j F Y') }}
                            @else —
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Contract & ID Card Files --}}
            @if($contract->contract_file || $contract->idcard_file)
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
                <h3 class="font-bold text-gray-700 mb-4 pb-2 border-b border-gray-100">เอกสารแนบ</h3>
                <div class="flex flex-wrap gap-4">
                    @if($contract->contract_file)
                    <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-3 border border-[#4A90E2] text-[#4A90E2] rounded-lg hover:bg-blue-50 transition-colors text-sm font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        ดูสัญญาเช่า
                    </a>
                    @endif
                    @if($contract->idcard_file)
                    <a href="{{ asset('storage/' . $contract->idcard_file) }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors text-sm font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        ดูสำเนาบัตรประชาชน
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Edit button --}}
            <div class="flex justify-end">
                <a href="{{ route('rooms.edit', $room->id) }}"
                   class="px-8 py-3 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold rounded-lg transition-colors shadow-md">
                    แก้ไขสัญญา
                </a>
            </div>

        </main>
    </div>

</body>
</html>
