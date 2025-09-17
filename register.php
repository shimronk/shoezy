<?php
// register.php
session_start();
require_once __DIR__ . '/db.php';   // must define $conn = new mysqli(...)

$errors = [];
$success = false;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Trim + collect
  $username     = trim($_POST['username'] ?? '');
  $email        = trim($_POST['email'] ?? '');
  $password     = $_POST['password'] ?? '';
  $phone        = trim($_POST['phone'] ?? '');
  $address_line = trim($_POST['address_line'] ?? '');
  $city         = trim($_POST['city'] ?? '');
  $postal_code  = trim($_POST['postal_code'] ?? '');

  // Validate
  if ($username === '')         { $errors[] = 'Username is required.'; }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }
  if (strlen($password) < 6)    { $errors[] = 'Password must be at least 6 characters.'; }
  if ($phone === '')            { $errors[] = 'Phone is required.'; }
  if ($address_line === '')     { $errors[] = 'Home address is required.'; }
  if ($city === '')             { $errors[] = 'City is required.'; }
  if ($postal_code === '')      { $errors[] = 'Postal code is required.'; }

  // If OK, insert
  if (!$errors) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Optional: ensure unique email
    $check = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
      $errors[] = 'This email is already registered.';
    }
    $check->close();

    if (!$errors) {
      $stmt = $conn->prepare("
        INSERT INTO users (username, email, password, phone, address_line, city, postal_code)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      ");
      if (!$stmt) {
        $errors[] = "DB prepare failed: " . $conn->error;
      } else {
        $stmt->bind_param(
          "sssssss",
          $username, $email, $hash, $phone, $address_line, $city, $postal_code
        );
        if ($stmt->execute()) {
          $success = true;

          // Optional: auto-login after registration
          $_SESSION['user_id'] = $stmt->insert_id;
          $_SESSION['username'] = $username;

          // Redirect to home or account page
          header("Location: index.php");
          exit;
        } else {
          // Duplicate username? add a unique index if you want to enforce it
          $errors[] = "Registration failed: " . $stmt->error;
        }
        $stmt->close();
      }
    }
  }
}
?>
<?php /* If you use a navbar include, keep the path correct for your project */ ?>
<?php /* include __DIR__ . '/navbar.php'; */ ?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#fafafa}
    .wrap{max-width:560px;margin:40px auto;padding:24px;background:#fff;border:1px solid #eee;border-radius:12px}
    label{display:block;margin-top:12px;font-weight:600}
    input{width:100%;padding:10px;border:1px solid #ccc;border-radius:8px;margin-top:6px}
    button{margin-top:18px;padding:12px 16px;border:0;border-radius:10px;cursor:pointer;background:#111;color:#fff}
    .errors{background:#ffecec;border:1px solid #ffbcbc;color:#a40000;padding:12px;border-radius:10px;margin-bottom:12px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Create your account</h2>

    <?php if ($errors): ?>
      <div class="errors">
        <ul style="margin:0;padding-left:18px">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <label>Username
        <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </label>

      <label>Email
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </label>

      <label>Password (min 6 chars)
        <input type="password" name="password" minlength="6" required>
      </label>

      <label>Phone
        <input type="text" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </label>

      <label>Home address
        <input type="text" name="address_line" required placeholder="Street / House No."
               value="<?= htmlspecialchars($_POST['address_line'] ?? '') ?>">
      </label>

      <div class="row">
        <label>City
          <input type="text" name="city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </label>
        <label>Postal code
          <input type="text" name="postal_code" required value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>">
        </label>
      </div>

      <button type="submit">Create account</button>
    </form>
  </div>
</body>
</html>
