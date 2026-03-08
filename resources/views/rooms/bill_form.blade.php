<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>สร้างบิล - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'bills.create'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">

        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-4">
                {{-- Profile Dropdown --}}
                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 hover:bg-gray-50 rounded-lg p-1 pr-2 transition-colors">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">Admin</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="py-2">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="font-medium">แก้ไขโปรไฟล์</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-medium">ตั้งค่าระบบ</span>
                            </a>
                            <hr class="my-2">
                            <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmLogout()" class="flex items-center gap-3 px-4 py-2.5 text-red-500 hover:bg-red-50 transition-colors w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span class="font-medium">ออกจากระบบ</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-6">
            @php
                $monthName = \Carbon\Carbon::parse($selectedMonth)->locale('th')->translatedFormat('F Y');
                $meter = $room->meterReadings->first();
            @endphp

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('bills.create') }}?month={{ $selectedMonth }}"
                   class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-[#4A90E2] transition-colors inline-flex shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h3 class="text-xl font-bold text-gray-800">
                    {{ $existingBill ? 'แก้ไขบิล' : 'สร้างบิล' }} ห้อง {{ $room->room_number }} ประจำเดือน {{ $monthName }}
                </h3>
                @if($existingBill && $existingBill->status === 'paid')
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">ชำระแล้ว — ไม่สามารถแก้ไขได้</span>
                @elseif($existingBill)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">มีบิลอยู่แล้ว — แก้ไขได้</span>
                @endif
            </div>

            @if($existingBill && $existingBill->status === 'paid')
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-green-700">บิลนี้ชำระเงินและออกใบเสร็จแล้ว ไม่สามารถแก้ไขได้ หากต้องการแก้ไขให้ยกเลิกใบเสร็จก่อน</p>
            </div>
            @endif

            @if(!$meter && !$existingBill)
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-300 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-bold text-yellow-700">ยังไม่มีข้อมูลมิเตอร์สำหรับเดือนนี้</p>
                    <p class="text-sm text-yellow-600 mt-0.5">กรุณาบันทึกค่ามิเตอร์น้ำและไฟก่อน จึงจะสามารถสร้างบิลได้</p>
                </div>
                <a href="{{ route('meters.water') }}?month={{ $selectedMonth }}"
                   class="shrink-0 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                    บันทึกมิเตอร์
                </a>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                {{-- Tenant Info --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">{{ $room->contract->tenant_name ?? '-' }}</h4>
                        <p class="text-sm text-[#4A90E2]">กำลังเข้าพัก</p>
                    </div>
                </div>

                <form id="billForm" class="grid grid-cols-1 lg:grid-cols-2 gap-8" @if($existingBill && $existingBill->status === 'paid') inert @endif>
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="billing_month" value="{{ $selectedMonth }}">

                    {{-- Left Column --}}
                    <div class="space-y-6">
                        {{-- Room Number --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ห้อง <span class="text-red-500">*</span></label>
                            <input type="text" value="{{ $room->room_number }}" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg px-4 py-2.5 text-gray-500">
                        </div>

                        {{-- Water Section --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าน้ำ <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์เริ่มต้น</label>
                                    <input type="number" id="water_prev" value="{{ $meter->water_prev ?? 0 }}" oninput="calculateWater()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์ล่าสุด</label>
                                    <input type="number" id="water_curr" value="{{ $meter->water_curr ?? '' }}" oninput="calculateWater()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยที่ใช้</label>
                                    <div class="flex">
                                        <input type="number" id="water_units" name="water_units" value="{{ $meter->water_unit ?? 0 }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยละ</label>
                                    <div class="flex">
                                        <input type="number" id="water_rate"
                                       value="{{ $existingBill && $existingBill->water_units > 0 ? round($existingBill->water_amount / $existingBill->water_units, 2) : ($setting->water_rate ?? 18) }}"
                                       oninput="calculateWater()" class="w-full border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500 mb-1">ค่าน้ำ</label>
                                <div class="flex">
                                    <input type="number" id="water_amount" name="water_amount" value="0" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm font-bold">
                                    <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                </div>
                            </div>
                        </div>

                        {{-- Electric Section --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าไฟ <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์เริ่มต้น</label>
                                    <input type="number" id="elec_prev" value="{{ $meter->elec_prev ?? 0 }}" oninput="calculateElec()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">เลขมิเตอร์ล่าสุด</label>
                                    <input type="number" id="elec_curr" value="{{ $meter->elec_curr ?? '' }}" oninput="calculateElec()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยที่ใช้</label>
                                    <div class="flex">
                                        <input type="number" id="elec_units" name="electric_units" value="{{ $meter->elec_unit ?? 0 }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">หน่วย</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">หน่วยละ</label>
                                    <div class="flex">
                                        <input type="number" id="elec_rate"
                                       value="{{ $existingBill && $existingBill->electric_units > 0 ? round($existingBill->electric_amount / $existingBill->electric_units, 2) : ($setting->electric_rate ?? 8) }}"
                                       oninput="calculateElec()" class="w-full border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:border-[#4A90E2] outline-none">
                                        <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500 mb-1">ค่าไฟ</label>
                                <div class="flex">
                                    <input type="number" id="elec_amount" name="electric_amount" value="0" readonly class="w-full bg-gray-50 border border-gray-300 rounded-l-lg px-3 py-2 text-sm font-bold">
                                    <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2 text-sm text-gray-500">บาท</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">
                        {{-- Room Rate --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ค่าห้อง <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" id="room_rate" name="room_rate"
                                       value="{{ $existingBill?->room_rate ?? $setting->rent_per_month ?? $room->price ?? 3500 }}"
                                       oninput="calculateTotal()" class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        {{-- Due Date --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">วันกำหนดชำระ <span class="text-red-500">*</span></label>
                            <input type="date" id="due_date" name="due_date"
                                   value="{{ $existingBill?->due_date ? \Carbon\Carbon::parse($existingBill->due_date)->format('Y-m-d') : \Carbon\Carbon::parse($selectedMonth)->day((int)($setting->payment_due_day ?? 5))->format('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                        </div>

                        {{-- Other Fees --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ค่าปรับ</label>
                            <input type="text" id="fine_desc" placeholder="กรุณากรอกรายละเอียดค่าปรับ" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 mb-2 focus:border-[#4A90E2] outline-none">
                            <div class="flex">
                                <input type="number" id="other_fees" name="other_fees"
                                       value="{{ $existingBill?->other_fees ?? $setting->late_fee_per_day ?? 0 }}"
                                       oninput="calculateTotal()" placeholder="กรุณากรอกค่าปรับ"
                                       class="w-full border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] outline-none">
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ยอดรวม <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" id="total_amount" name="total_amount" value="0" readonly class="w-full bg-blue-50 border-2 border-[#4A90E2] rounded-l-lg px-4 py-2.5 text-lg font-bold text-[#4A90E2]">
                                <span class="bg-[#4A90E2] text-white rounded-r-lg px-4 py-2.5 font-bold">บาท</span>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-4">
                            @if($existingBill && $existingBill->status === 'paid')
                            <button type="button" disabled
                                    class="w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                ชำระแล้ว — ไม่สามารถแก้ไข
                            </button>
                            @elseif(!$meter && !$existingBill)
                            <button type="button" disabled
                                    class="w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                บันทึกมิเตอร์ก่อนสร้างบิล
                            </button>
                            @elseif($existingBill)
                            <button type="button" onclick="saveBill()"
                                    class="w-full bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-lg transition-colors shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                แก้ไขบิล
                            </button>
                            @else
                            <button type="button" onclick="saveBill()"
                                    class="w-full bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 rounded-lg transition-colors shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                บันทึกข้อมูล
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script>
        function calculateWater() {
            const prev = parseFloat(document.getElementById('water_prev').value) || 0;
            const curr = parseFloat(document.getElementById('water_curr').value) || 0;
            const rate = parseFloat(document.getElementById('water_rate').value) || 0;
            const units = Math.max(0, curr - prev);
            const amount = units * rate;

            document.getElementById('water_units').value = units;
            document.getElementById('water_amount').value = amount;
            calculateTotal();
        }

        function calculateElec() {
            const prev = parseFloat(document.getElementById('elec_prev').value) || 0;
            const curr = parseFloat(document.getElementById('elec_curr').value) || 0;
            const rate = parseFloat(document.getElementById('elec_rate').value) || 0;
            const units = Math.max(0, curr - prev);
            const amount = units * rate;

            document.getElementById('elec_units').value = units;
            document.getElementById('elec_amount').value = amount;
            calculateTotal();
        }

        function calculateTotal() {
            const waterAmount = parseFloat(document.getElementById('water_amount').value) || 0;
            const elecAmount = parseFloat(document.getElementById('elec_amount').value) || 0;
            const roomRate = parseFloat(document.getElementById('room_rate').value) || 0;
            const otherFees = parseFloat(document.getElementById('other_fees').value) || 0;

            const total = waterAmount + elecAmount + roomRate + otherFees;
            document.getElementById('total_amount').value = total;
        }

        function saveBill() {
            const formData = new FormData(document.getElementById('billForm'));

            fetch("{{ route('bills.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกบิลเรียบร้อย!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // ไปหน้าดูบิล
                        window.location.href = '/bills/view/{{ $room->id }}?month={{ $selectedMonth }}';
                    });
                } else {
                    Swal.fire('Error', data.message || 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึก', 'error');
            });
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        function confirmLogout() {
            Swal.fire({
                title: 'ออกจากระบบ?',
                text: 'คุณต้องการออกจากระบบใช่หรือไม่',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f27b6d',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'ออกจากระบบ',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('profileContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('profileDropdown')?.classList.add('hidden');
            }
        });

        // Calculate on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if($existingBill)
            // Pre-fill from existing saved bill
            document.getElementById('water_units').value = {{ $existingBill->water_units ?? 0 }};
            document.getElementById('water_amount').value = {{ $existingBill->water_amount ?? 0 }};
            document.getElementById('elec_units').value = {{ $existingBill->electric_units ?? 0 }};
            document.getElementById('elec_amount').value = {{ $existingBill->electric_amount ?? 0 }};
            calculateTotal();
            @else
            calculateWater();
            calculateElec();
            @endif
        });
    </script>
</body>
</html>
