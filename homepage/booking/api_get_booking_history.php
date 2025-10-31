<?php
// api_get_booking_history.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. ตรวจสอบว่าล็อกอินหรือยัง (เหมือนเดิม)
if (empty($_SESSION['personid'])) {
    echo json_encode(['success' => false, 'error' => 'ผู้ใช้ยังไม่ได้เข้าสู่ระบบ']);
    exit;
}

// นี่คือ ID ของผู้ใช้ที่ล็อกอินอยู่
$person_id = $_SESSION['personid'];
$response = ['success' => false, 'bookings' => []];

try {
    // 2. เชื่อมต่อฐานข้อมูล (เหมือนเดิม)
    $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. ===== SQL QUERY ที่อัปเดตใหม่ =====
    // นี่คือส่วนที่เปลี่ยนแปลงครับ
    $stmt = $pdo->prepare("
        SELECT
            bg.bgName,
            bd.tableId
        FROM 
            bookingdetail AS bd
        JOIN 
            boradgame AS bg ON bd.bgId = bg.bgid
        JOIN
            tableroom AS tr ON bd.tableId = tr.tableId
        WHERE 
            bd.personId = ? AND tr.state = 0
        ORDER BY
            bd.bkdId DESC
    ");

    // เราส่ง personId เข้าไปที่ ?
    $stmt->execute([$person_id]);
    $bookings = $stmt->fetchAll();

    // 4. ส่งข้อมูลกลับไป (เหมือนเดิม)
    $response['success'] = true;
    $response['bookings'] = $bookings;

} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด (เหมือนเดิม)
    $response['error'] = $e->getMessage();
}

// 5. ส่งข้อมูลกลับเป็น JSON ให้ JavaScript (เหมือนเดิม)
echo json_encode($response);