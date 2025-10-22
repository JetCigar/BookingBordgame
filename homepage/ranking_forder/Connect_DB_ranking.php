<?php
// --- Connect/db.php ---

// ข้อมูลสำหรับเชื่อมต่อฐานข้อมูล XAMPP
define('DB_HOST', 'localhost');  // หรือ 'localhost'
define('DB_USER', 'root');         // user เริ่มต้นของ XAMPP
define('DB_PASS', '');             // password เริ่มต้นของ XAMPP (ค่าว่าง)
define('DB_NAME', 'bookingbordgame'); // ชื่อฐานข้อมูลของคุณ

// 1. ทำการเชื่อมต่อ
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 2. ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// 3. ตั้งค่า Charset เป็น utf8mb4 เพื่อรองรับภาษาไทย
if (!$conn->set_charset("utf8mb4")) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}

// ถ้าเชื่อมต่อสำเร็จ ตัวแปร $conn จะพร้อมใช้งานในไฟล์ที่เรียกใช้
?>