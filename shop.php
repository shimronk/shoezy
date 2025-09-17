<?php
// File: shop.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';
include __DIR__ . '/navbar.php';

/**
 * Build a safe image URL for a product.
 * - Accepts full URLs (http/https)
 * - Accepts relative paths already including "images/"
 * - Accepts bare filenames and maps them to "images/<filename>"
 * - Falls back to "images/noimage.png" if missing
 */
function product_image_url(?string $raw): string {
  $raw = trim((string)$raw);

  // If it's already an absolute URL, just return it
  if ($raw !== '' && preg_match('~^https?://~i', $raw)) {
    return $raw;
  }

  // Candidates to try (URL paths for the browser) — order matters
  $filename = basename($raw);                    // w1.jpg
  $candidates = [];

  if ($raw !== '' && stripos($raw, 'images/') === 0) {
    // already something like "images/w1.jpg"
    $candidates[] = $raw;
  }
  if ($filename !== '') {
    $candidates[] = "images/{$filename}";
  }

  // Always include fallback last
  $candidates[] = "images/noimage.png";

  // Choose the first candidate that physically exists on disk;
  // if none found, return the last (fallback)
  foreach ($candidates as $rel) {
    $disk = __DIR__ . '/' . $rel;
    if (is_file($disk)) {
      return $rel;
    }
  }
  return end($candidates);
}

// Fetch products (adjust columns to your schema)
$sql = "SELECT id, name, price, image, description, stock FROM products ORDER BY id DESC";
$res = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root { --card-b: #e8e8e8; --muted:#666; }
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#fafafa; margin:0; }
    .wrap { max-width: 1200px; margin: 24px auto; padding: 0 14px 24px; }
    h2 { margin: 16px 0 20px; font-size: 28px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px; }
    .card { background:#fff; border:1px solid var(--card-b); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; }
    .img-wrap { aspect-ratio: 4/3; background:#f7f7f7; border-bottom:1px solid var(--card-b); display:flex; align-items:center; justify-content:center; }
    .img-wrap img { width:100%; height:100%; object-fit:cover; display:block; }
    .content { padding:12px 12px 14px; display:flex; flex-direction:column; gap:6px; }
    .title { font-weight:700; font-size:16px; line-height:1.2; }
    .desc { color:var(--muted); font-size:13px; height:34px; overflow:hidden; }
    .price { font-weight:600; margin-top:4px; }
    form { margin-top:8px; display:grid; grid-template-columns: 1fr 1fr; gap:8px; align-items:end; }
    label { font-size:13px; color:#333; display:block; }
    select, input[type=number] { width:100%; padding:6px 8px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
    .row-span { grid-column: 1 / -1; }
    button { background:#222; color:#fff; border:0; padding:9px 12px; border-radius:8px; cursor:pointer; }
    button:disabled { opacity:.6; cursor:not-allowed; }
    .oos { color:#b00; font-weight:600; }
  </style>
</head>
<body>
<div class="wrap">
  <h2>Shop All Shoes</h2>

  <?php if(!$res || $res->num_rows === 0): ?>
    <p>No products found.</p>
  <?php else: ?>
    <div class="grid">
      <?php while($p = $res->fetch_assoc()): ?>
        <?php
          $imgUrl = product_image_url($p['image'] ?? '');
          $stock  = (int)($p['stock'] ?? 0);
          $disabled = $stock <= 0;
        ?>
        <div class="card">
          <div class="img-wrap">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 loading="lazy">
          </div>
          <div class="content">
            <div class="title"><?= htmlspecialchars($p['name']) ?></div>
            <?php if (!empty($p['description'])): ?>
              <div class="desc"><?= htmlspecialchars($p['description']) ?></div>
            <?php endif; ?>
            <div class="price">Rs. <?= number_format((float)$p['price'], 2) ?></div>

            <?php if ($disabled): ?>
              <div class="row-span oos">Out of stock</div>
            <?php else: ?>
              <form action="cart.php" method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

                <div>
                  <label>Size
                    <select name="size" required>
                      <option value="" disabled selected>Choose size</option>
                      <?php for($s=35;$s<=46;$s++): ?>
                        <option value="<?= $s ?>"><?= $s ?></option>
                      <?php endfor; ?>
                    </select>
                  </label>
                </div>

                <div>
                  <label>Qty
                    <input type="number" name="qty" value="1" min="1" max="<?= max(1, $stock) ?>">
                  </label>
                </div>

                <div class="row-span">
                  <button type="submit">Add to Cart</button>
                </div>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
