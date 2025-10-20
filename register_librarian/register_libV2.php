 <!-- Lib part -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container">
    <h1>สมัครบรรณารักษ์</h1> <!--register_librarian heading -->
    <form id="registrationForm" action="register_lib_save_v2.php" method="post" nnovalidate>
      

      <div class="form-group">
        <label for="auid">Username</label> <!-- 1.1 User auid input--> 
        <input type="text" id="auid" name="auid" placeholder="ตั้งชื่อสมัครบรรณารักษ์" required>
        <div class="error-message"></div> <!-- vaildation-->
      </div>

      <!-- 2.1 แถวรวมในบรรทัดเดียวกันที่ 1  (ชื่อ User , นามสกุล surname) --> 
      <div class="form-row">
        <div class="form-group">
            <label for="lbname">ชื่อผู้ใช้</label> <!-- 1.2 Mname member input -->
            <input type="text" id="lbname" name="lbname" placeholder="เช่น jarachai" required>
            <div class="error-message "></div> <!-- vaildation-->
        </div>

        <div class="form-group"> <!-- 1.3 Mlastname member input -->
            <label for="lblastname">นามสกุล</label>
            <input type="text" id="lblastname" name="lblastname" placeholder="เช่น jarachai" required>
            <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
        </div>
      </div> 
      <!-- จุดสิ้นสุดแถว ชื่อ User , นามสกุล surname -->

      <div class="form-group">
        <label for="person_ID_librarian">รหัสบัตรประชาชน</label> <!-- person Id input--> 
        <input type="text" id="person_ID_librarian" name="person_ID_librarian" placeholder="ตัวอย่าง 1749700113541" required> 
        <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
      </div>
      
      <div class="form-group">
        <label for="email">อีเมล(Email)</label> <!-- 1.4 Email input--> 
        <input type="email" id="email" name="email" placeholder="name@example.com" required>
        <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
      </div>

      <!-- 2.2 แถวรวมในบรรทัดเดียวกันที่ 2 (gender เพศ , นามสกุล surname) --> 
      <div class="form-row">
        <div class="form-group">
             <label for="lbgender">เพศ</label> <!-- 1.5 mgender member input -->
             <select id="lbgender" name="lbgender" required>
                    <option value="">-- กรุณาเลือกเพศ --</option>
                    <option value="male">ชาย</option>
                    <option value="female">หญิง</option>
            </select>
            <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
      </div>

        <div class="form-group"> <!-- 1.6 mdatebirth member input -->
             <label for="lb_dob">วันเกิด</label>
             <input type="date" id="lb_dob" name="lb_dob" required>
             <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
        </div>

      </div> 
      
      <!-- 2.3 แถวรวมในบรรทัดเดียวกันที่ 3 (phone เบอร์โทร , faculty คณะ) --> 
      <div class="form-row">
        <div class="form-group">
            <label for="lbphone">เบอร์โทร</label> <!-- 1.7 phone member input -->
            <input type="text" id="lbphone" name="lbphone" placeholder=" " required>
            <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
        </div>

      </div> 

      <div class="form-group"> 
        <label for="password">รหัสผ่าน</label>
        <input type="password" id="password" name="password" required>
        <div class="error-message" style = "color : red; "></div> <!-- vaildation-->
      </div>
      <div class="form-group">
        <label for="confirmPassword">ยืนยันรหัสผ่าน</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>
        <div class="error-message" style = "color : red; "></div></div> <!-- vaildation-->
      </div>
      <button type="submit" class="btn-primary">สมัครผู้ใช้</button>
      
    </form>
  </main>
  <script src="script_lib.js"></script>
</body>
</html>