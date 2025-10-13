<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>homepage</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <style>
        *{
            box-sizing: 0px;
            padding: 0px;
            margin: 0px;
            font-family: Kanit;
        }
        .logo{
            width: 70px;
            height: 70px;
        }

        .navbar-content{
            list-style-type: none;
            height: 85px;
            background-color: rgb(0, 0, 130);
            border: solid red 1px;
            display: flex;
            justify-content: space-between;
            color: white;
            align-items: center;
        }
        .menu-content{
            list-style-type: none;
            display: flex;
        }
        .menu-content li{
            margin-left: 60px;
        }

        .cat-row{ display:flex; gap:12px; overflow-x:auto; padding:8px 4px; scroll-snap-type:x proximity; }
        .cat-card{
        scroll-snap-align:start; display:flex; align-items:center; gap:10px;
        background:#fff; border:1px solid #e9e9ef; border-radius:12px;
        padding:10px 14px; text-decoration:none; color:#222; min-width:170px;
        box-shadow:0 1px 4px rgba(0,0,0,.05);
        }
        .cat-card:hover{ transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,.12); }
        .icon{ font-size:18px; }
        .texts .title{ font-weight:600; }
        .title{
            text-align: center;
        }

        .NewArrials-content{

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
                    $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','',[
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
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
                    $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','',[
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
                    ]);

                    $types = $pdo->query("SELECT boradgame.bgName,bordgamedescription.image_url,bordgamedescription.bdId
                                            FROM boradgame
                                            INNER JOIN bordgamedescription
                                            ON boradgame.bdId = bordgamedescription.bdId ORDER BY bdId DESC")->fetchAll();
                    ?>
                    <section>
                    <div class="cat-row">
                    <?php foreach ($types as $t): ?>
                    <a class="cat-card" href="list.php?type=<?= (int)$t['bdId'] ?>">
                        <div class="texts">
                        <div>
                            <?php
                            $img = $t['image_url'] ?? '';
                            if ($img !== '') {
                                // ถ้าเป็น URL เต็ม (http/https) ใช้ตามนั้น, ถ้าเป็น path ภายใน ให้ต่อกับโฟลเดอร์โปรเจกต์
                                if (preg_match('#^https?://#i', $img)) {
                                    $src = $img;
                                } else {
                                    $src = '/BookingBordgame/' . ltrim($img, '/');
                                }

                                echo '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" ' .
                                    'loading="lazy" decoding="async" ' .
                                    'style="max-width:300px;aspect-ratio:16/9;object-fit:cover;border-radius:12px">';
                            } else {
                                // เผื่อกรณีไม่มีรูป
                                echo '<div style="width:300px;height:169px;background:#eee;border-radius:12px;display:grid;place-items:center;color:#777">ไม่มีรูป</div>';
                            }
                            ?>
                        </div>
                        <div class="title"><?= htmlspecialchars($t['bgName']) ?></div>
                        <!-- ถ้ายังไม่มีจำนวน ให้ซ่อนไว้ก่อน -->
                        </div>
                    </a>
                    <?php endforeach; ?>
                    </div>
                    </section>
                    </div>
    </main>
    <footer></footer>
</body>
</html>