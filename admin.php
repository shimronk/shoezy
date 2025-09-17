<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';   // <-- loads $conn

// quick sanity
if (!isset($conn)) { die('DB not loaded'); }

// admin gate (case-insensitive)
if (strcasecmp($_SESSION['role'] ?? '', 'admin') !== 0) {
  header('HTTP/1.1 403 Forbidden');
  exit('Access denied');
}

// Create/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category = $_POST['category'] ?? 'general';
    $image = $_POST['image'] ?? '';

    if (isset($_POST['id']) && $_POST['id'] !== '') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image=? WHERE id=?");
        $stmt->bind_param("ssdissi", $name, $desc, $price, $stock, $category, $image, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssdiis", $name, $desc, $price, $stock, $category, $image);
        $stmt->execute();
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<?php include 'navbar.php'; ?>
<div style="max-width:900px;margin:24px auto;padding:16px;">
  <h2>Admin — Products</h2>
  <form method="post" style="border:1px solid #ccc;padding:12px;border-radius:10px;margin-bottom:16px;">
    <input type="hidden" name="id" value="">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
      <input name="name" placeholder="Name" required>
      <input name="price" type="number" step="0.01" placeholder="Price" required>
      <input name="stock" type="number" placeholder="Stock" value="10">
      <input name="category" placeholder="Category (men/women/kids/sport/general)" value="general">
      <input name="image" placeholder="Image URL (optional)">
      <textarea name="description" placeholder="Description" style="grid-column:1/-1;"></textarea>
    </div>
    <button type="submit" style="margin-top:10px;">Save</button>
  </form>

  <table border="1" cellpadding="6" cellspacing="0" width="100%">
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Category</th><th>Action</th></tr>
    <?php while($p = $products->fetch_assoc()): ?>
      <tr>
        <td><?php echo $p['id']; ?></td>
        <td><?php echo htmlspecialchars($p['name']); ?></td>
        <td><?php echo number_format($p['price'],2); ?></td>
        <td><?php echo (int)$p['stock']; ?></td>
        <td><?php echo htmlspecialchars($p['category']); ?></td>
        <td>
          <a href="admin.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
