<?php
// register_lib_save_v2.php   บันทึกลงฐานข้อมูล librarian
require __DIR__ . '/../Connect/db.php';;

function sendHtml($state_form, $title, $message) { //สร้างหน้าเว็บ HTML เมื่่อกรอก form register ของ member เสร็จ

        //$state_form = ใช้เป็นตัวบ่งบอกสถานะ (true/false)
        //$title = ข้อความหัวเรื่องที่จะนำไปแสดง
        //$message = ข้อความรายละเอียดที่จะนำไปแสดง

    $color = $state_form ? '#0f766e' : '#dc2626';
    // FIX: ปรับแท็ก <a> ให้ถูกต้อง
    echo "<!doctype html><html lang='th'>
    <meta charset='utf-8'><body style='font-family:Kanit, sans-serif;padding:24px'>
        <h2 style='color:$color'>$title</h2><p>$message</p>
        <p>index.html« กลับหน้าแรก</a></p>
          </body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// 1) รับข้อมูลจากฟอร์ม (trim ลบช่องว่าง) 
$username   = trim($_POST['auid']             ?? '');
$firstName  = trim($_POST['lbname']           ?? '');
$lastName   = trim($_POST['lblastname']       ?? '');
$email      = trim($_POST['email']            ?? '');
$personId   = preg_replace('/\D+/', '', $_POST['person_ID_librarian'] ?? '');
$gender     = $_POST['lbgender']              ?? '';
$birthday   = $_POST['lb_dob']                ?? '';
$phone      = trim($_POST['lbphone']          ?? '');
$password   = $_POST['password']              ?? '';
$confirm    = $_POST['confirmPassword']       ?? '';

// ADDED: ทำให้ phone เก็บเฉพาะตัวเลข
$phoneDigits = preg_replace('/\D+/', '', $phone);

// 2) ตรวจสอบความถูกต้องเบื้องต้น
$errors = [];
if ($username === '')                           $errors[] = 'กรุณากรอก Username';
// ADDED: เช็คความยาว Username อย่างน้อย 5 ตัว
if ($username !== '' && strlen($username) < 5)  $errors[] = 'Username ควรมีอย่างน้อย 5 ตัวอักษร';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
if (!preg_match('/^\d{13}$/', $personId))       $errors[] = 'เลขบัตรประชาชนต้องมี 13 หลัก';

// FIX: รองรับทั้งค่าไทยและอังกฤษ
$validGenders = ['male','female','other','ชาย','หญิง','อื่นๆ'];
if (!in_array($gender, $validGenders, true))    $errors[] = 'กรุณาเลือกเพศ';

if ($birthday === '')                           $errors[] = 'กรุณากรอกวันเกิด';

if ($firstName === '' || $lastName === '')      $errors[] = 'กรุณากรอกชื่อและนามสกุล';

// ADDED: เช็คเบอร์โทร 9–10 หลัก (เฉพาะตัวเลข)
if ($phoneDigits === '' || !preg_match('/^\d{9,10}$/', $phoneDigits)) {
    $errors[] = 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 9–10 หลัก';
}

if ($password === '')                           $errors[] = 'กรุณากรอกรหัสผ่าน';
if (strlen($password) < 8)                      $errors[] = 'รหัสผ่านควรมีอย่างน้อย 8 ตัวอักษร';
if ($password !== $confirm)                     $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';

if ($errors) {
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'พบข้อผิดพลาด:<br>- ' . implode('<br>- ', $errors));
}

try {
    // ADDED: แนะนำให้ตั้ง charset ใน db.php เช่น $mysqli->set_charset('utf8mb4');

    // 3) ตรวจซ้ำ Username และ personId
    $stmt = $mysqli->prepare("SELECT 1 FROM authentication WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    // NOTE: ใช้ get_result() ได้หากมี mysqlnd
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'Username นี้ถูกใช้แล้ว กรุณาเปลี่ยนใหม่');
    }
    $stmt->close();

    // FIX: ตรวจซ้ำ personId ในตาราง librarian (เดิมตรวจใน member)
    $stmt = $mysqli->prepare("SELECT 1 FROM librarian WHERE personId = ?");
    $stmt->bind_param("s", $personId);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) {
        sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', 'เลขบัตรประชาชนนี้มีในระบบแล้ว');
    }
    $stmt->close();

    // 4) เริ่ม Transaction
    $mysqli->begin_transaction();

    // ADDED: แฮชรหัสผ่านก่อนบันทึก เปลี่ยนเป็นไม่เเฮท
    $passwordHash = $password;

    // 4.1 insert authentication (role = librarian)
    $stmt = $mysqli->prepare("
        INSERT INTO authentication (username, password_hash, email, role)
        VALUES (?, ?, ?, 'librarian')
    ");
    $stmt->bind_param("sss", $username, $passwordHash, $email);
    $stmt->execute();
    $stmt->close();

    // 4.2 ใช้ auid ที่เพิ่งสร้าง
    $auid = $mysqli->insert_id;

    // 4.3 insert librarian
    $stmt = $mysqli->prepare("
        INSERT INTO librarian (personId, lbname, lblastname, lbgender, lbbirthDay, lbphone, auid) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    "); //ยังไม่มีเพศ
    // FIX: เก็บเบอร์โทรเป็นตัวเลขที่ทำความสะอาดแล้ว
    $stmt->bind_param("ssssssi", $personId, $firstName, $lastName, $gender, $birthday, $phoneDigits, $auid);
    $stmt->execute();
    $stmt->close();

    // 4.4 commit
    $mysqli->commit();

    // sendHtml(true, 'สมัครสมาชิกสำเร็จ', 'คุณสามารถเข้าสู่ระบบได้แล้ว');
  
  
    // สมัคสำเร็จเด้งไปหน้าlogin
    echo"<script>
        alert('สมัครสมาชิกสำเร็จ! คุณสามารถเข้าสู่ระบบได้แล้ว');
        window.location.href = '../../BookingBordgame/Login_Registet_Member/Login_Register.php';
        </script>";
        exit;



} catch (Throwable $e) {
    try { $mysqli->rollback(); } catch (Throwable $__ ) {}
    $msg = 'เกิดข้อผิดพลาดไม่คาดคิด: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    sendHtml(false, 'สมัครสมาชิกไม่สำเร็จ', $msg);
}
?>
