<?php 
include('includes/header.php'); 
include('includes/navbar.php');

// 1. การเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "bookingbordgame");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ----------------------------------------------------
// ส่วนดำเนินการ (BAN / UNBAN) - ใช้ Prepared Statements
// ----------------------------------------------------

if(isset($_GET['ban']) || isset($_GET['unban'])) {
    $personId = isset($_GET['ban']) ? $_GET['ban'] : $_GET['unban'];
    // 🚩 กำหนดสถานะใหม่
    $new_status = isset($_GET['ban']) ? 'banned' : 'active'; 
    
    // อัปเดตสถานะ (m_status) ในตาราง member
    $stmt = $conn->prepare("UPDATE member SET m_status=? WHERE personId=?");
    $stmt->bind_param("ss", $new_status, $personId);
    
    if ($stmt->execute()) {
        // Redirect กลับมาหน้าเดิมหลังการดำเนินการ
        header("location:../../../../BookingBordgame/admin/user_status.php");
    } else {
        // แสดง Error หากอัปเดตสถานะไม่สำเร็จ
        echo "<div class='alert alert-danger'>Error updating status: " . $stmt->error . "</div>";
    }
    $stmt->close();
    exit();
}

// ----------------------------------------------------
// ดึงข้อมูลสำหรับแสดงรายการผู้ใช้
// ----------------------------------------------------

// 🚩 ดึงข้อมูลผู้ใช้ทั้งหมด พร้อมคอลัมน์ m_status
$query = "SELECT personId, mname, mlastname, mphone, mfaculty, m_status FROM member"; 
$result = mysqli_query($conn, $query);

?> 
 
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">ควบคุมบัญชีผู้ใช้ (แบน / ปลดล็อค)</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการผู้ใช้และสถานะ</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>personId</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>โทรศัพท์</th>
                            <th>คณะ</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $is_banned = ($row['m_status'] == 'banned'); // 🚩 ตรวจสอบสถานะ
                                $status_text = $is_banned 
                                    ? '<span class="badge badge-danger">ถูกแบน</span>' 
                                    : '<span class="badge badge-success">ใช้งานปกติ</span>';
                                $action_button = $is_banned 
                                    ? "<a href='user_status.php?unban=".htmlspecialchars($row['personId'])."' class='btn btn-success btn-sm'>ปลดล็อค</a>"
                                    : "<a href='user_status.php?ban=".htmlspecialchars($row['personId'])."' class='btn btn-danger btn-sm' onclick=\"return confirm('ยืนยันการแบนผู้ใช้นี้?');\">แบน</a>";
                                
                                echo "<tr>";
                                echo "<td>".htmlspecialchars($row['personId'])."</td>";
                                echo "<td>".htmlspecialchars($row['mname'])." ".htmlspecialchars($row['mlastname'])."</td>";
                                echo "<td>".htmlspecialchars($row['mphone'])."</td>";
                                echo "<td>".htmlspecialchars($row['mfaculty'])."</td>";
                                echo "<td>{$status_text}</td>";
                                echo "<td>{$action_button}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>ไม่มีผู้ใช้ในระบบ</td></tr>";
                        }
                        
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div> 
</div>
<?php 
//include('includes/footer.php'); 
include('includes/scripts.php'); 
?>