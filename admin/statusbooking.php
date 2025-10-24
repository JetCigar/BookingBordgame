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
    bg.state AS bg_state,            -- 1 = เกมว่าง
    t.state  AS table_state,         -- 1 = โต๊ะว่าง

    -- ตัวเลือก: ทำให้รู้ว่าแถวนี้เป็นเรคคอร์ดล่าสุดของ bgid / tableId หรือไม่ (ไม่ต้องเพิ่มคอลัมน์)
    lbg.latest_bkd_for_bg,
    ltb.latest_bkd_for_table

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

                            // 1) นิยาม "คืนสำเร็จ" แบบง่าย: state ของเกมและโต๊ะเป็น 1 ทั้งคู่
                            $returnedSimple = ((int)$row['bg_state'] === 1 && (int)$row['table_state'] === 1);

                            // 2) (แนะนำ) ให้แม่นยำขึ้น: ต้องเป็นเรคคอร์ดล่าสุดของทั้งเกมและโต๊ะด้วย
                            $isLatestForBg    = ((int)$row['latest_bkd_for_bg']    === $bkdId);
                            $isLatestForTable = ((int)$row['latest_bkd_for_table'] === $bkdId);
                            $returnedNow = $returnedSimple && $isLatestForBg && $isLatestForTable;

                            // แถวที่เพิ่งคืนสำเร็จ (จาก redirect)
                            $trClass = ($doneBkd && $doneBkd === $bkdId) ? ' class="table-success"' : '';
                        ?>
                            <tr<?= $trClass ?> id="bkd-<?= $bkdId ?>">
                                <td><?= $bkdId ?></td>
                                <td><?= htmlspecialchars($row['personId']) ?></td>
                                <td><?= $fullName ?></td>
                                <td><?= htmlspecialchars($row['bgName']) ?></td>
                                <td><?= $tableId ?></td>

                                <td>
                                    <?php if ($returnedNow): ?>
                                        <span class="badge badge-success">คืนสำเร็จ</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">ยังไม่คืน</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($returnedNow): ?>
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