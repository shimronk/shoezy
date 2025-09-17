<?php
// import_from_html.php — one-time importer from your existing HTML pages to MySQL
// Usage:
//   Preview (no writes):  http://localhost/YourFolder/import_from_html.php?dry=1
//   Import (writes DB):   http://localhost/YourFolder/import_from_html.php
ini_set('display_errors', 1); error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

// --- Map your HTML files to categories (adjust names/case if different) ---
$files = [
  'Group U/Men.html'   => 'men',
  'Group U/Women.html' => 'women',
  'Group U/Kids.html'  => 'kids',
  'Group U/Sport.html' => 'sport',
];
// If some are .php instead of .html, you can add:
// 'Group U/Kids.php' => 'kids',

$dryRun = isset($_GET['dry']); // ?dry=1 to preview
$totalFound = 0; $inserted = 0; $skippedExisting = 0; $missingFiles = [];

function text_clean($s) {
  $s = strip_tags($s);
  $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5);
  return trim(preg_replace('/\s+/', ' ', $s));
}

// Try to find a price near a product name
function extract_products($html) {
  $out = [];
  // Normalize whitespace
  $htmlNorm = preg_replace('/\s+/', ' ', $html);

  // Find names in common tags: h2/h3/h4 or things with "card-title" in class
  preg_match_all('/<(h2|h3|h4)[^>]*>(.*?)<\/\1>/i', $htmlNorm, $hdrs, PREG_OFFSET_CAPTURE);
  $candidates = [];
  foreach ($hdrs[2] as $m) {
    $name = text_clean($m[0]);
    if ($name && mb_strlen($name) <= 120) {
      $candidates[] = ['name' => $name, 'pos' => $m[1]];
    }
  }
  // Also try elements with class "card-title"
  preg_match_all('/class="[^"]*card-title[^"]*"[^>]*>(.*?)<\/[^>]+>/i', $htmlNorm, $ct, PREG_OFFSET_CAPTURE);
  foreach ($ct[1] as $m) {
    $name = text_clean($m[0]);
    if ($name && mb_strlen($name) <= 120) {
      $candidates[] = ['name' => $name, 'pos' => $m[1]];
    }
  }

  // For each candidate name, look around it for price & image
  foreach ($candidates as $c) {
    $start = max(0, $c['pos'] - 800);
    $end   = min(strlen($htmlNorm), $c['pos'] + 1200);
    $win   = substr($htmlNorm, $start, $end - $start);

    // Price patterns: LKR 7,950 | Rs 7,950 | Rs. 7950
    $price = null;
    if (preg_match('/(?:LKR|Rs\.?)[\s]*([0-9][0-9,\.]*)/i', $win, $pm)) {
      $p = str_replace([',', ' '], '', $pm[1]);
      if (is_numeric($p)) $price = (float)$p;
    }

    // Image near the name
    $image = null;
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $win, $im)) {
      $image = $im[1];
    }

    $out[] = ['name' => $c['name'], 'price' => $price, 'image' => $image];
  }

  // Dedup by name
  $seen = [];
  $uniq = [];
  foreach ($out as $x) {
    $key = mb_strtolower($x['name']);
    if (isset($seen[$key])) continue;
    $seen[$key] = true;
    $uniq[] = $x;
  }
  return $uniq;
}

// Prepared statements
$checkStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name)=LOWER(?) AND LOWER(TRIM(category))=LOWER(TRIM(?))");
$insStmt   = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($files as $rel => $category) {
  $path = __DIR__ . DIRECTORY_SEPARATOR . $rel;
  if (!file_exists($path)) { $missingFiles[] = $rel; continue; }

  $html = file_get_contents($path);
  $items = extract_products($html);
  $totalFound += count($items);

  echo "<h3>Scanning $rel → category=$category</h3>";
  if (!$items) { echo "<p>Found 0 candidates.</p>"; continue; }

  foreach ($items as $it) {
    $name  = $it['name'];
    $price = $it['price'] ?? 0.00;
    $img   = $it['image'] ?? null;

    // Skip empty names
    if (!$name) continue;

    // Check duplicate (same name + category)
    $checkStmt->bind_param("ss", $name, $category);
    $checkStmt->execute();
    $checkStmt->bind_result($cnt);
    $checkStmt->fetch();
    $checkStmt->free_result();

    if ($cnt > 0) {
      $skippedExisting++;
      echo "• SKIP (exists): ".htmlspecialchars($name)." — ".$category."<br>";
      continue;
    }

    echo "• ".htmlspecialchars($name)." — Rs ".number_format($price,2)." — img: ".htmlspecialchars((string)$img);

    if ($dryRun) {
      echo " (dry-run)<br>";
    } else {
      $desc = '';
      $stock = 10;
      $insStmt->bind_param("ssdiis", $name, $desc, $price, $stock, $category, $img);
      $insStmt->execute();
      echo " — <strong>inserted</strong><br>";
      $inserted++;
    }
  }
}

echo "<hr>";
echo "<p>Total candidates found: $totalFound</p>";
echo "<p>Inserted: $inserted</p>";
echo "<p>Skipped (duplicate): $skippedExisting</p>";
if ($missingFiles) {
  echo "<p>Missing files:</p><ul>";
  foreach ($missingFiles as $mf) echo "<li>".htmlspecialchars($mf)."</li>";
  echo "</ul>";
}
