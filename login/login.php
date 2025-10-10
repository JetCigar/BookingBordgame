<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>loginsesion</title>
</head>
<body>



<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','');

$stmt = $pdo->prepare("SELECT username, password_hash FROM authentication WHERE username = ?");
$stmt->execute([$_POST['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    session_regenerate_id(true);
    $_SESSION['username'] = $user['username'];
    header('Location: /BookingBordgame/login/dashboard.php');
    exit;
} else {
    echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
?>


</body>
</html>

