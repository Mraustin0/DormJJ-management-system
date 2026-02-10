<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไม่พบสัญญาเช่า - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto p-8">
        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">ไม่พบสัญญาเช่า</h1>
        <p class="text-gray-500 mb-8">
            บัญชีของคุณยังไม่ได้ผูกกับสัญญาเช่าใดๆ<br>
            กรุณาติดต่อเจ้าหน้าที่เพื่อดำเนินการ
        </p>
        <div class="space-y-4">
            <a href="{{ route('tenant.profile') }}" class="block w-full px-6 py-3 bg-[#4A90E2] text-white rounded-lg font-semibold hover:bg-[#3a7bc8] transition-colors">
                ไปที่โปรไฟล์
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    ออกจากระบบ
                </button>
            </form>
        </div>
    </div>
</body>
</html>
