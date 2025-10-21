<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>homepage</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <style>
        *{
            box-sizing: border-box; /* แก้ box-sizing เป็น border-box เพื่อความถูกต้อง */
            padding: 0;
            margin: 0;
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
            padding: 0 20px; /* เพิ่ม padding เพื่อจัดช่องไฟ */
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

        /* เพิ่ม style สำหรับ Card บอร์ดเกม */
        .game-card {
            display: grid;
            gap: 5px;
            min-width: 320px;
            max-width: 320px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
        }
        .game-card img {
            max-width: 100%;
            height: auto;
        }
        .game-details {
            font-size: 0.9em;
            color: #555;
        }
        .game-details strong {
            color: #333;
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
        <div style="padding: 10px 20px;">BoardGame Category</div>
            <div>
                <?php
                    try {
                        $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','',[
                        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
                        ]);

                        $types = $pdo->query("SELECT btId, btName FROM bordgameType ORDER BY btName")->fetchAll();
                ?>
                    <section>
                    <div class="cat-row" style="padding: 10px 20px;">
                        <?php foreach ($types as $t): ?>
                        <a class="cat-card" href="list.php?type=<?= (int)$t['btId'] ?>">
                            <div class="texts">
                            <div class="title"><?= htmlspecialchars($t['btName']) ?></div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    </section>
                <?php
                    } catch (PDOException $e) {
                        echo "<p style='color:red;padding:20px;'>Database Error (Category): " . $e->getMessage() . "</p>";
                    }
                ?>
            </div>

        <div style="padding: 10px 20px;">New Arrials</div>
            <div>
                <?php
                    try {
                        $pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','',[
                        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
                        ]);
                        
                        // 🚩 แก้ไขคำสั่ง SQL: ดึงรายละเอียด (bddescript, bdage, bdtime) มาด้วย
                        $games = $pdo->query("SELECT 
                                boradgame.bgName,
                                bordgamedescription.image_url,
                                bordgamedescription.bdId,
                                bordgamedescription.bddescript,
                                bordgamedescription.bdage,
                                bordgamedescription.bdtime
                                FROM boradgame
                                INNER JOIN bordgamedescription
                                ON boradgame.bdId = bordgamedescription.bdId 
                                ORDER BY boradgame.bgid DESC 
                                LIMIT 10")->fetchAll(); // Limit 10 for New Arrivals
                ?>
                    <section>
                    <div class="cat-row" style="padding: 10px 20px;">
                    <?php foreach ($games as $g): ?>
                    <a class="cat-card game-card" href="list.php?id=<?= (int)$g['bdId'] ?>" style="align-items:flex-start; min-width:320px;">
                        <div style="display:flex; flex-direction:column; width:100%;">
                        <div>
                            <?php
                            $img = $g['image_url'] ?? '';
                            if ($img !== '') {
                                // ตรวจสอบ path รูปภาพ
                                if (preg_match('#^https?://#i', $img)) {
                                    $src = $img;
                                } else {
                                    // แก้ไข path ให้เริ่มจากรูทของโปรเจกต์
                                    $src = '/BookingBordgame/' . ltrim($img, '/');
                                }

                                echo '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" ' .
                                    'loading="lazy" decoding="async" ' .
                                    'style="max-width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:6px 6px 0 0; margin: 0 -10px -5px -10px;">';
                            } else {
                                // เผื่อกรณีไม่มีรูป
                                echo '<div style="width:100%;height:169px;background:#eee;border-radius:6px 6px 0 0;display:grid;place-items:center;color:#777; margin: 0 -10px -5px -10px;">ไม่มีรูป</div>';
                            }
                            ?>
                        </div>
                        <div class="texts" style="padding:10px 0;">
                            <div class="title" style="text-align:left; font-size:1.1em;"><?= htmlspecialchars($g['bgName']) ?></div>
                            <div class="game-details">
                                <p style="margin-top: 5px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; min-height: 2.4em;">
                                    <?= htmlspecialchars($g['bddescript']) ?>
                                </p>
                                <p><strong>อายุ:</strong> <?= htmlspecialchars($g['bdage']) ?></p>
                                <p><strong>เวลา:</strong> <?= htmlspecialchars($g['bdtime']) ?></p>
                            </div>
                        </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    </div>
                    </section>
                <?php
                    } catch (PDOException $e) {
                        echo "<p style='color:red;padding:20px;'>Database Error (New Arrials): " . $e->getMessage() . "</p>";
                    }
                ?>
            </div>
    </main>
    <footer></footer>
</body>
</html>