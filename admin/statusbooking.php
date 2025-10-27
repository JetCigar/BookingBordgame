<?php
// ส่วนนี้สมมติว่าคุณใช้ Admin Template เดิม
include('includes/header.php');
include('includes/navbar.php');

// 1. การเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "bookingbordgame");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ----------------------------------------------------
// 2. ดึงข้อมูลประวัติการยืมบอร์ดเกม (รวมตาราง boradgame)
// ----------------------------------------------------

$query = "
  SELECT 
  bd.bkdId, bd.personId, bd.tableId, bd.bgid,
  m.mname, m.mlastname,
  bg.bgName,
  bg.state AS bg_state,
  t.state  AS table_state,

  lbg.latest_bkd_for_bg,
  ltb.latest_bkd_for_table,

  /* ← เพิ่มฟิลด์คำนวณผลการคืนต่อ “รายการนี้” */
  CASE
    WHEN COALESCE(lbg.latest_bkd_for_bg, 0)    > bd.bkdId
      OR COALESCE(ltb.latest_bkd_for_table, 0) > bd.bkdId
    THEN 1
    WHEN bg.state = 1 AND t.state = 1
    THEN 1
    ELSE 0
  END AS returned_calc

FROM bookingdetail bd
INNER JOIN member    m  ON bd.personId = m.personId
INNER JOIN boradgame bg ON bd.bgid    = bg.bgid
INNER JOIN tableroom t  ON bd.tableId = t.tableId
LEFT JOIN (
  SELECT bgid, MAX(bkdId) AS latest_bkd_for_bg
  FROM bookingdetail
  GROUP BY bgid
) lbg ON lbg.bgid = bd.bgid
LEFT JOIN (
  SELECT tableId, MAX(bkdId) AS latest_bkd_for_table
  FROM bookingdetail
  GROUP BY tableId
) ltb ON ltb.tableId = bd.tableId
ORDER BY bd.bkdId DESC
";
$result  = mysqli_query($conn, $query);
$doneBkd = filter_input(INPUT_GET, 'done_bkd', FILTER_VALIDATE_INT);
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">สถานะการยืม</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการยืมทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>bkdId</th>
                            <th>personId</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>บอร์ดเกม</th>
                            <th>โต๊ะ</th>
                            <th>สถานะการคืน</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            $fullName = htmlspecialchars($row['mname']) . ' ' . htmlspecialchars($row['mlastname']);
                            $bkdId    = (int)$row['bkdId'];
                            $bgId     = (int)$row['bgid'];
                            $tableId  = (int)$row['tableId'];

                            $returned = ((int)$row['returned_calc'] === 1);

                            $trClass = ($doneBkd && $doneBkd === $bkdId) ? ' class="table-success"' : '';
                        ?>
                            <tr<?= $trClass ?> id="bkd-<?= $bkdId ?>">
                                <td><?= $bkdId ?></td>
                                <td><?= htmlspecialchars($row['personId']) ?></td>
                                <td><?= $fullName ?></td>
                                <td><?= htmlspecialchars($row['bgName']) ?></td>
                                <td><?= $tableId ?></td>
                                <td>
                                    <?php if ($returned): ?>
                                        <span class="badge badge-success">คืนสำเร็จ</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">ยังไม่คืน</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($returned): ?>
                                        <button class="btn btn-sm btn-secondary" disabled>คืนแล้ว</button>
                                    <?php else: ?>
                                        <form action="../admin/return_booking.php" method="post"
                                            onsubmit="return confirm('ยืนยันการคืนรายการ #<?= $bkdId ?> ?');"
                                            style="display:inline">
                                            <input type="hidden" name="bkdId" value="<?= $bkdId ?>">
                                            <input type="hidden" name="bgId" value="<?= $bgId ?>">
                                            <input type="hidden" name="tableId" value="<?= $tableId ?>">
                                            <button type="submit" class="btn btn-sm btn-success">คืน</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php
mysqli_close($conn);
include('includes/scripts.php');
?>