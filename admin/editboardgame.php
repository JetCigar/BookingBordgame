<?php 
include('includes/header.php'); 
include('includes/navbar.php');

// 1. การเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "bookingbordgame");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error_message = ''; // ตัวแปรสำหรับเก็บข้อความ Error ที่จะแสดงใน HTML

// ----------------------------------------------------
// ฟังก์ชันจัดการอัปโหลดรูปภาพ (ปรับปรุง Path)
// ----------------------------------------------------
function handleImageUpload($conn, $bdId, $existingImageUrl) {
    global $error_message; 
    
    //  แก้ไข: Path ที่ PHP ใช้ย้ายไฟล์ (สัมพันธ์กับไฟล์ PHP ปัจจุบัน /admin/)
    $target_dir = "../public/"; 
    //  แก้ไข: Path ที่จะบันทึกใน DB (เริ่มต้นด้วย public/ และไม่มี / นำหน้า)
    $db_path_prefix = "public/"; 
    $image_url = $existingImageUrl; 
    
    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['boardgame_image']) && $_FILES["boardgame_image"]["error"] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES["boardgame_image"]["name"]);
        $unique_file_name = time() . '_' . $file_name;
        $target_file = $target_dir . $unique_file_name; 
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบไฟล์ภาพและขนาด...
        if (getimagesize($_FILES["boardgame_image"]["tmp_name"]) === false) {
            $error_message .= "<div class='alert alert-danger'>ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ.</div>";
            return false;
        }
        if ($_FILES["boardgame_image"]["size"] > 5000000) { // 5MB
            $error_message .= "<div class='alert alert-danger'>ขนาดไฟล์ใหญ่เกินไป.</div>";
            return false;
        }
        if(!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $error_message .= "<div class='alert alert-danger'>อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.</div>";
            return false;
        }

        // ย้ายไฟล์
        if (move_uploaded_file($_FILES["boardgame_image"]["tmp_name"], $target_file)) {
            $image_url = $db_path_prefix . $unique_file_name; 
        } else {
            $error_message .= "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์.</div>";
            return false;
        }
    } else if (isset($_FILES['boardgame_image']) && $_FILES["boardgame_image"]["error"] != UPLOAD_ERR_NO_FILE) {
         $error_message .= "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์. (Code: " . $_FILES["boardgame_image"]["error"] . ")</div>";
         return false;
    }
    
    // อัปเดตฐานข้อมูลด้วย image_url ใหม่
    if ($bdId > 0 && $image_url !== $existingImageUrl) {
        $stmt = $conn->prepare("UPDATE bordgamedescription SET image_url=? WHERE bdId=?");
        $stmt->bind_param("si", $image_url, $bdId);
        $stmt->execute();
        $stmt->close();
    }
    
    return $image_url;
}
// ----------------------------------------------------

// ----------------------------------------------------
// ดึงข้อมูลสำหรับ Dropdown Menu (bordgametype)
// ----------------------------------------------------

$bt_result = mysqli_query($conn, "SELECT btId, btName FROM bordgametype"); 
$board_types = [];
if ($bt_result) {
    while ($row = mysqli_fetch_assoc($bt_result)) {
        $board_types[] = $row;
    }
} else {
    // หยุดการทำงานทันทีเมื่อเกิด SQL Error ที่สำคัญ (ป้องกัน Headers Sent)
    die("<div class='alert alert-danger'>FATAL ERROR: ไม่พบตาราง bordgametype หรือเกิดข้อผิดพลาดในการดึงข้อมูล. กรุณาตรวจสอบชื่อตารางในฐานข้อมูล.</div>");
}


// ----------------------------------------------------
// ส่วนดำเนินการ (Add, Delete, Edit, Update) - ใช้ Prepared Statements
// ----------------------------------------------------

