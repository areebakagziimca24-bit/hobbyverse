<?php
// admin.php - single-file admin dashboard with inventory + analytics
// Place in project root. Assumes db_connect.php defines $conn (mysqli) and session cookies work.
session_start();
require_once __DIR__ . '/db_connect.php';

// ---------- Config ----------
$LOW_STOCK_THRESHOLD = 5; // <= this shows "Low stock"
$RESTOCK_AMOUNT = 10;      // amount added when Quick Restock clicked

// ---------- Access check ----------
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not admin -> redirect to normal login
    header('Location: login.php');
    exit;
}

// ---------- Helpers ----------
function esc($conn, $v) {
    return mysqli_real_escape_string($conn, trim($v));
}
function flash_and_redirect($msg = '', $loc = 'admin.php') {
    // store in session for one-time display
    if ($msg !== '') $_SESSION['admin_notice'] = $msg;
    header("Location: $loc");
    exit;
}

// ---------- Handle POST actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_stock' && isset($_POST['product_id'])) {
        $pid = intval($_POST['product_id']);
        $stock = intval($_POST['stock']);
        mysqli_query($conn, "UPDATE products SET stock = $stock WHERE product_id = $pid");
        flash_and_redirect("Stock updated for product ID $pid.");
    }

    if ($action === 'restock' && isset($_POST['product_id'])) {
        $pid = intval($_POST['product_id']);
        $inc = intval($_POST['amount'] ?? $RESTOCK_AMOUNT);
        mysqli_query($conn, "UPDATE products SET stock = stock + $inc WHERE product_id = $pid");
        flash_and_redirect("Product ID $pid restocked by $inc units.");
    }

    if ($action === 'delete_product' && isset($_POST['product_id'])) {
        $pid = intval($_POST['product_id']);
        mysqli_query($conn, "DELETE FROM products WHERE product_id = $pid");
        flash_and_redirect("Product ID $pid deleted.");
    }

    if ($action === 'add_product') {
        $name = esc($conn, $_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $hobby = intval($_POST['hobby_id'] ?? 0);
        $image = esc($conn, $_POST['image'] ?? '');
        $stock = intval($_POST['stock'] ?? 0);

        if ($name === '' || $price <= 0) {
            flash_and_redirect("Provide valid product name and price.");
        } else {
            $q = "INSERT INTO products (product_name, price, hobby_id, image, stock)
                  VALUES ('$name', $price, $hobby, '$image', $stock)";
            mysqli_query($conn, $q);
            flash_and_redirect("Product '$name' added.");
        }
    }

    if ($action === 'add_hobby') {
        $hname = esc($conn, $_POST['hobby_name'] ?? '');
        $desc = esc($conn, $_POST['description'] ?? '');
        $img = esc($conn, $_POST['hobby_image'] ?? '');
        if ($hname === '') flash_and_redirect("Hobby name required.");
        mysqli_query($conn, "INSERT INTO hobbies (hobby_name, description, hobby_image) VALUES ('$hname', '$desc', '$img')");
        flash_and_redirect("Hobby '$hname' added.");
    }
}

// After POST handling fall-through to display page. Fetch latest data:

// Users (adjust columns according to your users table)
$users_res = mysqli_query($conn, "SELECT user_id, username, email, role FROM users ORDER BY user_id DESC");

