<?php
session_start();
$pdo = new PDO(
  'mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4',
  'root',
  '',
  [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]
);



function escape_like(string $s): string {
  return strtr($s, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_']);
}

$q  = trim($_GET['q'] ?? '');
$search = [];

if ($q !== '') {
  $kw = '%' . escape_like($q) . '%';
  $sql = "
    SELECT b.bgid,b.bgName,bd.bddescript,bd.bdage,bd.bdtime
    FROM boradgame AS b
    INNER JOIN bordgamedescription AS bd
    ON b.bdId = bd.bdId
    WHERE b.bgName LIKE :kw
    LIMIT 100
  ";
//  เช็ค erorr
  try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':kw' => $kw]);
    $search = $stmt->fetchAll();
    $_SESSION['bgName'] = $search[0]['bgName']; // เก็บด้วยคีย์คงที่
    header("location:homepage.php");
    // echo htmlspecialchars($search[0]['bgName'] ?? '', ENT_QUOTES, 'UTF-8');
    // echo htmlspecialchars($search[0]['bddescript'] ?? '', ENT_QUOTES, 'UTF-8');
    // echo htmlspecialchars($search[0]['bdage'] ?? '', ENT_QUOTES, 'UTF-8');
  } catch (PDOException $e) {
    echo 'SQL Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo '<pre>' . htmlspecialchars($sql, ENT_QUOTES, 'UTF-8') . '</pre>';
    exit;
  }
}else{
  header("location:homepage.php");
}
