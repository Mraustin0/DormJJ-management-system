<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยัน OTP - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s;
        }
        .otp-input:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        .otp-input.filled {
            border-color: #4A90E2;
            background-color: #f0f7ff;
        }
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
            <h2 class="text-sm font-bold tracking-wider text-gray-800 mb-4 uppercase">Confirm OTP</h2>
            <p class="text-sm text-gray-500 mb-8">กรุณากรอกรหัส OTP 6 หลักที่ส่งไปยังอีเมลของคุณ</p>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.otp.verify') }}" method="POST" id="otpForm">
                @csrf

                {{-- Hidden input for actual OTP value --}}
                <input type="hidden" name="otp" id="otpValue">

                {{-- OTP Input Boxes --}}
                <div class="flex justify-center gap-3 mb-8">
                    <input type="text" maxlength="1" class="otp-input" data-index="0" inputmode="numeric" pattern="[0-9]">
                    <input type="text" maxlength="1" class="otp-input" data-index="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" maxlength="1" class="otp-input" data-index="2" inputmode="numeric" pattern="[0-9]">
                    <input type="text" maxlength="1" class="otp-input" data-index="3" inputmode="numeric" pattern="[0-9]">
                    <input type="text" maxlength="1" class="otp-input" data-index="4" inputmode="numeric" pattern="[0-9]">
                    <input type="text" maxlength="1" class="otp-input" data-index="5" inputmode="numeric" pattern="[0-9]">
                </div>

                <div>
                    <button type="submit"
                            class="w-full bg-black text-white text-sm font-bold px-6 py-3 rounded-full transition-all duration-300 uppercase tracking-wider hover:bg-gray-800 active:scale-95">
                        ยืนยัน
                    </button>
                </div>
            </form>

            {{-- Resend OTP Form (แยกออกมาจาก form หลัก) --}}
            <div class="mt-6">
                <form action="{{ route('password.otp.resend') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[#ef4444] text-sm hover:underline">
                        Resend code
                    </button>
                </form>
            </div>

            <div class="text-center pt-4">
                <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-[#4A90E2]">
                    กลับไปกรอกอีเมลใหม่
                </a>
            </div>
        </div>
    </main>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const otpValue = document.getElementById('otpValue');
        const form = document.getElementById('otpForm');

        // Auto-focus first input
        inputs[0].focus();

        inputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', (e) => {
                const value = e.target.value;

                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Mark as filled
                if (value) {
                    input.classList.add('filled');
                    // Move to next input
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    input.classList.remove('filled');
                }

                updateOtpValue();
            });

            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    inputs[index - 1].classList.remove('filled');
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);

                pastedData.split('').forEach((char, i) => {
                    if (inputs[i]) {
                        inputs[i].value = char;
                        inputs[i].classList.add('filled');
                    }
                });

                // Focus on next empty input or last input
                const nextEmpty = Array.from(inputs).findIndex(inp => !inp.value);
                if (nextEmpty !== -1) {
                    inputs[nextEmpty].focus();
                } else {
                    inputs[inputs.length - 1].focus();
                }

                updateOtpValue();
            });
        });

        function updateOtpValue() {
            otpValue.value = Array.from(inputs).map(i => i.value).join('');
        }

        // Update OTP value before submit
        form.addEventListener('submit', (e) => {
            updateOtpValue();
            if (otpValue.value.length !== 6) {
                e.preventDefault();
                alert('กรุณากรอกรหัส OTP ให้ครบ 6 หลัก');
            }
        });
    </script>

</body>
</html>
