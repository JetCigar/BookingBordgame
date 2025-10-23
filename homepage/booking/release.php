<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

$userId  = (int)($_SESSION['member_id'] ?? 0);   // <<< วางตรงนี้
$tableId = (int)($_POST['table_id'] ?? 0);
if ($userId === 0 || $tableId === 0) { echo json_encode(['ok'=>false]); exit; }

$pdo = new PDO('mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4','root','',[
  PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
]);

$sql = "UPDATE tableroom
        SET hold_by=NULL, hold_expires_at=NULL
        WHERE tableId=? AND state=1 AND hold_by=? AND hold_expires_at > NOW()";
$pdo->prepare($sql)->execute([$tableId, $userId]);

echo json_encode(['ok'=>true]);
