<?php
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__."/db.php";

/** safe column helper */
if (!function_exists('ensure_column_exists')) {
  function ensure_column_exists(mysqli $conn, string $table, string $column, string $definition): void {
    $tableEsc  = $conn->real_escape_string($table);
    $columnEsc = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$columnEsc}'");
    $exists = $res && $res->num_rows > 0;
    if ($res) { $res->free(); }
    if (!$exists) {
      try {
        $conn->query("ALTER TABLE `{$tableEsc}` ADD COLUMN `{$columnEsc}` {$definition}");
      } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'Duplicate column name') === false) { throw $e; }
      }
    }
  }
}

if (($_SESSION['role'] ?? '') !== 'admin'){ exit("Access denied"); }

ensure_column_exists($conn,'order_items','size','VARCHAR(10)');

$sql="SELECT o.id,o.total,o.created_at,
             u.username,u.email,u.phone,
             CONCAT(u.address_line,', ',u.city,' ',u.postal_code) AS full_address
      FROM orders o
      JOIN users u ON u.id=o.user_id
      ORDER BY o.id DESC";
$orders=$conn->query($sql);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Orders</title></head>
<body>
<h2>Orders (Admin)</h2>
<?php if(!$orders || !$orders->num_rows): ?>
<p>No orders yet.</p>
<?php else: ?>
<table border="1" cellpadding="6" width="100%">
<tr><th>ID</th><th>User</th><th>Contact</th><th>Address</th><th>Total</th><th>Date</th><th>Items</th></tr>
<?php while($o=$orders->fetch_assoc()): 
  $oid=(int)$o['id'];
  $res=$conn->query("SELECT oi.qty,oi.size,p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$oid");
  $items=[];
  while($res && $it=$res->fetch_assoc()){
    $items[]=$it['name']." (Size ".$it['size'].") x ".$it['qty'];
  }
?>
<tr>
  <td><?=$oid?></td>
  <td><?=htmlspecialchars($o['username'])?></td>
  <td><?=htmlspecialchars($o['email'])?><br><?=htmlspecialchars($o['phone'])?></td>
  <td><?=htmlspecialchars($o['full_address'])?></td>
  <td><?=number_format($o['total'],2)?></td>
  <td><?=$o['created_at']?></td>
  <td><?=htmlspecialchars(implode("; ",$items))?></td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>
<a href="admin.php">Back</a>
</body>
</html>
