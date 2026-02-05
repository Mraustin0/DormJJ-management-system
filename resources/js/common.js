let currentRoom = null;
let currentContract = null;
let sidebarOpen = true;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    sidebarOpen = !sidebarOpen;

    if (sidebarOpen) {
        sidebar.classList.remove('-translate-x-full');
        if (mainContent) mainContent.classList.add('md:ml-72');
        if (window.innerWidth < 768) {
            if (overlay) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            }
        }
    } else {
        sidebar.classList.add('-translate-x-full');
        if (mainContent) mainContent.classList.remove('md:ml-72');
        if (overlay) {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }
}

function openRoomModal(room, contract) {
    currentRoom = room;
    currentContract = contract;

    const modal = document.getElementById('roomModal');
    const modalPanel = document.getElementById('modalPanel');

    modal.classList.remove('hidden');
    setTimeout(() => {
        modalPanel.classList.remove('scale-95');
        modalPanel.classList.add('scale-100');
    }, 10);

    resetModalViews();
    document.getElementById('modalTitle').innerText = 'ข้อมูลห้อง ' + room.room_number;

    if (room.status === 'ว่าง') {
        document.getElementById('vacantView').classList.remove('hidden');
        document.getElementById('vacantView').classList.add('flex');
    } else {
        document.getElementById('occupiedView').classList.remove('hidden');
        populateTenantInfo();

        const rId = room.id;

        const btnMoveOut = document.getElementById('btnMoveOut');
        if (btnMoveOut) {
            btnMoveOut.href = "/rooms/" + rId + "/moveout";
        }

        const btnMeter = document.getElementById('btnMeter');
        if (btnMeter) {
            btnMeter.href = "/meters?search=" + room.room_number;
        }
        const btnBill = document.getElementById('btnBill');
        if (btnBill) {
            btnBill.href = "/bills?search=" + room.room_number;
        }
    }
}

function resetModalViews() {
    document.getElementById('vacantView').classList.add('hidden');
    document.getElementById('vacantView').classList.remove('flex');
    document.getElementById('occupiedView').classList.add('hidden');
    document.getElementById('editView').classList.add('hidden');
}

function populateTenantInfo() {
    if (currentContract) {
        document.getElementById('tenantName').innerText = currentContract.tenant_name || '-';
        document.getElementById('tenantId').innerText = currentContract.nid || '-';
        document.getElementById('tenantPhone').innerText = currentContract.phone || '-';
    } else {
        document.getElementById('tenantName').innerText = 'ไม่พบข้อมูล';
        document.getElementById('tenantId').innerText = '-';
        document.getElementById('tenantPhone').innerText = '-';
    }
}

function switchToEditMode(isNew) {
    resetModalViews();
    document.getElementById('editView').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'แก้ไขข้อมูล';
    document.getElementById('editRoomNumber').value = currentRoom.room_number;
    if (!isNew && currentContract) {
        document.getElementById('editName').value = currentContract.tenant_name || '';
    } else {
        document.getElementById('editName').value = '';
    }
}

function closeModal() {
    const modal = document.getElementById('roomModal');
    const modalPanel = document.getElementById('modalPanel');
    modalPanel.classList.remove('scale-100');
    modalPanel.classList.add('scale-95');
    setTimeout(() => { modal.classList.add('hidden'); }, 200);
}

document.getElementById('roomModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
