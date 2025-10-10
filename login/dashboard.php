<?php
// dashboard.php
// หน้านี้ใช้แสดงหลังจากผู้ใช้ล็อกอินแล้ว

// 1) เริ่ม session เพื่อเข้าถึงตัวแปร $_SESSION
session_start();

// 2) ตรวจว่ามีการล็อกอินหรือยัง (มี user_id ใน session ไหม)
//    ถ้าไม่มีกลับไปหน้า login
// if (empty($_SESSION['user_id'])) {
//   header("Location: login.html");
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
  <title>Dashboard</title>
  <style>
    /* สไตล์เล็กน้อยให้อ่านง่าย */
    :root { color-scheme: light dark; }
    body { font-family: system-ui, sans-serif; margin: 0; }
    header {
      padding: 16px 20px;
      background: #1f2937; /* เทาเข้ม */
      color: #fff;
      display: flex; align-items: center; justify-content: space-between;
    }
    main { max-width: 900px; margin: 32px auto; padding: 0 20px; }
    .card {
      border: 1px solid #e5e7eb; border-radius: 12px;
      padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
      background: #fff;
    }
    a.button {
      display: inline-block; padding: 10px 16px; border-radius: 8px;
      text-decoration: none; border: 1px solid #3b82f6;
    }
    a.button:hover { background: #3b82f6; color: #fff; }
    .card{
      background-color: red;
    }

    
  </style>
</head>
<body>
  <header>
    <div><strong>My App</strong> • Dashboard</div>
    <nav>
      <a class="button" href="logout.php">ออกจากระบบ</a>
    </nav>
  </header>

  <main>
    <div class="card">
      <h1>ยินดีต้อนรับ, <?php echo $displayName; ?></h1>
      <p>คุณล็อกอินด้วยชื่อผู้ใช้: <strong><?php echo $username; ?></strong></p>

      <!-- ตัวอย่าง: พื้นที่เนื้อหาในระบบ -->
      <hr>
      <p>นี่คือหน้าภายในระบบของคุณ คุณสามารถวางเมนู/ลิงก์ไปหน้าจัดการต่าง ๆ ได้ที่นี่</p>
      <ul>
        <li><a href="profile.php">โปรไฟล์ของฉัน</a></li>
        <li><a href="settings.php">ตั้งค่า</a></li>
        <li><a href="reports.php">รายงาน</a></li>
      </ul>
    </div>
  </main>
</body>
</html>
