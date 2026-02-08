<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการเข้าพัก - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'accommodation'])

    <div id="mainContent" class="md:ml-72 flex-1 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'ข้อมูลการเข้าพัก'])

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-xl font-bold text-gray-800">รายชื่อผู้เข้าพัก (<span id="resultCount">{{ $contracts->total() }}</span> รายการ)</h3>
                    <div class="relative w-full md:w-auto">
                        <input type="text" id="searchInput" placeholder="ค้นหาชื่อ หรือ ห้อง..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-64">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="py-4 px-4 font-bold text-gray-600 w-16 rounded-tl-lg">#</th>
                                <th class="py-4 px-4 font-bold text-gray-600 w-24">ห้อง</th>
                                <th class="py-4 px-4 font-bold text-gray-600">ชื่อ-สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-600">วันที่เข้าพัก</th>
                                <th class="py-4 px-4 font-bold text-gray-600 rounded-tr-lg">ระยะสัญญา</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($contracts as $index => $contract)
                            @php
                                $startDate = $contract->check_in_date ? \Carbon\Carbon::parse($contract->check_in_date) : null;
                                $durationMonths = $contract->contract_duration ?? 12;
                                $duration = $durationMonths . ' เดือน';
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-gray-500">{{ $contracts->firstItem() + $index }}</td>
                                <td class="py-4 px-4 font-bold text-[#4A90E2]">{{ $contract->room->room_number ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-700 font-medium">{{ $contract->tenant_name }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $startDate ? $startDate->format('d/m/Y') : '-' }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $duration }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">ไม่พบข้อมูลผู้เข้าพัก</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $contracts->links('pagination::tailwind') }}
                </div>

            </div>
        </main>
    </div>

    <script>
        // ค้นหาแบบ Real-time
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.querySelector('tbody');
        const rows = tableBody.querySelectorAll('tr');
        const resultCount = document.getElementById('resultCount');
        const totalRows = {{ $contracts->total() }};

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                // ข้าม row ที่เป็น "ไม่พบข้อมูล"
                if (row.querySelector('td[colspan]')) return;

                const roomNumber = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const tenantName = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';

                if (roomNumber.includes(searchTerm) || tenantName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // อัปเดตจำนวนที่แสดง
            if (searchTerm === '') {
                resultCount.textContent = totalRows;
            } else {
                resultCount.textContent = visibleCount;
            }
        });

        // กด Enter เพื่อค้นหา (optional)
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>