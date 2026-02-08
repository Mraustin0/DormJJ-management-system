<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
            20%, 40%, 60%, 80% { transform: translateX(4px); }
        }
        .animate-shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm p-8 mx-4">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#4A90E2] leading-tight italic">
                WELCOME TO<br>JJ APARTMENT
            </h1>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-green-500 mt-0.5">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm animate-shake">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-red-500 mt-0.5">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-bold leading-none mb-1">เข้าสู่ระบบไม่สำเร็จ</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li class="text-xs text-red-600">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center">
            <h2 class="text-sm font-bold tracking-wider text-gray-800 mb-6 uppercase">
                LOG IN
            </h2>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <input type="text" name="username" placeholder="Username"
                           value="{{ old('username') }}"
                           class="w-full px-4 py-3 border-b border-gray-300 text-gray-700 focus:outline-none focus:border-[#4A90E2] transition-colors bg-transparent placeholder-gray-400"
                           required>
                </div>

                <div>
                    <input type="password" name="password" placeholder="Password"
                           class="w-full px-4 py-3 border-b border-gray-300 text-gray-700 focus:outline-none focus:border-[#4A90E2] transition-colors bg-transparent placeholder-gray-400"
                           required>
                </div>

                <div class="text-right pt-1">
                    <a href="{{ route('password.request') }}" class="text-xs text-[#4A90E2] hover:underline">
                        ลืมรหัสผ่าน
                    </a>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-black text-white text-sm font-bold px-6 py-3 rounded-full transition-all duration-300 uppercase tracking-wider
                            hover:bg-gray-800 active:scale-95">
                        LOGIN
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
