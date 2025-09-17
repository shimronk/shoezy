<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

function base_url(): string {
  $doc  = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
  $dir  = rtrim(str_replace('\\','/', realpath(__DIR__)), '/');
  $base = str_replace($doc, '', $dir);
  return $base === '' ? '' : $base;  // e.g., "/SHOEZY_DB_wired_categories"
}
$BASE = base_url();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $hashed, $role);
    $stmt->fetch();
    if (password_verify($password, $hashed)) {
      session_regenerate_id(true);
      $_SESSION['username'] = $username;
      $_SESSION['role']     = strtolower(trim($role));  // "admin" or "customer"
      $_SESSION['user_id']  = (int)$id;
      header("Location: {$BASE}/index.php");
      exit;
    }
  }
  $error = "Invalid username or password.";
}
?>
<?php include __DIR__ . '/navbar.php'; ?>

<div style="max-width:480px;margin:32px auto;padding:16px;">
  <h2>Login</h2>
  <?php if (!empty($error)): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label><br>
    <input name="username" required style="width:100%;padding:10px;margin:8px 0;"><br>
    <label>Password</label><br>
    <input type="password" name="password" required style="width:100%;padding:10px;margin:8px 0;"><br>
    <button type="submit" style="padding:10px 14px;">Login</button>
  </form>
</div>
