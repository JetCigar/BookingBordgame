<?php
session_start();
// user ใน session

$displayName = htmlspecialchars($_SESSION['display_name'] ?? '');

$user = $_SESSION['display_name'] ?? null;
if (!$user) {
  header('Location:../../../../Login_Registet_Member/Login_Register.php'); exit;
}



$tableId = filter_input(INPUT_POST, 'table_id', FILTER_VALIDATE_INT);


if (isset($_POST['table_id'])) {
    $_SESSION['table_id'] = $_POST['table_id']; // เก็บค่าไว้ใน session
    $_SESSION['game_name'] = $_POST['game_name']; // เก็บค่าไว้ใน session

    echo "บันทึก table_id ลงใน session แล้ว: " . $_SESSION['table_id'];
    echo "บันทึก game ลงใน session แล้ว: " . $_SESSION['game_name'];
} else {
    echo "ไม่พบค่า table_id ที่ส่งมา";
}



require __DIR__ . '../../../Connect/db.php'; // $mysqli

// ตัวอย่างบันทึกเวลาเป็นเวลาปัจจุบันฝั่ง DB
// แนะนำใช้ transaction + ตรวจ state กันชนกันได้ (optimistic lock)
$mysqli->begin_transaction();

// try {
//   // จองเฉพาะโต๊ะที่ยังว่างอยู่ (state=1)
//   $upd = $mysqli->prepare("UPDATE board_table SET state=0 WHERE tableId=? AND state=1");
//   $upd->bind_param('i', $tableId);
//   $upd->execute();

//   if ($upd->affected_rows !== 1) {
//     $mysqli->rollback();
//     die('โต๊ะนี้ไม่ว่างแล้ว');
//   }

//   // บันทึกการจอง พร้อมเวลาปัจจุบัน (server time)
//   $ins = $mysqli->prepare("INSERT INTO bookings(user_id, table_id, booked_at)");
//   $ins->bind_param('ii', $userId, $tableId);
//   $ins->execute();
//   $mysqli->commit();
//   echo "จองโต๊ะ $tableId สำเร็จ";
// } catch (Exception $e) {
//   $mysqli->rollback();
//   http_response_code(500);
//   echo "เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage());
// }
// ?>