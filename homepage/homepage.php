<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // ดึงsesion มาจาก login
    session_start();
    $displayName = htmlspecialchars($_SESSION['display_name'] ?? '');

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
        .menu-content li {
            position: relative;
            font-weight: 600;
            letter-spacing: .2px;
            padding: 8px 12px;
            border-radius: 999px;
            transition: transform .15s ease, background-color .15s ease, box-shadow .15s ease;
            cursor: pointer;
            user-select: none;
        }

        /* โฮเวอร์/โฟกัสเมนู */
        .menu-content li:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.10);
        }

        .menu-content li:focus-visible {
            outline: 2px solid #93c5fd;
            /* วงโฟกัสมองเห็นชัด */
            outline-offset: 2px;
            background: rgba(255, 255, 255, 0.18);
        }

        /* ถ้าจะทำ active ไว้ใช้คลาสนี้ได้ */
        .menu-content li.active {
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
    </style>
</head>

<body>

    <head class="head-content">
        <nav>
            <ul class="navbar-content">
                <li>
                    <img class="logo" src="../image//bgrmutt.jpg" alt="">
                </li>
                <ul class="menu-content">

                    <li>Home</li>
                    <li>Category</li>
                    <li>Hot</li>
                    <li>Contact</li>
                </ul>
                <li><a class="btn-sigin" href="../Login_Registet_Member/Login_Register.php">SignIn</a></li>
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

            $types = $pdo->query("SELECT boradgame.bgName,bordgamedescription.image_url,bordgamedescription.bdId,bordgamedescription.bddescript,bordgamedescription.bdage,TIME_FORMAT(bordgamedescription.bdtime, '%H:%i:%s') AS bdtime
                                            FROM boradgame
                                            INNER JOIN bordgamedescription
                                            ON boradgame.bdId = bordgamedescription.bdId ORDER BY bdId DESC")->fetchAll();

            $tables = $pdo->query("SELECT tableId, state FROM tableroom ORDER BY tableId")->fetchAll();

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
                            data-bgname="<?= htmlspecialchars($t['bgName'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bddescription="<?= htmlspecialchars($t['bddescript'], ENT_QUOTES, 'UTF-8') ?>"
                            data-image="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                            data-age="<?= htmlspecialchars($t['bdage'], ENT_QUOTES, 'UTF-8') ?>"
                            data-time="<?= htmlspecialchars($t['bdtime'], ENT_QUOTES, 'UTF-8') ?>"
                            data-bdid="<?= (int)$t['bdId'] ?>">
                            <div class="texts">
                                <div>
                                    <?php if ($src): ?>
                                        <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>"
                                            loading="lazy" decoding="async"
                                            style="max-width:300px;aspect-ratio:16/9;object-fit:cover;border-radius:12px">
                                    <?php endif; ?>
                                </div>
                                <div class="title"><?= htmlspecialchars($t['bgName']) ?></div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>

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
                            </ul>
                        </div>
                        <div class="step" id="step2">
                            <div id="table-list" class="table-grid">
                                <?php foreach ($tables as $tb):
                                    $available = ((int)$tb['state'] === 1); ?>
                                    <button type="button"
                                        class="table-card"
                                        data-table-id="<?= (int)$tb['tableId'] ?>"
                                        <?= $available ? '' : 'disabled' ?>>
                                        <div class="table-name">โต๊ะ <?= (int)$tb['tableId'] ?></div>
                                        <div class="table-status"><?= $available ? 'ว่าง' : 'ไม่ว่าง' ?></div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="step">
                            <p>หน้า 3: สรุป/ยืนยัน…</p>
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
                        if (titleEl) {
                            titleEl.textContent = (i === 1) ?
                                'กรุณาเลือกโต๊ะที่ต้องการนั่ง' :
                                (titleEl.dataset.baseTitle || titleEl.textContent);
                        }


                    }
                    if (nextBtn) {
                        nextBtn.addEventListener('click', () => {
                            if (currenIndex < steps.length - 1) {
                                currenIndex += 1;
                                showStep(currenIndex);

                            } else {
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

                    // ใส่ลง DOM
                    const titleEl = document.getElementById("popup-title");
                    const descEl = document.getElementById("popup-desc");
                    const ageEl = document.getElementById("popup-age");
                    const imgEl = document.getElementById("popup-image");
                    const timeEl = document.getElementById("popup-time");

                    if (titleEl) titleEl.textContent = name;
                    if (descEl) descEl.textContent = desc;
                    if (ageEl) ageEl.textContent = age;
                    if (timeEl) timeEl.textContent = time;
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
        </aside>

    </main>
    <footer>
    </footer>
</body>

</html>