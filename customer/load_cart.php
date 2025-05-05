<?php
session_start();
require_once("../dbh.php");

$cid = $_SESSION['cid'] ?? '';
$cartTime = $_SESSION['cartTime'] ?? '';

if (!$cid || !$cartTime) {
  echo '';
  exit;
}

$sql = "SELECT c.*, p.pName, p.price, p.pPicture, m.mName
        FROM CartItem c
        JOIN Product p ON c.pid = p.pid
        JOIN Merchant m ON c.mid = m.mid
        WHERE c.cid = ? AND c.cartTime = ?
        ORDER BY c.mid";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $cid, $cartTime);
$stmt->execute();
$result = $stmt->get_result();

$groupedItems = [];
while ($row = $result->fetch_assoc()) {
    $groupedItems[$row['mid']]['mName'] = $row['mName'];
    $groupedItems[$row['mid']]['items'][] = $row;
}
?>

<?php foreach ($groupedItems as $group): ?>
  <div class="mb-4">
    <h5>
      <a class="text-primary text-decoration-none" href="merchant.php?mid=<?= urlencode($group['items'][0]['mid']) ?>">
        <?= htmlspecialchars($group['mName']) ?>
      </a>
    </h5>

    <?php foreach ($group['items'] as $item): ?>
      <div class="d-flex align-items-center mb-3">
        <img src="../<?= htmlspecialchars($item['pPicture']) ?>" alt="<?= $item['pName'] ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
        <div class="ms-3 flex-grow-1">
          <strong><?= htmlspecialchars($item['pName']) ?></strong><br>
          <small>NT$<?= $item['price'] ?> x <?= $item['quantity'] ?></small><br>
          <small class="text-muted"><?= nl2br(htmlspecialchars($item['specialNote'])) ?></small>
        </div>
        <div>
          <span class="fw-bold">NT$<?= $item['price'] * $item['quantity'] ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
