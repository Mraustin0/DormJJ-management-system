<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตั้งค่าระบบ - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        #map { height: 350px; width: 100%; border-radius: 1rem; }
        .leaflet-control-attribution { display: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'settings'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'ตั้งค่าระบบ'])

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
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200">
                                <div id="map" class="shadow-lg"></div>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="text-xs text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#4A90E2]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        คลิกบนแผนที่หรือลากหมุดเพื่อเปลี่ยนตำแหน่ง
                                    </p>
                                    <div class="text-xs text-gray-400">
                                        <span id="coordDisplay">{{ $setting->latitude ?? '16.4419' }}, {{ $setting->longitude ?? '102.8360' }}</span>
                                    </div>
                                </div>
                            </div>
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

        const map = L.map('map').setView([lat, lng], 16);

        // ใช้ CartoDB Voyager สวยกว่า OSM ปกติ
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);

        // Custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background: #4A90E2; width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.3);"></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        let marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);

        function updateCoordDisplay(latitude, longitude) {
            document.getElementById('coordDisplay').textContent = latitude.toFixed(6) + ', ' + longitude.toFixed(6);
        }

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(7);
            document.getElementById('longitude').value = position.lng.toFixed(7);
            updateCoordDisplay(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
            updateCoordDisplay(e.latlng.lat, e.latlng.lng);
        });

        // Fix map freeze when sidebar is toggled
        window.addEventListener('sidebarToggled', function() {
            map.invalidateSize();
        });
    </script>
</body>
</html>
