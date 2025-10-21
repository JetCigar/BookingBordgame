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
        bd.bkdId,
        m.mname,
        m.mlastname,
        m.personId,
        bg.bgName,       /* <--- ดึงชื่อบอร์ดเกมจากตาราง boradgame */
        bd.tableId
    FROM 
        bookingdetail bd
    INNER JOIN 
        member m ON bd.personId = m.personId
    INNER JOIN 
        boradgame bg ON bd.bgid = bg.bgid /* <--- JOIN ตารางบอร์ดเกม */
    ORDER BY 
        bd.bkdId DESC
";

$result = mysqli_query($conn, $query);

?> 
 
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">ประวัติการยืมบอร์ดเกมของผู้ใช้</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการยืมทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>รหัสจอง (bkdId)</th>
                            <th>รหัสผู้ใช้ (personId)</th>
                            <th>ชื่อ-นามสกุลผู้ใช้</th>
                            <th>บอร์ดเกมที่ยืม (bgName)</th>
                            <th>หมายเลขโต๊ะ (tableId)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $fullName = htmlspecialchars($row['mname']) . ' ' . htmlspecialchars($row['mlastname']);
                                echo "<tr>";
                                echo "<td>".htmlspecialchars($row['bkdId'])."</td>";
                                echo "<td>".htmlspecialchars($row['personId'])."</td>";
                                echo "<td>{$fullName}</td>";
                                echo "<td>".htmlspecialchars($row['bgName'])."</td>";
                                echo "<td>".htmlspecialchars($row['tableId'])."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>ไม่มีประวัติการยืมบอร์ดเกม</td></tr>";
                        }
                        ?>
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