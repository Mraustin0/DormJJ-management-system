<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลมิเตอร์น้ำ/ไฟ - ผู้เช่า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('tenant.partials.sidebar', ['activePage' => 'meters'])

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
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">ข้อมูลมิเตอร์น้ำ/ไฟ</h2>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Electric Usage Chart --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">การใช้ไฟฟ้า ทั้งหมด</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span>
                        <span class="text-xs text-gray-500">จำนวนหน่วยไฟฟ้าที่ใช้(หน่วย)</span>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="electricChart"></canvas>
                    </div>
                </div>

                {{-- Water Usage Chart --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">การใช้น้ำ ทั้งหมด</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block w-3 h-3 rounded-sm bg-blue-500"></span>
                        <span class="text-xs text-gray-500">จำนวนหน่วยที่ใช้น้ำ(หน่วย)</span>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="waterChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Cost Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Electric Cost Chart --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span>
                        <span class="text-xs text-gray-500">จำนวนเงินค่าไฟฟ้า(บาท)</span>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="electricCostChart"></canvas>
                    </div>
                </div>

                {{-- Water Cost Chart --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block w-3 h-3 rounded-sm bg-red-500"></span>
                        <span class="text-xs text-gray-500">จำนวนเงินค่าน้ำ(บาท)</span>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="waterCostChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Electric Stats --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-600">สูงสุด</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $electricStats['max'] }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-600">ต่ำสุด</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $electricStats['min'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">เฉลี่ย</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $electricStats['avg'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Water Stats --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <table class="w-full text-sm">
                        <tbody>
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-600">สูงสุด</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $waterStats['max'] }}</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-600">ต่ำสุด</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $waterStats['min'] }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-600">เฉลี่ย</td>
                                <td class="py-2 text-right font-bold text-gray-800">{{ $waterStats['avg'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const labels = @json($chartLabels);
        const electricUnits = @json($electricUnits);
        const waterUnits = @json($waterUnits);
        const electricCosts = @json($electricCosts);
        const waterCosts = @json($waterCosts);

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Sarabun', size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { family: 'Sarabun', size: 11 } }
                }
            }
        };

        // Electric Usage Chart
        new Chart(document.getElementById('electricChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: electricUnits,
                    backgroundColor: '#8B5CF6',
                    borderRadius: 4,
                    maxBarThickness: 40,
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    datalabels: { display: false }
                }
            }
        });

        // Water Usage Chart
        new Chart(document.getElementById('waterChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: waterUnits,
                    backgroundColor: '#3B82F6',
                    borderRadius: 4,
                    maxBarThickness: 40,
                }]
            },
            options: commonOptions
        });

        // Electric Cost Chart
        new Chart(document.getElementById('electricCostChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: electricCosts,
                    backgroundColor: '#22C55E',
                    borderRadius: 4,
                    maxBarThickness: 40,
                }]
            },
            options: commonOptions
        });

        // Water Cost Chart
        new Chart(document.getElementById('waterCostChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: waterCosts,
                    backgroundColor: '#EF4444',
                    borderRadius: 4,
                    maxBarThickness: 40,
                }]
            },
            options: commonOptions
        });
    </script>
</body>
</html>
