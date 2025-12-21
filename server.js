const express = require('express');
const app = express();
const path = require('path');
require('dotenv').config();

// 1. ตั้งค่า View Engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// 2. เปิด public
app.use(express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// --- ROUTES ---

// หน้าแรก (Dashboard)
app.get('/', (req, res) => {
    res.render('dashboard');
});

app.get('/rooms', (req, res) => {
    // Config หอพัก (4 ชั้น, รวม 45 ห้อง)
    const rooms = [];
    const roomsPerFloor = [11, 12, 12, 10]; // ชั้น 1=11ห้อง, ชั้น 2-3=12ห้อง, ชั้น 4=10ห้อง
    
    // ห้องพัดลมมีแค่ 101, 102
    const fanRoomNumbers = ['101', '102']; 

    // วนลูปสร้างข้อมูลทีละชั้น
    roomsPerFloor.forEach((count, index) => {
        const floor = index + 1;
        for (let i = 1; i <= count; i++) {
            // สร้างเลขห้อง เช่น "101", "212"
            const roomNum = `${floor}${i.toString().padStart(2, '0')}`;
            
            // เช็คว่าเป็นห้องพัดลมไหม?
            const isFan = fanRoomNumbers.includes(roomNum);
            
            // ยัดข้อมูลเข้า Array
            rooms.push({
                number: roomNum,
                floor: floor,
                building: 'A', 
                type: isFan ? 'Standard (พัดลม)' : 'VIP (แอร์)',
                price: isFan ? 3500 : 4500,
                // สุ่มสถานะ: ถ้า random ได้มากกว่า 0.7 ให้มีคนอยู่ (occupied)
                status: Math.random() > 0.7 ? 'occupied' : 'available' 
            });
        }
    });
    // ส่งข้อมูลไปที่หน้า rooms/index.ejs
    res.render('rooms/index', { rooms: rooms });
});

// หน้าฟอร์มเพิ่มห้อง (เอาไว้กดเล่น)
app.get('/rooms/create', (req, res) => {
    res.render('rooms/create');
});

// ----------------

// Start Server
const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});