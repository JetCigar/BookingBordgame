<?php
// db.php
// ตั้งค่าการเชื่อมต่อฐานข้อมูลของคุณ
$host = '127.0.0.1';       // หรือ 'localhost'
$db   = 'bookingbordgame'; // ชื่อฐานข้อมูลตามไฟล์ .sql ของคุณ
$user = 'root';            // Username ปกติของ XAMPP
$pass = '';                // Password ปกติของ XAMPP (เว้นว่างไว้)
$charset = 'utf8mb4';

define('PROJECT_ROOT', '/BOOKINGBORDGAME');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // ใน Production จริง ไม่ควรแสดง Error นี้ให้ผู้ใช้เห็น
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>