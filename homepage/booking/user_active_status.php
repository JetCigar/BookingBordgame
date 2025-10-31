<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect("localhost","root","","bookingbordgame");
mysqli_set_charset($conn,'utf8mb4');

$memberId = (int)($_SESSION['member_id'] ?? 0);
$bgId     = (int)($_POST['bgId'] ?? 0);
$tableId  = (int)($_POST['tableId'] ?? 0);

if (!$memberId || !$bgId || !$tableId) {
  http_response_code(400);
  exit('ข้อมูลไม่ครบ');
}

mysqli_begin_transaction($conn);
try {
  // 1) บล็อกผู้ใช้ที่ยังมีรายการค้างอยู่
  $sql = "
    SELECT COUNT(*) AS cnt
    FROM bookingdetail b
    JOIN boradgame g ON g.bgid = b.bgId AND g.state = 0
    WHERE b.memberId = ?
      AND b.bkdId = (SELECT MAX(b2.bkdId) FROM bookingdetail b2 WHERE b2.bgId = b.bgId)
  ";
  $st = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($st, 'i', $memberId);
  mysqli_stmt_execute($st);
  $res = mysqli_stmt_get_result($st);
  $hasActive = ((int)mysqli_fetch_assoc($res)['cnt']) > 0;
  mysqli_free_result($res);
  mysqli_stmt_close($st);

  if ($hasActive) {
    mysqli_rollback($conn);
    exit('คุณยังมีเกมค้างคืน/ยังไม่คืน');
  }

  // 2) จับจองเกมแบบอะตอมมิก (ต้องยัง "ว่าง" = state=1)
  $st = mysqli_prepare($conn, "UPDATE boradgame SET state=0 WHERE bgid=? AND state=1");
  mysqli_stmt_bind_param($st, 'i', $bgId);
  mysqli_stmt_execute($st);
  if (mysqli_stmt_affected_rows($st) !== 1) {
    mysqli_stmt_close($st);
    mysqli_rollback($conn);
    exit('เกมนี้ไม่ว่างแล้ว');
  }
  mysqli_stmt_close($st);

  // 3) จับจองโต๊ะ (ถ้ามี state เช่นเดียวกัน)
  $st = mysqli_prepare($conn, "UPDATE tableroom SET state=0 WHERE tableId=? AND state=1");
  mysqli_stmt_bind_param($st, 'i', $tableId);
  mysqli_stmt_execute($st);
  if (mysqli_stmt_affected_rows($st) !== 1) {
    mysqli_stmt_close($st);
    // คืนสถานะเกมกลับว่าง เพราะจองโต๊ะไม่สำเร็จ
    $fix = mysqli_prepare($conn, "UPDATE boradgame SET state=1 WHERE bgid=?");
    mysqli_stmt_bind_param($fix, 'i', $bgId);
    mysqli_stmt_execute($fix);
    mysqli_stmt_close($fix);

    mysqli_rollback($conn);
    exit('โต๊ะไม่ว่างแล้ว');
  }
  mysqli_stmt_close($st);

  // 4) บันทึก bookingdetail
  $st = mysqli_prepare($conn, "INSERT INTO bookingdetail (memberId, bgId, tableId, create_at) VALUES (?, ?, ?, NOW())");
  mysqli_stmt_bind_param($st, 'iii', $memberId, $bgId, $tableId);
  mysqli_stmt_execute($st);
  mysqli_stmt_close($st);

  mysqli_commit($conn);
  echo 'จองสำเร็จ';
} catch (Throwable $e) {
  mysqli_rollback($conn);
  http_response_code(500);
  exit('เกิดข้อผิดพลาด');
}
