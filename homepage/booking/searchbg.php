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
    SELECT b.bgid,b.bgName,bd.bddescript,bd.bdage,bd.bdtime,bd.image_url,b.state
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
    $bgName =  $search[0]['bgName'];
    
    if($bgName){
      $_SESSION['bgName'] = $search[0]['bgName'];
      echo $search[0]['bddescript'];
      echo $search[0]['bdage'];
      echo $search[0]['bdtime'];
      echo $search[0]['image_url'];
      echo $search[0]['state'];

      $_SESSION['bg_image'] = $search[0]['image_url'];
      $_SESSION['bg_state'] = $search[0]['state'];
      $_SESSION['shouldHide']  = false;
      
      header("location:homepage.php");
    }
    else{
      $_SESSION['bgName'] = 'ไม่พบข้อมูล';
      $_SESSION['shouldHide']  = true;
      header("location:homepage.php");
    }


    // $_SESSION['bgName'] = $search[0]['bgName'];
    //   header("location:homepage.php");
      
    // $_SESSION['bgName'] = $search[0]['bgName']; // เก็บด้วยคีย์คงที่
    // header("location:homepage.php");
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
