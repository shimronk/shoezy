<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = (int)$_SESSION['user_id'];

// fetch orders for this user
$sql = "SELECT id, total, created_at FROM orders WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

include __DIR__ . '/navbar.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#fafafa}
    .wrap{max-width:1000px;margin:24px auto;padding:16px;background:#fff;border:1px solid #ddd;border-radius:8px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border:1px solid #ddd;vertical-align:top}
    th{background:#f3f3f3;text-align:left}
    .success{background:#e6ffed;border:1px solid #b7f5c2;color:#0a6b2b;padding:8px;border-radius:6px;margin-bottom:12px}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>My Orders</h2>

    <?php if (isset($_GET['placed'])): ?>
      <div class="success">Thank you! Your order has been placed.</div>
    <?php endif; ?>

    <?php if (!$orders || $orders->num_rows === 0): ?>
      <p>No orders yet. <a href="shop.php">Shop now</a></p>
    <?php else: ?>
      <table>
        <tr><th>ID</th><th>Date</th><th>Total</th><th>Items</th></tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
          <?php
            $oid = (int)$o['id'];
            $itemsRes = $conn->query(
              "SELECT oi.qty, oi.size, p.name
               FROM order_items oi
               JOIN products p ON p.id = oi.product_id
               WHERE oi.order_id = {$oid}"
            );
            $list = [];
            if ($itemsRes) {
              while ($it = $itemsRes->fetch_assoc()) {
                $list[] = $it['name'] . " (Size " . htmlspecialchars($it['size']) . ") x " . (int)$it['qty'];
              }
            }
          ?>
          <tr>
            <td><?= $oid ?></td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
            <td>Rs. <?= number_format((float)$o['total'], 2) ?></td>
            <td><?= htmlspecialchars(implode('; ', $list)) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
