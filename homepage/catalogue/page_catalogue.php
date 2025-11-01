<?php
// page_catalogue.php
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db.php';

session_start();

if (!defined('PROJECT_ROOT')) {
    // ถ้าไฟล์อื่นยังไม่ได้ define ให้กำหนดฐานโปรเจกต์ไว้ที่นี่
    define('PROJECT_ROOT', '/BookingBordgame/');
}
// รูปสำรอง (ให้มีไฟล์นี้จริงในโปรเจกต์ หรือเปลี่ยนเป็นไฟล์ที่คุณมี)
if (!defined('IMG_PLACEHOLDER')) {
    define('IMG_PLACEHOLDER', rtrim(PROJECT_ROOT, '/').'/image/bgrmutt.jpg');
}

/** แปลงค่า image_url ให้กลายเป็น src ที่ใช้ได้แน่นอน */
function img_src(?string $raw): string {
    $raw = trim(str_replace('\\', '/', (string)$raw)); // ปรับ \ เป็น /
    if ($raw === '') {
        return IMG_PLACEHOLDER;
    }
    if (preg_match('#^https?://#i', $raw)) {
        return $raw; // เป็นลิงก์นอกอยู่แล้ว
    }
    // เป็นไฟล์ในโปรเจกต์ → ต่อด้วย PROJECT_ROOT
    return rtrim(PROJECT_ROOT, '/').'/'.ltrim($raw, '/');
}


// ดึงชื่อผู้ใช้และ ID ผู้ใช้จาก Session มาเก็บในตัวแปร
$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
$displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');


// ดึงข้อมูล Filter จาก URL (method="GET")
$search_term = $_GET['search'] ?? '';
$filter_type = $_GET['type'] ?? '';
$filter_age = $_GET['age'] ?? '';
$filter_time = $_GET['time'] ?? ''; 

// 3. สร้าง Dynamic SQL Query
$sql = "SELECT
            g.bgName,
            d.bddescript,
            d.bdage,
            d.bdtime,
            d.image_url,
            t.btName
        FROM
            boradgame AS g
        JOIN
            bordgamedescription AS d ON g.bdId = d.bdId
        JOIN
            bordgametype AS t ON g.btId = t.btId";

$conditions = [];
$params = [];

// เพิ่มเงื่อนไข state พื้นฐาน (1 = พร้อมให้บริการ)
$conditions[] = "g.state = ?";
$params[] = 1;

// 3.1. เพิ่มเงื่อนไขการค้นหา (Search)
if (!empty($search_term)) {
    $conditions[] = "g.bgName LIKE ?";
    $params[] = "%" . $search_term . "%";
}

// 3.2. เพิ่มเงื่อนไข Filter ประเภท (Type)
if (!empty($filter_type) && is_numeric($filter_type)) {
    $conditions[] = "g.btId = ?";
    $params[] = $filter_type;
}

// 3.3. เพิ่มเงื่อนไข Filter อายุ (Age)
if (!empty($filter_age)) {
    $conditions[] = "d.bdage = ?";
    $params[] = $filter_age;
}

// 3.4. เพิ่มเงื่อนไข Filter เวลา (Time)
if (!empty($filter_time)) {
    $conditions[] = "d.bdtime <= ?";
    $params[] = $filter_time;
}

// 3.5. รวมเงื่อนไขทั้งหมด
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// 4. สั่ง Execute Query ด้วย Prepared Statements
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

// 5. ดึงข้อมูลสำหรับใส่ใน Filter Dropdowns
$types = $pdo->query("SELECT * FROM bordgametype ORDER BY btName")->fetchAll();
$ages = $pdo->query("SELECT DISTINCT bdage FROM bordgamedescription ORDER BY bdage")->fetchAll();

?>




<!DOCTYPE html>
<html lang="en">

