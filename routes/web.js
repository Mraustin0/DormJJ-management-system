const express = require('express');
const router = express.Router();
const roomController = require('../controllers/roomController');

// หน้าแรกเด้งไปหน้าห้องพักเลย
router.get('/', (req, res) => res.redirect('/rooms'));

// เส้นทางสำหรับดูรายชื่อห้อง
router.get('/rooms', roomController.index);

module.exports = router;