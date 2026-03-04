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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="p-6 sm:p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">ข้อมูลหอพัก</h2>

            {{-- ===== ค่าเช่า Grid ===== --}}
            <div class="mb-8">
                <h3 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-red-500 rounded-full inline-block"></span>
                    ข้อมูลค่าเช่า
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    {{-- ค่าเช่า --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center gap-1">
                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mb-1">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">ค่าเช่า/เดือน</p>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($setting->rent_per_month ?? 4000, 0) }}</p>
                        <p class="text-xs text-gray-400">บาท</p>
                    </div>

                    {{-- ค่าน้ำ --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center gap-1">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-1">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">ค่าน้ำ/หน่วย</p>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($setting->water_rate ?? 25, 0) }}</p>
                        <p class="text-xs text-gray-400">บาท</p>
                    </div>

                    {{-- ค่าไฟ --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center gap-1">
                        <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center mb-1">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">ค่าไฟ/หน่วย</p>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($setting->electric_rate ?? 8, 0) }}</p>
                        <p class="text-xs text-gray-400">บาท</p>
                    </div>

                    {{-- ค่าปรับ --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center gap-1">
                        <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mb-1">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">ค่าปรับล่าช้า/วัน</p>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($setting->late_fee_per_day ?? 50, 0) }}</p>
                        <p class="text-xs text-gray-400">บาท</p>
                    </div>

                    {{-- กำหนดชำระ --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center gap-1">
                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center mb-1">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">กำหนดชำระทุกวันที่</p>
                        <p class="text-lg font-bold text-gray-800">{{ $setting->payment_due_day ?? '5' }}</p>
                        <p class="text-xs text-gray-400">ของเดือน</p>
                    </div>
                </div>
            </div>

            {{-- ===== ข้อมูลหอพัก Grid ===== --}}
            <div class="mb-8">
                <h3 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-red-500 rounded-full inline-block"></span>
                    ข้อมูลหอพัก
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    {{-- ชื่อหอพัก --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">ชื่อหอพัก</p>
                            <p class="font-bold text-gray-800">{{ $setting->apartment_name ?? 'JJ Apartment' }}</p>
                        </div>
                    </div>

                    {{-- อีเมล --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">อีเมล</p>
                            <p class="font-bold text-gray-800">{{ $setting->email ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- เบอร์โทร --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">เบอร์โทร</p>
                            <p class="font-bold text-gray-800">{{ $setting->phone ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- ที่อยู่ --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 mb-0.5">ที่อยู่</p>
                            <div class="flex items-start gap-2">
                                <p class="font-bold text-gray-800 text-sm leading-snug" id="addressText">{{ $fullAddress }}</p>
                                <button onclick="copyAddress()" class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 mt-0.5" title="คัดลอกที่อยู่">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== แผนที่ ===== --}}
            <div class="mb-8">
                <h3 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-1 h-5 bg-red-500 rounded-full inline-block"></span>
                    ตำแหน่งหอพัก
                </h3>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" style="height: 350px;">
                    <div id="map" class="w-full h-full"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
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
                html: '<div style="width:16px;height:16px;background:#ef4444;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
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
