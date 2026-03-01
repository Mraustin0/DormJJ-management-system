<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งย้ายออก - ผู้เช่า</title>
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
        <div class="p-6 sm:p-8">
            {{-- History icon top-right --}}
            <div class="flex items-start justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">แจ้งย้ายออก</h2>
                @if(isset($hasMoveoutRequest) && $hasMoveoutRequest)
                    <a href="{{ route('tenant.moveout.status') }}" class="text-gray-400 hover:text-gray-600 transition-colors" title="ติดตามสถานะ">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Success Message (SweetAlert2) --}}
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

            <div class="max-w-2xl mx-auto">
                {{-- ข้อมูลผู้เช่า --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลผู้เช่า</h3>
                    <div class="space-y-3 ml-4">
                        <div class="flex">
                            <span class="text-gray-500 w-40 flex-shrink-0">ชื่อ-สกุล:</span>
                            <span class="text-gray-800 font-medium">{{ $contract->tenant_name }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-40 flex-shrink-0">เลขบัตรประชาชน:</span>
                            <span class="text-gray-800 font-medium">{{ $contract->nid ? (substr($contract->nid,0,1).'-'.substr($contract->nid,1,4).'-'.substr($contract->nid,5,5).'-'.substr($contract->nid,10,2).'-'.substr($contract->nid,12)) : '-' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-40 flex-shrink-0">เบอร์โทรศัพท์:</span>
                            <span class="text-gray-800 font-medium">{{ $contract->phone ?? '-' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-40 flex-shrink-0">อีเมล:</span>
                            <span class="text-gray-800 font-medium">{{ $contract->email ?? Auth::user()->email ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลสัญญา --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">ข้อมูลสัญญา</h3>
                    <div class="space-y-3 ml-4">
                        <div class="flex items-center">
                            <span class="text-gray-500 w-40 flex-shrink-0">สัญญาเช่า:</span>
                            @if($contract->contract_file)
                                <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank" class="flex items-center gap-1.5 text-blue-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    หนังสือสัญญาเช่าห้อง{{ $contract->room->room_number }}.pdf
                                </a>
                            @else
                                <span class="text-gray-400">ไม่มีไฟล์สัญญา</span>
                            @endif
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-40 flex-shrink-0">วันที่เริ่มเช่า:</span>
                            <span class="text-gray-800 font-medium">
                                @if($contract->start_date)
                                    {{ \Carbon\Carbon::parse($contract->start_date)->format('j') }} {{ thaiMonth(\Carbon\Carbon::parse($contract->start_date)->format('m')) }} {{ \Carbon\Carbon::parse($contract->start_date)->format('Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('tenant.moveout.store') }}" method="POST" id="moveoutForm" class="ml-4">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-1">วันที่แจ้งย้ายออก <span class="text-red-500">*</span></label>
                        <div class="max-w-sm">
                            <input type="date" name="notify_date" value="{{ old('notify_date', date('Y-m-d')) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent @error('notify_date') border-red-500 @enderror">
                        </div>
                        @error('notify_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-gray-700 font-medium mb-1">วันที่ย้ายออกจริง <span class="text-red-500">*</span></label>
                        <div class="max-w-sm">
                            <input type="date" name="moveout_date" value="{{ old('moveout_date') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent @error('moveout_date') border-red-500 @enderror">
                        </div>
                        @error('moveout_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-center">
                        <button type="button" onclick="confirmMoveout()" class="px-10 py-2.5 bg-red-400 text-white rounded-lg font-semibold hover:bg-red-500 transition-colors">
                            แจ้งย้ายออก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function confirmMoveout() {
            const form = document.getElementById('moveoutForm');
            // Validate required fields first
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'ยืนยันการแจ้งย้ายออก',
                text: 'เมื่อยืนยันแล้วจะไม่สามารถยกเลิกได้ คุณต้องการดำเนินการต่อหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f87171',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยันย้ายออก',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
