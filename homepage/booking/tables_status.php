<?php
// tables_status.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    // ต่อฐานข้อมูล (แก้ path/user/pass ให้ตรงโปรเจกต์คุณ)
    $pdo = new PDO(
        'mysql:host=localhost;dbname=bookingbordgame;charset=utf8mb4',
        'root', '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    // ถ้าคุณยังไม่มี timeslot ก็ใช้ query ปกติอย่างนี้ได้เลย
    $stmt = $pdo->query("SELECT tableId, state FROM tableroom ORDER BY tableId");
    $rows = $stmt->fetchAll();

    $tables = [];
    foreach ($rows as $r) {
        // map: 1 => free, 0 => taken
        $status = ((int)$r['state'] === 1) ? 'free' : 'taken';
        $tables[] = [
            'tableId' => (int)$r['tableId'],
            'status'  => $status
        ];
    }

    echo json_encode([
        'ok' => true,
        'tables' => $tables,
        'serverTime' => date('c')
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false, 'error'=>'server_error'], JSON_UNESCAPED_UNICODE);
}
?>