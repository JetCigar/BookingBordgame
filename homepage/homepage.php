<!DOCTYPE html>
<html lang="en">

<head>
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
            padding: 25px;
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
            height: 360px;
            grid-template-rows: auto 1fr auto;
        }

        /* endpopup */



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
                <li><button>SignIn</button></li>
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
                        <a class="cat-card" href="list.php?type=<?= (int)$t['btId'] ?>">
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

            $types = $pdo->query("SELECT boradgame.bgName,bordgamedescription.image_url,bordgamedescription.bdId
                                            FROM boradgame
                                            INNER JOIN bordgamedescription
                                            ON boradgame.bdId = bordgamedescription.bdId ORDER BY bdId DESC")->fetchAll();
            ?>
            <section>
                <div class="cat-row">
                    <?php foreach ($types as $t): ?>
                        <button class="open-popup" style="border: none; background:none;">
                            <a class="cat-card open-popup">
                                <div class="texts">
                                    <div>
                                        <?php
                                        $img = $t['image_url'] ?? '';
                                        if ($img !== '') {
                                            $src = '/BookingBordgame/' . ltrim($img, '/');
                                            echo '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" ' .
                                                'loading="lazy" decoding="async" ' .
                                                'style="max-width:300px;
                                                aspect-ratio:16/9;object-fit:cover;border-radius:12px">';
                                        }
                                        ?>
                                    </div>
                                    <div class="title"><?= htmlspecialchars($t['bgName']) ?></div>


                                </div>
                            </a>
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
                            <p>หน้า 1: ข้อความแนะนำ…</p>
                        </div>
                        <div class="step">
                            <p>หน้า 2: รายละเอียดเพิ่มเติม…</p>
                        </div>
                        <div class="step">
                            <p>หน้า 3: สรุป/ยืนยัน…</p>
                        </div>
                    </div>
                    <div class="controls">
                        <button class="close-btn">close</button>
                        <button class="submit-btn next-btn">next</button>
                    </div>
                </div>
            </div>
            
            <script src="../../BookingBordgame/homepage/bgDescript.js"></script>
        </aside>
        
    </main>
    <footer>
    </footer>
</body>

</html>