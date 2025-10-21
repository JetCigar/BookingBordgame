<?php
// BookingBordgame/BookingGame/Connect/db.php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล XAMPP
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'bookingboardgame'; // ปรับให้ตรงกับฐานข้อมูลของคุณ

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    die('เชื่อมต่อฐานข้อมูลล้มเหลว: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
