<?php 
include('includes/header.php'); 
include('includes/navbar.php');

// 1. การเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "bookingbordgame");

// 2. การเพิ่มบอร์ดเกม
if(isset($_POST['add'])) {
    // ป้องกัน SQL Injection อย่างง่าย (แนะนำให้ใช้ Prepared Statements ในงานจริง)
    $bgName = mysqli_real_escape_string($conn, $_POST['bgName']);
    $releasestate = mysqli_real_escape_string($conn, $_POST['releasestate']);
    $bdId = mysqli_real_escape_string($conn, $_POST['bdId']);
    $btId = mysqli_real_escape_string($conn, $_POST['btId']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    
    $query = "INSERT INTO boradgame (bgName, releasestate, bdId, btId, state) VALUES ('$bgName', '$releasestate', '$bdId', '$btId', '$state')";
    mysqli_query($conn, $query);
    // Redirect หลังเพิ่มข้อมูล
    header("Location: editboardgame.php");
    exit();
}

// 3. การลบบอร์ดเกม
if(isset($_GET['delete'])) {
    $bgid = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM boradgame WHERE bgid=$bgid");
    // Redirect หลังลบข้อมูล
    header("Location: editboardgame.php");
    exit();
}

// 4. ดึงข้อมูลสำหรับแก้ไข
$edit_row = null;
if(isset($_GET['edit'])) {
    $bgid = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_result = mysqli_query($conn, "SELECT * FROM boradgame WHERE bgid=$bgid");
    $edit_row = mysqli_fetch_assoc($edit_result);
}

// 5. การอัพเดทบอร์ดเกม
if(isset($_POST['update'])) {
    $bgid = mysqli_real_escape_string($conn, $_POST['bgid']);
    $bgName = mysqli_real_escape_string($conn, $_POST['bgName']);
    $releasestate = mysqli_real_escape_string($conn, $_POST['releasestate']);
    $bdId = mysqli_real_escape_string($conn, $_POST['bdId']);
    $btId = mysqli_real_escape_string($conn, $_POST['btId']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    
    $query = "UPDATE boradgame SET bgName='$bgName', releasestate='$releasestate', bdId='$bdId', btId='$btId', state='$state' WHERE bgid=$bgid";
    mysqli_query($conn, $query);
    
    // Redirect หลังอัพเดทข้อมูล
    header("Location: editboardgame.php");
    exit();
}
?> 
 
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่ม/ลบ/แก้ไขบอร์ดเกม</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_row ? "แก้ไขบอร์ดเกม" : "เพิ่มบอร์ดเกม"; ?></h6>
        </div>
        <div class="card-body">
            <form method="post">
                <?php if($edit_row): ?>
                    <input type="hidden" name="bgid" value="<?php echo htmlspecialchars($edit_row['bgid']); ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label>ชื่อบอร์ดเกม</label>
                    <input type="text" name="bgName" class="form-control" required value="<?php echo htmlspecialchars($edit_row['bgName'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>วันที่วางจำหน่าย</label>
                    <input type="date" name="releasestate" class="form-control" required value="<?php echo htmlspecialchars($edit_row['releasestate'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>bdId</label>
                    <input type="number" name="bdId" class="form-control" required value="<?php echo htmlspecialchars($edit_row['bdId'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>btId</label>
                    <input type="number" name="btId" class="form-control" required value="<?php echo htmlspecialchars($edit_row['btId'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>state</label>
                    <input type="number" name="state" class="form-control" required value="<?php echo htmlspecialchars($edit_row['state'] ?? ''); ?>">
                </div>
                <button type="submit" name="<?php echo $edit_row ? 'update' : 'add'; ?>" class="btn btn-<?php echo $edit_row ? 'warning' : 'success'; ?>">
                    <?php echo $edit_row ? 'อัพเดท' : 'เพิ่ม'; ?>
                </button>
                <?php if($edit_row): ?>
                    <a href="editboardgame.php" class="btn btn-secondary">ยกเลิก</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการบอร์ดเกม</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>bgid</th>
                            <th>bgName</th>
                            <th>releasestate</th>
                            <th>bdId</th>
                            <th>btId</th>
                            <th>state</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM boradgame");
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['bgid'])."</td>";
                            echo "<td>".htmlspecialchars($row['bgName'])."</td>";
                            echo "<td>".htmlspecialchars($row['releasestate'])."</td>";
                            echo "<td>".htmlspecialchars($row['bdId'])."</td>";
                            echo "<td>".htmlspecialchars($row['btId'])."</td>";
                            echo "<td>".htmlspecialchars($row['state'])."</td>";
                            echo "<td>
                                <a href='editboardgame.php?edit=".htmlspecialchars($row['bgid'])."' class='btn btn-warning btn-sm'>แก้ไข</a>
                                <a href='editboardgame.php?delete=".htmlspecialchars($row['bgid'])."' class='btn btn-danger btn-sm' onclick=\"return confirm('ยืนยันการลบ?');\">ลบ</a>
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