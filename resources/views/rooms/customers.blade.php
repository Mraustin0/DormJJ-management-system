<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ข้อมูลลูกค้า - JJ Apartment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-x-hidden">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity duration-300 md:hidden" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col border-r border-gray-100">
        <div class="p-8 pb-4">
            <h1 class="text-[#4A90E2] font-bold text-2xl leading-tight tracking-wide">
                DORMITORY<br>MANAGEMENT<br>SYSTEM
            </h1>
        </div>
        <nav class="flex-1 px-6 space-y-6 overflow-y-auto py-4">
            <a href="{{ route('rooms.index') }}" class="block px-6 py-3 text-gray-800 text-lg font-bold rounded-lg text-center hover:bg-gray-100 transition-colors">
                หน้าหลัก
            </a>
            <div>
                <h3 class="text-gray-500 font-bold text-sm mb-3">จัดการข้อมูลหลัก</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('meters.water') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์น้ำ</a></li>
                    <li><a href="{{ route('meters.electric') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">บันทึกมิเตอร์ไฟฟ้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบิล</a></li>
                    <li><a href="{{ route('rooms.accommodation') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ข้อมูลการเข้าพัก</a></li>
                    <li><a href="{{ route('rooms.bills') }}" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ประวัติบิล</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">USER</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('rooms.customers') }}" class="block px-2 py-2 bg-[#A0A0A0] text-white rounded-lg font-medium text-lg shadow-sm">ข้อมูลลูกค้า</a></li>
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">สร้างบัญชีผู้ใช้</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-gray-500 font-bold text-sm uppercase mb-3">SETTING</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2]">ตั้งค่าระบบ</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block px-2 py-1.5 text-gray-800 font-medium text-lg hover:text-[#4A90E2] w-full text-left">ออกจากระบบ</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    <div id="mainContent" class="md:ml-72 min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out">
        
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="text-2xl font-bold text-[#4A90E2]">ข้อมูลลูกค้า</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->username ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-500">ผู้ดูแลระบบ</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#4A90E2] flex items-center justify-center text-white font-bold shadow-md">AD</div>
            </div>
        </nav>

        <main class="p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 min-h-[80vh]">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <h3 class="text-2xl font-bold text-gray-800">ข้อมูลลูกค้า</h3>

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
                                    <button onclick="openEditModal({{ json_encode($c) }})"
                                            class="text-gray-400 hover:text-[#4A90E2] transition-colors p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
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

    <div id="editModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform duration-200" id="modalPanel">
            
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">แก้ไขข้อมูลลูกค้า</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ชื่อ-สกุล</label>
                            <input type="text" id="editName" name="tenant_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">เลขบัตรประชาชน</label>
                            <input type="text" id="editNid" name="nid" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">เบอร์โทรศัพท์</label>
                            <input type="text" id="editPhone" name="phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">อีเมล</label>
                            <input type="email" id="editEmail" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#4A90E2] outline-none">
                        </div>
                    </div>

                    <div class="pt-6 text-center">
                        <button type="button" onclick="saveCustomer()" class="bg-[#4A90E2] hover:bg-[#357abd] text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition-colors w-full">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let sidebarOpen = window.innerWidth >= 768;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');

            sidebarOpen = !sidebarOpen;

            if (sidebarOpen) {
                if (window.innerWidth >= 768) {
                    sidebar.classList.add('md:translate-x-0');
                    if (mainContent) mainContent.classList.add('md:ml-72');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        requestAnimationFrame(() => overlay.classList.remove('opacity-0'));
                    }
                }
            } else {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('md:translate-x-0');
                    if (mainContent) mainContent.classList.remove('md:ml-72');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    if (overlay) {
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    }
                }
            }
        }

        // Modal Logic
        function openEditModal(customer) {
            const modal = document.getElementById('editModal');
            const panel = document.getElementById('modalPanel');
            
            // Fill Data
            document.getElementById('editId').value = customer.id;
            document.getElementById('editName').value = customer.tenant_name;
            document.getElementById('editNid').value = customer.nid || '';
            document.getElementById('editPhone').value = customer.phone || '';
            document.getElementById('editEmail').value = customer.email || '';

            // Show Modal
            modal.classList.remove('hidden');
            setTimeout(() => { panel.classList.remove('scale-95'); panel.classList.add('scale-100'); }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('editModal');
            const panel = document.getElementById('modalPanel');
            panel.classList.remove('scale-100'); panel.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 200);
        }

        // Save Function
        function saveCustomer() {
            const formData = new FormData(document.getElementById('editForm'));

            fetch("{{ route('customers.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'บันทึกสำเร็จ!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', 'เกิดข้อผิดพลาด', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'เชื่อมต่อ Server ไม่ได้', 'error');
            });
        }
    </script>
</body>
</html>