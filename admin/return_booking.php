<?php
session_start();
$conn = mysqli_connect("localhost","root","","bookingbordgame");
if (!$conn) { die("Connection failed: ".mysqli_connect_error()); }

$bkdId   = filter_input(INPUT_POST, 'bkdId', FILTER_VALIDATE_INT);
$bgId    = filter_input(INPUT_POST, 'bgId',  FILTER_VALIDATE_INT);
$tableId = filter_input(INPUT_POST, 'tableId', FILTER_VALIDATE_INT);

if (!$bkdId || !$bgId || !$tableId) {
  header('Location: statusbooking.php?error=ข้อมูลไม่ถูกต้อง');
  exit;
}

mysqli_begin_transaction($conn);
try {
  // เกมว่าง
  if ($st1 = mysqli_prepare($conn, "UPDATE boradgame SET state=1 WHERE bgid=?")) {
    mysqli_stmt_bind_param($st1, 'i', $bgId);
    mysqli_stmt_execute($st1);
    mysqli_stmt_close($st1);
  }
  // โต๊ะว่าง
  if ($st2 = mysqli_prepare($conn, "UPDATE tableroom SET state=1 WHERE tableId=?")) {
    mysqli_stmt_bind_param($st2, 'i', $tableId);
    mysqli_stmt_execute($st2);
    mysqli_stmt_close($st2);
  }

  mysqli_commit($conn);
  header('Location: statusbooking.php?msg=คืนสำเร็จ&done_bkd='.$bkdId);
  exit;

} catch (Throwable $e) {
  mysqli_rollback($conn);
  header('Location: statusbooking.php?error=คืนไม่สำเร็จ');
  exit;
}
