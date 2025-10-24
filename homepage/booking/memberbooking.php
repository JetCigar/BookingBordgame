<?php
session_start();
// user ใน session

$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');
$personId = htmlspecialchars($_SESSION['personid'] ?? '');


$user = $_SESSION['display_name'] ?? null;
if (!$user) {
  header('Location:../../../../Login_Registet_Member/Login_Register.php');
  exit;
}



$tableId = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);


if (isset($_POST['table_id'])) {
  $_SESSION['table_id'] = $_POST['table_id']; // เก็บค่าไว้ใน session
  $_SESSION['game_name'] = $_POST['game_name']; // เก็บค่าไว้ใน session
  $_SESSION['bgd_Id'] = $_POST['bgd_Id']; // เก็บค่าไว้ใน session

  $tableId = (int)($_SESSION['table_id']);
  $bgdId   = (int)($_SESSION['bgd_Id']);

  echo "บันทึก table_id ลงใน session แล้ว: " . $_SESSION['table_id'];
  echo "บันทึก game ลงใน session แล้ว: " . $_SESSION['game_name'];
  echo "บันทึก bgd_id ลงใน session แล้ว: " . $_SESSION['bgd_Id'];
  echo "บันทึก personid ลงใน session แล้ว: " . $_SESSION['personid'];
} else {
  echo "ไม่พบค่า table_id ที่ส่งมา";
}



require __DIR__ . '../../../Connect/db.php'; // $mysqli

// ตัวอย่างบันทึกเวลาเป็นเวลาปัจจุบันฝั่ง DB
// แนะนำใช้ transaction + ตรวจ state กันชนกันได้ (optimistic lock)
$mysqli->begin_transaction();

try {

  echo "tableId={$tableId}<br>";
  echo "bgdId={$bgdId}<br>";

  // จองเฉพาะโต๊ะที่ยังว่างอยู่ (state=1)
  $upd = $mysqli->prepare("UPDATE tableroom SET state='0' WHERE tableId=? ");
  $upd->bind_param('i', $tableId);
  $upd->execute();

  $updbd = $mysqli->prepare("UPDATE boradgame SET state='0' WHERE bdId=?");
  $updbd->bind_param('i', $bgdId);   // <- ใช้ $updbd ให้ถูก และตรวจชื่อแปรให้ตรง ($bdId / $bgd_Id)
  $updbd->execute();


  $bgdetail = $mysqli->prepare("INSERT INTO bordgamedescription VALUE (?,?,?) ");
  $bgdetail->bind_param('s','i','i',$personId,$bgdId,$tableId);   // <- ใช้ $updbd ให้ถูก และตรวจชื่อแปรให้ตรง ($bdId / $bgd_Id)
  $bgdetail->execute();


  $showAvalible = $mysqli->prepare("SELECT state FROM boradgame WHERE bdId=?");
  $showAvalible->bind_param('i', $bgdId);
  $showAvalible->execute();

  if ($res = $showAvalible->get_result()) {
    $row = $res->fetch_assoc();
    $state = $row ? (int)$row['state'] : null;
  } else {
    /* ทางเลือก B: ใช้ bind_result()/fetch() */
    $showAvalible->bind_result($state);
    $showAvalible->fetch();
  }
  $showAvalible->close();


  // บันทึกการจอง พร้อมเวลาปัจจุบัน (server time)
  // $ins = $mysqli->prepare("INSERT INTO bookings(user_id, table_id, booked_at)");
  // $ins->bind_param('ii', $userId, $tableId);
  // $ins->execute();
  $mysqli->commit();
  // echo "จองโต๊ะ $tableId สำเร็จ";
  // debug  

} catch (Exception $e) {
  $mysqli->rollback();
  http_response_code(500);
  echo "เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage());
}
