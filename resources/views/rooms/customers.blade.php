<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดการบัญชี - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>    
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    @include('partials.sidebar', ['activePage' => 'customers'])

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        @include('partials.navbar', ['pageTitle' => 'ข้อมูลลูกค้า'])

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <h3 class="text-2xl font-bold text-gray-800">ประวัติบัญชีผู้ใช้</h3>

                    <form action="{{ route('rooms.customers') }}" method="GET" class="relative w-full md:w-auto">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาข้อมูล" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-[#4A90E2] w-full md:w-64">
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="py-4 px-4 font-bold text-gray-700">#</th>
                                <th class="py-4 px-4 font-bold text-gray-700">ชื่อ สกุล</th>
                                <th class="py-4 px-4 font-bold text-gray-700">เลขบัตรประชาชน</th>
                                <th class="py-4 px-4 font-bold text-gray-700">เบอร์โทร</th>
                                <th class="py-4 px-4 font-bold text-gray-700">อีเมล</th>
                                <th class="py-4 px-4 font-bold text-gray-700">สถานะ</th>
                                <th class="py-4 px-4 font-bold text-gray-700 text-center">แก้ไข</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($customers as $index => $c)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 font-medium text-gray-700">A{{ str_pad($customers->firstItem() + $index, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-4 px-4 text-gray-800">{{ $c->tenant_name }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $c->nid ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-600">{{ $c->phone ?? '-' }}</td>
                                <td class="py-4 px-4 text-gray-500">{{ $c->email ?? '-' }}</td>

                                <td class="py-4 px-4">
                                    @if($c->room && $c->room->status !== 'ว่าง')
                                        <span class="text-[#56ab91] font-bold">ห้อง {{ $c->room->room_number }}</span>
                                    @else
                                        <span class="text-red-400 font-bold bg-red-50 px-3 py-1 rounded-full text-xs">ย้ายออก</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('rooms.edit', $c->room_id) }}"
                                       class="text-gray-400 hover:text-[#4A90E2] transition-colors p-1 inline-block">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">ไม่พบข้อมูลลูกค้า</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                <div class="mt-6 flex justify-end items-center gap-1">
                    @if($customers->onFirstPage())
                        <span class="w-8 h-8 flex items-center justify-center text-gray-300 text-sm">&lt;</span>
                    @else
                        <a href="{{ $customers->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded text-sm">&lt;</a>
                    @endif

                    @foreach($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                        @if($page == $customers->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center border border-gray-800 rounded text-sm font-bold text-gray-800">{{ $page }}</span>
                        @elseif($page <= 4 || $page == $customers->lastPage())
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded text-sm">{{ $page }}</a>
                        @elseif($page == 5)
                            <span class="w-8 h-8 flex items-center justify-center text-gray-400 text-sm">...</span>
                        @endif
                    @endforeach

                    @if($customers->hasMorePages())
                        <a href="{{ $customers->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded text-sm">&gt;</a>
                    @else
                        <span class="w-8 h-8 flex items-center justify-center text-gray-300 text-sm">&gt;</span>
                    @endif
                </div>
                @endif

            </div>
        </main>
    </div>

    <script>
        // Page ready - no modal needed, edit goes to separate page
    </script>
</body>
</html>