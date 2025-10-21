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
// ส่วนดำเนินการ (Edit, Update) - ใช้ Prepared Statements
// ----------------------------------------------------

// 4. ดึงข้อมูลสำหรับแก้ไข
$edit_row = null;
if(isset($_GET['edit'])) {
    $personId = $_GET['edit'];
    
    $stmt = $conn->prepare("SELECT * FROM member WHERE personId=?");
    $stmt->bind_param("s", $personId);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_row = $edit_result->fetch_assoc();
    $stmt->close();
}

// 5. การอัพเดทผู้ใช้ (ลบ auid ออกจาก Logic)
if(isset($_POST['update'])) {
    $personId = $_POST['personId']; 
    $mname = $_POST['mname'];
    $mlastname = $_POST['mlastname'];
    $mgender = $_POST['mgender'];
    $mbirthday = $_POST['mbrithday'];
    $mphone = $_POST['mphone'];
    $mfaculty = $_POST['mfaculty'];
    
    // นำ auid ออกจากคำสั่ง UPDATE
    $query = "UPDATE member SET mname=?, mlastname=?, mgender=?, mbrithday=?, mphone=?, mfaculty=? WHERE personId=?";
    $stmt = $conn->prepare($query);
    // mname, mlastname, mgender, mbirthday, mphone, mfaculty, personId
    $stmt->bind_param("sssssss", $mname, $mlastname, $mgender, $mbirthday, $mphone, $mfaculty, $personId);
    
    if ($stmt->execute()) {
        header("Location: editmember.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit();
}
?> 
 
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">แก้ไขข้อมูลผู้ใช้</h1>
    </div>
    
    <?php if(isset($_GET['edit']) && $edit_row): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">แก้ไขข้อมูลผู้ใช้: <?php echo htmlspecialchars($edit_row['mname'] . ' ' . $edit_row['mlastname']); ?></h6>
        </div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="personId" value="<?php echo htmlspecialchars($edit_row['personId']); ?>">
                <input type="hidden" name="update" value="1"> 
                
                <div class="form-group">
                    <label>รหัสผู้ใช้ (personId)</label>
                    <input type="text" name="displayPersonId" class="form-control" 
                           value="<?php echo htmlspecialchars($edit_row['personId']); ?>" 
                           readonly>
                    <small class="form-text text-muted">ไม่สามารถแก้ไขรหัสผู้ใช้ได้</small>
                </div>

                <div class="form-group">
                    <label>ชื่อ (mname)</label>
                    <input type="text" name="mname" class="form-control" required value="<?php echo htmlspecialchars($edit_row['mname']); ?>">
                </div>

                <div class="form-group">
                    <label>นามสกุล (mlastname)</label>
                    <input type="text" name="mlastname" class="form-control" required value="<?php echo htmlspecialchars($edit_row['mlastname']); ?>">
                </div>
                
                <div class="form-group">
                    <label>เพศ (mgender)</label>
                    <select name="mgender" class="form-control" required>
                        <option value="">-- เลือกเพศ --</option>
                        <option value="male" <?php echo ($edit_row['mgender'] == 'male') ? 'selected' : ''; ?>>ชาย</option>
                        <option value="female" <?php echo ($edit_row['mgender'] == 'female') ? 'selected' : ''; ?>>หญิง</option>
                        <option value="other" <?php echo ($edit_row['mgender'] == 'other') ? 'selected' : ''; ?>>อื่น ๆ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>วันเกิด (mbirthday)</label>
                    <input type="date" name="mbrithday" class="form-control" required value="<?php echo htmlspecialchars($edit_row['mbrithday']); ?>" readonly>
                    <small class="form-text text-muted">วันเกิดไม่สามารถแก้ไขได้</small>
                </div>

                <div class="form-group">
                    <label>เบอร์โทรศัพท์ (mphone)</label>
                    <input type="text" name="mphone" class="form-control" required value="<?php echo htmlspecialchars($edit_row['mphone']); ?>">
                </div>

                <div class="form-group">
                    <label>คณะ (mfaculty)</label>
                    <input type="text" name="mfaculty" class="form-control" value="<?php echo htmlspecialchars($edit_row['mfaculty']); ?>">
                </div>
                
                <button type="submit" name="update" class="btn btn-warning">
                    อัพเดทข้อมูล
                </button>
                <a href="editmember.php" class="btn btn-secondary">ยกเลิก</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการผู้ใช้ทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>personId</th>
                            <th>ชื่อ</th>
                            <th>นามสกุล</th>
                            <th>เพศ</th>
                            <th>โทรศัพท์</th>
                            <th>คณะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // นำ auid ออกจาก SELECT statement
                        $result = mysqli_query($conn, "SELECT personId, mname, mlastname, mgender, mphone, mfaculty FROM member");
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['personId'])."</td>";
                            echo "<td>".htmlspecialchars($row['mname'])."</td>";
                            echo "<td>".htmlspecialchars($row['mlastname'])."</td>";
                            echo "<td>".htmlspecialchars($row['mgender'])."</td>";
                            echo "<td>".htmlspecialchars($row['mphone'])."</td>";
                            echo "<td>".htmlspecialchars($row['mfaculty'])."</td>";
                            echo "<td>
                                <a href='editmember.php?edit=".htmlspecialchars($row['personId'])."' class='btn btn-warning btn-sm'>แก้ไข</a>
                            </td>";
                            echo "</tr>";
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