// Products with analytics: left join sums from order_items
// total_sold = SUM(quantity), total_revenue = SUM(subtotal)
$products_res = mysqli_query($conn, "
    SELECT p.*,
           IFNULL(oi.total_qty,0) AS total_sold,
           IFNULL(oi.total_rev,0) AS total_revenue
    FROM products p
    LEFT JOIN (
        SELECT product_id, SUM(quantity) AS total_qty, SUM(subtotal) AS total_rev
        FROM order_items
        GROUP BY product_id
    ) oi ON oi.product_id = p.product_id
    ORDER BY p.product_id DESC
");

// Hobbies
$hobbies_res = mysqli_query($conn, "SELECT * FROM hobbies ORDER BY hobby_id DESC");

// Flash message
$notice = '';
if (isset($_SESSION['admin_notice'])) {
    $notice = $_SESSION['admin_notice'];
    unset($_SESSION['admin_notice']);
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Hobbyverse Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
:root{
  --accent:#ff4c8b;
  --bg:#fff8fa;
  --card:#fff;
  --muted:#7b7b7b;
  --shadow: 0 10px 30px rgba(0,0,0,0.06);
  --low:#ffb3c6;
  --danger:#ffd6da;
}
*{box-sizing:border-box}
body{font-family:'Poppins',sans-serif;margin:0;background:var(--bg);color:#222;}
.header{
  background:var(--accent); color:#fff; padding:18px 28px; display:flex; align-items:center; justify-content:space-between;
  box-shadow:0 2px 6px rgba(0,0,0,0.06);
}
.header h1{margin:0;font-size:20px}
.header a{color:#fff;text-decoration:none;font-weight:600}

/* container */
.wrap{max-width:1200px;margin:28px auto;padding:0 18px}

/* card */
.card{background:var(--card);border-radius:12px;padding:18px;box-shadow:var(--shadow);margin-bottom:20px}
.row{display:flex;gap:20px;flex-wrap:wrap}
.col{flex:1}

/* tables */
.table{width:100%;border-collapse:collapse}
.table th, .table td{padding:12px 10px;border-bottom:1px solid #f5f5f5;text-align:left;vertical-align:middle}
.table th{background:#fff0f5;color:var(--accent);font-weight:600}
.small{font-size:13px;color:var(--muted)}
.actions button, .actions a{margin-right:8px}

/* forms */
.form-inline{display:flex;gap:8px;align-items:center}
.input, input, select, textarea{padding:8px;border-radius:8px;border:1px solid #e8e8e8;font-size:14px}
.btn{background:var(--accent);color:#fff;padding:8px 14px;border-radius:8px;border:none;cursor:pointer}
.btn.ghost{background:#fff;color:var(--accent);border:1px solid #ffd6e1}
.badge{display:inline-block;padding:6px 10px;border-radius:999px;font-weight:600;font-size:13px}
.badge.ok{background:#e9fff1;color:#23823a}
.badge.low{background:var(--low);color:#93504f}
.badge.out{background:var(--danger);color:#9a2b2b}

/* responsive */
@media (max-width:900px){
  .row{flex-direction:column}
}
.notice{padding:12px;margin-bottom:12px;border-radius:8px;background:#fff7f9;color:var(--muted);border:1px solid #ffdbe6}
.product-meta{display:flex;gap:8px;align-items:center}
.product-meta img{width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #f0f0f0}

/* small helpers */
.center{text-align:center}
</style>
</head>
<body>

<header class="header">
  <h1>Hobbyverse Admin ✨</h1>
  <div>
    <a href="index.php" target="_blank" class="small">View Site</a> &nbsp; | &nbsp;
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="wrap">
  <?php if ($notice): ?>
    <div class="card notice"><?= htmlspecialchars($notice) ?></div>
  <?php endif; ?>

  <!-- USERS -->
  <div class="card">
    <h2 style="color:var(--accent)">👤 Users</h2>
    <p class="small">Registered users (read-only here).</p>
    <table class="table">
      <thead>
        <tr>
          <th style="width:90px">User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th style="width:100px">Role</th>
          <th style="width:160px">Registered</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($u = mysqli_fetch_assoc($users_res)): ?>
          <tr>
            <td><?= htmlspecialchars($u['user_id']) ?></td>
            <td><?= htmlspecialchars($u['username'] ?: ($u['name'] ?? '')) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td class="small"><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="row">

    <!-- PRODUCTS / STOCK -->
    <div class="col card" style="min-width:320px">
      <h2 style="color:var(--accent)">📦 Products & Inventory</h2>
      <p class="small">Update stock, delete product, quick restock, see sales & revenue.</p>

      <table class="table" style="margin-bottom:12px">
        <thead>
          <tr><th style="width:70px">ID</th><th>Product</th><th style="width:100px">Price</th><th style="width:120px">Stock</th><th style="width:160px">Sales / Revenue</th><th style="width:170px">Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($p = mysqli_fetch_assoc($products_res)): 
              $stock = intval($p['stock']);
              $sold = intval($p['total_sold']);
              $rev = floatval($p['total_revenue']);
              $badgeClass = $stock <= 0 ? 'out' : ($stock <= $LOW_STOCK_THRESHOLD ? 'low' : 'ok');
          ?>
            <tr>
              <td><?= htmlspecialchars($p['product_id']) ?></td>
              <td>
                <div style="font-weight:600"><?= htmlspecialchars($p['product_name']) ?></div>
                <div class="small"><?= htmlspecialchars(substr($p['description'] ?? '',0,70)) ?></div>
              </td>
              <td>₹<?= number_format($p['price'],2) ?></td>

              <td>
                <form method="POST" class="form-inline" style="align-items:center">
                  <input type="hidden" name="action" value="update_stock">
                  <input type="hidden" name="product_id" value="<?= intval($p['product_id']) ?>">
                  <input type="number" name="stock" value="<?= $stock ?>" class="input" style="width:80px">
                  <button class="btn" type="submit">Save</button>
                </form>
                <div style="margin-top:6px">
                  <span class="badge <?= $badgeClass ?>">
                    <?= $stock <= 0 ? 'Out' : ($stock <= $LOW_STOCK_THRESHOLD ? 'Low' : 'In Stock') ?>
                  </span>
                </div>
              </td>

              <td>
                <div class="small">Sold: <strong><?= $sold ?></strong></div>
                <div class="small">Revenue: <strong>₹<?= number_format($rev,2) ?></strong></div>
              </td>

              <td class="actions">
                <!-- Quick Restock -->
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="restock">
                  <input type="hidden" name="product_id" value="<?= intval($p['product_id']) ?>">
                  <input type="hidden" name="amount" value="<?= intval($RESTOCK_AMOUNT) ?>">
                  <button class="btn ghost" type="submit" title="Add <?= $RESTOCK_AMOUNT ?> units">+<?= $RESTOCK_AMOUNT ?></button>
                </form>

                <!-- Delete product -->
                <form method="POST" style="display:inline" onsubmit="return confirm('Delete product <?= htmlspecialchars(addslashes($p['product_name'])) ?>?');">
                  <input type="hidden" name="action" value="delete_product">
                  <input type="hidden" name="product_id" value="<?= intval($p['product_id']) ?>">
                  <button class="btn ghost" type="submit" style="color:#ff4c8b;border-color:#ffd6e1">Delete</button>
                </form>

                <?php if (!empty($p['image'])): ?>
                  <a class="btn ghost" href="<?= htmlspecialchars($p['image']) ?>" target="_blank" style="color:#555;border-color:#eee">Image</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Add new product -->
      <details style="margin-top:12px;padding:12px;border-radius:8px;border:1px dashed #ffd6e1;background:#fff">
        <summary style="cursor:pointer;font-weight:600;color:var(--accent)">+ Add Product</summary>
        <form method="POST" style="margin-top:10px">
          <input type="hidden" name="action" value="add_product">
          <div style="margin-bottom:8px"><input class="input" name="name" placeholder="Product name" required></div>
          <div style="margin-bottom:8px;display:flex;gap:8px">
            <input class="input" name="price" placeholder="Price" type="number" step="0.01" required style="flex:1">
            <select name="hobby_id" class="input" style="width:160px">
              <option value="0">Hobby (optional)</option>
              <?php
                $tmp = mysqli_query($conn, "SELECT hobby_id, hobby_name FROM hobbies ORDER BY hobby_name ASC");
                while ($hh = mysqli_fetch_assoc($tmp)) {
                  echo '<option value="'.intval($hh['hobby_id']).'">'.htmlspecialchars($hh['hobby_name']).' ('.$hh['hobby_id'].')</option>';
                }
              ?>
            </select>
          </div>
          <div style="margin-bottom:8px"><input class="input" name="image" placeholder="Image URL (keep same path as DB)"></div>
          <div style="margin-bottom:8px"><input class="input" name="stock" placeholder="Stock" type="number" value="10"></div>
          <button class="btn" type="submit">Add Product</button>
        </form>
      </details>

    </div>

    <!-- HOBBIES -->
    <div class="col card" style="min-width:300px;max-width:540px">
      <h2 style="color:var(--accent)">🎯 Hobbies</h2>
      <p class="small">Add or view hobby categories (images allowed as URLs).</p>

      <table class="table" style="margin-bottom:12px">
        <thead><tr><th style="width:70px">ID</th><th>Hobby</th><th>Description</th></tr></thead>
        <tbody>
          <?php
            mysqli_data_seek($hobbies_res, 0);
            while ($h = mysqli_fetch_assoc($hobbies_res)):
          ?>
            <tr>
              <td><?= intval($h['hobby_id']) ?></td>
              <td><?= htmlspecialchars($h['hobby_name']) ?></td>
              <td class="small"><?= htmlspecialchars(substr($h['description'] ?? '',0,120)) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <details style="padding:12px;border-radius:8px;border:1px dashed #ffd6e1;background:#fff">
        <summary style="cursor:pointer;font-weight:600;color:var(--accent)">+ Add Hobby</summary>
        <form method="POST" style="margin-top:10px">
          <input type="hidden" name="action" value="add_hobby">
          <div style="margin-bottom:8px"><input class="input" name="hobby_name" placeholder="Hobby name" required></div>
          <div style="margin-bottom:8px"><textarea class="input" name="description" placeholder="Short description"></textarea></div>
          <div style="margin-bottom:8px"><input class="input" name="hobby_image" placeholder="Image URL (optional)"></div>
          <button class="btn" type="submit">Add Hobby</button>
        </form>
      </details>

    </div>

  </div> <!-- end row -->

</div> <!-- end wrap -->

</body>
</html>
