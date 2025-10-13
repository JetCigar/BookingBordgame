<?php
$pdo = new PDO(
  'mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4',
  'root','',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
);

$id = $_GET['id'] ?? null;
if (!$id) { exit('missing id'); }

// 1) คิวรี 1 แถว
$stmt = $pdo->prepare("SELECT bddescript,bdage,bdtime,image_url FROM bordgamedescription WHERE bdid = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(); // <-- ตอนนี้ $row ถูกกำหนดแล้ว (หรือเป็น false ถ้าไม่เจอ)

// 2) เช็คก่อนใช้
if ($row && !empty($row['image_url'])) {
    // แนะนำเก็บ path แบบ relative เช่น 'uploads/werewolf.jpg'
    $src = '/BookingBordgame/' . ltrim($row['image_url'], '/');
    echo '<img src="' . htmlspecialchars($src) . '" alt="" style="max-width:300px">';
    echo '<p>'.nl2br(htmlspecialchars($row['bddescript'] ?? '')).'</p>';
    echo '<h4>อายุแนะนำ</h4>';
    echo '<p>'.htmlspecialchars($row['bdage'] ?? '').'</p>';
    echo '<h4>เวลาเล่น</h4>';
    echo '<p>'.htmlspecialchars($row['bdtime'] ?? '').' </p>';


} else {
    echo 'ไม่พบรูปสำหรับ id นี้';
}
?>
