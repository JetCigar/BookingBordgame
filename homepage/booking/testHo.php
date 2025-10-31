<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // ดึงsesion มาจาก login
    session_start();
    $displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
    $displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');
    $searchBg =  htmlspecialchars($_SESSION['bgName']  ?? ''); // เอาค่า ที่ search มาจาก search.php
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
            max-width: 350px;
            background: #fff;
            padding-left: 50px;
            padding-right: 50px;
            padding-bottom: 20px;
            border-radius: 20px;
            box-shadow: 0px 2px 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 300ms ease-in-out;
        }

        .popup .popup-content h2 {
            font-size: 1.25rem;
             font-weight: 700;
             color: #1f2937;
             margin: 0;
            line-height: 1.4;

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

        #popup .popup-content .controls .close-btn {
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
            width: 100%;
            /* ความกว้างคงที่ของรูปฝั่งขวา */
            /* aspect-ratio: 3 / 4; */
            border-radius: 20px;
            overflow: hidden;
            margin: 0;
            border: 1px solid #e5e7eb;
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

        /* =============================dropdown menu===================================================== */

        

/* 1. ตัวครอบ (Container) - ต้องเป็น relative */
/* เราจะใช้คลาสนี้กับ <li> ที่เป็นตัวหุ้ม */
.profile-dropdown {
    position: relative; /* สำคัญมาก: เพื่อให้เมนู .dropdown-menu วางตำแหน่งโดยอ้างอิงจากตัวนี้ */
}

/* 2. ตัวคลิก (Trigger) - ชื่อผู้ใช้ */
/* เราจะปรับแต่ง <button> หรือ <a> ที่ใช้เป็นตัวคลิก */
.profile-trigger {
    display: flex;
    align-items: center;
    gap: 8px; /* ระยะห่างระหว่างชื่อกับลูกศร */
    cursor: pointer;
    user-select: none; /* ป้องกันการเลือกข้อความเวลาคลิก */
    background: none;
    border: none;
    padding: 8px 12px;
    border-radius: 999px;
    transition: background-color 0.15s ease;

    /* สไตล์จากโค้ดเดิมของคุณเพื่อให้เข้ากัน */
    color: white; 
    font-weight: 600;
    font-family: 'Kanit', sans-serif;
    font-size: 16px;
}

/* เพิ่มลูกศรเล็กๆ เพื่อบ่งบอกว่าคลิกได้ */
.profile-trigger::after {
    content: '▼';
    font-size: 10px;
    transform: translateY(1px); /* จัดตำแหน่งลูกศรให้สวยงาม */
    opacity: 0.8;
}

/* เอฟเฟกต์เมื่อ hover */
.profile-trigger:hover {
    background: rgba(255, 255, 255, 0.16);
}

/* 3. ตัวเมนู (Menu) - ซ่อนไว้ก่อน */
.dropdown-menu {
    display: none; /* ซ่อนเป็นค่าเริ่มต้น */
    position: absolute;
    top: 100%;     /* วางไว้ข้างล่าง trigger */
    right: 0;      /* ชิดขอบขวา */
    z-index: 100;  /* ต้องอยู่เหนือองค์ประกอบอื่น */
    
    min-width: 200px; /* ความกว้างขั้นต่ำของเมนู */
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
    margin-top: 8px; /* ระยะห่างเล็กน้อยจาก trigger */
    
    /* ล้างสไตล์ list */
    list-style: none;
    padding: 8px; /* เพิ่ม padding รอบเมนู */
    margin-left: 0; 
}

/* 4. สถานะ Active (แสดงเมนู) */
/* JavaScript จะเพิ่มคลาสนี้เมื่อถูกคลิก */
.dropdown-menu.active {
    display: block;
}

/* 5. รายการในเมนู (Menu Items) */
.dropdown-menu li a {
    display: block;
    padding: 10px 14px;
    text-decoration: none;
    color: #1f2937; /* สีเข้ม อ่านง่าย บนพื้นขาว */
    font-weight: 500;
    border-radius: 8px;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.dropdown-menu li a:hover {
    background-color: #f3f4f6; /* สีเทาอ่อนเมื่อ hover */
    color: #1d4ed8; /* สีน้ำเงิน (สีเดียวกับธีม) */
}

/* เพิ่มเส้นแบ่งก่อน "ออกจากระบบ" (ทางเลือก) */
.dropdown-menu li.divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 8px 0;
}

/* สไตล์พิเศษสำหรับ "ออกจากระบบ" ให้เป็นสีแดง (ทางเลือก) */
.dropdown-menu li a.logout-link:hover {
    background-color: #fee2e2; /* แดงอ่อน */
    color: #dc2626; /* แดงเข้ม */
}

    /* =========================================== pop up แจ้งเตือน ====================================================== */
.popup {
    z-index: 1000;
}

/* 2. ปรับขนาดกล่อง Modal ยืนยันให้เล็กและกระชับ */
.popup-content--confirm {
    max-width: 400px; 
   
    padding: 28px;
    grid-template-rows: auto auto; 
    gap: 24px; 
    
    text-align: center; 
    border-radius: 16px;
}

/*  จัดสไตล์หัวข้อ (h2) */
.popup-content--confirm h2 {
    font-size: 1.25rem; /* 20px */
    font-weight: 700;
    color: #1f2937; /* สีเทาดำ */
    margin: 0; /* ล้าง margin ที่ติดมากับ h2 */
    line-height: 1.4;
}

/*  จัดการปุ่ม .controls */
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
}

