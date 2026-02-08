<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งรหัสผ่านใหม่ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center">

    <main class="w-full max-w-sm p-8 mx-4">
        {{-- Logo --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#4A90E2] leading-tight italic">
                WELCOME TO<br>JJ APARTMENT
            </h1>
        </div>

        <div class="text-center">
            <h2 class="text-sm font-bold tracking-wider text-gray-800 mb-6 uppercase">ตั้งรหัสผ่านใหม่</h2>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-2">กรุณากรอกรหัสผ่านใหม่</label>
                    <input type="password" name="password"
                           placeholder="รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2]"
                           required minlength="6" autofocus>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-2">กรุณากรอกรหัสผ่านอีกครั้ง</label>
                    <input type="password" name="password_confirmation"
                           placeholder="ยืนยันรหัสผ่านใหม่"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2]"
                           required minlength="6">
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-black text-white text-sm font-bold px-6 py-3 rounded-full transition-all duration-300 uppercase tracking-wider hover:bg-gray-800 active:scale-95">
                        ยืนยัน
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
