<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die('Access denied'); }

/*
  Make sure your schema has:
  - users: username, email, phone, address_line, city, postal_code
  - order_items: product_id, qty, size (VARCHAR(10) recommended)
*/

$sql = "
  SELECT
    o.id,
    o.total,
    o.created_at,
    u.username,
    u.email,
    u.phone,
    CONCAT(u.address_line, ', ', u.city, ' ', u.postal_code) AS full_address,
    (
      SELECT GROUP_CONCAT(
               CONCAT(p.name, ' (Size: ', oi.size, ' × ', oi.qty, ')')
               ORDER BY p.name SEPARATOR ', '
             )
      FROM order_items oi
      JOIN products p ON p.id = oi.product_id
      WHERE oi.order_id = o.id
    ) AS items_with_sizes
  FROM orders o
  JOIN users u ON u.id = o.user_id
  ORDER BY o.created_at DESC
";

$result = $conn->query($sql);
if (!$result) {
  die('DB error: ' . htmlspecialchars($conn->error));
}
?>

<?php include 'navbar.php'; ?>
<div style="max-width:900px;margin:24px auto;padding:16px;">
  <h2>All Orders</h2>

  <table border="1" cellpadding="6" cellspacing="0" width="100%">
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Address</th>
      <th>Total</th>
      <th>Date</th>
      <th>Items (with sizes)</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['full_address']) ?></td>
        <td>Rs. <?= number_format((float)$row['total'], 2) ?></td>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><?= htmlspecialchars($row['items_with_sizes'] ?? '-') ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