<head>
    

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>homepage</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <style>
* {
            padding: 0px;
            margin: 0px;
            font-family: Kanit;
        }

        /* startpopup (จาก homepage.php) */
        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .popup {
            position: fixed;
            top: 100vh;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }

        .popup .overlay {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 1;
            transition: opacity 100ms ease-in-out 200ms;
        }


        .popup .popup-content h2 {
            margin: 10px 0px;
            font-size: 25px;
            color: #111;
            text-align: center;
        }

        .popup .popup-content {
            margin: 15px 0px;
            color: #222;
            font-size: 16px;
            text-align: center;

        }

        .popup .popup-content .controls {
            display: flex;
            margin: 20px 0px 0px;
        }

        .popup .popup-content .controls button {
            padding: 10px 20px;
            border: none;
            outline: none;
            font-size: 15px;
            border-radius: 20px;
            cursor: pointer;
        }

        .popup .popup-content .controls .close-btn {
            background: transparent;
            background: #e2e2e2ff;
          color: #1c1e22ff;
          border: none;
           padding: 10px 16px;
           border-radius: 8px;
           font-weight: 600;
    transition: background-color 0.2s ease;
        }


        .popup .popup-content .controls .perv-btn {
            background: transparent;
            color: #3284ed;

        }


        .popup .popup-content .controls .submit-btn {
            background: #3284ed;
            color: #fff;
        }

        .popup.active {
            top: 0px;
            transition: top 0ms ease-in-out 0ms;

        }

        .popup.popup.active .overlay {
            opacity: 1;
            transition: all 300ms ease-in-out;
        }

        .popup.active .popup-content {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .popup .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1.15);
            opacity: 0;
            width: 95%;
            max-width: 350px; /* default width */
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 2px 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 300ms ease-in-out;

        }

        .image-bg {
            display: flex;
            justify-content: center;
            align-items: center;
        }


        .title-age {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* endpopup */

        /* nav (จาก homepage.php) */
        .head-content nav {
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: saturate(160%) blur(4px);
        }

        .navbar-content {
            list-style: none;
            margin: 0;
            padding: 0 20px;
            height: 72px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;

            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }

        .logo {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .menu-content {
            list-style-type: none;
            display: flex;
        }

        .menu-content li {
            margin-left: 60px;
        }

        .menu-content {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 28px;
            margin: 0;
            padding: 0 16px;

            /* ให้ยืดกลางระหว่างโลโก้กับปุ่มขวา */
            flex: 1;
            justify-content: center;
        }

        /* ไอเท็มเมนู */
        .menu-content li>a {
            position: relative;
            font-weight: 600;
            letter-spacing: .2px;
            padding: 8px 12px;
            border-radius: 999px;
            transition: transform .15s ease, background-color .15s ease, box-shadow .15s ease;
            cursor: pointer;
            user-select: none;
            text-decoration: none;
            color: white;
        }

        /* โฮเวอร์/โฟกัสเมนู */
        .menu-content li>a:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.10);
        }

        .menu-content li>a:focus-visible {
            outline: 2px solid #93c5fd;
            /* วงโฟกัสมองเห็นชัด */
            outline-offset: 2px;
            background: rgba(255, 255, 255, 0.18);
        }

        /* ถ้าจะทำ active ไว้ใช้คลาสนี้ได้ */
        .menu-content li>a.active {
            background: rgba(255, 255, 255, 0.22);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
        }

        .btn-sigin:last-child {
            appearance: none;
            border: 0;
            background: #ffffff;
            color: #1d4ed8;
            font-weight: 700;
            padding: 10px 16px;
            border-radius: 999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            transition: transform .1s ease, box-shadow .2s ease, background-color .2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-sigin:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            background: #fcca0c;
            color: white;
            text-decoration: none;
        }

        .btn-sigin:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16);
        }
        /* endNav */

        /* Dropdown menu (จาก homepage.php) */
        .profile-dropdown {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            background: none;
            border: none;
            padding: 8px 12px;
            border-radius: 999px;
            transition: background-color 0.15s ease;
            color: white; 
            font-weight: 600;
            font-family: 'Kanit', sans-serif;
            font-size: 16px;
        }

        .profile-trigger::after {
            content: '▼';
            font-size: 10px;
            transform: translateY(1px);
            opacity: 0.8;
        }

        .profile-trigger:hover {
            background: rgba(255, 255, 255, 0.16);
        }

        .dropdown-menu {
            display: none; 
            position: absolute;
            top: 100%;     
            right: 0;      
            z-index: 100;  
            min-width: 200px; 
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            margin-top: 8px; 
            list-style: none;
            padding: 8px; 
            margin-left: 0; 
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu li a {
            display: block;
            padding: 10px 14px;
            text-decoration: none;
            color: #1f2937; 
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .dropdown-menu li a:hover {
            background-color: #f3f4f6; 
            color: #1d4ed8; 
        }

        .dropdown-menu li.divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 8px 0;
        }

        .dropdown-menu li a.logout-link:hover {
            background-color: #fee2e2; 
            color: #dc2626; 
        }

        /* Popup ยืนยันออกจากระบบ */

        .popup-content--confirm h2 {
            font-size: 1.25rem; 
            font-weight: 700;
            color: #1f2937; 
            margin: 0; 
            line-height: 1.4;
        }

        .popup-content--confirm .controls {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 0; 
        }

        .popup-content--confirm .close-btn {
            background: #e2e2e2ff; 
            color: #1c1e22ff; 
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        .popup-content--confirm .close-btn:hover {
            background: #e5e7eb; 
        }

        #confirm-logout-btn{
            background: #dc2626; 
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        /* Popup ประวัติการจอง (จาก homepage.php) */
        .popup-content--history {
            max-width: 500px; 
            min-height: 200px;
            padding: 24px;
            gap: 16px;
            grid-template-rows: auto 1fr auto; 
            text-align: left;
        }

        .popup-content--history h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #1f2937;
            font-weight: 700;
        }

        .history-body {
            max-height: 60vh; 
            overflow-y: auto;
            padding: 4px;
            border-radius: 8px;
            background: #f9fafb; 
            border: 1px solid #e5e7eb;
        }

        .booking-item {
            display: flex;
            justify-content: space-between; 
            align-items: center;
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }
        .booking-item:last-child {
            border-bottom: none;
        }

        .booking-item-game {
            font-weight: 600;
            color: #1d4ed8; 
            font-size: 1.05rem;
        }

        .booking-item-details {
            font-size: 1rem;
            color: #374151;
        }

        .booking-item-details strong {
            color: #111827;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .history-body .no-bookings {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
            font-weight: 500;
        }

        /* === CSS เฉพาะสำหรับหน้า Catalogue (จาก styles_Catalogue.css) === */
        
        .catalogue-container {
            display: flex;
            flex-wrap: wrap; 
            gap: 20px;
            max-width: 1400px;
            margin: 40px auto;
            margin-top: 40px ;
        }

        .sidebar {
            flex: 1; 
            min-width: 280px;
            background-color: #f1f3f4; 
            padding: 20px;
            border-radius: 8px; 
            height: fit-content;
            border: 1px solid #e0e0e0; 
        }

        .sidebar h2 {
            color: #000000; 
            border-bottom: 2px solid #1a73e8; 
            padding-bottom: 10px;
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #1a73e8; 
        }

        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 10px;
            background-color: #ffffff; 
            color: #202124;
            border: 1px solid #bdc1c6; 
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .btn-filter, .btn-reset {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-filter {
            background-color: #1a73e8; 
            color: #ffffff;
        }

        .btn-reset {
            background-color: #e8eaed; 
            color: #202124;
        }

        .content-area {
            flex: 3;
            min-width: 300px;
        }

        .content-area h1 {
            color: #000000;
            margin-top: 0;
        }

        .game-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .game-card {
            display: flex;
            background-color: #ffffff; 
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0; 
            transition: box-shadow 0.2s;
        }

        .game-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
        }

        .game-image {
            width: 200px;
            height: 150px; /* ปรับแก้เล็กน้อยเพื่อให้ภาพดูดีขึ้น */
            object-fit: cover;
            flex-shrink: 0;
        }

        .game-info {
            padding: 15px;
            flex: 1;
        }

        .game-title {
            color: #1a73e8; 
            margin: 0 0 10px 0;
        }

        .game-description {
            font-size: 14px;
            margin: 10px 0 0 0;
            line-height: 1.4;
            color: #3c4043; 
        }

        .game-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px; /* เพิ่มระยะห่าง */
        }

        .tag {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 3px;
            background-color: #e8eaed; 
            color: #3c4043; 
        }

        .no-results {
            color: #202124;
            font-size: 18px;
            text-align: center;
            padding: 40px;
            background-color: #f1f3f4; 
            border-radius: 8px;
        }



    </style>
