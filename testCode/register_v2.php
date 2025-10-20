<?php
// register_v2.php
require __DIR__ . '/db.php';

// ฟังก์ชันตอบกลับง่าย ๆ
function sendHtml($ok, $title, $message) {
    $color = $ok ? '#0f766e' : '#dc2626';
    echo "<!doctype html><html lang='th'><meta charset='utf-8'><body style='font-family:Kanit, sans-serif;padding:24px'>
            <h2 style='color:$color'>$title</h2><p>$message</p>
            <p>javascript:history.back()« กลับไปหน้าเดิม</a></p>
          </body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html'); // หรือไฟล์หน้า UI ของคุณ
    exit;
}

// ==== 1) ดึงค่า POST ====
$username   = trim($_POST['auid']             ?? ''); // ในฟอร์มใช้ชื่อ auid แต่เราจะเก็บเป็น username
$firstName  = trim($_POST['FullnameMember']   ?? '');
$lastName   = trim($_POST['LastNameMember']   ?? '');
$email      = trim($_POST['MemberEmail']      ?? '');
$personId   = preg_replace('/\D+/', '', $_POST['person_ID_Member'] ?? ''); // เอาเฉพาะตัวเลข
$gender     = $_POST['genderMember']          ?? '';
$birthday   = $_POST['BrithDayMemeber']       ?? ''; // ชื่อฟิลด์ตามฟอร์ม (สะกดแบบนี้)
$phone      = trim($_POST['MemberPhone']      ?? '');
$faculty    = trim($_POST['faculty']          ?? '');
$password   = $_POST['password_hash']         ?? '';
$confirm    = $_POST['confirmPassword']       ?? '';

// ==== 2) ตรวจสอบความถูกต้องเบื้องต้น ====
$errors = [];

if ($username === '')            $errors[] = 'กรุณากรอก Username';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
if (!preg_match('/^\d{13}$/', $personId))       $errors[] = 'เลขบัตรประชาชนต้องมี 13 หลัก';
if (!in_array($gender, ['male', 'female'], true)) $errors[] = 'กรุณาเลือกเพศ';
if ($birthday === '')            $errors[] = 'กรุณากรอกวันเกิด';
if ($password === '')            $errors[] = 'กรุณากรอกรหัสผ่าน';
if (strlen($password) < 8)       $errors[] = 'รหัสผ่านควรมีอย่างน้อย 8 ตัวอักษร';
if ($password !== $confirm)      $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
if ($firstName === '' || $lastName === '') $errors[] = 'กรุณากรอกชื่อและนามสกุล';

if ($errors) {
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'พบข้อผิดพลาด:<br>- ' . implode('<br>- ', $errors));
}

try {
    // ==== 3) ตรวจซ้ำ Username และ personId ====
    // Username (authentication.username เป็น UNIQUE)
    $stmt = $mysqli->prepare("SELECT 1 FROM authentication WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'Username นี้ถูกใช้แล้ว กรุณาเปลี่ยนใหม่');
    }
    $stmt->close();

    // personId (member.personId เป็น PRIMARY KEY)
    $stmt = $mysqli->prepare("SELECT 1 FROM member WHERE personId = ?");
    $stmt->bind_param("s", $personId);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'เลขบัตรประชาชนนี้มีในระบบแล้ว');
    }
    $stmt->close();

    // ==== 4) เริ่มธุรกรรม ====
    $mysqli->begin_transaction();

    // 4.1 เพิ่มใน authentication (กำหนด role เป็น 'member')
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("
        INSERT INTO authentication (username, password_hash, email, role)
        VALUES (?, ?, ?, 'member')
    ");
    $stmt->bind_param("sss", $username, $hash, $email);
    $stmt->execute();
    $stmt->close();

    // ดึง auid ล่าสุด
    $auid = $mysqli->insert_id; // ค่า AUTO_INCREMENT จาก authentication.auid

    // 4.2 เพิ่มใน member (ผูกกับ auid)
    $stmt = $mysqli->prepare("
        INSERT INTO member (personId, mname, mlastname, mgender, mbrithday, mphone, mfaculty, auid)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssi",
        $personId,
        $firstName,
        $lastName,
        $gender,
        $birthday,
        $phone,
        $faculty,
        $auid
    );
    $stmt->execute();
    $stmt->close();

    // 4.3 ยืนยันธุรกรรม
    $mysqli->commit();

    // ==== 5) ตอบกลับสำเร็จ ====
    sendHtml(true, 'สมัครสมาชิกสำเร็จ', 'คุณสามารถเข้าสู่ระบบได้แล้ว');

} catch (Throwable $e) {
    // หากเกิดข้อผิดพลาดให้ Rollback
    if ($mysqli->errno === 0) {
        // errno ยังไม่ตั้ง แต่อยู่ใน transaction — พยายาม rollback ไว้ก่อน
        try { $mysqli->rollback(); } catch (Throwable $__) {}
    } else {
        try { $mysqli->rollback(); } catch (Throwable $__) {}
    }

    // ข้อความสำหรับผู้ใช้
    $msg = 'เกิดข้อผิดพลาดไม่คาดคิด: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', $msg);
}