#cancel-logout-btn:hover {
    background: #e5e7eb; /
}

/* 6. สร้างสไตล์ปุ่มอันตราย (.btn-danger) */




#confirm-logout-btn{
    background: #dc2626; /* สีแดง (อันตราย) */
    color: #ffffff;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    transition: background-color 0.2s ease;
}


/* =============================================================================================================================
                                                     สไตล์สำหรับ Popup ประวัติการจอง
   =============================================================================================================================*/
.popup-content--history {
    max-width: 500px; /* ขนาดกำลังดี */
    min-height: 200px;
    padding: 24px;
    gap: 16px;
    grid-template-rows: auto 1fr auto; /* หัวข้อ, เนื้อหา, ปุ่ม */
    text-align: left;
}

.popup-content--history h2 {
    text-align: center;
    margin: 0 0 10px 0;
    color: #1f2937;
    font-weight: 700;
}

.history-body {
    max-height: 60vh; /* จำกัดความสูงถ้าข้อมูลเยอะ */
    overflow-y: auto;
    padding: 4px;
    border-radius: 8px;
    background: #f9fafb; /* สีพื้นหลังอ่อนๆ */
    border: 1px solid #e5e7eb;
}

/* สไตล์สำหรับรายการจองแต่ละรายการ */
.booking-item {
    display: flex;
    justify-content: space-between; /* ชื่อเกมอยู่ซ้าย | โต๊ะอยู่ขวา */
    align-items: center;
    padding: 14px;
    border-bottom: 1px solid #e5e7eb;
}
.booking-item:last-child {
    border-bottom: none;
}

