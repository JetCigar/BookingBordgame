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
// ดึงข้อมูลสำหรับ Dropdown Menu (bordgametype)
// ----------------------------------------------------

// ดึงข้อมูลประเภทบอร์ดเกม (bordgametype) - ยังคงจำเป็นสำหรับ Dropdown btId
$bt_result = mysqli_query($conn, "SELECT btId, btName FROM bordgametype"); 
$board_types = [];
if ($bt_result) {
    while ($row = mysqli_fetch_assoc($bt_result)) {
        $board_types[] = $row;
    }
} else {
    echo "<div class='alert alert-danger'>ไม่พบตาราง bordgametype หรือเกิดข้อผิดพลาดในการดึงข้อมูล</div>";
}


// ----------------------------------------------------
// ส่วนดำเนินการ (Add, Delete, Edit, Update) - ใช้ Prepared Statements
// ----------------------------------------------------

// 2. การเพิ่มบอร์ดเกม (จัดการข้อมูล boradgame และ INSERT ข้อมูล bordgamedescription)
if(isset($_POST['add'])) {
    // ข้อมูลสำหรับตาราง bordgamedescription (รายละเอียด, อายุ, เวลาเล่น)
    $bddescript = $_POST['bddescript']; 
    $bdage = $_POST['bdage'];
    $bdtime = $_POST['bdtime'];
    $state = $_POST['state'] ?? 1; // ใช้ค่า default 1

    // 1. INSERT ข้อมูลลงใน bordgamedescription เพื่อให้ได้ bdId อัตโนมัติ
    $stmt_desc = $conn->prepare("INSERT INTO bordgamedescription (bddescript, bdage, bdtime) VALUES (?, ?, ?)");
    $stmt_desc->bind_param("sss", $bddescript, $bdage, $bdtime); 
    $stmt_desc->execute();
    
    // 🚩 รับ bdId ที่ถูกสร้างขึ้นมาอัตโนมัติ (Auto-increment ID)
    $bdId = $conn->insert_id;
    $stmt_desc->close();
    
    // 2. INSERT ข้อมูลลงใน boradgame โดยใช้ bdId ที่เพิ่งสร้าง
    $bgName = $_POST['bgName'];
    $releasestate = $_POST['releasestate'];
    $btId = $_POST['btId'];
    
    $stmt = $conn->prepare("INSERT INTO boradgame (bgName, releasestate, bdId, btId, state) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $bgName, $releasestate, $bdId, $btId, $state);

    if ($stmt->execute()) {
        header("Location: admin.php"); 
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit();
}

// 3. การลบบอร์ดเกม
if(isset($_GET['delete'])) {
    $bgid = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM boradgame WHERE bgid=?");
    $stmt->bind_param("i", $bgid);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit();
}

// 4. ดึงข้อมูลสำหรับแก้ไข (รวมดึง bddescript, bdage และ bdtime)
$edit_row = null;
$edit_bddescript = ''; 
$edit_bdage = '';
$edit_bdtime = '';
if(isset($_GET['edit'])) {
    $bgid = $_GET['edit'];
    
    // ดึงข้อมูลหลักจาก boradgame
    $stmt = $conn->prepare("SELECT * FROM boradgame WHERE bgid=?");
    $stmt->bind_param("i", $bgid);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_row = $edit_result->fetch_assoc();
    $stmt->close();

    // ดึง bddescript, bdage และ bdtime จาก bordgamedescription โดยใช้ bdId
    if ($edit_row) {
        $bdId_to_fetch = $edit_row['bdId'];
        $stmt_desc = $conn->prepare("SELECT bddescript, bdage, bdtime FROM bordgamedescription WHERE bdId=?"); 
        $stmt_desc->bind_param("i", $bdId_to_fetch);
        $stmt_desc->execute();
        $desc_result = $stmt_desc->get_result();
        if ($desc_row = $desc_result->fetch_assoc()) {
            $edit_bddescript = $desc_row['bddescript']; 
            $edit_bdage = $desc_row['bdage'];
            $edit_bdtime = $desc_row['bdtime'];
        }
        $stmt_desc->close();
    }
}

// 5. การอัพเดทบอร์ดเกม (รวมการ Update ข้อมูลรายละเอียด)
if(isset($_POST['update'])) {
    // ข้อมูลสำหรับตาราง boradgame
    $bgid = $_POST['bgid'];
    $bgName = $_POST['bgName'];
    $releasestate = $_POST['releasestate'];
    $bdId = $_POST['bdId']; // ใช้ bdId ที่ถูกซ่อนไว้ในฟอร์ม
    $btId = $_POST['btId'];
    $state = $_POST['state'] ?? 1; 

    // ข้อมูลสำหรับตาราง bordgamedescription (รายละเอียด, อายุ, เวลาเล่น)
    $bddescript = $_POST['bddescript'];
    $bdage = $_POST['bdage'];
    $bdtime = $_POST['bdtime'];

    // 1. UPDATE ข้อมูลใน bordgamedescription (bddescript, bdage, bdtime)
    $stmt_desc = $conn->prepare("UPDATE bordgamedescription SET bddescript=?, bdage=?, bdtime=? WHERE bdId=?");
    $stmt_desc->bind_param("sssi", $bddescript, $bdage, $bdtime, $bdId);
    $stmt_desc->execute();
    $stmt_desc->close();
    
    // 2. UPDATE ข้อมูลใน boradgame
    $stmt = $conn->prepare("UPDATE boradgame SET bgName=?, releasestate=?, bdId=?, btId=?, state=? WHERE bgid=?");
    $stmt->bind_param("ssiiii", $bgName, $releasestate, $bdId, $btId, $state, $bgid);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
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
                    <input type="hidden" name="bdId" value="<?php echo htmlspecialchars($edit_row['bdId']); ?>">
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
                    <label>รายละเอียดบอร์ดเกม</label>
                    <textarea name="bddescript" class="form-control" rows="3" required><?php echo htmlspecialchars($edit_bddescript); ?></textarea>
                </div>

                <div class="form-group">
                    <label>ประเภทบอร์ดเกม</label>
                    <select name="btId" class="form-control" required>
                        <option value="">-- เลือกประเภท --</option>
                        <?php foreach ($board_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['btId']); ?>"
                                <?php echo (isset($edit_row['btId']) && $edit_row['btId'] == $type['btId']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['btName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>อายุผู้เล่นที่แนะนำ </label>
                    <input type="text" name="bdage" class="form-control" required value="<?php echo htmlspecialchars($edit_bdage); ?>">
                    <small class="form-text text-muted">ตัวอย่าง: 7+ หรือ 10+</small>
                </div>

                <div class="form-group">
                    <label>เวลาการเล่น</label>
                    <input type="text" name="bdtime" class="form-control" required value="<?php echo htmlspecialchars($edit_bdtime); ?>">
                    <small class="form-text text-muted">ตัวอย่าง: 00:10:00.0 หรือ 10 นาที</small>
                </div>

                <input type="hidden" name="state" value="<?php echo htmlspecialchars($edit_row['state'] ?? 1); ?>">


                <button type="submit" name="<?php echo $edit_row ? 'update' : 'add'; ?>" class="btn btn-<?php echo $edit_row ? 'warning' : 'success'; ?>">
                    <?php echo $edit_row ? 'อัพเดท' : 'เพิ่ม'; ?>
                </button>
                <?php if($edit_row): ?>
                    <a href="admin.php" class="btn btn-secondary">ยกเลิก</a> 
                <?php endif; ?>
            </form>
        </div>
    </div>

---

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
                            <th>รายละเอียด (bddescript)</th> 
                            <th>อายุ (bdage)</th>
                            <th>เวลาเล่น (bdtime)</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ใช้ JOIN เพื่อดึงรายละเอียดจาก bordgamedescription
                        $query = "
                            SELECT 
                                bg.*, 
                                bd.bddescript, 
                                bd.bdage, 
                                bd.bdtime 
                            FROM 
                                boradgame bg
                            INNER JOIN 
                                bordgamedescription bd ON bg.bdId = bd.bdId
                        ";
                        $result = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>".htmlspecialchars($row['bgid'])."</td>";
                            echo "<td>".htmlspecialchars($row['bgName'])."</td>";
                            echo "<td>".htmlspecialchars($row['releasestate'])."</td>";
                            echo "<td>".htmlspecialchars($row['bddescript'])."</td>";
                            echo "<td>".htmlspecialchars($row['bdage'])."</td>";
                            echo "<td>".htmlspecialchars($row['bdtime'])."</td>";
                            
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