// 2. การเพิ่มบอร์ดเกม
if(isset($_POST['add'])) {
    $bddescript = $_POST['bddescript']; 
    $bdage = $_POST['bdage'];
    $bdtime = $_POST['bdtime'];
    $state = $_POST['state'] ?? 1;

    // 1. INSERT ข้อมูลลงใน bordgamedescription 
    $stmt_desc = $conn->prepare("INSERT INTO bordgamedescription (bddescript, bdage, bdtime) VALUES (?, ?, ?)");
    $stmt_desc->bind_param("sss", $bddescript, $bdage, $bdtime); 
    if(!$stmt_desc->execute()){
         echo "Error inserting description: " . $stmt_desc->error;
         $stmt_desc->close();
         exit();
    }
    
    $bdId = $conn->insert_id;
    $stmt_desc->close();
    
    // อัปโหลดรูปภาพและอัปเดต image_url กลับไปที่ bordgamedescription
    $upload_result = handleImageUpload($conn, $bdId, ''); 
    if ($upload_result === false) {
        // ถ้าเกิดข้อผิดพลาดในการอัปโหลดไฟล์ จะไม่ทำต่อ
        exit();
    }
    
    // 2. INSERT ข้อมูลลงใน boradgame โดยใช้ bdId ที่เพิ่งสร้าง
    $bgName = $_POST['bgName'];
    $releasestate = $_POST['releasestate'];
    $btId = $_POST['btId'];
    
    $stmt = $conn->prepare("INSERT INTO boradgame (bgName, releasestate, bdId, btId, state) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $bgName, $releasestate, $bdId, $btId, $state);

    if ($stmt->execute()) {
        header("Location: editboardgame.php");
        exit();
    } else {
        echo "Error inserting boardgame: " . $stmt->error;
        $stmt->close();
        exit();
    }
}

// 3. การลบบอร์ดเกม (รวมการลบข้อมูล bordgamedescription)
if (isset($_GET['delete'])) {
    $bgid = (int)$_GET['delete'];

    // ดึง bdId + image_url ก่อน
    $stmt = $conn->prepare("
        SELECT bg.bdId, bd.image_url
        FROM boradgame bg
        JOIN bordgamedescription bd ON bd.bdId = bg.bdId
        WHERE bg.bgid=?
    ");
    $stmt->bind_param("i", $bgid);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $bdId  = $r['bdId'] ?? null;
    $img   = $r['image_url'] ?? null;

    mysqli_begin_transaction($conn);
    try {
        // 1) ลบ CHILD ทั้งหมดที่อ้างถึง boradgame ก่อน
        if ($st = $conn->prepare("DELETE FROM bookingdetail WHERE bgId=?")) {
            $st->bind_param("i", $bgid);
            $st->execute();
            $st->close();
        }
        // ถ้ามีตารางลูกอื่น ๆ ที่อ้างถึง boradgame เพิ่มตรงนี้ด้วย …

        // 2) ลบ boradgame (child ของ bordgamedescription)
        $st = $conn->prepare("DELETE FROM boradgame WHERE bgid=?");
        $st->bind_param("i", $bgid);
        $st->execute();
        $st->close();

        // 3) ลบ bordgamedescription (parent)
        if ($bdId !== null) {
            $st = $conn->prepare("DELETE FROM bordgamedescription WHERE bdId=?");
            $st->bind_param("i", $bdId);
            $st->execute();
            $st->close();
        }

        mysqli_commit($conn);

        // 4) ค่อยลบไฟล์หลัง commit
        if ($img) {
            $root = dirname(__DIR__); // /admin/.. → project root
            $path = $root . DIRECTORY_SEPARATOR . ltrim($img, '/');
            if (file_exists($path)) { @unlink($path); }
        }

        header("Location: editboardgame.php");
        exit;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        echo "Error deleting: " . htmlspecialchars($e->getMessage());
        exit;
    }
}


// 4. ดึงข้อมูลสำหรับแก้ไข (รวมดึง bddescript, bdage, bdtime และ image_url)
$edit_row = null;
$edit_bddescript = ''; 
$edit_bdage = '';
$edit_bdtime = '';
$edit_image_url = ''; 
if(isset($_GET['edit'])) {
    $bgid = $_GET['edit'];
    
    // ดึงข้อมูลหลักจาก boradgame
    $stmt = $conn->prepare("SELECT * FROM boradgame WHERE bgid=?");
    $stmt->bind_param("i", $bgid);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $edit_row = $edit_result->fetch_assoc();
    $stmt->close();

    // ดึงรายละเอียดและ URL รูปภาพจาก bordgamedescription
    if ($edit_row) {
        $bdId_to_fetch = $edit_row['bdId'];
        $stmt_desc = $conn->prepare("SELECT bddescript, bdage, bdtime, image_url FROM bordgamedescription WHERE bdId=?"); 
        $stmt_desc->bind_param("i", $bdId_to_fetch);
        $stmt_desc->execute();
        $desc_result = $stmt_desc->get_result();
        if ($desc_row = $desc_result->fetch_assoc()) {
            $edit_bddescript = $desc_row['bddescript']; 
            $edit_bdage = $desc_row['bdage'];
            $edit_bdtime = $desc_row['bdtime'];
            $edit_image_url = $desc_row['image_url']; 
        }
        $stmt_desc->close();
    }
}

// 5. การอัพเดทบอร์ดเกม
if(isset($_POST['update'])) {
    // ข้อมูลสำหรับตาราง boradgame
    $bgid = $_POST['bgid'];
    $bgName = $_POST['bgName'];
    $releasestate = $_POST['releasestate'];
    $bdId = $_POST['bdId']; 
    $btId = $_POST['btId'];
    $state = $_POST['state'] ?? 1; 

    // ข้อมูลสำหรับตาราง bordgamedescription
    $bddescript = $_POST['bddescript'];
    $bdage = $_POST['bdage'];
    $bdtime = $_POST['bdtime'];
    $existing_image_url = $_POST['existing_image_url']; 
    
    // 1. อัปโหลดรูปภาพและอัปเดต image_url ในฐานข้อมูล
    $upload_result = handleImageUpload($conn, $bdId, $existing_image_url);
    if ($upload_result === false) {
        exit();
    }
    
    // 2. UPDATE ข้อมูลอื่น ๆ ใน bordgamedescription (bddescript, bdage, bdtime)
    $stmt_desc = $conn->prepare("UPDATE bordgamedescription SET bddescript=?, bdage=?, bdtime=? WHERE bdId=?");
    $stmt_desc->bind_param("sssi", $bddescript, $bdage, $bdtime, $bdId);
    if(!$stmt_desc->execute()){
        echo "Error updating description: " . $stmt_desc->error;
        $stmt_desc->close();
        exit();
    }
    $stmt_desc->close();
    
    // 3. UPDATE ข้อมูลใน boradgame
    $stmt = $conn->prepare("UPDATE boradgame SET bgName=?, releasestate=?, bdId=?, btId=?, state=? WHERE bgid=?");
    $stmt->bind_param("ssiiii", $bgName, $releasestate, $bdId, $btId, $state, $bgid);
    
    if ($stmt->execute()) {
        header("Location: editboardgame.php");
        exit();
    } else {
        echo "Error updating boardgame: " . $stmt->error;
        $stmt->close();
        exit();
    }
}
?> 
 
<div class="container-fluid">

    <?php echo $error_message; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่ม/ลบ/แก้ไขบอร์ดเกม</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_row ? "แก้ไขบอร์ดเกม" : "เพิ่มบอร์ดเกม"; ?></h6>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <?php if($edit_row): ?>
                    <input type="hidden" name="bgid" value="<?php echo htmlspecialchars($edit_row['bgid']); ?>">
                    <input type="hidden" name="bdId" value="<?php echo htmlspecialchars($edit_row['bdId']); ?>">
                    <input type="hidden" name="existing_image_url" value="<?php echo htmlspecialchars($edit_image_url); ?>">
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
                    <label>รูปภาพบอร์ดเกม</label>
                    <?php if ($edit_row && $edit_image_url): 
                        //  กำหนด Path สำหรับแสดงผลในฟอร์ม (ใช้ ../)
                        $display_path_form = '../' . ltrim($edit_image_url, '/');
                        ?>
                        <div class="mb-2">
                            <img src="<?php echo htmlspecialchars($display_path_form); ?>" style="max-width: 150px; height: auto;">
                            <small class="form-text text-muted">รูปภาพปัจจุบัน</small>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="boardgame_image" class="form-control-file" <?php echo $edit_row && $edit_image_url ? '' : 'required'; ?>>
                    <small class="form-text text-muted">อัปโหลดใหม่เพื่อเปลี่ยนรูปภาพ (สูงสุด 5MB)</small>
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
                    <label>อายุผู้เล่นที่แนะนำ</label>
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
                    <a href="editboardgame.php" class="btn btn-secondary">ยกเลิก</a> 
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
                            <th>รูปภาพ</th> 
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
                        $query = "
                            SELECT 
                                bg.*, 
                                bd.bddescript, 
                                bd.bdage, 
                                bd.bdtime,
                                bd.image_url 
                            FROM 
                                boradgame bg
                            INNER JOIN 
                                bordgamedescription bd ON bg.bdId = bd.bdId
                        ";
                        $result = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            //  แสดงรูปภาพ: แก้ไข Path สำหรับแสดงผลในตาราง
                            echo "<td>";
                            if (!empty($row['image_url'])) {
                                $image_url_db = $row['image_url'];
                                
                                // 1. กำหนด Path สำหรับแสดงผล (HTML src)
                                $display_path = '../' . ltrim($image_url_db, '/');
                                
                                // 2. กำหนด Path สำหรับตรวจสอบ (PHP file_exists)
                                $project_root = dirname(__DIR__);
                                $check_path = $project_root . DIRECTORY_SEPARATOR . ltrim($image_url_db, '/');

                                if (file_exists($check_path)) {
                                    echo "<img src='".htmlspecialchars($display_path)."' style='max-width: 50px; height: auto;'>";
                                } else {
                                    echo "ไฟล์ไม่พบ";
                                }
                            } else {
                                echo "ไม่มีรูป";
                            }
                            echo "</td>";
                            
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