<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าระบบ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        #map { height: 200px; width: 100%; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'settings'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-xl font-bold text-[#4A90E2]">ระบบจัดการหอพัก JJ Apartment</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">Admin</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>
        </nav>

        <main class="p-8">
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">ข้อมูลค่าเช่า</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าเช่า/ต่อเดือน <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" name="rent_per_month" value="{{ $setting->rent_per_month }}" class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าน้ำ/หน่วย <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" name="water_rate" value="{{ $setting->water_rate }}" class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">หน่วย</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าปรับจ่ายค่าเช่าล่าช้า/วัน <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" name="late_fee_per_day" value="{{ $setting->late_fee_per_day }}" class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ค่าไฟ/หน่วย <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" name="electric_rate" value="{{ $setting->electric_rate }}" class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                                <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">หน่วย</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">กำหนดจ่ายค่าเช่า</label>
                        <div class="flex">
                            <input type="text" name="payment_due_day" value="{{ $setting->payment_due_day }}" placeholder="ทุกๆวันที่ 5 ของเดือน" class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            <span class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg px-4 py-2.5 text-gray-500">บาท</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">ตั้งค่าข้อมูลหอพัก</h3>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ชื่อหอพัก <span class="text-red-500">*</span></label>
                            <input type="text" name="apartment_name" value="{{ $setting->apartment_name }}" class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ที่อยู่หอพัก <span class="text-red-500">*</span></label>
                            <input type="text" name="address" value="{{ $setting->address }}" class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">จังหวัด</label>
                                <input type="text" name="province" value="{{ $setting->province }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">อำเภอ</label>
                                <input type="text" name="district" value="{{ $setting->district }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">แขวง/ตำบล</label>
                                <input type="text" name="subdistrict" value="{{ $setting->subdistrict }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">รหัสไปรษณีย์</label>
                                <input type="text" name="postal_code" value="{{ $setting->postal_code }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">ระบุตำแหน่งหอพักของคุณ</label>
                            <div id="map" class="border border-gray-300"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ $setting->latitude ?? '16.4419' }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ $setting->longitude ?? '102.8360' }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">เบอร์โทรติดต่อหอพัก</label>
                                <input type="text" name="phone" value="{{ $setting->phone }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">อีเมลติดต่อหอพัก</label>
                                <input type="email" name="email" value="{{ $setting->email }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:border-[#4A90E2] focus:ring-1 focus:ring-[#4A90E2] outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-3 px-12 rounded-lg shadow-md transition-colors">
                        บันทึก
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize Map
        const lat = parseFloat(document.getElementById('latitude').value) || 16.4419;
        const lng = parseFloat(document.getElementById('longitude').value) || 102.8360;

        const map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(7);
            document.getElementById('longitude').value = position.lng.toFixed(7);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
        });
    </script>
</body>
</html>
