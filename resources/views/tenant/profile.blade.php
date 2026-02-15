<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าบัญชี - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'profile'])

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
                    {{-- Notification Bell --}}
                    <button class="text-white relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                    {{-- User Avatar --}}
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="text-white text-sm hidden sm:block">
                            @if($contract)
                                <p class="font-semibold leading-tight">{{ $contract->tenant_name }}</p>
                                <p class="text-white/80 text-xs">ห้อง {{ $contract->room->room_number }}</p>
                            @else
                                <p class="font-semibold leading-tight">{{ $user->username }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            <div class="max-w-lg mx-auto">
                {{-- Title --}}
                <h2 class="text-xl font-bold text-gray-800 mb-6">ตั้งค่าบัญชี</h2>

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Profile Avatar --}}
                <div class="flex flex-col items-center mb-8">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-14 h-14 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-800">{{ $user->username }}</p>
                </div>

                {{-- Profile Form --}}
                <form action="{{ route('tenant.profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')

                    {{-- ชื่อ-สกุล --}}
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1.5">ชื่อ-สกุล</label>
                        <input type="text" name="tenant_name" value="{{ old('tenant_name', $contract->tenant_name ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent focus:bg-white transition-colors profile-input" disabled>
                    </div>

                    {{-- เบอร์ --}}
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1.5">เบอร์</label>
                        <input type="text" name="phone" value="{{ old('phone', $contract->phone ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent focus:bg-white transition-colors profile-input" disabled>
                    </div>

                    {{-- อีเมล --}}
                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm mb-1.5">อีเมล</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent focus:bg-white transition-colors profile-input @error('email') border-red-500 @enderror" disabled>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- เลขบัตรประชาชน --}}
                    <div class="mb-8">
                        <label class="block text-gray-600 text-sm mb-1.5">เลขบัตรประชาชน</label>
                        <input type="text" value="{{ $contract ? (substr($contract->nid, 0, 1) . '-' . substr($contract->nid, 1, 4) . '-' . substr($contract->nid, 5, 5) . '-' . substr($contract->nid, 10, 2) . '-' . substr($contract->nid, 12)) : '-' }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 bg-gray-100 text-gray-500" disabled readonly>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-4">
                        <button type="button" id="editBtn" onclick="enableEditing()" class="flex-1 px-6 py-3 border-2 border-red-500 text-red-500 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                            แก้ไข
                        </button>
                        <button type="submit" id="saveBtn" class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors opacity-50 cursor-not-allowed" disabled>
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        let isEditing = false;

        function enableEditing() {
            isEditing = !isEditing;
            const inputs = document.querySelectorAll('.profile-input');
            const saveBtn = document.getElementById('saveBtn');
            const editBtn = document.getElementById('editBtn');

            if (isEditing) {
                inputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('bg-gray-50');
                    input.classList.add('bg-white');
                });
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                editBtn.textContent = 'ยกเลิก';
                editBtn.classList.remove('border-red-500', 'text-red-500', 'hover:bg-red-50');
                editBtn.classList.add('border-gray-400', 'text-gray-500', 'hover:bg-gray-50');
            } else {
                inputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('bg-gray-50');
                    input.classList.remove('bg-white');
                });
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                editBtn.textContent = 'แก้ไข';
                editBtn.classList.add('border-red-500', 'text-red-500', 'hover:bg-red-50');
                editBtn.classList.remove('border-gray-400', 'text-gray-500', 'hover:bg-gray-50');
                // Reset form
                document.getElementById('profileForm').reset();
            }
        }
    </script>
</body>
</html>
