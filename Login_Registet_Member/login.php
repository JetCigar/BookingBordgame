<?php
// login.php
session_start();

// ไม่อนุญาตให้เข้าด้วย GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Login_Register.php?error=badmethod#login');
    exit;
}

// รับค่าจากฟอร์ม
$auid     = trim($_POST['auid'] ?? '');
$password = $_POST['password'] ?? '';

// ตรวจสอบค่าว่าง (ฝั่งเซิร์ฟเวอร์)
$errors = [];
if ($auid === '')     { $errors['auid'] = 'กรุณากรอกไอดีผู้ใช้'; }
if ($password === '') { $errors['password'] = 'กรุณากรอกรหัสผ่าน'; }

if ($errors) {
    // เก็บ error ไว้ชั่วคราวใน session แล้วส่งกลับไปหน้าเดิม
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old'] = ['auid' => $auid];
    header('Location: Login_Register.php#login');
    exit;
}

// เชื่อมต่อฐานข้อมูล (ใช้ db.php ที่มี $mysqli)
require __DIR__ . '/../Connect/db.php'; // แก้ path ตามโครงสร้างของคุณ

// ค้นหาผู้ใช้ (ในตาราง authentication คอลัมน์ username เก็บค่าจากฟิลด์ auid ตอนสมัคร)
$stmt = $mysqli->prepare("
    SELECT auid, username, password_hash, role
    FROM authentication
    WHERE username = ?
    LIMIT 1
");
$stmt->bind_param('s', $auid);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// ตรวจสอบรหัสผ่าน
if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['auth_error'] = 'ไอดีผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    $_SESSION['old'] = ['auid' => $auid];
    header('Location: Login_Register.php#login');
    exit;
}

// สำเร็จ: สร้างเซสชันแล้วไป dashboard
session_regenerate_id(true);
$_SESSION['auid']     = $user['auid'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'] ?? 'member';

// (ทางเลือก) บันทึกเวลาล็อกอินล่าสุด
try {
    $u = $mysqli->prepare("UPDATE authentication SET last_login = NOW() WHERE auid = ?");
    $u->bind_param('i', $user['auid']);
    $u->execute();
    $u->close();
} catch (Throwable $__) {}

header('Location:dashboard.php');
exit;