.booking-item-game {
    font-weight: 600;
    color: #1d4ed8; /* สีน้ำเงินธีมหลัก */
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


/* กรณีไม่พบข้อมูล */
.history-body .no-bookings {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
    font-weight: 500;
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
                <a href="logout.php" class="logout-link">ออกจากระบบ</a>
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

    <main>
        <div class="search-content">
            <section class="sc-hero">
                <div class="sc-wrap">
                    <div class="sc-left">
                        <h1>ค้นหาเกม</h1>
                        <h1 id="searchBg" style="display: none;" data-searchbg="<?= htmlspecialchars($searchBg, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($searchBg) ?></h1>
                        <h1 id="sc-title"><?= htmlspecialchars($searchBg, ENT_QUOTES, 'UTF-8') ?></h1>
                        <p class="sc-sub">ว่าง</p>

                        <div class="sc-actions">
                            <button type="button" class="sc-btn sc-btn--ghost">More Info</button>
                            <button type="button" class="sc-btn sc-btn--accent">Book Now</button>
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
                            <img src="../../../BookingBordgame/public/werewolf.jpg" alt="Featured cover">
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
                                <div class="showavalible"><?= htmlspecialchars($t['state']) ?></div>
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
                                    // ถ้าคุณยังไม่มีสถานะ 'HELD' ให้มีแค่ free/taken ไปก่อน
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

            


            <script>
// หน้า pop up

document.addEventListener('DOMContentLoaded', () => {

    // ค้นหาองค์ประกอบทั้งหมดที่เกี่ยวข้อง
    const logoutLink = document.querySelector('a.logout-link');
    const confirmModal = document.getElementById('confirm-logout-popup');
    
    // (ตรวจสอบให้แน่ใจว่าองค์ประกอบมีอยู่จริงก่อน)
    if (logoutLink && confirmModal) {
        
        const cancelBtn = document.getElementById('cancel-logout-btn');
        const confirmBtn = document.getElementById('confirm-logout-btn');
        const overlay = confirmModal.querySelector('.overlay');

        // เมื่อคลิก "ออกจากระบบ" ใน Dropdown
        logoutLink.addEventListener('click', (e) => {
            // *** (สำคัญมาก) ป้องกันไม่ให้ลิงก์ทำงานทันที ***
            e.preventDefault(); 
            
            const profileMenu = document.getElementById('profile-menu');
                if (profileMenu) {
            profileMenu.classList.remove('active');
        }
        
        // แสดง Popup ยืนยัน
        confirmModal.classList.add('active');
        });

        // 3. เมื่อคลิก "ยืนยัน ออกจากระบบ" (ใน Popup)
        confirmBtn.addEventListener('click', () => {
            // ปิด Popup 
            confirmModal.classList.remove('active');
            
            //  สั่งให้เบราว์เซอร์ไปที่ URL ของลิงก์ที่ถูกดักไว้
            window.location.href = logoutLink.href;
        });

        // 4. เมื่อคลิก "ยกเลิก"
        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });

        // 5. (Best Practice) เมื่อคลิกที่ Overlay (พื้นหลังสีดำ)
        overlay.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });
    }
});

//--------------------------------------------------------------
                document.addEventListener('DOMContentLoaded', () => {
    
    // 1. ค้นหาองค์ประกอบที่เราต้องใช้
    const profileTrigger = document.getElementById('profile-trigger');
    const profileMenu = document.getElementById('profile-menu');

    // 2. ตรวจสอบว่าองค์ประกอบเหล่านี้มีอยู่จริงหรือไม่ (ป้องกัน Error หากยังไม่ล็อกอิน)
    if (profileTrigger && profileMenu) {

        // 3. เมื่อคลิกที่ Trigger (ชื่อผู้ใช้)
        profileTrigger.addEventListener('click', (e) => {
            // ป้องกันพฤติกรรมเริ่มต้น (ถ้าใช้ <a>) และการส่งต่อ event
            e.preventDefault();
            e.stopPropagation(); 
            
            // สลับ (toggle) คลาส 'active' เพื่อแสดง/ซ่อนเมนู
            profileMenu.classList.toggle('active');
        });

        // 4. [สำคัญมาก] เมื่อคลิกที่ใดก็ได้นอกเมนู
        //    นี่คือส่วนที่ทำให้เมนูปิดเองเมื่อคลิกที่อื่น (Best Practice UX)
        window.addEventListener('click', (e) => {
            
            // ตรวจสอบว่าเมนูกำลังเปิดอยู่
            if (profileMenu.classList.contains('active')) {
                
                // ตรวจสอบว่าคลิกที่ "นอก" trigger และ "นอก" เมนู
                if (!profileTrigger.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('active'); // ถ้าใช่, ให้ปิดเมนู
                }
            }
        });
    }
});


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
           


          


        </aside>

    </main>
    <footer>
    </footer>
</body>

</html>