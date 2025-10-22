<?php
// --- homepage_forder/ranking_forder/hot_ranking.php ---

// 1. เรียกไฟล์ config (db.php)

require_once 'Connect_DB_ranking.php';

// 2. สร้างคำสั่ง SQL (Query)

$sql = "
    SELECT
        t1.bgId,
        t3.bgName,
        t4.image_url,
        t5.btname,  /* <--- เพิ่ม t5.btname เข้ามา */
        COUNT(t1.bkdId) AS booking_count
    FROM
        bookingdetail AS t1
    JOIN
        boradgame AS t3 ON t1.bgId = t3.bgid
    JOIN
        bordgamedescription AS t4 ON t3.bdId = t4.bdId
    /* --- เพิ่มการ JOIN ตาราง bordgametype (ตั้งชื่อเล่นว่า t5) --- */
    JOIN
        bordgametype AS t5 ON t3.btId = t5.btId 
    GROUP BY
        t1.bgId, 
        t3.bgName, 
        t4.image_url,
        t5.btname /* <--- ต้องเพิ่มใน GROUP BY ด้วย */
    ORDER BY
        booking_count DESC
    LIMIT 10;
";

// 3. สั่งให้ฐานข้อมูลทำงาน (Execute Query)
$result = $conn->query($sql);

// 4. เตรียมตัวแปร (Array) ไว้เก็บข้อมูล
$rankings = [];
if ($result && $result->num_rows > 0) {
    // 5. วนลูปดึงข้อมูลทีละแถว
    while ($row = $result->fetch_assoc()) {
        $rankings[] = $row;
    }
}

// 6. ปิดการเชื่อมต่อฐานข้อมูล (เมื่อใช้งานเสร็จ)
$conn->close();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อันดับบอร์ดเกมยอดนิยม</title>
    <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
    <link rel="stylesheet" href="../homepage_style.css"> 
    
    <style>
        /* (ผมใส่ CSS กลับเข้ามาในนี้นะครับ เพื่อให้โค้ดจบในไฟล์เดียว) */
        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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

        /* CSS สำหรับป้าย Tag ประเภทเกม */
        .game-type-badge {
            background-color: #e0e0e0;
            color: #333;
            font-size: 0.85em;
            padding: 3px 8px;
            border-radius: 12px;
            white-space: nowrap; /* กันไม่ให้ป้ายตัดคำ */
        }
    </style>
</head>
<body>

    <header class="head-content">
        <nav>
            <ul class="navbar-content">
                <li>
                    <img class="logo" src="../image/bgrmutt.jpg" alt="Logo">
                </li>
                <ul class="menu-content">
                    <a href="../homepage.php"><li>Home</li></a> 
                    <li>Category</li>
                    <li>Hot</li>
                    <li>Contact</li>
                </ul>
                <li><button>SignIn</button></li>
            </ul>
        </nav>
    </header>

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
                                <small style="color: #777;">(ID: <?php echo $game['bgId']; ?>)</small>
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

</body>
</html>