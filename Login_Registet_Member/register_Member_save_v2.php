<?php
// register_v2.php   บันทึกลงฐานข้อมูล member
require __DIR__ . '/../Connect/db.php';

function sendHtml($state_form, $title, $message) {
    // ฟังก์ชันนี้ใช้เฉพาะตอนสมัครไม่สำเร็จ
    $color = $state_form ? '#0f766e' : '#dc2626';
    echo "<!doctype html><html lang='th'>
    <meta charset='utf-8'><body style='font-family:Kanit, sans-serif;padding:24px'>
        <h2 style='color:$color'>$title</h2><p>$message</p>
        <p><a href='Login_Register.php'>« กลับหน้าแรก</a></p>
    </body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location:/Login_Register.php');
    exit;
}

// 1) รับข้อมูลจากฟอร์ม
$username   = trim($_POST['auid']             ?? '');
$firstName  = trim($_POST['FullnameMember']   ?? '');
$lastName   = trim($_POST['LastNameMember']   ?? '');
$email      = trim($_POST['MemberEmail']      ?? '');
$personId   = preg_replace('/\D+/', '', $_POST['person_ID_Member'] ?? '');
$gender     = $_POST['genderMember']          ?? '';
$birthday   = $_POST['BrithDayMemeber']       ?? '';
$phone      = trim($_POST['MemberPhone']      ?? '');
$faculty    = trim($_POST['faculty']          ?? '');
$password   = $_POST['password1']             ?? '';
$confirm    = $_POST['confirmPassword']       ?? '';

// 2) ตรวจสอบความถูกต้องเบื้องต้น
$errors = [];
if ($username === '')                           $errors[] = 'กรุณากรอก Username';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
if (!preg_match('/^\d{13}$/', $personId))       $errors[] = 'เลขบัตรประชาชนต้องมี 13 หลัก';
if (!in_array($gender, ['male', 'female'], true)) $errors[] = 'กรุณาเลือกเพศ';
if ($birthday === '')                           $errors[] = 'กรุณากรอกวันเกิด';
if ($password === '')                           $errors[] = 'กรุณากรอกรหัสผ่าน';
if (strlen($password) < 8)                      $errors[] = 'รหัสผ่านควรมีอย่างน้อย 8 ตัวอักษร';
if ($password !== $confirm)                     $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
if ($firstName === '' || $lastName === '')      $errors[] = 'กรุณากรอกชื่อและนามสกุล';

if ($errors) {
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'พบข้อผิดพลาด:<br>- ' . implode('<br>- ', $errors));
}

try {
    // 3) ตรวจซ้ำ Username และ personId
    $stmt = $mysqli->prepare("SELECT 1 FROM authentication WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'Username นี้ถูกใช้แล้ว กรุณาเปลี่ยนใหม่');
    }
    $stmt->close();

    $stmt = $mysqli->prepare("SELECT 1 FROM member WHERE personId = ?");
    $stmt->bind_param("s", $personId);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'เลขบัตรประชาชนนี้มีในระบบแล้ว');
    }
    $stmt->close();

    // 4) เริ่ม Transaction
    $mysqli->begin_transaction();

    // 4.1 insert authentication (role = member)
    $stmt = $mysqli->prepare("
        INSERT INTO authentication (username, password_hash, email, role)
        VALUES (?, ?, ?, 'member')
    ");
    $stmt->bind_param("sss", $username, $password, $email);
    $stmt->execute();
    $stmt->close();

    // 4.2 ใช้ auid ที่เพิ่งสร้าง
    $auid = $mysqli->insert_id;

    // 4.3 insert member
    $stmt = $mysqli->prepare("
        INSERT INTO member (personId, mname, mlastname, mgender, mbrithday, mphone, mfaculty, auid)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssssssi", $personId, $firstName, $lastName, $gender, $birthday, $phone, $faculty, $auid);
    $stmt->execute();
    $stmt->close();

    // 4.4 commit
    $mysqli->commit();

    // ✅ แจ้งเตือนแล้วกลับไปหน้า index.html
    echo "<script>
        alert('สมัครสมาชิกสำเร็จ! คุณสามารถเข้าสู่ระบบได้แล้ว');
        window.location.href = '../../BookingBordgame/Login_Registet_Member/Login_Register.php';
    </script>";
    exit;

} catch (Throwable $e) {
    try {
        $mysqli->rollback();
    } catch (Throwable $__) {
    }
    $msg = 'เกิดข้อผิดพลาดไม่คาดคิด: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', $msg);
<<<<<<< HEAD
}
=======
}
>>>>>>> merge
