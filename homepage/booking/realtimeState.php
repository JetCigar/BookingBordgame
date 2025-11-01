<?php
// --- homepage_forder/ranking_forder/hot_ranking.php ---
// 1. เรียกไฟล์ config (db.php)
session_start();
require_once '../ranking_forder/Connect_DB_ranking.php';

// ปิดแคช และบอกว่าเป็น JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');


// 2. สร้างคำสั่ง SQL (Query)
$sql = "
SELECT bg.bgid AS bgId , bg.state FROM boradgame AS bg;";


$bg_State = $conn->query($sql);
// $row = $bg_State->fetch_row(); // ได้ array


$rows = [];
if ($bg_State) {
  while ($row =$bg_State->fetch_assoc()) {
    // บังคับเป็นตัวเลขเพื่อให้ JSON ชัดเจน
    $rows[] = [
      'bgId'  => (int)$row['bgId'],
      'state' => (int)$row['state'],
    ];
  }
} else {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>$conn->error], JSON_UNESCAPED_UNICODE);
  $conn->close();
  exit;
}

echo json_encode(['ok'=>true, 'stateBg'=>$rows, 'time'=>time()], JSON_UNESCAPED_UNICODE);
$conn->close();


