<?php
// register.php
session_start();

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4',
        'root',
        '',
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('DB error');
}

// 1) รับค่าจากฟอร์ม
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$pass     = $_POST['password'] ?? '';
$pass2    = $_POST['password_confirm'] ?? '';

// 2) ตรวจสอบฝั่งเซิร์ฟเวอร์
$errors = [];

if ($username === '' || $email === '' || $pass === '' || $pass2 === '') {
    $errors[] = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
}
if ($pass !== $pass2) {
    $errors[] = 'ยืนยันรหัสผ่านไม่ตรงกัน';
}
if (strlen($pass) < 8) {
    $errors[] = 'รหัสผ่านควรมีอย่างน้อย 8 ตัวอักษร';
}

if ($errors) {
    // แสดงสั้นๆ (คุณจะ render กลับหน้าเดิมก็ได้)
    echo implode('<br>', $errors);
    exit;
}

// 3) เช็คซ้ำ username / email
$stmt = $pdo->prepare("SELECT 1 FROM authentication WHERE username = ? OR email = ? LIMIT 1");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    exit('มี Username หรือ Email นี้ในระบบแล้ว');
}

// 4) แฮชรหัสผ่านแล้วบันทึก
$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO authentication (username, email, password_hash) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $hash]);

// 5) สมัครสำเร็จ -> จะให้ล็อกอินอัตโนมัติหรือพาไปหน้า login ก็ได้
// ตัวอย่าง: ล็อกอินทันที
$_SESSION['username'] = $username;
header('Location: /BookingBordgame/login/dashboard.php');
exit;
