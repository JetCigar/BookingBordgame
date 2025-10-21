 <!-- Member part -->
<!DOCTYPE html>     
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ</title>
  <link rel="stylesheet" href="stylesV2.css">
  <link href='https://fonts.googleapis.com/css?family=Kanit' rel='stylesheet'>
</head>


    <body>
        <div class="container">
            
            <!--  =====  หน้า Login  =====-   r-->      
            <div class = "form-box login">
                <form action="login.php" method="post">
                    <h1>เข้าสู่ระบบ</h1>
                <div class = "input-box">
                        <label for="auid">ไอดีผู้ใช้</label> 
                        <input type="text" id="auid" name="auid"  placeholder="กรุณากรอกไอดีผู้ใช้" required>
                         <div class="error-message"></div> <!-- vaildation-->
                </div>
                <div>
                    <div class="input-box">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" id="password" name="password" placeholder="กรุณากรอกรหัสผ่าน" required>
                         <div class="error-message"></div> <!-- vaildation-->
                        
                    </div>
                 </div>
                        <button type="submit" class="btn">เข้าสู่ระบบ</button>
                  </form>
            </div>           

            
                <!--  ======  หน้า registe    ====== -->                      
            <div class = "form-box register">
                    <h1>สมัครผู้ใช้</h1>
                    <form id="registrationForm" action="register_Member_save_v2.php" method="post" novalidate>
                <div class = "input-box">
                        <label for="auid">Username</label> <!-- 1.1 User auid input--> 
                        <input type="text" id="auid" name="auid" placeholder="ตั้ง Username เช่น User123" required>
                         <div class="error-message"></div> <!-- vaildation-->
                </div>
                
                <!-- 2.1 แถวรวมในบรรทัดเดียวกันที่ 1  (ชื่อ User , นามสกุล surname) --> 
                <div class="form-row">
                      <div class="input-box">
                        <label for="FullnameMember">ชื่อผู้ใช้</label> <!-- 1. 2 Mname member input --> 
                        <input type="text" id="FullnameMember" name="FullnameMember" placeholder="เช่น jarachai" required>
                         <div class="error-message"></div> <!-- vaildation-->
                     </div> 

                        <div class="input-box"> <!-- 1.3 Mlastname member input -->
                          <label for="LastNameMember">นามสกุล</label>
                          <input type="text" id="LastNameMember" name="LastNameMember" placeholder="เช่น jarachai" required>
                           <div class="error-message"></div> <!-- vaildation-->
                    </div>
                </div> 

            <div class="input-box">
                 <label for="MemberEmail">อีเมล(Email)</label> <!-- 1.4 Email input--> 
                 <input type="email" id="MemberEmail" name="MemberEmail" placeholder="name@example.com" required> 
                  <div class="error-message"></div> <!-- vaildation-->
               </div>

      <div class="input-box">
                <label   label for="person_ID_Member">รหัสบัตรประชาชน</label> <!-- person Id input--> 
                <input type="text" id="person_ID_Member" name="person_ID_Member" placeholder="ตัวอย่าง 1749700113541" required> 
                <div class="error-message"></div> <!-- vaildation-->
        </div>
      <!-- จุดสิ้นสุดแถว form-row ชื่อ User , นามสกุล surname -->

                 <!-- 2.2 แถวรวมในบรรทัดเดียวกันที่ 2 (gender เพศ , นามสกุล surname) --> 
             <div class="form-row">
                   <div class="input-box">
                         <label for="genderMember">เพศ</label> <!-- 1.5 mgender member input -->
                             <select id="genderMember" name="genderMember" required>
                                      <option value="">-- กรุณาเลือกเพศ --</option>
                                     <option value="male">ชาย</option>
                                    <option value="female">หญิง</option>
              </select>
               <div class="error-message"></div> <!-- vaildation-->
           </div>

        <div class="input-box"> <!-- 1.6 mdatebirth member input -->
             <label for="BrithDayMemeber">วันเกิด</label>
             <input type="date" id="BrithDayMemeber" name="BrithDayMemeber" required>
              <div class="error-message"></div> <!-- vaildation-->
        </div>

      </div> 
      


      <!-- 2.3 แถวรวมในบรรทัดเดียวกันที่ 3 (phone เบอร์โทร , faculty คณะ) --> 
      <div class="form-row">
        <div class="input-box">
            <label for="MemberPhone">เบอร์โทร</label> <!-- 1.7 phone member input -->
            <input type="text" id="MemberPhone" name="MemberPhone" placeholder=" " required>
             <div class="error-message"></div> <!-- vaildation-->
        </div>

        <div class="input-box"> 
            <label for="faculty">คณะ</label> <!-- 1.8 faculty member input -->
            <select id="faculty" name="faculty" required>
                    <option value="">-- กรุณาคณะ --</option>
                    <option value="ทค.">คณะเทคโนโลยีคหกรรมศาสตร์</option>
                    <option value="กพน.">คณะการแพทย์บูรณาการ (กพบ.)</option>
                    <option value="ศก.">คณะศิลปศาสตร์ (ศศ.)</option>
                    <option value="วท.">คณะวิทยาศาสตร์และเทคโนโลยี (วท.)</option>
                    <option value="สถ.">คณะสถาปัตยกรรมศาสตร์ (สถ.)</option>
                    <option value="คอ.">คณะครุศาสตร์อุตสาหกรรม (คอ.)</option>
                    <option value="ทก.">คณะเทคโนโลยีการเกษตร (ทก.)</option>
                    <option value="บธ.">คณะบริหารธุรกิจ (บธ.)</option>
                    <option value="วศ.">คณะวิศวกรรมศาสตร์ (วศ.)</option>
                    <option value="ทสม.">คณะเทคโนโลยีสื่อสารมวลชน (ทสม.)</option>
                    <option value="ศก.">คณะศิลปกรรมศาสตร์ (ศก.)</option>
                    <option value="พยาบาลศาสตร์">คณะพยาบาลศาสตร์</option>
            </select>
             <div class="error-message"></div> <!-- vaildation-->
        </div>
      </div> 
                <div>
                    <div class="input-box">
                        <label for="password1">รหัสผ่าน</label>
                        <input type="password" id="password1" name="password1" placeholder="กรุณากรอกรหัสผ่าน" required>
                         <div class="error-message"></div> <!-- vaildation-->
                    </div>
                 </div>
                  <div class="input-box">
                        <label for="confirmPassword">ยืนยันรหัสผ่าน</label>
                                 <input type="password" id="confirmPassword" name="confirmPassword" required>
                               <div class="error-message"></div> <!-- vaildation-->   
                           </div>
                        <button type="submit" class="btn">สมัครผู้ใช้</button>
                  </form>
            </div>
            
                <div class = "toggle-box"> 
                         <div class="toggle-pannel toggle-left">
                                <h1>RMUTT BoardGame</h1>
                                <p1>ยังไม่มีบัญชี? </p1>
                                  <button class="btn register-btn">สมัครผู้ใช้</button>
                        </div>

                        <div class="toggle-pannel toggle-right">
                                <h1>RMUTT BoardGame</h1>
                                <p1>มีบัญชีแล้ว  </p1>
                                   <button class="btn login-btn">เข้าสู่ระบบ</button>
                        </div>
            </div>
        </div>
    </body>
    <script src="script_member.js"></script>
</html>
