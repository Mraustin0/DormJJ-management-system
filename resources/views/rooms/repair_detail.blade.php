<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รายละเอียดการแจ้งซ่อม - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'repairs'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'รายละเอียดการแจ้งซ่อม'])

        <main class="p-6">

            {{-- Back button --}}
            <a href="{{ route('repairs.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#4A90E2] mb-5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                
            </a>

            {{-- Status Banner --}}
            @if($repair->status == 'in_progress')
                <div class="flex items-center gap-3 bg-orange-50 border border-orange-200 rounded-xl px-5 py-3 mb-5">
                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-orange-700 font-medium text-sm">สถานะ: <strong>กำลังดำเนินการ</strong></p>
                </div>
            @elseif($repair->status == 'completed')
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3 mb-5">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 font-medium text-sm">สถานะ: <strong>ซ่อมเสร็จสิ้นแล้ว</strong></p>
                </div>
            @elseif($repair->status == 'cancelled')
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-5 py-3 mb-5">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500 font-medium text-sm">สถานะ: <strong>ยกเลิกแล้ว</strong></p>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">รายละเอียดการแจ้งซ่อม</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- Left: Repair Info --}}
                    <div class="space-y-6">

                        {{-- Basic Info --}}
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="text-gray-400 text-sm w-32 flex-shrink-0 pt-0.5">ห้อง</span>
                                <span class="font-bold text-gray-900 text-base">{{ $repair->room->room_number ?? '-' }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-gray-400 text-sm w-32 flex-shrink-0 pt-0.5">วันที่แจ้งซ่อม</span>
                                <span class="font-medium text-gray-800">
                                    {{ $repair->created_at ? $repair->created_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                            @if($repair->category)
                            <div class="flex items-start gap-3">
                                <span class="text-gray-400 text-sm w-32 flex-shrink-0 pt-0.5">หมวดหมู่</span>
                                <span class="font-medium text-gray-800">{{ $repair->category }}</span>
                            </div>
                            @endif
                            <div class="flex items-start gap-3">
                                <span class="text-gray-400 text-sm w-32 flex-shrink-0 pt-0.5">ปัญหาที่แจ้ง</span>
                                <span class="font-bold text-gray-900">{{ $repair->title }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="text-gray-400 text-sm w-32 flex-shrink-0 pt-0.5">สถานะ</span>
                                <span>
                                    @if($repair->status == 'pending')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">รอดำเนินการ</span>
                                    @elseif($repair->status == 'in_progress')
                                        <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold">กำลังดำเนินการ</span>
                                    @elseif($repair->status == 'completed')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">เสร็จสิ้น</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">ยกเลิก</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($repair->description)
                        <div>
                            <p class="text-sm font-bold text-gray-500 mb-2">รายละเอียดปัญหา</p>
                            <div class="bg-gray-50 rounded-lg p-4 text-gray-700 text-sm leading-relaxed border border-gray-100">
                                {{ $repair->description }}
                            </div>
                        </div>
                        @endif

                        {{-- Admin Remark (if any) --}}
                        @if($repair->remark)
                        <div>
                            <p class="text-sm font-bold text-gray-500 mb-2">หมายเหตุจากผู้ดูแล</p>
                            <div class="bg-blue-50 rounded-lg p-4 text-gray-700 text-sm leading-relaxed border border-blue-100">
                                {{ $repair->remark }}
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- Right: Photo + Actions --}}
                    <div class="space-y-6">

                        {{-- Photo --}}
                        <div>
                            <p class="text-sm font-bold text-gray-500 mb-3">รูปถ่ายประกอบ</p>
                            @if($repair->image)
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                    <img src="{{ asset('storage/' . $repair->image) }}"
                                         alt="รูปถ่ายการแจ้งซ่อม"
                                         class="w-full object-cover max-h-72 cursor-pointer hover:opacity-90 transition-opacity"
                                         onclick="openImageModal(this.src)">
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5 text-center">คลิกเพื่อดูรูปขนาดใหญ่</p>
                            @else
                                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center py-12 text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm">ไม่มีรูปถ่าย</p>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons (only when pending or in_progress) --}}
                        @if($repair->status == 'pending' || $repair->status == 'in_progress')
                        <div class="space-y-3 pt-2">
                            @if($repair->status == 'pending')
                            <button onclick="confirmStatus('in_progress')"
                                    class="w-full bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-6 rounded-xl shadow transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                ยืนยันรับเรื่อง
                            </button>
                            @endif

                            <button onclick="confirmStatus('completed')"
                                    class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-6 rounded-xl shadow transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ซ่อมเสร็จสิ้น
                            </button>

                            
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center p-4" onclick="closeImageModal()">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl object-contain">
    </div>

    <script>
        const repairId = {{ $repair->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const statusLabels = {
            in_progress: 'ยืนยันรับเรื่อง',
            completed:   'ซ่อมเสร็จสิ้น',
            cancelled:   'ยกเลิกคำร้อง',
        };

        const statusTexts = {
            in_progress: 'สถานะจะเปลี่ยนเป็น "กำลังดำเนินการ" และแจ้งเตือนผู้เช่า',
            completed:   'สถานะจะเปลี่ยนเป็น "เสร็จสิ้น" และแจ้งเตือนผู้เช่า',
            cancelled:   'คำร้องจะถูกยกเลิก กรุณาระบุเหตุผล',
        };

        function confirmStatus(newStatus) {
            if (newStatus === 'cancelled') {
                // For cancel, ask for reason
                Swal.fire({
                    title: statusLabels[newStatus] + '?',
                    text: statusTexts[newStatus],
                    input: 'textarea',
                    inputPlaceholder: 'ระบุเหตุผลการยกเลิก...',
                    inputAttributes: { 'aria-label': 'เหตุผล' },
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    preConfirm: (remark) => {
                        if (!remark || !remark.trim()) {
                            Swal.showValidationMessage('กรุณาระบุเหตุผล');
                        }
                        return remark;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        doUpdateStatus(newStatus, result.value);
                    }
                });
            } else {
                // For in_progress / completed, optionally add remark
                Swal.fire({
                    title: statusLabels[newStatus] + '?',
                    text: statusTexts[newStatus],
                    input: 'textarea',
                    inputPlaceholder: 'หมายเหตุเพิ่มเติม (ถ้ามี)',
                    inputAttributes: { 'aria-label': 'หมายเหตุ' },
                    showCancelButton: true,
                    confirmButtonColor: newStatus === 'in_progress' ? '#4A90E2' : '#14b8a6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                }).then(result => {
                    if (result.isConfirmed) {
                        doUpdateStatus(newStatus, result.value || '');
                    }
                });
            }
        }

        function doUpdateStatus(newStatus, remark) {
            fetch(`/repairs/${repairId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus, remark: remark }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                } else {
                    Swal.fire('ผิดพลาด', data.message ?? 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error'));
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
        }
    </script>

</body>
</html>
