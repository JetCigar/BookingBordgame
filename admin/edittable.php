<?php 
include('includes/header.php'); 
include('includes/navbar.php');

// 1. การเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "bookingbordgame");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ตัวแปรสำหรับเก็บข้อผิดพลาด
$error_message = ''; 

// ----------------------------------------------------
// ส่วนดำเนินการ (Add, Delete, Edit, Update) - ใช้ Prepared Statements
// ----------------------------------------------------

// 2. การเพิ่มโต๊ะ
if(isset($_POST['add'])) {
    $table_id = $_POST['tableId']; // รับ tableId ที่ผู้ใช้กรอกสำหรับเพิ่มใหม่
    $state = $_POST['state'] ?? 1; 
    
    // 🚩 แก้ไข: INSERT ข้อมูลเฉพาะ tableId และ state
    $stmt = $conn->prepare("INSERT INTO tableroom (tableId, state) VALUES (?, ?)");
    $stmt->bind_param("ii", $table_id, $state);

    if ($stmt->execute()) {
        header("Location: edittable.php"); 
    } else {
        $error_message = "<div class='alert alert-danger'>Error adding table: " . $stmt->error . "</div>";
    }
    $stmt->close();
    // exit();
}

// 3. การลบโต๊ะ
if(isset($_GET['delete'])) {
    $table_id = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM tableroom WHERE tableId=?");
    $stmt->bind_param("i", $table_id);
    
    if ($stmt->execute()) {
        header("Location: edittable.php");
    } else {
        $error_message = "<div class='alert alert-danger'>Error deleting table: " . $stmt->error . "</div>";
    }
    $stmt->close();
    exit();
}

// 4. ดึงข้อมูลสำหรับแก้ไข
$edit_row = null;
if(isset($_GET['edit'])) {
    $table_id = $_GET['edit'];
    
    // 🚩 แก้ไข: ดึงข้อมูล tableId และ state เท่านั้น
    $stmt = $conn->prepare("SELECT tableId, state FROM tableroom WHERE tableId=?");
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_row = $edit_result->fetch_assoc();
    $stmt->close();
}

// 5. การอัพเดทโต๊ะ
if(isset($_POST['update'])) {
    $table_id = $_POST['tableId'];
    $state = $_POST['state'];
    
    // 🚩 แก้ไข: UPDATE ข้อมูลเฉพาะ state
    $query = "UPDATE tableroom SET state=? WHERE tableId=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $state, $table_id);
    
    if ($stmt->execute()) {
        header("Location: edittable.php");
    } else {
        $error_message = "<div class='alert alert-danger'>Error updating table: " . $stmt->error . "</div>";
    }
    $stmt->close();
    exit();
}
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่ม/ลบ/แก้ไขโต๊ะ</h1>
    </div>

    <?php echo $error_message; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_row ? "แก้ไขโต๊ะ ID: " . htmlspecialchars($edit_row['tableId']) : "เพิ่มโต๊ะใหม่"; ?></h6>
        </div>
        <div class="card-body">
            <form method="post">
                <?php if($edit_row): ?>
                    <input type="hidden" name="tableId" value="<?php echo htmlspecialchars($edit_row['tableId']); ?>">
                <?php else: ?>
                    <div class="form-group">
                        <label>หมายเลขโต๊ะ (Table ID)</label>
                        <input type="number" name="tableId" class="form-control" required value="<?php echo htmlspecialchars($edit_row['tableId'] ?? ''); ?>">
                        <small class="form-text text-muted">กรอกตัวเลข ID ของโต๊ะใหม่</small>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>สถานะ (State)</label>
                    <select name="state" class="form-control" required>
                        <option value="1" <?php echo (isset($edit_row['state']) && $edit_row['state'] == 1) ? 'selected' : ''; ?>>1 - พร้อมใช้งาน (Available)</option>
                        <option value="0" <?php echo (isset($edit_row['state']) && $edit_row['state'] == 0) ? 'selected' : ''; ?>>0 - ไม่พร้อมใช้งาน (Maintenance)</option>
                    </select>
                </div>

                <button type="submit" name="<?php echo $edit_row ? 'update' : 'add'; ?>" class="btn btn-<?php echo $edit_row ? 'warning' : 'success'; ?>">
                    <?php echo $edit_row ? 'อัพเดทสถานะ' : 'เพิ่มโต๊ะ'; ?>
                </button>
                <?php if($edit_row): ?>
                    <a href="edittable.php" class="btn btn-secondary">ยกเลิก</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการโต๊ะทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Table ID</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 🚩 แก้ไข: SELECT ข้อมูลเฉพาะ tableId และ state
                        $query = "SELECT tableId, state FROM tableroom ORDER BY tableId";
                        $result = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_assoc($result)) {
                            $status_text = $row['state'] == 1 ? '<span class="badge badge-success">พร้อมใช้งาน</span>' : '<span class="badge badge-danger">ไม่พร้อมใช้งาน</span>';
                            
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['tableId'])."</td>";
                            echo "<td>{$status_text}</td>";
                            echo "<td>
                                <a href='edittable.php?edit=".htmlspecialchars($row['tableId'])."' class='btn btn-warning btn-sm'>แก้ไข</a>
                                <a href='edittable.php?delete=".htmlspecialchars($row['tableId'])."' class='btn btn-danger btn-sm' onclick=\"return confirm('ยืนยันการลบโต๊ะ ID: ".htmlspecialchars($row['tableId'])."?');\">ลบ</a>
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