exports.index = (req, res) => {
    // สมมติว่านี่คือข้อมูลที่ดึงมาจาก Database (แต่จริงๆ เราพิมพ์เอง)
    const mockRooms = [
        { id: 1, number: '101', price: 3500, status: 'available', type: 'Standard' },
        { id: 2, number: '102', price: 3500, status: 'occupied', tenant: 'สมชาย ใจดี', type: 'Standard' },
        { id: 3, number: '103', price: 4000, status: 'maintenance', type: 'Corner' },
        { id: 4, number: '201', price: 3500, status: 'available', type: 'Standard' },
    ];

    // ส่งข้อมูล mockRooms ไปที่หน้า View ชื่อ 'rooms/index'
    res.render('rooms/index', { rooms: mockRooms });
};