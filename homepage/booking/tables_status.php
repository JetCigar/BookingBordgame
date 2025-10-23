<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4',
    'root','',
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );

  // เคลียร์ hold ที่หมดเวลา
  $pdo->exec("
    UPDATE tableroom
    SET hold_by=NULL, hold_expires_at=NULL
    WHERE hold_expires_at IS NOT NULL AND hold_expires_at < NOW()
  ");

  $rows = $pdo->query("SELECT tableId, state, hold_expires_at FROM tableroom ORDER BY tableId")->fetchAll();

  $tables = [];
  $now = time();
  foreach ($rows as $r) {
    $status = ((int)$r['state'] === 0) ? 'taken'
      : (!empty($r['hold_expires_at']) && strtotime($r['hold_expires_at']) > $now ? 'held' : 'free');

    $tables[] = ['tableId'=>(int)$r['tableId'], 'status'=>$status];
  }

  echo json_encode(['ok'=>true, 'tables'=>$tables, 'serverTime'=>date('c')], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'server_error'], JSON_UNESCAPED_UNICODE);
}
