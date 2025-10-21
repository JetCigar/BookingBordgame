<?php
// BookingBordgame/BookingGame/homepage/ranking_forder/hot_ranking.php
// หน้าเว็บหลัก: รวมส่วนหัว HTML, ตารางอันดับ, ส่วนปิดท้าย

require_once __DIR__ . '/../Connect/db.php'; // แก้ path ด้วย __DIR__
// หมายเหตุ: __DIR__ = .../homepage/ranking_forder
// ../Connect/db.php -> ชี้ไปที่ .../homepage/Connect/db.php
// แต่ในโครงสร้างนี้ เราวาง Connect ไว้ใต้ BookingGame ดังนั้นใช้เส้นทางด้านล่างแทน:
require_once dirname(__DIR__, 2) . '/lib/ranking.php';

// ใช้ฟังก์ชันเวอร์ชัน boradgame/bgid (ตาม dump ของคุณ)
$result = getBoradgameRanking($mysqli);

?>
<!doctype html>
<html lang=\"th\">
<head>
  <meta charset=\"utf-8\">
  <title>อันดับการจองบอร์ดเกม</title>
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <style>
    :root { --accent:#2563eb; --border:#e5e7eb; --muted:#6b7280; }
    body { font-family: system-ui, -apple-system, 'Segoe UI', Tahoma, sans-serif; margin: 24px; color:#111; }
    h1 { margin: 0 0 6px; font-size: 1.6rem; }
    .sub { color: var(--muted); margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid var(--border); padding: 10px; }
    th { background: #f3f4f6; text-align: left; }
    tr:nth-child(even) { background: #fafafa; }
    .badge { display:inline-block; min-width:38px; text-align:center; padding:4px 8px; border-radius:999px; background:#eef2ff; color:var(--accent); font-weight:700; }
    .wrap { max-width: 960px; margin: 0 auto; }
  </style>
</head>
<body>
<div class=\"wrap\">
  <h1>อันดับการจองบอร์ดเกม</h1>
  <p class=\"sub\">เรียงจากยอด (นับจำนวนรายการจอง <code>bkdId</code>) มาก → น้อย</p>

  <table>
    <thead>
      <tr>
        <th>อันดับ</th>
        <th>bgid</th>
        <th>ชื่อเกม</th>
        <th>ยอด (นับ bkdId)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result && $result->num_rows > 0) {
          // ตรวจว่ามีคอลัมน์ rank_no จาก SQL ไหม
          $first = $result->fetch_assoc();
          $hasRank = array_key_exists('rank_no', $first);
          $result->data_seek(0);

          $rank = 0; $prevTotal = null; $seen = 0;
          while ($row = $result->fetch_assoc()) {
              $bgId   = htmlspecialchars($row['bgId']);
              $bgName = htmlspecialchars($row['bgName']);
              $total  = (int)$row['total_bookings'];

              if ($hasRank) {
                  $rank_no = htmlspecialchars($row['rank_no']);
              } else {
                  // จัดอันดับฝั่ง PHP (dense-ish)
                  $seen++;
                  if ($prevTotal === null)      $rank = 1;
                  elseif ($total < $prevTotal)  $rank = $seen;
                  $prevTotal = $total;
                  $rank_no = $rank;
              }

              echo "<tr>\n" .
                   "  <td><span class=\"badge\">{$rank_no}</span></td>\n" .
                   "  <td>{$bgId}</td>\n" .
                   "  <td>{$bgName}</td>\n" .
                   "  <td>{$total}</td>\n" .
                   "</tr>\n";
          }
      } else {
          echo '<tr><td colspan="4">ไม่มีข้อมูลการจอง</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>
</body>
</html>
