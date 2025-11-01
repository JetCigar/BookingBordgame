<?php
// --- homepage_forder/ranking_forder/hot_ranking.php ---
// 1. เรียกไฟล์ config (db.php)
session_start();
require_once '../ranking_forder/Connect_DB_ranking.php';
header('Content-Type: application/json; charset=utf-8');


// 2. สร้างคำสั่ง SQL (Query)
$sql = "
SELECT
  bg.bgid       AS bgId,
  bg.bgName,
  bd.image_url, 
  COUNT(*)    AS booking_count
FROM bookingdetail AS bgd
JOIN boradgame     AS bg  ON bgd.bgId = bg.bgid
JOIN bordgamedescription AS bd ON bg.bdId = bd.bdId
GROUP BY bg.bgid, bg.bgName,bd.image_url
ORDER BY booking_count DESC
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

//ตัวเเปล เลือก อันดับ1 มา
$rank_1 = $rankings[0];
$_SESSION['rank_1BgName'] = $rank_1['bgName'];
$_SESSION['rank_1BgImg'] = $rank_1['image_url'];
// test การดึง
// echo htmlspecialchars($rank_1['bgName'], ENT_QUOTES, 'UTF-8') . ' — ' . htmlspecialchars($rank_1['image_url']) . (int)$rank_1['booking_count']  . "<br>";

$n = count($rankings);
// for ($i = 0; $i < $n; $i++) {
//   $r = $rankings[$i];
//   echo htmlspecialchars($r['bgName'], ENT_QUOTES, 'UTF-8') . ' — ' . (int)$r['booking_count'] . "<br>";
// }

// 6. ปิดการเชื่อมต่อฐานข้อมูล (เมื่อใช้งานเสร็จ)

// ตอนประกอบ $rankings ก่อน json_encode
$raw = (string)($row['image_url'] ?? '');
$img = preg_match('#^https?://#', $raw)
       ? $raw
       : ('/BookingBordgame/' . ltrim($raw, '/'));

$rankings[] = [
  // 'bgId' => (int)$row['bgId'],
  // 'bgName' => $row['bgName'],
  'image_url' => $img, // <- ใช้ชื่อเดิม image_url แต่เป็นพาธเต็มแล้ว
  // 'booking_count' => (int)$row['booking_count'],
];
//เเปลงเป็น json
echo json_encode([
  'data' => $rankings,
], JSON_UNESCAPED_UNICODE);

$conn->close();