</head>

<body>
<!-- Heading  sign out -->
    <head class="head-content">
        <nav>
            <ul class="navbar-content">
                <li>
                    <img class="logo" src="../../image//bgrmutt.jpg" alt="">
                </li>
                <ul class="menu-content">

                    <li><a href="../booking/homepage.php">Home</a></li>
                    <li><a href="../catalogue/page_catalogue.php">Category</a></li>
                    <li><a href="../ranking_forder/hot_ranking.php">Hot</a></li>
                    <li><a href="../contact.php">Contact</a></li>
                </ul>

                </li>

                    <?php
            // ตรวจสอบว่า $displayName (ที่ดึงมาจาก Session) มีค่าหรือไม่
if (!empty($displayName)) :
?>
    <li class="profile-dropdown"> 
        
        <button type="button" id="profile-trigger" class="profile-trigger">
            <span><?= htmlspecialchars($displayName) ?></span>
        </button>

        <ul id="profile-menu" class="dropdown-menu">
            <li>
                <a id="booking-history-trigger">สถานะการจอง</a>  
            </li>
            <li class="divider"></li> <li>
                <a href="../booking/logout.php" class="logout-link">ออกจากระบบ</a>
            </li>
        </ul>

    </li>
        <?php
            else :
        ?>
        <li style="display: flex; align-items: center; gap: 16px;">
            <div class="title" style="color: #e0e0e0; font-weight: 500; font-style: italic;">
            คุณยังไม่ได้เข้าสู่ระบบ
            </div>
            <a class="btn-sigin" href="../../Login_Registet_Member/Login_Register.php">
                เข้าสู่ระบบ
            </a>
        </li>
                 <?php
                     endif; // จบการตรวจสอบ
                  ?>

             </ul>
    </nav>
            
                     <div id="history-popup" class="popup">
                            <div class="overlay"></div>
                                 <div class="popup-content popup-content--history">
                                     <h2>สถานะการจองของคุณ</h2>
        
                                     <div id="history-content-loader" class="history-body">
                                           <p>กำลังโหลดข้อมูล...</p>
                                     </div>

                                     <div class="controls" style="justify-content: flex-end;">
                                           <button type="button" class="close-btn">ปิด</button>
                                    </div>
                                </div>
                            </div>

    <div id="confirm-logout-popup" class="popup"> 
        <div class="overlay"></div>
        <div class="popup-content popup-content--confirm"> <h2>คุณต้องการออกจากระบบหรือไม่</h2>
        
            <div class="controls">
                <button type="button" id="cancel-logout-btn" class="close-btn">
                    ยกเลิก
                </button>
                <button type="button" id="confirm-logout-btn" class="submit-btn btn-danger">
                    ออกจากระบบ
                </button>
          </div>
        </div>
    </div>
                    <!--จบ heading-->
     <div class="catalogue-container">
        
        <aside class="sidebar">
            
            <form action="page_catalogue.php" method="GET" class="filter-form">
                
                <div class="form-group">
                    <label for="search">ค้นหาบอร์ดเกม</label>
                    <input type="text" id="search" name="search" placeholder="เช่น Werewolf, Spender..." value="<?= htmlspecialchars($search_term) ?>">
                </div>

                <div class="form-group">
                    <label for="type">ประเภทเกม</label>
                    <select id="type" name="type">
                        <option value="">-- ทุกประเภท --</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type['btId'] ?>" <?= ($filter_type == $type['btId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['btName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="age">อายุผู้เล่น</label>
                    <select id="age" name="age">
                        <option value="">-- ทุกช่วงอายุ --</option>
                        <?php foreach ($ages as $age): ?>
                            <option value="<?= htmlspecialchars($age['bdage']) ?>" <?= ($filter_age == $age['bdage']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($age['bdage']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="time">เวลาเล่นไม่เกิน</label>
                    <select id="time" name="time">
                        <option value="">-- ไม่จำกัดเวลา --</option>
                        <option value="00:15:00" <?= ($filter_time == '00:15:00') ? 'selected' : '' ?>>15 นาที</option>
                        <option value="00:30:00" <?= ($filter_time == '00:30:00') ? 'selected' : '' ?>>30 นาที</option>
                        <option value="01:00:00" <?= ($filter_time == '01:00:00') ? 'selected' : '' ?>>1 ชั่วโมง</option>
                        <option value="99:00:00" <?= ($filter_time == '99:00:00') ? 'selected' : '' ?>>มากกว่า 1 ชั่วโมง</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-filter">ค้นหา</button>
                    <a href="page_catalogue.php" class="btn-reset">ล้างค่า</a>
                </div>
            </form>
        </aside>

        <main class="content-area">
            <h1>รายการบอร์ดเกม (<?= count($games) ?> รายการ)</h1>
            <div class="game-list">
                
                <?php if (count($games) > 0): ?>
                    <?php foreach ($games as $game): ?>
                        <div class="game-card">
                            <img
                                src="<?= htmlspecialchars(img_src($game['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($game['bgName']) ?>"
                                class="game-image"
                                onerror="this.onerror=null;this.src='<?= htmlspecialchars(IMG_PLACEHOLDER, ENT_QUOTES, 'UTF-8') ?>';"
                                />
                            <div class="game-info">
                                <h3 class="game-title"><?= htmlspecialchars($game['bgName']) ?></h3>
                                <div class="game-tags">
                                    <span class="tag tag-type"><?= htmlspecialchars($game['btName']) ?></span>
                                    <span class="tag tag-age">อายุ <?= htmlspecialchars($game['bdage']) ?></span>
                                    <span class="tag tag-time">เวลา <?= htmlspecialchars(substr($game['bdtime'], 0, 5)) ?> นาที</span>
                                </div>
                                <p class="game-description">
                                    <?= htmlspecialchars(mb_substr($game['bddescript'], 0, 100)) ?>...
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-results">ไม่พบรายการบอร์ดเกมที่ตรงกับเงื่อนไขของคุณ</p>
                <?php endif; ?>

            </div>
        </main>

    </div>



</body>
<script>
// Pop up ยืนยันออกจากระบบ
document.addEventListener('DOMContentLoaded', () => {

    const logoutLink = document.querySelector('a.logout-link');
    const confirmModal = document.getElementById('confirm-logout-popup');
    
    if (logoutLink && confirmModal) {
        
        const cancelBtn = document.getElementById('cancel-logout-btn');
        const confirmBtn = document.getElementById('confirm-logout-btn');
        const overlay = confirmModal.querySelector('.overlay');

        logoutLink.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            const profileMenu = document.getElementById('profile-menu');
                if (profileMenu) {
            profileMenu.classList.remove('active');
        }
        
        confirmModal.classList.add('active');
        });

        confirmBtn.addEventListener('click', () => {
            confirmModal.classList.remove('active');
            window.location.href = logoutLink.href;
        });

        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });

        overlay.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });
    }
});

