<?php
require __DIR__ . '../db.php';

$sql = "
SELECT
  bg.bgid,
  bg.bgName,
  bgd.bddescript,
  COALESCE(COUNT(bk.bkdId), 0) AS total_bookings
FROM boradgame AS bg
LEFT JOIN bordgamedescription AS bgd
  ON bg.bdId = bgd.bdId
LEFT JOIN bookings AS bk
  ON bk.bgid = bg.bgid
GROUP BY bg.bgid, bg.bgName, bgd.bddescript
ORDER BY total_bookings DESC, bg.bgName ASC
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>Ranking ยอดการจองบอร์ดเกม</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <div class="container">
    <h1>🏆 Ranking ยอดการจองบอร์ดเกม</h1>
    <table>
      <thead>
        <tr>
          <th>อันดับ</th>
          <th>bgId</th>
          <th>ชื่อบอร์ดเกม</th>
          <th>รายละเอียด</th>
          <th>จำนวนการจอง</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="5" class="empty">ยังไม่มีข้อมูล</td></tr>
        <?php else: ?>
          <?php $rank = 1; foreach ($rows as $r): ?>
            <tr>
              <td class="rank"><?= $rank++; ?></td>
              <td><?= htmlspecialchars($r['bgid']); ?></td>
              <td class="name"><?= htmlspecialchars($r['bgName']); ?></td>
              <td class="desc"><?= htmlspecialchars($r['bddescript']); ?></td>
              <td class="count"><?= htmlspecialchars($r['total_bookings']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <p class="footnote">
      * ถ้าเห็น 0 แปลว่ายังไม่มีการจองเกมนั้นในตาราง <code>bookings</code>
    </p>
  </div>
</body>
</html>