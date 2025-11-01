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
// 2. Logic การอัปเดตสถานะบอร์ดเกม (state)
// ----------------------------------------------------
if(isset($_POST['update_state'])) {
    $bgid = $_POST['bgid'];
    $new_state = $_POST['state'];
    
    // อัปเดต state ในตาราง boradgame
    $stmt = $conn->prepare("UPDATE boradgame SET state = ? WHERE bgid = ?");
    $stmt->bind_param("ii", $new_state, $bgid);

    if ($stmt->execute()) {
        // Redirect กลับมาหน้าเดิมหลังการอัปเดต
        header("Location: admin.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating state: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

?> 
 
    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <form
                    class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                            <img class="img-profile rounded-circle"
                                src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                             <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Boardgame List</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>รูปภาพ</th>
                                        <th>bgid</th>
                                        <th>bgName</th>
                                        <th>releasestate</th>
                                        <th>bdId</th>
                                        <th>btId</th>
                                        <th>สถานะ (State)</th>
                                        <th>ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // ปรับปรุง QUERY เพื่อ JOIN ตาราง bordgamedescription และดึง image_url
                                    $query = "
                                        SELECT 
                                            bg.*, 
                                            bd.image_url 
                                        FROM 
                                            boradgame bg
                                        INNER JOIN 
                                            bordgamedescription bd ON bg.bdId = bd.bdId
                                    ";
                                    $result = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        
                                        // แสดงรูปภาพ
                                        echo "<td>";
                                        if (!empty($row['image_url'])) {
                                            $image_url_db = $row['image_url'];
                                            $display_path = '../' . ltrim($image_url_db, '/');
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
                                        echo "<td>".htmlspecialchars($row['bdId'])."</td>";
                                        echo "<td>".htmlspecialchars($row['btId'])."</td>";
                                        
                                        // คอลัมน์สถานะ (State) - เป็น Dropdown Form
                                        echo "<td style='min-width: 150px;'>";
                                        echo "<form method='post' action='admin.php' class='d-flex align-items-center'>";
                                        echo "<input type='hidden' name='bgid' value='".htmlspecialchars($row['bgid'])."'>";
                                        echo "<select name='state' class='form-control form-control-sm mr-2'>";
                                        
                                        // Option 1: 1 - พร้อมใช้งาน (Available)
                                        echo "<option value='1' " . ($row['state'] == 1 ? 'selected' : '') . ">1 - พร้อมใช้งาน (Available)</option>";
                                        
                                        // Option 0: 0 - ไม่พร้อมใช้งาน (Out of Stock / Borrowed)
                                        echo "<option value='0' " . ($row['state'] == 0 ? 'selected' : '') . ">0 - ไม่พร้อมใช้งาน (Out)</option>";
                                        
                                        echo "</select>";
                                        echo "</td>";
                                        
                                        // คอลัมน์ดำเนินการ
                                        echo "<td>";
                                        echo "<button type='submit' name='update_state' class='btn btn-primary btn-sm'>อัปเดต</button>";
                                        echo "</form>";
                                        echo "</td>";
                                        
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            </div>
        <?php 
mysqli_close($conn);
include('includes/footer.php'); 
include('includes/scripts.php'); 
?>