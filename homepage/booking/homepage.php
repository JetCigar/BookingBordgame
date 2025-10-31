<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // ดึงsesion มาจาก login
    session_start();
    $displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
    $displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');
    $searchBg =  htmlspecialchars($_SESSION['bgName']  ?? ''); // เอาค่า ที่ search มาจาก search.php
    $bg_image =  htmlspecialchars($_SESSION['bg_image'] ?? '');
    $show_Avalible = htmlspecialchars($_SESSION['bg_state'] ?? '');
    $shouldHide = htmlspecialchars($_SESSION['shouldHide'] ?? '');

    //ranking no1
    $rank_1name = htmlspecialchars($_SESSION['rank_1BgName'] ?? '');
    $rank_1Image = htmlspecialchars($_SESSION['rank_1BgImg'] ?? '');

    unset($_SESSION['bgName'], $_SESSION['bg_image'], $_SESSION['bg_state']);
    $_SESSION['bgName']   = '';               // เช่น ไม่มีชื่อเริ่มต้น
    $_SESSION['bg_image'] = $rank_1Image; // รูปเริ่มต้น
    $_SESSION['bg_state'] = '';
    $_SESSION['shouldHide'] = true;
    ?>



    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>homepage</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <style>
        * {
            box-sizing: 0px;
            padding: 0px;
            margin: 0px;
            font-family: Kanit;
        }

        /* startpopup */
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
            max-width: 350px;
            background: #fff;
            padding-left: 50px;
            padding-right: 50px;
            padding-bottom: 20px;
            padding-top: 10px;
            border-radius: 20px;
            box-shadow: 0px 2px 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 300ms ease-in-out;
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
            justify-content: space-between;
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
            color: #3284ed;

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
            width: 95%;
            max-width: 360px;
            min-height: 260px;
            display: grid;
            grid-template-rows: auto 1fr auto;

        }

        .image-bg {
            display: flex;
            justify-content: center;
            /* จัดกึ่งกลางแนวนอน */
            align-items: center;
            /* (ถ้าต้องการกึ่งกลางแนวตั้งด้วย) */
        }


        .title-age {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        /* endpopup */



        /* popuptable */
        .table-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .table-card {
            padding: 10px;
            border: 1px solid #e9e9ef;
            border-radius: 10px;
            cursor: pointer;
            background: #fff;
        }

        .table-card[disabled] {
            opacity: .5;
            cursor: not-allowed;
        }

        .table-card.selected {
            outline: 2px solid #3284ed;
        }

        /* endpopuptable */


        .logo {
            width: 70px;
            height: 70px;
        }

        .navbar-content {
            list-style-type: none;
            height: 85px;
            background-color: #2563eb;
            display: flex;
            justify-content: space-between;
            color: white;
            align-items: center;
        }

        .menu-content {
            list-style-type: none;
            display: flex;
        }

        .menu-content li {
            margin-left: 60px;
        }

        .cat-row {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 8px 4px;
            scroll-snap-type: x proximity;
        }

        .cat-card {
            scroll-snap-align: start;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #e9e9ef;
            border-radius: 12px;
            padding: 10px 14px;
            text-decoration: none;
            color: #222;
            min-width: 170px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
        }

        .cat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
        }

        .icon {
            font-size: 18px;
        }


        .texts .title {
            font-weight: 600;
        }

        .title {
            text-align: center;
        }

        /* nav */
        /* === NAV BAR (เฉพาะส่วนนี้) ===================================== */

        /* คุม nav ทั้งบล็อกแบบติดบนสุด + ขอบล่างเบาๆ */
        .head-content nav {
            position: sticky;
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

        /* start table css */

        .table-card {
            padding: 10px;
            border: 1px solid #e9e9ef;
            border-radius: 10px;
            cursor: pointer;
            background: #fff;
            transition: background-color .2s, outline-color .2s, border-color .2s;
        }

        /* จุดสีเล็ก ๆ หน้าข้อความสถานะ */
        .table-card .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 6px;
            background: currentColor;
        }

        /* ว่าง (เขียว) */
        .table-card.free {
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .table-card.free .table-status {
            color: #166534;
        }

        /* กำลังจอง/ถูก hold (ส้มอ่อน) */
        .table-card.held {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .table-card.held .table-status {
            color: #9a3412;
        }

        /* ไม่ว่าง/จองแล้ว (แดงอ่อน) */
        .table-card.taken,
        .table-card[disabled] {
            background: #fee2e2;
            border-color: #fecaca;
            opacity: 1;
        }

        .table-card.taken .table-status {
            color: #991b1b;
        }

        /* การ์ดที่ผู้ใช้ “เลือก” */
        .table-card.selected {
            outline: 2px solid #1d4ed8;
            background: #dbeafe;
            /* ฟ้าอ่อนให้รู้ว่ากำลังเลือก */
        }

        /* แถบ legend สี */
        .legend {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 10px 0;
        }

        .legend .chip {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .legend .chip.free {
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        .legend .chip.held {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .legend .chip.taken {
            background: #fee2e2;
            border-color: #fecaca;
        }

        /* start end css */


        /* show avilavle card */
        .showavalible {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600
        }

        .showavalible .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #9ca3af;
            /* เทา (default) */
        }

        /* ใช้คลาสเพื่อคุมสี */
        .showavalible.is-free .dot {
            background: #16a34a;
        }

        /* เขียว */
        .showavalible.is-taken .dot {
            background: #dc2626;
        }

        /* แดง  */

        /* end show avilable card */

        /* .search-content {
            height: 500px;
            width: 100%;
            border: #111 1px solid;
        } */

        :root {
            /* --- สีหลักที่ใช้ทั้งบล็อก --- */
            --sc-ink: #0f172a;
            /* สีตัวอักษรหลัก */
            --sc-muted: #64748b;
            /* สีตัวอักษรรอง/คำอธิบาย */
            --sc-accent: #3284ed;
            /* ปุ่ม Buy Now (เขียวอมเหลือง) */
            --sc-sky: #0ea5e9;
            /* ปุ่ม Search (ฟ้า) */
            --sc-card: #ffffff;
            /* พื้นหลังการ์ด */
            --sc-ring: #00000014;
            /* เงาบางๆ/เส้นขอบโปร่ง */
        }

        .search-content {
            /* border: 1px solid #111; */
            padding: 16px;
        }


        /* =====================================================================
   HERO (ส่วนบน)
   - กริด 2 คอลัมน์: ซ้ายเนื้อหา / ขวารูปภาพ
   - ความกว้างคงที่ เพื่อให้ไม่ responsive ตามที่ต้องการ
===================================================================== */

        .sc-hero {
            width: 100%;
            border-radius: 20px;
            background: radial-gradient(1400px 500px at 25% -10%, #eef4ff 0%, #f7fafc 40%, #ffffff 72%);
            box-shadow: 0 10px 40px var(--sc-ring);
            overflow: hidden;
        }

        /* กล่องในสุดของ hero
   NOTE: อยากให้บล็อกทั้งก้อนแคบ/กว้างขึ้น ปรับที่ width ตรงนี้จุดเดียว */
        .sc-wrap {
            width: 1160px;
            /* ความกว้างคงที่ */
            margin: 0 auto;
            min-height: 500px;
            /* ความสูงขั้นต่ำของ hero */
            padding: 28px 18px;

            display: grid;
            grid-template-columns: 660px 420px;
            /* ซ้าย 660px / ขวา 420px */
            column-gap: 40px;
            align-items: center;
            /* จัดแนวให้เนื้อหาอยู่กึ่งกลางแนวตั้ง */
        }

        /* ข้อความหัวเรื่อง / บรรทัดรอง */
        .sc-title {
            font-size: 58px;
            line-height: 1.05;
            margin: 6px 0;
            color: #111827;
            font-weight: 800;
        }

        .sc-sub {
            color: var(--sc-muted);
            margin-bottom: 14px;
        }

        /* ปุ่มคู่ใต้หัวเรื่อง */
        .sc-actions {
            display: flex;
            gap: 12px;
            margin: 8px 0 22px;
        }

        .sc-btn {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 1px 0 var(--sc-ring);
        }

        .sc-btn--ghost {
            background: #fff;
            color: #111827;
        }

        .sc-btn--accent {
            background: var(--sc-accent);
            color: #111827;
        }

        .sc-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px var(--sc-ring);
        }

        /* แถบค้นหา (ฟอร์ม + ช่องพิมพ์ + ปุ่ม) */
        .sc-searchbar {
            width: 620px;
            /* ความกว้างคงที่ของ searchbar */
            background: #ffffffcc;
            backdrop-filter: saturate(150%) blur(6px);
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 14px 36px var(--sc-ring);
        }

        .sc-searchbar form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .sc-input {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 10px 14px;
        }

        .sc-input svg {
            width: 18px;
            height: 18px;
            opacity: .6;
        }

        .sc-input input {
            border: 0;
            outline: 0;
            width: 100%;
            font-size: 16px;
            background: transparent;
        }

        .sc-go {
            border: 0;
            border-radius: 999px;
            padding: 10px 18px;
            background: var(--sc-sky);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 0 #0000000b;
        }

        .sc-go:hover {
            filter: brightness(1.05);
        }

        /* รูปโปรโมตด้านขวา */
        .sc-right {
            display: flex;
            justify-content: center;
        }

        .sc-hero-figure {
            width: 70%;
            /* ความกว้างคงที่ของรูปฝั่งขวา */
            /* aspect-ratio: 3 / 4; */
            border-radius: 20px;
            overflow: hidden;
            margin: 0;
            border: 0px solid #e5e7eb;
            box-shadow: 0 16px 48px #00000022;
        }

        .sc-hero-figure img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* =====================================================================
   RESULTS (ส่วนล่าง)
   - แสดงเฉพาะเมื่อมีพารามิเตอร์ q
   - กริด 6 คอลัมน์คงที่
===================================================================== */

        .sc-results {
            width: 1160px;
            margin: 18px auto 0;
            padding: 0 0 18px;
        }

        .sc-results-head {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 12px;
        }

        .sc-results-head h3 {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .sc-count {
            color: var(--sc-muted);
        }

        .sc-empty {
            color: var(--sc-muted);
            padding: 16px 0;
        }

        /* กำหนดกริด 6 ช่องคงที่ (180px ต่อช่อง) */
        .sc-grid {
            display: grid;
            grid-template-columns: 180px 180px 180px 180px 180px 180px;
            gap: 14px;
        }

        /* การ์ดแต่ละใบในผลลัพธ์ */
        .sc-card {
            width: 180px;
            background: var(--sc-card);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px var(--sc-ring);
        }

        .sc-cover {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }

        .sc-card-body {
            padding: 10px;
        }

        .sc-card-title {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 6px;
        }

        .sc-status {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--sc-muted);
            font-size: 12px;
        }

        .sc-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #cbd5e1;
            display: inline-block;
        }

        .sc-dot.is-green {
            background: #16a34a;
        }

        /* ว่าง */
        .sc-dot.is-red {
            background: #dc2626;
        }


        /* title  newarive */
        /* party-headings.css */
        /* ใช้ร่วมกับตัวแปรที่คุณมีอยู่แล้ว: --sc-ink, --sc-accent, --sc-sky, --sc-card, --sc-ring */
        :root {
            --ink: var(--sc-ink, #0f172a);
            --accent: var(--sc-accent, #bef264);
            /* เหลือง-เขียว */
            --sky: var(--sc-sky, #0ea5e9);
            /* ฟ้า */
            --card: var(--sc-card, #fff);
            --ring: var(--sc-ring, #00000014);
        }

        /* ตัวหัวข้อหลัก */
        .party-title {
            --title-accent: var(--sky);
            /* ค่าปริยาย (เปลี่ยนได้ใน modifier) */
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px 12px 12px;
            margin: 28px 0 14px;
            color: var(--ink);
            font-weight: 900;
            font-size: clamp(20px, 2.6vw, 30px);
            line-height: 1.1;
            background: var(--card);
            border-radius: 999px;
            box-shadow: 0 8px 24px var(--ring);
            isolation: isolate;
            /* ให้เงา/เอฟเฟกต์ซ้อนสวยขึ้น */
        }



        /* ข้อความ */
        .party-title span {
            letter-spacing: .3px;
        }

        /* ตัวแปรเฉพาะหัวข้อ */
        .party-title.--category {
            --title-accent: var(--sky);
        }

        .party-title.--new {
            --title-accent: var(--accent);
        }

        .party-title.--new::before {
            content: "✨";
        }

        /* ไอคอนเปลี่ยนให้รู้ว่าเป็น “ใหม่” */

        /* end title nearive */


        .cat-card .open-popup {
            background-color: #0ea5e9;
        }
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
                    <li><a href="">Category</a></li>
                    <li><a href="../ranking_forder/hot_ranking.php">Hot</a></li>
                    <li><a href="../../homepage/contact.html">Contact</a></li>
                </ul>
                <!-- <li><a class="btn-sigin" href="../Login_Registet_Member/Login_Register.php">SignIn</a></li>btn sigIn -->
                <div class="title"><?= htmlspecialchars($displayName) ?></div>
            </ul>
        </nav>
    </head>
    <main>
        <div class="search-content">
            <section class="sc-hero">
                <div class="sc-wrap">
                    <div class="sc-left">
                        <h1 <?= $shouldHide ? '' : 'style="display:none;"' ?>>มาเเรง อันดับ 1</h1>
                        <h1>ค้นหาเกม</h1>
                        <h1 data-rank_1Image="<?= htmlspecialchars($rank_1Image, ENT_QUOTES, 'UTF-8') ?>" style="display: none;"></h1>
                        <h1 id="searchBg" style="display: none;" data-searchbg="<?= htmlspecialchars($searchBg, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($searchBg) ?></h1>
                        <h2 style="color:#3284ed" id="sc-title"><?= htmlspecialchars($searchBg, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="showavalible" <?= $shouldHide ? 'style="display:none;"' : '' ?>> <?= htmlspecialchars($show_Avalible) ?></p>
                        <div class="sc-actions">
                            <button type="button" class="sc-btn sc-btn--ghost" <?= $shouldHide ? 'style="display:none;"' : '' ?>>More Info</button>
                            <button type="button" class="sc-btn sc-btn--accent" <?= $shouldHide ? 'style="display:none;"' : '' ?>>Boo kNow</button>
                        </div>

                        <div class="sc-searchbar">
                            <form method="get" action="searchbg.php">
                                <div class="sc-input">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <circle cx="11" cy="11" r="7" stroke-width="2" fill="none" stroke="currentColor"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2" stroke="currentColor"></line>
                                    </svg>
                                    <input id="q" type="search" name="q" placeholder="Search book..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                </div>
                                <button class="sc-go" type="submit" onclick="search_Bg()">Search</button>
                                <!-- <h1><?= htmlspecialchars($search) ?></h1> -->
                            </form>
                        </div>
                    </div>

                    <div class="sc-right">
                        <figure class="sc-hero-figure">
                            <!-- <img src="../../../BookingBordgame/public/werewolf.jpg" alt="Featured cover"> -->
                            <img id="hot-top-img" <?= $shouldHide ? 'style="display:;"' : '' ?> style="max-wide:100%; height:100%; object-fit:cover" src="/BookingBordgame/<?= htmlspecialchars($bg_image, ENT_QUOTES, 'UTF-8') ?>" />
                        </figure>
                    </div>

                </div>
            </section>
        </div>
        <h2 class="party-title --category"><span>BoardGame Category</span></h2>
        <div>
            <?php

            $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $types = $pdo->query("SELECT btId, btName FROM bordgameType ORDER BY btName")->fetchAll();
            ?>
            <section>
                <div class="cat-row">
                    <?php foreach ($types as $t): ?>
                        <a class="cat-card" <?= (int)$t['btId'] ?>">
                            <div class="texts">
                                <div class="title"><?= htmlspecialchars($t['btName']) ?></div>

                                <!-- ถ้ายังไม่มีจำนวน ให้ซ่อนไว้ก่อน -->
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <div class="party-title --new"><span>New Arrivals</span></div>
        <div>
            <?php
            $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $types = $pdo->query("SELECT boradgame.bgid,boradgame.bgName,boradgame.state,bordgamedescription.image_url,bordgamedescription.bdId,bordgamedescription.bddescript,bordgamedescription.bdage,TIME_FORMAT(bordgamedescription.bdtime, '%H:%i:%s') AS bdtime
                                            FROM boradgame
                                            INNER JOIN bordgamedescription
                                            ON boradgame.bdId = bordgamedescription.bdId ORDER BY bdId DESC")->fetchAll();

            $tables = $pdo->query("SELECT tableId, state FROM tableroom ORDER BY tableId")->fetchAll();

            // $bgstate = $pdo->query("SELECT state FROM boradgame WHERE bdId=?")->fetchAll();

            ?>
            <section>
                <div class="cat-row">
                    <?php foreach ($types as $t):
                        $img = $t['image_url'] ?? '';
                        $src = $img !== '' ? '/BookingBordgame/' . ltrim($img, '/') : '';
                    ?>

                        <button
                            type="button"
                            class="cat-card open-popup"
                            style="border:none;background:none;width:200px;padding:0px 200px 0px 200px; box-shadow:1px 1px 8px 1px rgba(66, 66, 66, 0.87);"
                            data-bgid="<?= htmlspecialchars($t['bgid'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bgname="<?= htmlspecialchars($t['bgName'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bddescription="<?= htmlspecialchars($t['bddescript'], ENT_QUOTES, 'UTF-8') ?>"
                            data-image="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                            data-age="<?= htmlspecialchars($t['bdage'], ENT_QUOTES, 'UTF-8') ?>"
                            data-time="<?= htmlspecialchars($t['bdtime'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bdid=" <?= (int)$t['bdId'] ?>">
                            <div class="texts" style="margin-left:-120px;">
                                <div>
                                    <?php if ($src): ?>
                                        <img style="width:250px; height:auto; object-fit:cover" src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                                            loading="lazy" decoding="async"
                                            style="max-width:300px;aspect-ratio:16/9;object-fit:cover;border-radius:12px">
                                    <?php endif; ?>
                                </div>

                                <div class="title"><?= htmlspecialchars($t['bgName']) ?></div>
                                <!-- //เพิ่ม data ลงไปเพื่อ update Dom เเบบ ทุก 2-3 วินาที -->
                                <div class="showavalible" data-bgid="<?= (int)$t['bgid'] ?>"><?= htmlspecialchars($t['state']) ?></div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>



        <!-- hidden formsubmit -->
        <form id="confirmForm" action="../booking/memberbooking.php" method="post">
            <input type="hidden" name="table_id" id="table_id">
            <!-- <input type="hidden" name="game_id" id="game_id"> -->
            <input type="hidden" name="game_name" id="game_name">
            <input type="hidden" name="bgd_Id" id="bgd_Id">
            <input type="hidden" name="bgid" id="bgid">
        </form>


        <aside>
            <div class="center">
            </div>

            <div id="popup" class="popup">
                <div class="overlay"></div>
                <div class="popup-content">
                    <h2 id="popup-title"></h2>
                    <div class="popup-body">
                        <div class="step">

                            <div class="image-bg" style="max-width:100%; align-items:center;">
                                <img id="popup-image" style="max-width:100%;border-radius:12px;display:none">
                            </div>
                            <p id="popup-desc" style="font-size: 16px; margin:1rem 1rem">หน้า 1: ข้อความแนะนำ…</p>
                            <div class="title-age" style="display: flex;">
                                <h1 style="font-size: 20px;">อายุ</h1>
                                <h1 id="popup-age" style="font-size: 20px;">age</h1>
                            </div>
                            <h1 style="font-size: 25px;">เวลาในการเล่น</h1>
                            <h1 id="popup-time" style="font-size: 16px;">time</h1>
                            <ul style="list-style-type: none; display:flex; justify-content:center;">
                                <li style="padding: 0rem 0.25rem 0rem 0.25rem; color:#909090;">ชั่วโมง</li>
                                <li style="padding: 0rem 0.25rem 0rem 0.25rem;color:#909090;">นาที</li>
                                <li style="padding: 0rem 0.25rem 0rem 0.25rem;color:#909090;">วินาที</li>
                                <li id="popup-bdid" style="display:none">bdid</li>
                                <li id="popup-bgid" style="display:none">bgid</li>
                            </ul>
                        </div>

                        <div class="step" id="step2">
                            <h2>กรุณาเลือกโต๊ะที่ต้องการนั่ง</h2>
                            <!-- legend สี -->
                            <div class="legend">
                                <span class="chip free">ว่าง</span>
                                <span class="chip held">กำลังจอง</span>
                                <span class="chip taken">ไม่ว่าง</span>
                            </div>


                            <div id="table-list" class="table-grid">
                                <?php foreach ($tables as $tb):
                                    $available = ((int)$tb['state'] === 1);
                                    // ค่อยทำ 'HELD' ให้มีแค่ free/taken ไปก่อน 
                                    $statusClass = $available ? 'free' : 'taken';
                                    $label       = $available ? 'ว่าง' : 'ไม่ว่าง';
                                ?>
                                    <button type="button"
                                        class="table-card <?= $statusClass ?>"
                                        data-table-id="<?= (int)$tb['tableId'] ?>"
                                        data-status="<?= $statusClass ?>"
                                        <?= $available ? '' : 'disabled' ?>>
                                        <div class="table-name">โต๊ะ<?= (int)$tb['tableId'] ?></div>
                                        <div class="table-status"><span class="status-dot"></span><?= $label ?></div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <p>เลือกแล้ว: <strong id="picked">-</strong></p>
                            <input type="hidden" name="table_id" id="table_id">
                        </div>

                        <!-- pickup table -->



                        <div class="step">
                            <h1 style="color: #fcca0c;">ยืนยันการยืม</h1>
                            <!-- <div style="display:flex;justify-content:space-between;margin:auto">
                                <p>ชื่อ</p>
                                <p></p>
                            </div> -->
                            <table style="display: flex;justify-content:center; font-size:25px">

                                <tr>
                                    <h2>หมายเลขบัตรประชาชน</h2>
                                </tr>
                                <tr style="display: flex;justify-content:center; font-size:25px">
                                    <td style="display: flex;"><?= htmlspecialchars($displayperson_id) ?></td>
                                </tr>
                                <tr style="display: flex;justify-content:space-between;">
                                    <td style="margin-right:10px;">คุณ</td>
                                    <td style="display: flex;"><?= htmlspecialchars($displayName) ?></td>
                                </tr>
                                <tr style="display: flex; justify-content:space-between;">
                                    <td>โต๊ะที่จอง</td>
                                    <td>
                                        <p id="table"></p>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                    <div class="controls">
                        <button class="close-btn">close</button>
                        <button class="perv-btn">back</button>
                        <button class="submit-btn next-btn">next</button>
                    </div>
                </div>
            </div>

            <!--  startสำหรับ ฟังก์ชั่น searchBg -->
            <script>
                // goble scope val
                var val = "";
                const qInput = document.querySelector('input[name="q"]');
                qInput.addEventListener('input', () => {
                    val = qInput.value.trim();
                });

                const searchBg = document.getElementById('searchBg');
                const sc_title = document.getElementById('sc-title');
                const vrsearch = searchBg.dataset.searchbg || ""; // ค่า "ที่ซ่อน" ใน tag ดึงมา

                // const bdid = trigger.dataset.bdid || "";
                function search_Bg() {
                    if (vrsearch == val) {
                        console.log("พบข้อูล")
                        sc_title.textContent = val;
                        console.log(sc_title.textContent);
                    } else {
                        console.log("ไม่พบข้อมูล");
                        console.log("งับๆ");
                        console.log(sc_title.textContent);

                    }
                }
            </script>
            <!-- end search -->


            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.showavalible').forEach(el => {
                        const raw = el.textContent.trim();
                        const n = parseInt(raw, 10);

                        // เคลียร์ของเดิม
                        el.textContent = '';
                        // สร้างองค์ประกอบใหม่
                        const dot = document.createElement('span');
                        dot.className = 'dot';
                        const label = document.createElement('span');
                        label.className = 'state-label';
                        if (n === 1) {
                            el.classList.add('is-free');
                            label.textContent = 'ว่าง';
                        } else if (n === 0) {
                            el.classList.add('is-taken');
                            label.textContent = 'ไม่ว่าง';
                        } else {
                            label.textContent = raw || '—'; // กรณีไม่ใช่ 0/1
                        }
                        el.append(dot, label);
                    });
                });
            </script>
            <!-- end show avilible -->



            <script>
                // ===== popup core (ของเดิม) =====
                function createPopup(id) {
                    let popupNode = document.querySelector(id);
                    let overlay = popupNode.querySelector(".overlay");
                    let closeBtn = popupNode.querySelector(".close-btn");
                    let prevBtn = popupNode.querySelector(".perv-btn");
                    let steps = Array.from(popupNode.querySelectorAll('.step'));
                    let nextBtn = popupNode.querySelector('.next-btn');
                    let currenIndex = 0;
                    const titleEl = popupNode.querySelector("#popup-title");


                    function showStep(i) {
                        steps.forEach((el, idx) => {
                            if (idx === i) {
                                if (closeBtn) closeBtn.hidden = (i !== 0); // หน้าแรก: โชว์ close / หน้าอื่น: ซ่อน
                                if (prevBtn) prevBtn.hidden = (i === 0);
                                el.hidden = false;
                                el.classList.add('is-active');

                            } else {
                                el.hidden = true;
                                el.classList.remove('is-active');
                            }
                        });

                        if (nextBtn) {
                            nextBtn.textContent = (i === steps.length - 1) ? 'Book' : 'next';
                        }


                        // สลับหน้า โต๊ะ
                        // if (titleEl) {
                        //     titleEl.textContent = (i === 1) ?
                        //         'กรุณาเลือกโต๊ะที่ต้องการนั่ง' :
                        //         (titleEl.dataset.baseTitle || titleEl.textContent);
                        // }
                    }


                                if (nextBtn) {
                                    nextBtn.addEventListener('click', () => {
                                        if (currenIndex < steps.length - 1) {
                                            currenIndex += 1;
                                            showStep(currenIndex);

                                        } else {
                                            confirmToForm();
                                            document.getElementById('confirmForm').requestSubmit();
                                            closePopup();
                                        }
                                    });
                                }

                                if (prevBtn) {
                                    prevBtn.addEventListener('click', () => {
                                        if (currenIndex > 0) {
                                            currenIndex -= 1;

                                            showStep(currenIndex);
                                        }
                                    });
                                }

                                function openPopup() {
                                    popupNode.classList.add("active");
                                    currenIndex = 0;
                                    confirmToForm()
                                    if (steps.length) showStep(0);
                                }

                                function closePopup() {
                                    popupNode.classList.remove("active");
                                }
                                overlay.addEventListener("click", closePopup);
                                closeBtn.addEventListener("click", closePopup);
                                return openPopup;
                            }

                            const openPopup = createPopup("#popup");

                            // ===== อัปเดตข้อมูล + เปิด popup เมื่อคลิกการ์ด =====
                            document.addEventListener("click", (e) => {
                                const trigger = e.target.closest(".open-popup");
                                if (!trigger) return;

                                const name = trigger.dataset.bgname || "";
                                const desc = trigger.dataset.bddescription || "";
                                const img = trigger.dataset.image || "";
                                const age = trigger.dataset.age || "";
                                const time = trigger.dataset.time || "";
                                const bdid = trigger.dataset.bdid || "";
                                const bgid = trigger.dataset.bgid || "";
                                // const table = trigger.dataset.data-table-id || "";
                                // ใส่ลง DOM
                                const titleEl = document.getElementById("popup-title");
                                const descEl = document.getElementById("popup-desc");
                                const ageEl = document.getElementById("popup-age");
                                const imgEl = document.getElementById("popup-image");
                                const timeEl = document.getElementById("popup-time");
                                const bdidEl = document.getElementById("popup-bdid");
                                const bgidEl = document.getElementById("popup-bgid");

                                if (titleEl) titleEl.textContent = name;
                                if (descEl) descEl.textContent = desc;
                                if (ageEl) ageEl.textContent = age;
                                if (timeEl) timeEl.textContent = time;
                                if (bdidEl) bdidEl.textContent = bdid;
                                if (bgidEl) bgidEl.textContent = bgid;

                                if (imgEl) {
                                    if (img) {
                                        imgEl.src = img;
                                        imgEl.alt = name || "boardgame";
                                        imgEl.style.display = "block";
                                    } else {
                                        imgEl.removeAttribute("src");
                                        imgEl.style.display = "none";
                                    }
                                }

                                openPopup(); // เปิด popup หลังอัปเดตข้อมูลเรียบร้อย
                            });
            </script>
            <script>
                // ไฮไลต์การ์ดโต๊ะที่ผู้ใช้เลือก + เปิดปุ่ม next เมื่อเลือกแล้ว
                let selectedTableId = null;

                const tableListEl = document.getElementById('table-list');

                if (tableListEl) {
                    tableListEl.addEventListener('click', (e) => {
                        const card = e.target.closest('.table-card');
                        if (!card) return;

                        // กันคลิกการ์ดที่ไม่ว่าง/ถูก hold
                        const status = card.dataset.status;
                        if (card.hasAttribute('disabled') || status === 'taken' || status === 'held') return;

                        // ล้างตัวเลือกเก่า + ตั้งตัวใหม่
                        tableListEl.querySelectorAll('.table-card.selected').forEach(el => el.classList.remove('selected'));
                        card.classList.add('selected');
                        selectedTableId = card.dataset.tableId;

                        // เปิดปุ่ม next
                        const nextBtnEl = document.querySelector('#popup .next-btn');

                        if (nextBtnEl) nextBtnEl.disabled = false;
                    });
                }

                // เคลียร์ค่าเมื่อเปิด popup รอบใหม่
                function resetTableSelection() {
                    selectedTableId = null;
                    document.querySelectorAll('#table-list .selected').forEach(el => el.classList.remove('selected'));
                    const nextBtnEl = document.querySelector('#popup .next-btn');
                    if (nextBtnEl) nextBtnEl.disabled = true;
                }

                // เรียกใช้ reset ตอนเปิด popup (สอดกับฟังก์ชันของคุณ)
                const _openPopupOrig = openPopup;
                window.openPopup = function() {
                    resetTableSelection();
                    _openPopupOrig();
                };
            </script>



            <script>
                const list = document.getElementById('table-list');
                const pickedEl = document.getElementById('picked');
                const hiddenInp = document.getElementById('table_id');
                const savetable = document.getElementById('table');
                const hidTime = document.getElementById('clicked_at');
                const pickedTimeEl = document.getElementById('picked-time');

                list.addEventListener('click', (e) => {
                    const btn = e.target.closest('.table-card'); // ปุ่มที่ถูกคลิกจริง
                    if (!btn) return;

                    // กันคลิกโต๊ะที่ไม่ว่าง
                    if (btn.hasAttribute('disabled') || btn.dataset.status !== 'free') return;

                    // ไฮไลต์ตัวที่เลือก (ตัวเลือก)
                    list.querySelectorAll('.table-card.selected').forEach(el => el.classList.remove('selected'));
                    btn.classList.add('selected');


                    // เอาค่าโต๊ะไปใช้ต่อ
                    const id = btn.dataset.tableId; // << ค่าที่ต้องการ
                    pickedEl.textContent = id; // โชว์บนหน้า
                    hiddenInp.value = id; // เก็บไว้ส่งต่อ (เช่น submit ฟอร์ม)
                    console.log('เลือกโต๊ะ:', id);
                    savetable.textContent = id;

                });
            </script>

            <script>
                function confirmToForm() {
                    // const bgId = 
                    const bgName = document.getElementById('game_name').value = document.getElementById('popup-title').textContent.trim();
                    const bgdId = document.getElementById('bgd_Id').value = document.getElementById('popup-bdid').textContent.trim();
                    const bgId = document.getElementById('bgid').value = document.getElementById('popup-bgid').textContent.trim();
                    // const gameNameEl = document.getElementsByName('open-popup');
                    // const gameName = gameNameEl.dataset.id;                    
                }

                function selectbdId(e) {
                    const btn = e.target.closest('[data-bdid]'); // ใช้ selector แบบ [attr]
                    if (!btn) return;
                    const bdid = btn.dataset.bdid;
                    console.log(bdid)
                }
            </script>



            <!-- //json ranking -->
            <script>
                async function loadRanking() {
                    try {
                        const res = await fetch('ranking.php', {
                            cache: 'no-store'
                        });
                        console.log('HTTP status:', res.status);
                        const json = await res.json(); // <- อ่านครั้งเดียวพอ
                        const object = json.data;
                        console.log('json:', json);
                        const top = object[0];
                        // ตรวจเช็ค path
                        function toSrc(p) {
                            if (!p) return '';
                            return /^https?:\/\//.test(p) ? p : ('/BookingBordgame/' + p.replace(/^\/+/, ''));
                        }
                        const img = document.getElementById('hot-top-img');
                        // img.src = toSrc(top.image_url);
                        // console.log(top.image_url);
                        // if (!json.ok) return;
                        // const data = json.data || [];
                        // console.table(data);
                    } catch (err) {
                        console.error('loadRanking error:', err);
                    }
                }
                loadRanking();
            </script>

            <!-- //loaded stateBg -->
            <script>
                async function loaded_stateBg() {
                    try {
                        const res = await fetch('realtimeState.php', {
                            cache: 'no-store'
                        });
                        console.log('HTTP status:', res.status);
                        const json = await res.json(); // <- อ่านครั้งเดียวพอ
                        console.log('json:', json.stateBg[0]);

                        // const test = state_Object[0];
                        //ตรวจเช็ค path
                        // function toSrc(p) {
                        //     if (!p) return '';
                        //     return /^https?:\/\//.test(p) ? p : ('/BookingBordgame/' + p.replace(/^\/+/, ''));
                        // }
                        // const img = document.getElementById('hot-top-img');
                        // img.src = toSrc(top.image_url);
                        // console.log(top.image_url);
                        // if (!json.ok) return;
                        // const data = json.data || [];
                        // console.table(data);

                    } catch (err) {
                        console.error('loadRanking error:', err);
                    }
                }
                loaded_stateBg();
            </script>



            <!-- //ดึง state 2-3 วินาที -->
            <script>
                function applyState(el, state) {
                    const lab = el.querySelector('.state-label');
                    el.classList.remove('is-free', 'is-taken');
                    if (state === 1) {
                        el.classList.add('is-free');
                        if (lab) lab.textContent = 'ว่าง';
                    } else if (state === 0) {
                        el.classList.add('is-taken');
                        if (lab) lab.textContent = 'ไม่ว่าง';
                    } else {
                        if (lab) lab.textContent = '—';
                    }
                }

                async function refreshStates() {
                    try {
                        const res = await fetch('/BookingBordgame/homepage/booking/realtimeState.php', {
                            cache: 'no-store'
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);

                        const json = await res.json(); // { ok, stateBg: [ {bgId, state}, ... ] }
                        if (!json.ok) throw new Error(json.error || 'unknown error');

                        const map = Object.fromEntries(json.stateBg.map(x => [String(x.bgId), Number(x.state)]));

                        document.querySelectorAll('.showavalible').forEach(el => {
                            const bgid = el.dataset.bgid || el.closest('[data-bgid]')?.dataset.bgid;
                            if (!bgid) return;
                            const st = map[String(bgid)];
                            if (st === 0 || st === 1) applyState(el, st);
                        });
                    } catch (err) {
                        console.error('refreshStates error:', err);
                    }
                }

                // โหลดครั้งแรกและรีเฟรชทุก 3 วิ
                refreshStates();
                setInterval(refreshStates, 3000);

                // ถ้าเพิ่งกลับมาโฟกัสหน้าจอ ให้รีเฟรชทันที
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) refreshStates();
                });

                // TIP: ถ้ามีปุ่มยืม/คืนที่ยิง AJAX สำเร็จ ให้เรียก refreshStates() ทันทีหลังอัปเดต
            </script>


            <!-- table -->
            <script>
                function initStatusLabels(scope = document) {
                    scope.querySelectorAll('.table-card .table-status').forEach(box => {
                        if (!box.querySelector('.status-label')) {
                            const label = document.createElement('span');
                            label.className = 'status-label';
                            // ถ้ามีข้อความเก่า "ว่าง/ไม่ว่าง" เป็น text node ให้ห่อแทนที่
                            const txt = Array.from(box.childNodes)
                                .find(n => n.nodeType === Node.TEXT_NODE && n.textContent.trim() !== '');
                            if (txt) {
                                label.textContent = txt.textContent.trim();
                                box.replaceChild(label, txt);
                            } else {
                                box.appendChild(label);
                            }
                        }
                    });
                }
            </script>



            <!-- //ดึงโต๊ะทุกๆ 2-3 วินาที -->
            <script>
                const API = '/BookingBordgame/homepage/booking/tables_status.php';

                function setTableStatus(card, status) {
                    const box = card.querySelector('.table-status') || card;
                    initStatusLabels(card); // แน่ใจว่ามี label แล้ว
                    const label = box.querySelector('.status-label');

                    card.classList.remove('free', 'taken', 'held');
                    card.classList.add(status);

                    label.textContent =
                        status === 'free' ? 'ว่าง' :
                        status === 'taken' ? 'ไม่ว่าง' :
                        status === 'held' ? 'กำลังจอง' : '—';

                    card.disabled = (status !== 'free');
                    card.dataset.status = status;
                }

                async function refreshTables() {
                    try {
                        const res = await fetch(API + '?ts=' + Date.now(), {
                            cache: 'no-store'
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const json = await res.json(); // { ok:true, tables:[{tableId,status}] }
                        if (json.ok === false) throw new Error(json.error || 'server error');

                        const map = new Map((json.tables || []).map(t => [String(t.tableId), t.status]));

                        //  สร้าง label ให้ครบก่อน แล้วอัปเดต "ทุกใบ"
                        initStatusLabels();
                        document.querySelectorAll('.table-card[data-table-id]').forEach(card => {
                            const id = card.dataset.tableId;
                            const next = map.get(String(id));
                            if (!next) return; // ไม่มีใน JSON ก็ข้าม
                            setTableStatus(card, next); // ⬅️ อัปเดตทุกครั้ง
                        });
                    } catch (e) {
                        console.error('refreshTables error:', e);
                    }
                }

                // โหลดครั้งแรก + ทุก 3 วิ + กลับมาโฟกัสหน้า
                document.addEventListener('DOMContentLoaded', () => {
                    initStatusLabels();
                    refreshTables();
                });
                setInterval(refreshTables, 3000);
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) refreshTables();
                });
            </script>

        </aside>

    </main>
    <footer>
    </footer>
</body>

</html>