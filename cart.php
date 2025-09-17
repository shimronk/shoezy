<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

/**
 * Safe helper: ensures a column exists before trying to add it.
 */
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

// ---------- Cart utilities ----------
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

function cart_key($product_id, $size) {
  return (int)$product_id . '|' . (string)$size;
}

function add_to_cart($id, $qty, $size) {
  $id   = (int)$id;
  $qty  = max(1, (int)$qty);
  $size = trim((string)$size) ?: 'N/A';
  $key  = cart_key($id, $size);
  if (!isset($_SESSION['cart'][$key])) {
    $_SESSION['cart'][$key] = ['product_id'=>$id, 'size'=>$size, 'qty'=>0];
  }
  $_SESSION['cart'][$key]['qty'] += $qty;
}

// --- MIGRATION for old-format carts ---
$old = $_SESSION['cart'];
foreach ($old as $k => $v) {
  if (is_array($v) && array_key_exists('qty', $v) && !array_key_exists('product_id', $v)) {
    $pid  = (int)$k;
    $qty  = max(0, (int)($v['qty'] ?? 0));
    $size = 'N/A';
    unset($_SESSION['cart'][$k]);
    if ($qty > 0) {
      $key = cart_key($pid, $size);
      $_SESSION['cart'][$key] = ['product_id'=>$pid, 'size'=>$size, 'qty'=>$qty];
    }
  }
}

// ---------- Handle POST actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  if ($_POST['action'] === 'add') {
    add_to_cart($_POST['id'] ?? 0, $_POST['qty'] ?? 1, $_POST['size'] ?? 'N/A');
    header("Location: cart.php"); exit;
  }

  if ($_POST['action'] === 'update') {
    foreach (($_POST['qty'] ?? []) as $key => $q) {
      $q = max(0, (int)$q);
      $newKey = (strpos($key, '|') === false) ? cart_key((int)$key, 'N/A') : $key;
      if (!isset($_SESSION['cart'][$newKey])) continue;
      if ($q === 0) unset($_SESSION['cart'][$newKey]);
      else $_SESSION['cart'][$newKey]['qty'] = $q;
    }
  }

  if ($_POST['action'] === 'clear') {
    $_SESSION['cart'] = [];
  }

  if ($_POST['action'] === 'checkout') {
    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
    $user_id = (int)$_SESSION['user_id'];

    // Ensure DB schema for items we write
    ensure_column_exists($conn, 'order_items', 'size', 'VARCHAR(10)');
    ensure_column_exists($conn, 'order_items', 'price_at_purchase', 'DECIMAL(10,2) NULL');

    // Build items + total
    $total = 0.0;
    $items = [];
    $stmtP = $conn->prepare("SELECT price FROM products WHERE id = ?");
    foreach ($_SESSION['cart'] as $row) {
      $pid  = (int)($row['product_id'] ?? 0);
      $qty  = (int)($row['qty'] ?? 0);
      $size = (string)($row['size'] ?? 'N/A');
      if ($pid <= 0 || $qty <= 0) continue;

      $stmtP->bind_param("i", $pid);
      $stmtP->execute();
      $res = $stmtP->get_result();
      if ($res && ($p = $res->fetch_assoc())) {
        $price = (float)$p['price'];
        $total += $price * $qty;
        $items[] = [$pid, $qty, $size, $price];
      }
    }
    $stmtP->close();

    if ($total <= 0) { header("Location: cart.php"); exit; }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id,total,created_at) VALUES (?,?,NOW())");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order_items
    $stmtI = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, size, price_at_purchase) VALUES (?,?,?,?,?)");
    foreach ($items as $it) {
      [$pid,$qty,$size,$price] = $it;
      $stmtI->bind_param("iiisd",$order_id,$pid,$qty,$size,$price);
      $stmtI->execute();
    }
    $stmtI->close();

    $_SESSION['cart'] = [];
    // IMPORTANT: send customers to their page, not the admin page
    header("Location: customer_orders.php?placed=1"); 
    exit;
  }
}

// ---------- Display Cart ----------
include __DIR__ . '/navbar.php';
$display = []; $total = 0.0;
$stmtP = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
foreach ($_SESSION['cart'] as $key=>$row) {
  $pid  = (int)($row['product_id'] ?? 0);
  $qty  = (int)($row['qty'] ?? 0);
  $size = (string)($row['size'] ?? 'N/A');
  if ($pid <= 0 || $qty <= 0) continue;
  $stmtP->bind_param("i",$pid);
  $stmtP->execute();
  $res = $stmtP->get_result();
  if ($res && ($p=$res->fetch_assoc())) {
    $subtotal = $p['price'] * $qty;
    $total += $subtotal;
    $display[] = ['key'=>$key,'name'=>$p['name'],'size'=>$size,'price'=>$p['price'],'qty'=>$qty,'subtotal'=>$subtotal];
  }
}
$stmtP->close();
?>
<div style="max-width:960px;margin:20px auto;padding:12px;">
  <h2>Your Cart</h2>
  <?php if(!$display): ?>
    <p>Your cart is empty. <a href="shop.php">Shop now</a></p>
  <?php else: ?>
    <form method="post" action="cart.php">
      <input type="hidden" name="action" value="update">
      <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <tr><th>Product</th><th>Size</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
        <?php foreach($display as $it): ?>
        <tr>
          <td><?=htmlspecialchars($it['name'])?></td>
          <td><?=htmlspecialchars($it['size'])?></td>
          <td>Rs. <?=number_format($it['price'],2)?></td>
          <td><input type="number" name="qty[<?=$it['key']?>]" value="<?=$it['qty']?>" min="0" style="width:70px;"></td>
          <td>Rs. <?=number_format($it['subtotal'],2)?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="4" align="right"><strong>Total:</strong></td><td><strong>Rs. <?=number_format($total,2)?></strong></td></tr>
      </table>
      <button type="submit">Update Cart</button>
    </form>
    <form method="post" style="display:inline-block;margin-top:8px;">
      <input type="hidden" name="action" value="clear">
      <button type="submit">Clear Cart</button>
    </form>
    <form method="post" style="display:inline-block;margin-top:8px;">
      <input type="hidden" name="action" value="checkout">
      <button type="submit">Checkout</button>
    </form>
  <?php endif; ?>
</div>
