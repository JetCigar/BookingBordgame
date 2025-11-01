<?php
// register_v2.php   บันทึกลงฐานข้อมูล member
require __DIR__ . '/../Connect/db.php';

function sendHtml($state_form, $title, $message)
{
    // ฟังก์ชันนี้ใช้เฉพาะตอนสมัครไม่สำเร็จ
    $color = $state_form ? '#0f766e' : '#dc2626';
    echo "<!doctype html>
<html lang='th'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$title</title>
    
    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
    <link href='https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;700&display=swap' rel='stylesheet'>
    
    <style>
        /* 2. กำหนด CSS Variable จากค่า PHP */
        :root {
            --theme-color: $color;
        }
        
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f3f4f6; /* สีพื้นหลังเทาอ่อน */
            color: #374151;
            margin: 0;
            padding: 24px;
            
            /* 3. จัดให้อยู่กึ่งกลางจอ (แนวตั้งและแนวนอน) */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90vh; /* ใช้ 90vh แทน 100vh เพื่อเผื่อระยะขอบเล็กน้อย */
        }
        
        /* 4. ดีไซน์การ์ดหลัก */
        .alert-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            padding: 32px 40px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
            
            /* 5. ใช้แถบสีด้านบนเพื่อบอกสถานะ */
            border-top: 5px solid var(--theme-color);
        }
        
        /* 6. สไตล์ของหัวข้อ (Title) */
        .alert-card h2 {
            color: #111827; /* สีดำเข้ม */
            font-size: 24px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 12px;
        }
        
        /* 7. สไตล์ของข้อความ (Message) */
        .alert-card p {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563; /* สีเทาเข้ม */
            margin-top: 0;
            margin-bottom: 28px;
        }
        
        /* 8. สไตล์ของปุ่ม (Link) */
        .back-link {
            display: inline-block;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            color: #ffffff; /* สีตัวอักษรขาว */
            background-color: var(--theme-color); /* สีพื้นหลังตามสถานะ */
            padding: 12px 28px;
            border-radius: 8px;
            
            /* 9. เพิ่ม Effect ตอนเมาส์ชี้ */
            transition: all 0.2s ease-in-out;
        }
        
        .back-link:hover {
            filter: brightness(0.9); /* ทำให้สีเข้มขึ้นเล็กน้อย */
            transform: scale(1.03);
        }
    </style>
</head>
<body>
    <div class='alert-card'>
        <h2>$title</h2>
        <p>$message</p>
        <a href='Login_Register.php' class='back-link'>« กลับหน้าแรก</a>
    </div>
</body>
</html>";
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
}
