<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // ให้โยน exception เวลา query fail
$conn = mysqli_connect("localhost","root","","bookingbordgame");
mysqli_set_charset($conn, 'utf8mb4');

$bkdId   = filter_input(INPUT_POST, 'bkdId', FILTER_VALIDATE_INT);
$bgId    = filter_input(INPUT_POST, 'bgId',  FILTER_VALIDATE_INT);
$tableId = filter_input(INPUT_POST, 'tableId', FILTER_VALIDATE_INT);

if (!$bkdId || !$bgId || !$tableId) {
  header('Location: statusbooking.php?error=ข้อมูลไม่ถูกต้อง');
  exit;
}

try {
  // เริ่มทรานแซคชัน
  mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

  // 1) ล็อคทรัพยากรที่กำลังจะเปลี่ยนสถานะ (กันแข่ง)
  //    (ถ้าตารางเป็น InnoDB คำสั่ง FOR UPDATE จะล็อคแถวได้)
  $rs1 = mysqli_query($conn, "SELECT state FROM boradgame WHERE bgid={$bgId} FOR UPDATE");
  if (mysqli_num_rows($rs1) === 0) { throw new Exception('ไม่พบบอร์ดเกม'); }
  $rs2 = mysqli_query($conn, "SELECT state FROM tableroom WHERE tableId={$tableId} FOR UPDATE");
  if (mysqli_num_rows($rs2) === 0) { throw new Exception('ไม่พบโต๊ะ'); }

  // 2) ตรวจว่ามี "เรคคอร์ดยืมใหม่กว่า" ทับทรัพยากรนี้หรือยัง?
  //    ถ้ามี แปลว่าเคสเก่า “ถือว่าคืนสำเร็จแล้วโดยนิยาม” (แนว returned_calc)
  $qMaxBg   = mysqli_query($conn, "SELECT MAX(bkdId) AS mx FROM bookingdetail WHERE bgid={$bgId}");
  $rowMaxBg = mysqli_fetch_assoc($qMaxBg);
  $maxBg    = (int)($rowMaxBg['mx'] ?? 0);

  $qMaxTb   = mysqli_query($conn, "SELECT MAX(bkdId) AS mx FROM bookingdetail WHERE tableId={$tableId}");
  $rowMaxTb = mysqli_fetch_assoc($qMaxTb);
  $maxTb    = (int)($rowMaxTb['mx'] ?? 0);

  $superseded = ($maxBg > $bkdId) || ($maxTb > $bkdId);

  if ($superseded) {
    // มีการยืมใหม่ทับแล้ว: ไม่ต้องเปลี่ยน state ปัจจุบัน
    mysqli_commit($conn);
    header('Location: statusbooking.php?msg=ถือว่าคืนแล้ว(มีการยืมครั้งใหม่)&done_bkd='.$bkdId);
    exit;
  }

  // 3) แถวนี้ยังเป็นล่าสุดจริง ๆ → ปล่อยเกม/โต๊ะ
  //    ใช้ WHERE ... AND state<>1 เพื่อให้ idempotent (กดซ้ำก็ไม่พัง)
  $st1 = mysqli_prepare($conn, "UPDATE boradgame SET state=1 WHERE bgid=? AND state<>1");
  mysqli_stmt_bind_param($st1, 'i', $bgId);
  mysqli_stmt_execute($st1);
  mysqli_stmt_close($st1);

  $st2 = mysqli_prepare($conn, "UPDATE tableroom SET state=1 WHERE tableId=? AND state<>1");
  mysqli_stmt_bind_param($st2, 'i', $tableId);
  mysqli_stmt_execute($st2);
  mysqli_stmt_close($st2);

  mysqli_commit($conn);
  header('Location: statusbooking.php?msg=คืนสำเร็จ&done_bkd='.$bkdId);
  exit;

} catch (Throwable $e) {
  if ($conn && mysqli_errno($conn) === 0) {
    // ถ้าไม่ได้อยู่ในสถานะ error ของ mysqli ให้ rollback ไว้ก่อน
    @mysqli_rollback($conn);
  } else {
    @mysqli_rollback($conn);
  }
  // ส่งข้อความสั้น ๆ กลับไปที่หน้า list
  header('Location: statusbooking.php?error='.rawurlencode('คืนไม่สำเร็จ: '.$e->getMessage()));
  exit;
}
