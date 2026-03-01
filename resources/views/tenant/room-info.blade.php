<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลหอพัก - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>body { font-family: 'Sarabun', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'room-info'])

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
        <div class="p-6 sm:p-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">ข้อมูลหอพัก</h2>

            {{-- ข้อมูลค่าเช่า --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-800 mb-5 ml-6">ข้อมูลค่าเช่า</h3>
                <div class="space-y-4 ml-12">
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">ค่าเช่า/เดือน:</span>
                        <span class="text-gray-800 font-medium">{{ number_format($setting->rent_per_month ?? 4000, 0, '.', ',') }} บาท</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">ค่าน้ำ/หน่วย:</span>
                        <span class="text-gray-800 font-medium">{{ number_format($setting->water_rate ?? 25, 0) }} บาท</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">ค่าไฟ/หน่วย:</span>
                        <span class="text-gray-800 font-medium">{{ number_format($setting->electric_rate ?? 8, 0) }} บาท</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">ค่าปรับจ่ายล่าช้า/วัน</span>
                        <span class="text-gray-800 font-medium">{{ number_format($setting->late_fee_per_day ?? 50, 0) }} บาท</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">กำหนดจ่ายค่าเช่า</span>
                        <span class="text-gray-800 font-medium">ทุก ๆ วันที่ {{ $setting->payment_due_day ?? '5' }} ของเดือน</span>
                    </div>
                </div>
            </div>

            {{-- ตั้งค่าข้อมูลหอพัก --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-800 mb-5">ตั้งค่าข้อมูลหอพัก</h3>
                <div class="space-y-4 ml-6">
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">ชื่อหอพัก:</span>
                        <span class="text-gray-800 font-medium">{{ $setting->apartment_name ?? 'JJ Apartment' }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-gray-500 w-48 flex-shrink-0">ที่อยู่:</span>
                        <div class="flex items-start gap-2">
                            <span class="text-gray-800 font-medium" id="addressText">{{ $fullAddress }}</span>
                            <button onclick="copyAddress()" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 mt-0.5" title="คัดลอกที่อยู่">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">อีเมล:</span>
                        <span class="text-gray-800 font-medium">{{ $setting->email ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-500 w-48 flex-shrink-0">เบอร์</span>
                        <span class="text-gray-800 font-medium">{{ $setting->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- ระบุตำแหน่งหอพักของคุณ --}}
            <div class="mb-10">
                <h3 class="text-lg font-bold text-gray-800 mb-5">ระบุตำแหน่งหอพักของคุณ</h3>
                <div id="map" class="rounded-lg border border-gray-200 overflow-hidden" style="height: 350px; max-width: 600px;"></div>
            </div>
        </div>
    </main>

    <script>
        // Copy address
        function copyAddress() {
            const text = document.getElementById('addressText').innerText;
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'คัดลอกที่อยู่แล้ว',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        // Leaflet Map
        document.addEventListener('DOMContentLoaded', function() {
            const lat = {{ $setting->latitude ?? 16.0544 }};
            const lng = {{ $setting->longitude ?? 103.6520 }};

            const map = L.map('map', {
                scrollWheelZoom: true,
                zoomControl: true
            }).setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const blueIcon = L.divIcon({
                html: '<div style="width:16px;height:16px;background:#3B82F6;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8],
                className: ''
            });

            L.marker([lat, lng], { icon: blueIcon }).addTo(map);

            setTimeout(() => map.invalidateSize(), 200);
        });
    </script>
</body>
</html>
