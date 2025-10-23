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
        SET hold_by=?, hold_expires_at = NOW() + INTERVAL 2 MINUTE
        WHERE tableId=? AND state=1
          AND (hold_expires_at IS NULL OR hold_expires_at < NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $tableId]);

echo json_encode(['ok' => $stmt->rowCount() === 1]);
