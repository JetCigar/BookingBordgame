<?php
// --- homepage_forder/ranking_forder/hot_ranking.php ---

// 1. เริ่ม Session และดึงข้อมูลผู้ใช้ (เหมือน homepage.php)
session_start();
$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
$displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');

// 2. เรียกไฟล์ config (db.php)
require_once 'Connect_DB_ranking.php';

// 3. สร้างคำสั่ง SQL (Query)
$sql = "
    SELECT
        t1.bgId,
        t3.bgName,
        t4.image_url,
        t5.btname,
        COUNT(t1.bkdId) AS booking_count
    FROM
        bookingdetail AS t1
    JOIN
        boradgame AS t3 ON t1.bgId = t3.bgid
    JOIN
        bordgamedescription AS t4 ON t3.bdId = t4.bdId
    JOIN
        bordgametype AS t5 ON t3.btId = t5.btId 
    GROUP BY
        t1.bgId, 
        t3.bgName, 
        t4.image_url,
        t5.btname
    ORDER BY
        booking_count DESC
    LIMIT 10;
";

// 4. สั่งให้ฐานข้อมูลทำงาน (Execute Query)
$result = $conn->query($sql);

// 5. เตรียมตัวแปร (Array) ไว้เก็บข้อมูล
$rankings = [];
if ($result && $result->num_rows > 0) {
    // 6. วนลูปดึงข้อมูลทีละแถว
    while ($row = $result->fetch_assoc()) {
        $rankings[] = $row;
    }
}

// 7. ปิดการเชื่อมต่อฐานข้อมูล (เมื่อใช้งานเสร็จ)
$conn->close();

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อันดับบอร์ดเกมยอดนิยม</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    
    <style>
        * {
            box-sizing: border-box; /* <-- แก้ไขจาก 0px เป็น border-box */
            padding: 0px;
            margin: 0px;
            font-family: Kanit;
        }

        /* === NAV BAR (จาก homepage.php) === */
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
        /* === End Nav Bar === */


        /* === Dropdown menu (จาก homepage.php) === */
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
        /* === End Dropdown === */

        /* === Popup (จาก homepage.php) === */
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
        
        .popup.active {
            top: 0px;
            transition: top 0ms ease-in-out 0ms;
        }

        .popup.active .popup-content {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        /* Popup ยืนยันออกจากระบบ (จาก homepage.php) */
        .popup-content--confirm {
            max-width: 400px; 
            padding: 28px;
            display: grid; /* <-- เพิ่ม display grid */
            grid-template-rows: auto auto; 
            gap: 24px; 
            text-align: center; 
            border-radius: 16px;
        }

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

        /* ปุ่มยกเลิก (แก้ไขแล้ว) */
        #cancel-logout-btn { 
            background: #e2e2e2ff; 
            color: #1c1e22ff; 
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        
        #cancel-logout-btn:hover {
            background: #e5e7eb; 
        }

        /* ปุ่มยืนยัน (แก้ไขแล้ว) */
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
            display: grid; /* <-- เพิ่ม display grid */
            gap: 16px;
            grid-template-rows: auto 1fr auto; 
            text-align: left;
        }

        .popup-content--history h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #1f2937;
            font-weight: 700;
            font-size: 25px; /* <-- เพิ่ม */
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

        /* ปุ่ม Close (สำหรับ History) */
        .popup-content--history .controls {
            display: flex; /* <-- เพิ่ม */
            justify-content: flex-end; /* <-- เพิ่ม */
            margin-top: 10px; /* <-- เพิ่ม */
        }
        .popup-content--history .close-btn {
            background: #e2e2e2ff;
            color: #1c1e22ff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        .popup-content--history .close-btn:hover {
            background: #e5e7eb;
        }
        /* === End Popup === */


        /* === CSS เฉพาะสำหรับหน้า Ranking === */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        h1 {
            text-align: center;
            color: #1d4ed8;
            margin-bottom: 25px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        .ranking-table thead th {
            background-color: #f4f4f4;
            font-weight: 700;
            color: #333;
        }

        .game-image-table {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
            vertical-align: middle;
        }

        .rank-number-table {
            font-size: 1.2em;
            font-weight: 700;
            color: #555;
            text-align: center;
            width: 50px;
        }

        .ranking-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .ranking-table tbody tr:last-child td {
            border-bottom: none;
        }

        .no-data-cell {
            text-align: center;
            padding: 20px;
            color: #888;
            font-style: italic;
        }

        .game-type-badge {
            background-color: #e0e0e0;
            color: #333;
            font-size: 0.85em;
            padding: 3px 8px;
            border-radius: 12px;
            white-space: nowrap;
        }
        /* === End CSS Ranking === */

    </style>
</head>

<body>

    <head class="head-content">
        <nav>
            <ul class="navbar-content">
                <li>
                    <img class="logo" src="../../image//bgrmutt.jpg" alt="">
                </li>
                <ul class="menu-content">

                    <li><a href="../booking/homepage.php">Home</a></li>
                    <li><a href="../catalogue/page_catalogue.php">Category</a></li>
                    <li><a href="hot_ranking.php" class="active">Hot</a></li> <li><a href="../contact.php">Contact</a></li> </ul>

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

                <div class="controls">
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
    <div class="container">
        <h1>อันดับบอร์ดเกมยอดนิยม</h1>
        
        <table class="ranking-table">
            <thead>
                <tr>
                    <th>อันดับ</th>
                    <th colspan="2">เกม</th>
                    <th>ประเภท</th>
                    <th>จำนวนการจอง</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rankings)): ?>
                    <tr>
                        <td colspan="5" class="no-data-cell">ยังไม่มีข้อมูลการจองครับ</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rankings as $index => $game): ?>
                        <tr>
                            <td class="rank-number-table">
                                <?php echo $index + 1; ?>
                            </td>

                            <td style="width: 80px;">
                                <?php
                                $imagePath = ltrim($game['image_url'], '/');
                                $fullImagePath = '../../' . $imagePath;
                                ?>
                                <img src="<?php echo htmlspecialchars($fullImagePath); ?>"
                                    alt="<?php echo htmlspecialchars($game['bgName']); ?>"
                                    class="game-image-table">
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($game['bgName']); ?></strong>
                                <br>
                                
                            </td>

                            <td>
                                <span class="game-type-badge">
                                    <?php echo htmlspecialchars($game['btname']); ?>
                                </span>
                            </td>

                            <td style="width: 150px;">
                                <?php echo $game['booking_count']; ?> ครั้ง
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


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
        fetchBookingHistory();
    }

    function closeHistoryPopup() {
        historyPopup.classList.remove('active');
    }

    async function fetchBookingHistory() {
        try {
            // *** แก้ไข Path ให้ถูกต้อง: ชี้ไปที่โฟลเดอร์ booking ***
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

</body>
</html>