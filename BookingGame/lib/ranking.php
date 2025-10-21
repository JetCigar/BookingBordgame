<?php
// BookingBordgame/BookingGame/lib/ranking.php
// ฟังก์ชันดึงอันดับบอร์ดเกม โดยนับจำนวนแถว bkdId ต่อ bgId

function getBoradgameRanking(mysqli $mysqli, ?string $startDate = null, ?string $endDate = null) {
    $filterByDate = ($startDate !== null && $endDate !== null);

    $sqlWithRank = "
        SELECT
          t.bgId,
          t.bgName,
          t.total_bookings,
          DENSE_RANK() OVER (ORDER BY t.total_bookings DESC) AS rank_no
        FROM (
          SELECT
            bg.bgid     AS bgId,
            bg.bgName   AS bgName,
            COUNT(bkd.bkdId) AS total_bookings
          FROM boradgame AS bg
          LEFT JOIN booking_detail AS bkd
            ON bkd.bgId = bg.bgid
          " . ($filterByDate ? "LEFT JOIN booking AS b ON b.bookingId = bkd.bookingId" : "") . "
          " . ($filterByDate ? "WHERE b.created_at >= ? AND b.created_at < ?" : "") . "
          GROUP BY bg.bgid, bg.bgName
        ) AS t
        ORDER BY t.total_bookings DESC, t.bgName ASC
    ";

    $sqlNoRank = "
        SELECT
          bg.bgid     AS bgId,
          bg.bgName   AS bgName,
          COUNT(bkd.bkdId) AS total_bookings
        FROM boradgame AS bg
        LEFT JOIN booking_detail AS bkd
          ON bkd.bgId = bg.bgid
        " . ($filterByDate ? "LEFT JOIN booking AS b ON b.bookingId = bkd.bookingId" : "") . "
        " . ($filterByDate ? "WHERE b.created_at >= ? AND b.created_at < ?" : "") . "
        GROUP BY bg.bgid, bg.bgName
        ORDER BY total_bookings DESC, bg.bgName ASC
    ";

    $stmt = $mysqli->prepare($sqlWithRank);
    if ($stmt) {
        if ($filterByDate) {
            $stmt->bind_param('ss', $startDate, $endDate);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) return $res;
    }

    $stmt2 = $mysqli->prepare($sqlNoRank);
    if (!$stmt2) {
        throw new RuntimeException('Prepare failed: ' . $mysqli->error);
    }
    if ($filterByDate) {
        $stmt2->bind_param('ss', $startDate, $endDate);
    }
    $stmt2->execute();
    return $stmt2->get_result();
}
