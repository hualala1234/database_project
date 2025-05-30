<?php
header('Content-Type: application/json; charset=utf-8');
include '../dbh.php';
$cid = $_GET['cid'] ?? null;
if ($cid === null) {
    echo json_encode(['error' => 'No cid provided']);
    exit;
}

$category = $_GET['category'] ?? '';
$merchants = [];

if ($category !== '') {
    $sql = "
      SELECT 
          m.mid,
          m.mName,
          m.mPicture,
          m.mAddress,
          m.rating,
          IFNULL(m.ratingCount, 0) AS ratingCount,
          IF(f.cid IS NOT NULL, 1, 0) AS isFavorited,
          GROUP_CONCAT(rcl.categoryName SEPARATOR ', ') AS categoryNames
      FROM merchant AS m
      LEFT JOIN restaurantcategories AS rc ON m.mid = rc.mid
      LEFT JOIN restaurantcategorylist rcl ON rc.categoryId = rcl.categoryId
      LEFT JOIN Favorite f ON m.mid = f.mid AND f.cid = ?
      WHERE EXISTS (
          SELECT 1 FROM restaurantcategorylist rcl2
          JOIN restaurantcategories rc2 ON rcl2.categoryId = rc2.categoryId
          WHERE rc2.mid = m.mid AND rcl2.categoryName = ?
      )
      GROUP BY m.mid
      ORDER BY RAND()
  ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('is', $cid, $category);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $merchants[] = $row;
        }
    }
}

echo json_encode($merchants, JSON_UNESCAPED_UNICODE);
exit;
