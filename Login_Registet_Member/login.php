<?php
// login.php
session_start();

function fail() {
    $_SESSION['auth_error'] = 'ไอดีผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    header('Location: Login_Register.php#login'); exit;
}

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
if ($auid === '') {
    $errors['auid'] = 'กรุณากรอกไอดีผู้ใช้';
}
if ($password === '') {
    $errors['password'] = 'กรุณากรอกรหัสผ่าน';
}

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
// $stmt = $mysqli->prepare("
//     SELECT auid, username, password_hash, role
//     FROM authentication
//     WHERE username = ?
//     LIMIT 1
// ");

$sqlquery = $mysqli->prepare("SELECT authentication.auid, authentication.username, authentication.password_hash, authentication.role,member.personid,
        member.mname       AS first_name,
        member.mlastname   AS last_name,
        CONCAT(member.mname,' ',member.mlastname) AS full_name,
        member.mgender, member.mbrithday, member.mphone, member.mfaculty
        FROM authentication 
        INNER JOIN member 
        ON member.auid = authentication.auid
        WHERE authentication.username = ?;
        ");

$sqlquery->bind_param('s', $auid);
$sqlquery->execute();
$user = $sqlquery->get_result()->fetch_assoc();
// $sqlquery->close();


// $stmt->bind_param('s', $auid);
// $stmt->execute();
// $res  = $stmt->get_result();
// $user = $res->fetch_assoc();
// $stmt->close();




// 2) ไม่พบชื่อผู้ใช้
// if (!$user) {
//     fail(); // ตั้ง session error แล้ว redirect
// }

// 3) ตรวจรหัสผ่านจริง ๆ
if (!$user || !(($user['password_hash'])== $password)) {
    fail(); // ตั้ง session error แล้ว redirect
}


//ดึง sesion ไปหน้า homepage
session_regenerate_id(true);
$_SESSION['auid']     = $user['auid'];
$_SESSION['personid']   = $user['personid'];
$_SESSION['display_name'] = $user['full_name'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'] ?? 'member';

header('Location:../homepage/booking/homepage.php');



// ตรวจสอบรหัสผ่าน
// if (!$user['username'] || !$user['password_hash']) {
//     $_SESSION['auth_error'] = 'ไอดีผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
//     $_SESSION['old'] = ['auid' => $auid];
//     header('Location: Login_Register.php#login');
//     exit;
// }



// สำเร็จ: สร้างเซสชันแล้วไป dashboard
// session_regenerate_id(true);
// $_SESSION['auid']     = $user['auid'];
// $_SESSION['display_name'] = $user['mname'];
// $_SESSION['username'] = $user['username'];
// $_SESSION['role']     = $user['role'] ?? 'member';


// (ทางเลือก) บันทึกเวลาล็อกอินล่าสุด
// try {
//     $u = $mysqli->prepare("UPDATE authentication SET last_login = NOW() WHERE auid = ?");
//     $u->bind_param('i', $user['auid']);
//     $u->execute();
//     $u->close();
// } catch (Throwable $__) {
// }

// header('Location:../homepage/homepage.php');
// header('Location:dashboard.php');
// exit;
