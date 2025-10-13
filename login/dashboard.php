<?php
// dashboard.php
// หน้านี้ใช้แสดงหลังจากผู้ใช้ล็อกอินแล้ว

// 1) เริ่ม session เพื่อเข้าถึงตัวแปร $_SESSION
session_start();

// 2) ตรวจว่ามีการล็อกอินหรือยัง (มี user_id ใน session ไหม)
//    ถ้าไม่มีกลับไปหน้า login
// if (empty($_SESSION['user_id'])) {
//   header("Location: ../login.html");
//   exit;
// }

// 3) ดึงข้อมูลที่เก็บไว้ตอนล็อกอิน (สำหรับแสดงผล)

//    *หุ้มด้วย htmlspecialchars เพื่อกัน XSS เสมอ*
$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
$username    = htmlspecialchars($_SESSION['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hamepage</title>
  <style>

  </style>
</head>
<body>
  <header>
    
    <nav>
      <a class="button" href="logout.php">ออกจากระบบ</a>
    </nav>
  </header>
  <main>
    <div class="card">
      <h1>ยินดีต้อนรับ<?php echo $displayName; ?></h1>
      <p>คุณล็อกอินด้วยชื่อผู้ใช้: <strong><?php echo $username; ?></strong></p>

      <!-- ตัวอย่าง: พื้นที่เนื้อหาในระบบ -->
      <hr>
      <p>นี่คือหน้าภายในระบบของคุณ คุณสามารถวางเมนู/ลิงก์ไปหน้าจัดการต่าง ๆ ได้ที่นี่</p>
    </div>
  </main>
</body>
</html>