// Dropdown โปรไฟล์
document.addEventListener('DOMContentLoaded', () => {
    
    const profileTrigger = document.getElementById('profile-trigger');
    const profileMenu = document.getElementById('profile-menu');

    if (profileTrigger && profileMenu) {

        profileTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); 
            profileMenu.classList.toggle('active');
        });

        window.addEventListener('click', (e) => {
            if (profileMenu.classList.contains('active')) {
                if (!profileTrigger.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('active'); 
                }
            }
        });
    }
});


// Popup ประวัติการจอง
document.addEventListener('DOMContentLoaded', () => {

    const historyTrigger = document.getElementById('booking-history-trigger');
    const historyPopup = document.getElementById('history-popup');
    
    if (!historyTrigger || !historyPopup) {
        return;
    }

    const historyContent = document.getElementById('history-content-loader');
    const historyCloseBtn = historyPopup.querySelector('.close-btn');
    const historyOverlay = historyPopup.querySelector('.overlay');

    function openHistoryPopup() { 
        document.getElementById('profile-menu')?.classList.remove('active');
        historyPopup.classList.add('active');
        historyContent.innerHTML = '<p style="text-align:center; padding: 30px; font-weight: 500;">กำลังโหลดข้อมูล...</p>';
        fetchBookingHistory(); // <-- เปลี่ยนจาก log out() เป็น fetchBookingHistory()
    }

    function closeHistoryPopup() {
        historyPopup.classList.remove('active');
    }

    async function fetchBookingHistory() {
        try {
            // *** ตรวจสอบว่า path นี้ถูกต้องเมื่อเรียกจาก /catalogue/page_catalogue.php ***
            // ถ้า api_get_booking_state.php อยู่ใน /booking/
            const response = await fetch('../booking/api_get_booking_state.php'); 
            const data = await response.json();

            if (data.success) {
                renderBookingHistory(data.bookings);
            } else {
                historyContent.innerHTML = `<div class="no-bookings" style="color: red;">${escapeHTML(data.error) || 'ไม่สามารถโหลดข้อมูลได้'}</div>`;
            }
        } catch (err) {
            console.error('Fetch Error:', err);
            historyContent.innerHTML = '<div class="no-bookings" style="color: red;">เกิดข้อผิดพลาดในการเชื่อมต่อ</div>';
        }
    }

    function renderBookingHistory(bookings) {
        if (bookings.length === 0) {
            historyContent.innerHTML = '<div class="no-bookings">ไม่พบข้อมูลการจองที่กำลังใช้งาน</div>';
            return;
        }

        let html = '';
        bookings.forEach(booking => {
            html += `
                <div class="booking-item">
                    <span class="booking-item-game">${escapeHTML(booking.bgName)}</span>
                    <span class="booking-item-details">
                        โต๊ะ: <strong>${escapeHTML(booking.tableId)}</strong>
                    </span>
                </div>
            `;
        });

        historyContent.innerHTML = html;
    }

    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return str.toString().replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }

    historyTrigger.addEventListener('click', (e) => {
        e.preventDefault(); 
        openHistoryPopup();
    });

    historyCloseBtn.addEventListener('click', closeHistoryPopup);
    historyOverlay.addEventListener('click', closeHistoryPopup);

});

</script>
</html>