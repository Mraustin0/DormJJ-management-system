<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ - ผู้เช่า</title>
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
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800">โปรไฟล์</h2>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6">
            <div class="max-w-2xl mx-auto">
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Profile Form --}}
                <form action="{{ route('tenant.profile.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div class="w-20 h-20 bg-[#4A90E2] rounded-full flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $user->username }}</h3>
                            @if($contract)
                                <p class="text-gray-500">ห้อง {{ $contract->room->room_number }} - {{ $contract->tenant_name }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">ชื่อผู้ใช้</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent @error('username') border-red-500 @enderror">
                        @error('username')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">อีเมล</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Section --}}
                    <div class="border-t border-gray-100 pt-6 mt-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">เปลี่ยนรหัสผ่าน</h4>
                        <p class="text-gray-500 text-sm mb-4">เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยน</p>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">รหัสผ่านใหม่</label>
                            <input type="password" name="password"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#4A90E2] focus:border-transparent">
                        </div>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-[#4A90E2] text-white rounded-lg font-semibold hover:bg-[#3a7bc8] transition-colors">
                        บันทึกข้อมูล
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
