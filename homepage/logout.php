<?php
// 1. เริ่มต้นเซสชัน (จำเป็นต้องทำก่อนเสมอเพื่อเข้าถึงเซสชันปัจจุบัน)
session_start();

// 2. ล้างข้อมูลทั้งหมดใน $_SESSION
$_SESSION = array();

// 3. ทำลายคุกกี้เซสชัน (Best Practice)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, // ตั้งเวลาในอดีต
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. ทำลายเซสชันบนเซิร์ฟเวอร์
session_destroy();

// 5. เปลี่ยนเส้นทาง (Redirect) กลับไปยังหน้าล็อกอิน
//  แก้ไข 'login.php' หากหน้าล็อกอินของคุณชื่ออื่น 
header("Location: homepage.php"); // อ้างอิงจาก path ที่คุณ comment ไว้
exit;
?>