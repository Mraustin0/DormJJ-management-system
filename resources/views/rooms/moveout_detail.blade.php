<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>แจ้งย้ายออก - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'moveout'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'แจ้งย้ายออกห้อง ' . ($moveout->room->room_number ?? '-')])

        <main class="p-6 max-w-5xl">

            {{-- Back --}}
            <a href="{{ route('moveout.requests') }}"
               class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 mb-6 text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                
            </a>

            @php
                $contract    = $moveout->contract;
                $room        = $moveout->room ?? $contract->room;
                $isPending   = $moveout->status === 'pending';
                $isApproved  = $moveout->status === 'approved';
                $isRejected  = $moveout->status === 'rejected';
                $deposit     = $contract->deposit ?? 0;
                $contractFile = $contract->contract_file ? asset('storage/' . $contract->contract_file) : null;
                $startDate   = $contract->check_in_date ?? $contract->start_date ?? null;
            @endphp

            {{-- Status Banner (if processed) --}}
            @if($isApproved)
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-4 mb-5">
                <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-green-700">อนุมัติแล้ว</p>
                    <p class="text-sm text-green-600">ดำเนินการเมื่อ {{ $moveout->processed_at ? $moveout->processed_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
            @elseif($isRejected)
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-5">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-red-700">ปฏิเสธแล้ว</p>
                    <p class="text-sm text-red-600">เหตุผล: {{ $moveout->admin_remark ?? '-' }}</p>
                </div>
            </div>
            @endif

            {{-- Main Card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

                    {{-- LEFT: ข้อมูล --}}
                    <div class="space-y-7">

                        {{-- ข้อมูลผู้เช่า --}}
                        <div>
                            <h3 class="font-bold text-gray-700 text-base mb-3 pb-2 border-b border-gray-100">ข้อมูลผู้เช่า</h3>
                            <div class="space-y-2 text-sm">
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">ชื่อ-สกุล:</span>
                                    <span class="font-semibold text-gray-800">{{ $contract->tenant_name ?? '-' }}</span>
                                </div>
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">เลขบัตรประชาชน:</span>
                                    <span class="font-medium text-gray-700">{{ $contract->nid ?? '-' }}</span>
                                </div>
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">เบอร์โทรศัพท์:</span>
                                    <span class="font-medium text-gray-700">{{ $contract->phone ?? '-' }}</span>
                                </div>
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">อีเมล:</span>
                                    <span class="font-medium text-gray-700">{{ $contract->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- ข้อมูลสัญญา --}}
                        <div>
                            <h3 class="font-bold text-gray-700 text-base mb-3 pb-2 border-b border-gray-100">ข้อมูลสัญญา</h3>
                            <div class="space-y-2 text-sm">
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">สัญญาเช่า:</span>
                                    @if($contractFile)
                                        <a href="{{ $contractFile }}" target="_blank" class="text-[#4A90E2] hover:underline font-medium flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            หนังสือสัญญาเช่าห้อง{{ $room->room_number ?? '' }}.pdf
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">วันที่เริ่มเช่า:</span>
                                    <span class="font-medium text-gray-700">
                                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->locale('th')->translatedFormat('j F Y') : '-' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-[140px_1fr]">
                                    <span class="text-gray-400">เงินประกัน:</span>
                                    <span class="font-semibold text-gray-800">฿{{ number_format($deposit, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- ข้อมูลการแจ้งย้ายออก --}}
                        <div>
                            <h3 class="font-bold text-gray-700 text-base mb-3 pb-2 border-b border-gray-100">ข้อมูลการแจ้งย้ายออก</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <label class="block text-gray-400 mb-1">วันที่แจ้งย้ายออก <span class="text-red-400">*</span></label>
                                    <input type="date" id="notifyDate"
                                           value="{{ $moveout->notify_date ? $moveout->notify_date->format('Y-m-d') : '' }}"
                                           {{ !$isPending ? 'readonly' : '' }}
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-[#4A90E2] outline-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">
                                </div>
                                <div>
                                    <label class="block text-gray-400 mb-1">วันที่ย้ายออกจริง <span class="text-red-400">*</span></label>
                                    <input type="date" id="moveoutDate"
                                           value="{{ $moveout->moveout_date ? $moveout->moveout_date->format('Y-m-d') : '' }}"
                                           {{ !$isPending ? 'readonly' : '' }}
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-[#4A90E2] outline-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: ข้อมูลการชำระเงิน --}}
                    <div>
                        <h3 class="font-bold text-gray-700 text-base mb-3 pb-2 border-b border-gray-100">ข้อมูลการชำระเงิน</h3>

                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block text-gray-400 mb-1">ยอดค้างชำระ</label>
                                <input type="number" id="outstandingDebt" min="0" placeholder="0"
                                       oninput="calculateReturn()"
                                       value="{{ $isApproved ? ($deposit - ($moveout->deposit_return ?? 0)) : '' }}"
                                       {{ !$isPending ? 'readonly' : '' }}
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-1">เงินประกัน/เงินมัดจำ</label>
                                <input type="text" value="฿{{ number_format($deposit, 0) }}" readonly
                                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-500 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-1">ค่าเสียหาย (ถ้ามี)</label>
                                <input type="number" id="fineAmount" min="0" placeholder="0"
                                       oninput="calculateReturn()"
                                       {{ !$isPending ? 'readonly' : '' }}
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-1">ยอดรวมที่ต้องคืน</label>
                                <input type="text" id="depositReturn"
                                       value="{{ $isApproved ? '฿' . number_format($moveout->deposit_return ?? 0, 0) : '฿' . number_format($deposit, 0) }}"
                                       readonly
                                       class="w-full border-2 border-[#4A90E2] bg-blue-50 rounded-lg px-3 py-2.5 text-[#4A90E2] font-bold text-base cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-1">วันที่คืนเงินประกัน <span class="text-red-400">*</span></label>
                                <input type="date" id="returnDate"
                                       {{ !$isPending ? 'readonly' : '' }}
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>

                            <div>
                                <label class="block text-gray-400 mb-1">หมายเหตุ</label>
                                <textarea id="adminRemark" rows="3" placeholder="กรุณากรอกหมายเหตุ"
                                          {{ !$isPending ? 'readonly' : '' }}
                                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 focus:border-[#4A90E2] outline-none resize-none {{ !$isPending ? 'bg-gray-50 text-gray-500' : '' }}">{{ $moveout->admin_remark ?? '' }}</textarea>
                            </div>
                        </div>

                        @if($isPending)
                        {{-- Action Buttons --}}
                        <div class="flex gap-3 mt-6">
                            <button type="button" onclick="confirmReject()"
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-xl transition-colors">
                                ปฏิเสธ
                            </button>
                            <button type="button" onclick="confirmApprove()"
                                    class="flex-1 bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-xl transition-colors">
                                อนุมัติ
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">หลังอนุมัติ ห้องจะเปลี่ยนสถานะเป็น ห้องว่าง</p>
                        @elseif($isApproved)
                        <div class="mt-6 flex items-center gap-2 text-green-600 bg-green-50 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold text-sm">ดำเนินการอนุมัติแล้ว ห้องเปลี่ยนเป็นว่างแล้ว</span>
                        </div>
                        @elseif($isRejected)
                        <div class="mt-6 flex items-center gap-2 text-red-600 bg-red-50 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold text-sm">ปฏิเสธคำร้องแล้ว</span>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        const deposit = {{ $deposit ?? 0 }};

        function calculateReturn() {
            const debt = parseFloat(document.getElementById('outstandingDebt').value) || 0;
            const fine = parseFloat(document.getElementById('fineAmount').value) || 0;
            const ret  = deposit - debt - fine;
            document.getElementById('depositReturn').value = '฿' + ret.toLocaleString('th-TH', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        function confirmApprove() {
            const returnDate = document.getElementById('returnDate').value;
            if (!returnDate) {
                Swal.fire({ icon: 'warning', title: 'กรุณาระบุวันที่คืนเงินประกัน', confirmButtonColor: '#4A90E2' });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการอนุมัติ?',
                text: 'ห้องจะเปลี่ยนสถานะเป็นว่าง และสัญญาจะสิ้นสุด',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4A90E2',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then(result => {
                if (!result.isConfirmed) return;

                const debt = parseFloat(document.getElementById('outstandingDebt').value) || 0;
                const fine = parseFloat(document.getElementById('fineAmount').value) || 0;

                fetch('{{ route("moveout.approve", $moveout->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        outstanding_debt: debt,
                        fine_amount:      fine,
                        return_date:      returnDate,
                        admin_remark:     document.getElementById('adminRemark').value
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'อนุมัติสำเร็จ!', showConfirmButton: false, timer: 1500 })
                            .then(() => { window.location.href = '{{ route("moveout.requests") }}'; });
                    } else {
                        Swal.fire({ icon: 'error', title: data.message || 'เกิดข้อผิดพลาด', confirmButtonColor: '#4A90E2' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', confirmButtonColor: '#4A90E2' }));
            });
        }

        function confirmReject() {
            Swal.fire({
                title: 'ปฏิเสธคำร้องนี้?',
                input: 'textarea',
                inputLabel: 'กรุณาระบุเหตุผลในการปฏิเสธ',
                inputPlaceholder: 'เหตุผล...',
                inputValidator: (value) => { if (!value) return 'กรุณาระบุเหตุผล' },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'ยืนยัน ปฏิเสธ',
                cancelButtonText: 'ยกเลิก'
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch('{{ route("moveout.reject", $moveout->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ admin_remark: result.value })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'ปฏิเสธคำร้องแล้ว', showConfirmButton: false, timer: 1500 })
                            .then(() => { window.location.href = '{{ route("moveout.requests") }}'; });
                    } else {
                        Swal.fire({ icon: 'error', title: data.message || 'เกิดข้อผิดพลาด', confirmButtonColor: '#4A90E2' });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', confirmButtonColor: '#4A90E2' }));
            });
        }
    </script>
</body>
</html>
