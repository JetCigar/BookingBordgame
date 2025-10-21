<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-dice"></i>
            </div>
        <div class="sidebar-brand-text mx-3">BG ADMIN <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="admin.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        การจัดการเกม
    </div>

    <li class="nav-item">
        <a class="nav-link" href="editboardgame.php">
            <i class="fas fa-fw fa-dice-d6"></i>
            <span>เพิ่ม/ลบ/แก้ไขบอร์ดเกม</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-couch"></i>
            <span>เพิ่ม/ลบ/แก้ไขโต๊ะ</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        การจัดการผู้ใช้
    </div>

    <li class="nav-item">
        <a class="nav-link" href="editmember.php">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>แก้ไขผู้ใช้</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserControl"
            aria-expanded="true" aria-controls="collapseUserControl">
            <i class="fas fa-fw fa-user-lock"></i>
            <span>ควบคุมบัญชีผู้ใช้</span>
        </a>
        <div id="collapseUserControl" class="collapse" aria-labelledby="headingUserControl" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">การควบคุมและประวัติ:</h6>
                <a class="collapse-item" href="#">ปลดล็อคผู้ใช้</a>
                <a class="collapse-item" href="#">แบนผู้ใช้</a>
                <a class="collapse-item" href="user_history.php">ดูประวัติผู้ใช้</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        การจอง & การเงิน
    </div>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>ยกเลิก/แก้ไขสถานะการจอง</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-hand-holding-usd"></i>
            <span>เก็บค่าปรับ</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>