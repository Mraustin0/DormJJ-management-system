const express = require('express');
const app = express();
const path = require('path');
require('dotenv').config();

// ตั้งค่า View Engine เป็น EJS
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// เปิดโฟลเดอร์ public ให้คนเข้าถึงได้ (CSS, Images)
app.use(express.static(path.join(__dirname, 'public')));

// รับค่าจาก Form (POST request)
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// ใน server.js เพิ่มบรรทัดนี้ต่อจาก app.use(express.json());
const webRoutes = require('./routes/web');
app.use('/', webRoutes);

// Routes (เดี๋ยวเราค่อยมาเติม)
app.get('/', (req, res) => {
    res.send('<h1>System is Ready! 🚀</h1><p>รอเชื่อมต่อกับ Database และ Views</p>');
});

// Start Server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});