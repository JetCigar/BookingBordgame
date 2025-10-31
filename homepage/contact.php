<?php
// 1. เริ่ม Session และดึงข้อมูลผู้ใช้ (เหมือน homepage.php)
session_start();
$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
$displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');
?>
<html>
    <html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="homepage_style.css"> <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
<style>
    

        /* === NAV BAR (แก้ไข) === */
        .head-content nav {
            position: sticky; /* <-- แก้ไขจาก center */
            top: 0;
            z-index: 50;
            backdrop-filter: saturate(160%) blur(4px);
        }

        /* แถบหลัก */
        .navbar-content {
            list-style: none;
            margin: 0;
            padding: 0 20px;
            height: 72px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;

            /* พื้นหลังไล่เฉดฟ้า */
            background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;

            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }

        
        /* โลโก้ */
        .logo {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* กลุ่มเมนูกลาง */
        .menu-content {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 28px;
            margin: 0;
            padding: 0 16px;
            flex: 1;
            justify-content: center;
        }

        /* ไอเท็มเมนู */
        .menu-content li > a{
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
        .menu-content li> a:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.10);
        }

        .menu-content li >a:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
            background: rgba(255, 255, 255, 0.18);
        }

        .menu-content li >a.active {
            background: rgba(255, 255, 255, 0.22);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);
        }

        /* ปุ่ม SignIn (ตัวสุดท้ายในแถว) */
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
        
        /* === (เพิ่มใหม่) CSS Dropdown และ Popups จาก homepage.php === */

        /* Dropdown menu */
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

        /* Popup */
        .popup {
            position: fixed;
            top: 100vh;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }
        
        .popup .overlay {  /* จางพื้นหลัง */
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 1;
            transition: opacity 100ms ease-in-out 200ms;
        }

        .popup .popup-content { /* popup ทั้งกล่อง*/
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

        /* Popup ยืนยันออกจากระบบ */
        .popup-content--confirm {
            display: grid; 
            grid-template-rows: auto auto; 
            gap: 24px; 
            text-align: center; 
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

        /* Popup ประวัติการจอง */
        .popup-content--history {
            max-width: 500px; 
            min-height: 200px;
            padding: 24px;
            display: grid; 
            gap: 16px;
            grid-template-rows: auto 1fr auto; 
            text-align: left;
        }

        .popup-content--history h2 {
            text-align: center;
            margin: 0 0 10px 0;
            color: #1f2937;
            font-weight: 700;
            font-size: 25px; 
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

        .popup-content--history .controls {
            display: flex; 
            justify-content: flex-end; 
            margin-top: 10px; 
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

        /* === CSS สำหรับเนื้อหา (โค้ดเดิมของคุณ) === */
        /* (ผมไม่ได้แก้ไขส่วนนี้) */
        .Top {
            /* ...สไตล์เดิมของคุณ... */
        }
        .container {
            /* ...สไตล์เดิมของคุณ... */
        }

</style>
</head>
<body>
    <head class="head-content">
        <nav>
            <ul class="navbar-content">
                <li>
                    <img class="logo" src="../image/bgrmutt.jpg" alt="">
                </li>
                <ul class="menu-content">
                    <li><a href="booking/homepage.php">Home</a></li>
                    <li><a href="catalogue/page_catalogue.php">Category</a></li>
                    <li><a href="ranking_forder/hot_ranking.php">Hot</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li> </ul>

                </li> <?php
                // (เพิ่มใหม่) ตรวจสอบว่า $displayName (ที่ดึงมาจาก Session) มีค่าหรือไม่
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
                            <li class="divider"></li> 
                            <li>
                                <a href="booking/logout.php" class="logout-link">ออกจากระบบ</a>
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
                        <a class="btn-sigin" href="Login_Registet_Member/Login_Register.php">
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
    </head>
     <div class="Top" >
        <div class = "Top-left">
             <p1>contact us</p1>
        </div>
        <div class = "Top-right">
             <p1>
                 <ul>
                     <li>โทรศัพท์ : 093-0130298 </li>
                       <li>Facebook : <a href="https://www.facebook.com/rmutt.boardgame.club">RMUTT Board Game Club</a> </li>
                 </ul>     
             </p1>
        </div>
    </div>
    <div class="container">
        <div class="about">
            <h1>ที่อยู่ห้องบอร์ดเกม มทร.ธัญบุรี
                 </h1>
            <p1>ชั้น 3 อาคารวิทยบริการ สำนักวิทยบริการและเทคโนโลยีสารสนเทศ มทร.ธัญบุรี
เลขที่  39 หมู่ 1 ถนนรังสิต-นครนายก ต. คลองหก อ. ธัญบุรี จ. ปทุมธานี 12110
โทร. 0-2549-3643</p1>
        </div>
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
                // *** แก้ไข Path (ไฟล์นี้อยู่ใน root) ***
                const response = await fetch('booking/api_get_booking_state.php'); 
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