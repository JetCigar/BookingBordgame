<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // ดึงsesion มาจาก login
    session_start();
    $displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
    $displayperson_id = htmlspecialchars($_SESSION['personid'] ?? '');
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

                    <li><a href="../homepage/homepage.php">Home</a></li>
                    <li><a href="">Category</a></li>
                    <li><a href="../homepage/ranking_forder/hot_ranking.php">Hot</a></li>
                    <li><a href="../homepage/contact.html">Contact</a></li>
                </ul>
                <!-- <li><a class="btn-sigin" href="../Login_Registet_Member/Login_Register.php">SignIn</a></li>btn sigIn -->
                <div class="title"><?= htmlspecialchars($displayName) ?></div>
            </ul>
        </nav>
    </head>
    <main>

        <div class="NewArrials-content">card</div>
        <div>BoardGame Category</div>
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

        <div>New Arrials</div>
        <div>
            <?php
            $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $types = $pdo->query("SELECT boradgame.bgName,boradgame.state,bordgamedescription.image_url,bordgamedescription.bdId,bordgamedescription.bddescript,bordgamedescription.bdage,TIME_FORMAT(bordgamedescription.bdtime, '%H:%i:%s') AS bdtime
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
                            style="border:none;background:none;padding:0"
                            data-bgid="<?= htmlspecialchars($t['bgid'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bgname="<?= htmlspecialchars($t['bgName'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bddescription="<?= htmlspecialchars($t['bddescript'], ENT_QUOTES, 'UTF-8') ?>"
                            data-image="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                            data-age="<?= htmlspecialchars($t['bdage'], ENT_QUOTES, 'UTF-8') ?>"
                            data-time="<?= htmlspecialchars($t['bdtime'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bdid=" <?= (int)$t['bdId'] ?>">


                            <div class="texts">
                                <div>
                                    <?php if ($src): ?>
                                        <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
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
            <!-- <input type="hidden" name="bgid" id="bgid"> -->
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
                                <!-- <li id="popup-bgid" style="">bgid</li> -->
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

                                <tr><h2>หมายเลขบัตรประชาชน</h2></tr>
                                <tr style="display: flex;justify-content:space-between ; font-size:25px">
                                    <td style="display: flex;"><?= htmlspecialchars($displayperson_id)?></td>
                                </tr >
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
                    // const bgid = target.dataset.bgid || "";
                    // const table = trigger.dataset.data-table-id || "";
                    // ใส่ลง DOM
                    const titleEl = document.getElementById("popup-title");
                    const descEl = document.getElementById("popup-desc");
                    const ageEl = document.getElementById("popup-age");
                    const imgEl = document.getElementById("popup-image");
                    const timeEl = document.getElementById("popup-time");
                    const bdidEl = document.getElementById("popup-bdid");
                    // const bgidEl = document.getElementById("popup-bgid");

                    if (titleEl) titleEl.textContent = name;
                    if (descEl) descEl.textContent = desc;
                    if (ageEl) ageEl.textContent = age;
                    if (timeEl) timeEl.textContent = time;
                    if (bdidEl) bdidEl.textContent = bdid;
                    if (bgidEl)  bgidEl.textContent = bgid;

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
                    // const bgId = document.getElementById('bgid').value = document.getElementById('popup-bgid').textContent.trim();
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


        </aside>

    </main>
    <footer>
    </footer>
</body>

</html>