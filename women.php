<?php
// File: Group U/women.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../db.php';
include __DIR__ . '/../navbar.php';

$category = 'women'; // make sure this matches your DB values
$stmt = $conn->prepare("SELECT * FROM products WHERE category=? ORDER BY id DESC");
$stmt->bind_param("s", $category);
$stmt->execute();
$products = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Women - SHOEZY</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#fafafa}
    .wrap{max-width:1100px;margin:24px auto;padding:16px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
    .card{background:#fff;border:1px solid #e5e5e5;border-radius:10px;padding:12px}
    .card h3{margin:8px 0 6px;font-size:18px}
    .price{font-weight:600}
    .img{width:100%;height:180px;object-fit:cover;border-radius:8px;border:1px solid #eee;background:#f7f7f7}
    form{margin-top:8px}
    label{display:block;margin:4px 0 6px}
    button{padding:8px 12px;border:1px solid #111;background:#111;color:#fff;border-radius:8px;cursor:pointer}
    input[type=number], select{padding:6px;border:1px solid #ccc;border-radius:6px}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Women</h2>
    <div class="grid">
      <?php while($p = $products->fetch_assoc()): ?>
        <div class="card">
          <?php if (!empty($p['image'])): ?>
            <img class="img" src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
          <?php endif; ?>
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <p><?php echo htmlspecialchars($p['description']); ?></p>
          <div class="price">Rs. <?php echo number_format((float)$p['price'],2); ?></div>

          <form action="../cart.php" method="post">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
            <label>Size
              <select name="size" required>
                <option value="" disabled selected>Choose size</option>
                <?php for($s=35;$s<=46;$s++): ?>
                  <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                <?php endfor; ?>
              </select>
            </label>
            <label>Qty
              <input type="number" name="qty" value="1" min="1" max="<?php echo (int)($p['stock'] ?? 99); ?>" style="width:80px;">
            </label>
            <button type="submit">Add to Cart</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>
