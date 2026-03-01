<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อม - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'repairs'])

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
                    @include('tenant.partials.notification-bell')
                    @include('tenant.partials.user-dropdown')
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="p-6 sm:p-8">
            {{-- History icon top-right --}}
            <div class="flex justify-end mb-4">
                <a href="{{ route('tenant.repairs') }}" class="text-gray-400 hover:text-gray-600 transition-colors" title="ประวัติแจ้งซ่อม">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </a>
            </div>

            <div class="max-w-lg mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">แจ้งซ่อม ห้อง {{ $contract->room->room_number }}</h2>

                {{-- Success Message (SweetAlert2) --}}
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: @json(session('success')),
                                icon: 'success',
                                confirmButtonColor: '#f87171',
                                confirmButtonText: 'ตกลง'
                            });
                        });
                    </script>
                @endif

                <form action="{{ route('tenant.repairs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- วันที่แจ้งซ่อม --}}
                    <div class="mb-5">
                        <label class="block text-gray-700 font-medium mb-1.5">วันที่แจ้งซ่อม <span class="text-red-500">*</span></label>
                        <input type="date" name="repair_date" value="{{ old('repair_date', date('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent">
                    </div>

                    {{-- ปัญหาที่แจ้งซ่อม (Category) --}}
                    <div class="mb-5">
                        <label class="block text-gray-700 font-medium mb-1.5">ปัญหาที่แจ้งซ่อม <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="category" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent appearance-none bg-white @error('category') border-red-500 @enderror">
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>กรุณาเลือกปัญหาที่พบ</option>
                                <option value="ระบบไฟฟ้า" {{ old('category') == 'ระบบไฟฟ้า' ? 'selected' : '' }}>ระบบไฟฟ้า</option>
                                <option value="ระบบประปา" {{ old('category') == 'ระบบประปา' ? 'selected' : '' }}>ระบบประปา</option>
                                <option value="ประตูและหน้าต่าง" {{ old('category') == 'ประตูและหน้าต่าง' ? 'selected' : '' }}>ประตูและหน้าต่าง</option>
                                <option value="เครื่องใช้และอุปกรณ์ในห้อง" {{ old('category') == 'เครื่องใช้และอุปกรณ์ในห้อง' ? 'selected' : '' }}>เครื่องใช้และอุปกรณ์ในห้อง</option>
                                <option value="พื้นผนังฝ้า" {{ old('category') == 'พื้นผนังฝ้า' ? 'selected' : '' }}>พื้นผนังฝ้า</option>
                                <option value="อื่นๆ" {{ old('category') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- รายละเอียด --}}
                    <div class="mb-5">
                        <label class="block text-gray-700 font-medium mb-1.5">รายละเอียด <span class="text-red-500">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" required placeholder="กรุณากรอกรายละเอียดแจ้งซ่อม"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-red-400 focus:border-transparent @error('description') border-red-500 @enderror">
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hidden title field (auto-filled from category) --}}
                    <input type="hidden" name="title" id="titleField" value="{{ old('title') }}">

                    {{-- รูปถ่ายเพิ่มเติม --}}
                    <div class="mb-8">
                        <label class="block text-gray-700 font-medium mb-1.5">รูปถ่ายเพิ่มเติม</label>
                        <div class="flex items-center gap-3">
                            <label class="flex-1 cursor-pointer">
                                <div class="flex items-center gap-3 border border-gray-300 rounded-lg px-4 py-2.5 hover:border-red-400 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span id="imageFileName" class="text-sm text-gray-500 truncate">กรุณาแนบรูปถ่าย</span>
                                </div>
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="handleImageSelect(this)">
                            </label>
                            <button type="button" onclick="this.previousElementSibling.querySelector('input[type=file]').click()" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors whitespace-nowrap">
                                อัปโหลด
                            </button>
                        </div>
                        {{-- Image preview --}}
                        <div id="imagePreview" class="hidden mt-3">
                            <img id="previewImg" src="" alt="Preview" class="max-h-48 rounded-lg border border-gray-200">
                        </div>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-center">
                        <button type="submit" class="px-10 py-2.5 bg-red-400 text-white rounded-lg font-semibold hover:bg-red-500 transition-colors">
                            ส่งรายการแจ้งซ่อม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Auto-fill title from category
        document.querySelector('select[name="category"]')?.addEventListener('change', function() {
            document.getElementById('titleField').value = this.value;
        });

        // Set title on page load if category is already selected
        const catSelect = document.querySelector('select[name="category"]');
        if (catSelect && catSelect.value) {
            document.getElementById('titleField').value = catSelect.value;
        }

        // Image upload preview
        function handleImageSelect(input) {
            if (input.files && input.files[0]) {
                document.getElementById('imageFileName').textContent = input.files[0].name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
