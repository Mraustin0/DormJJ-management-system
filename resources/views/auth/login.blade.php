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
<body class="bg-gray-50 h-screen flex items-center justify-center overflow-hidden">

    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg border border-gray-100 mx-4 relative">
        
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#4A90E2] leading-tight mb-2">
                WELCOME TO<br>JJ APARTMENT
            </h1>
            <p class="text-gray-400 text-sm mt-2">ระบบจัดการหอพักออนไลน์</p>
        </div>

        {{-- --- ส่วนแสดง Error Message --- --}}
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
            <h2 class="text-xs font-bold tracking-widest text-gray-500 mb-6 uppercase">
                Please Log In
            </h2>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf 
                
                {{-- ช่อง Username --}}
                <div class="relative group">
                    <input type="text" name="username" placeholder="Username" 
                           value="{{ old('username') }}"
                           class="w-full px-4 py-3.5 border rounded-xl text-gray-700 focus:outline-none transition-all duration-300 bg-gray-50 focus:bg-white
                           {{ $errors->has('username') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 focus:border-[#4A90E2] focus:ring-2 focus:ring-[#4A90E2]/20' }}"
                           required>
                </div>

                {{-- ช่อง Password --}}
                <div class="relative group">
                    <input type="password" name="password" placeholder="Password" 
                           class="w-full px-4 py-3.5 border rounded-xl text-gray-700 focus:outline-none transition-all duration-300 bg-gray-50 focus:bg-white
                           {{ $errors->has('password') ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300 focus:border-[#4A90E2] focus:ring-2 focus:ring-[#4A90E2]/20' }}"
                           required>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-black text-white text-base font-bold px-6 py-3.5 rounded-xl transition-all duration-300 shadow-md uppercase tracking-wider
                            hover:bg-[#4A90E2] hover:shadow-lg hover:shadow-[#4A90E2]/30 hover:-translate-y-0.5
                            active:scale-95 active:shadow-none">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="absolute bottom-4 text-center w-full text-xs text-gray-400">
        &copy; {{ date('Y') }} JJ Apartment Management System
    </div>

</body>
</html>