<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function compute_base_url(): string {
  $doc = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
  $dir = rtrim(str_replace('\\','/', realpath(__DIR__)), '/'); // this file's folder
  $base = str_replace($doc, '', $dir);
  return $base === '' ? '' : $base;
}
if (!defined('BASE_URL')) define('BASE_URL', compute_base_url());

$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $it) { $cartCount += (is_array($it)&&isset($it['qty'])) ? (int)$it['qty'] : 1; }
}
$username = $_SESSION['username'] ?? null;
$role     = $_SESSION['role'] ?? null;

$GROUP = rawurlencode('Group U'); // "Group%20U"
?>
<style>
  .navbar{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;background:#111;color:#fff;position:sticky;top:0;z-index:1000}
  .nav-brand a{color:#fff;text-decoration:none;font-weight:700;letter-spacing:.5px}
  .nav-links{display:flex;gap:10px;align-items:center}
  .nav-links a{color:#ddd;text-decoration:none;padding:6px 8px;border-radius:8px}
  .nav-links a:hover{background:#222;color:#fff}
  .badge{display:inline-block;min-width:18px;padding:1px 6px;border-radius:999px;background:#fff;color:#111;font-size:12px;margin-left:6px}
</style>

<div class="navbar">
  <div class="nav-brand"><a href="<?= BASE_URL ?>/index.php">SHOEZY</a></div>
  <div class="nav-links">
    <a href="<?= BASE_URL ?>/index.php">Home</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/men.php">Men</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/women.php">Women</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/kids.php">Kids</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/sport.php">Sport</a>
    <a href="<?= BASE_URL ?>/shop.php">Shop</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/about.php">About</a>
    <a href="<?= BASE_URL ?>/<?= $GROUP ?>/contact.php">Contact</a>
    <a href="<?= BASE_URL ?>/cart.php">Cart <span class="badge"><?= (int)$cartCount ?></span></a>

    <?php if (strcasecmp((string)$role,'admin')===0): ?>
      <a href="<?= BASE_URL ?>/admin.php">Admin</a>
      <a href="<?= BASE_URL ?>/admin_orders.php">Orders</a>
    <?php endif; ?>

    <?php if ($username): ?>
      <span>Welcome, <?= htmlspecialchars($username) ?></span>
      <a href="<?= BASE_URL ?>/logout.php">Logout</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login.php">Login</a>
      <a href="<?= BASE_URL ?>/register.php">Register</a>
    <?php endif; ?>
  </div>
</div>
