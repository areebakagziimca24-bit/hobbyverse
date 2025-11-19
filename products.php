<?php
include 'db_connect.php';
include 'header.php';

$hobby_id = isset($_GET['hobby_id']) ? intval($_GET['hobby_id']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$hobby = null;
$mode = "all_hobbies";

if ($hobby_id > 0) {
    $hobby = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hobbies WHERE hobby_id=$hobby_id"));
    if ($hobby) $mode = "single_hobby";
}

$order_by = "product_id DESC";
if ($sort === "price_asc") $order_by = "price ASC";
elseif ($sort === "price_desc") $order_by = "price DESC";
elseif ($sort === "newest") $order_by = "product_id DESC";

/* WISHLIST CHECK FUNCTION */
function isInWishlist($conn, $user_id, $product_id) {
    $q = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
    return mysqli_num_rows($q) > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    Hobbyverse | <?php echo ($mode === "single_hobby") ? htmlspecialchars($hobby['hobby_name']) : "Discover Hobbies"; ?>
  </title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    /* ===== overall ===== */
    :root{
      --accent:#ff4c8b;
      --bg:#fff8fa;
      --muted:#777;
      --card-shadow: 0 8px 20px rgba(0,0,0,0.06);
      --card-radius: 12px;
    }
    body{font-family:'Poppins',sans-serif;margin:0;background:var(--bg);color:#222;}
    a{color:inherit;text-decoration:none;}

    /* ===== hero (kept but reduced padding to avoid huge space) ===== */
    .hero{
      text-align:center;
      padding:80px 20px;               /* reduced from 140px to 80px to match original density */
      background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9)),
                  url('images/hero_illustration.jpg') center/cover no-repeat;
      border-radius:0 0 40px 40px;
      box-shadow:0 6px 18px rgba(0,0,0,0.06);
      margin-bottom:28px;
      position:relative;
    }
    .hero h1{font-size:34px;margin:0 0 6px;color:#ff6f91;}
    .hero p{color:var(--muted);margin:0 0 12px;}

    /* ===== sort bar ===== */
    .sort-bar{ text-align:center;margin:18px 0 28px; }
    .sort-bar select{
      padding:8px 14px;border-radius:20px;border:1px solid #eee;background:#fff;
      font-size:14px; box-shadow:0 6px 18px rgba(0,0,0,0.03);
    }

    /* ===== main section & grid - FORCE 3 columns on desktop like old layout ===== */
    .section{ max-width:1100px;margin:0 auto 80px;padding:0 18px; }
    .grid{
      display:grid;
      grid-template-columns: repeat(3, 1fr);   /* force 3 columns on desktop */
      gap:28px;                                /* match older spacing */
      align-items:start;
    }

    /* ===== card sizes (made fixed & compact to match your old screenshot) ===== */
    .card{
      background:#fff;
      border-radius:var(--card-radius);
      box-shadow:var(--card-shadow);
      overflow:hidden;
      transition:transform .18s, box-shadow .18s;
      display:flex;
      flex-direction:column;
      height:100%;             /* equal height cards */
    }
    .card:hover{ transform:translateY(-6px); box-shadow:0 12px 30px rgba(0,0,0,0.08); }

    /* image: EXACT PATH preserved (we do not change how src is output) */
    .card .thumb{
      width:100%;
      height:160px;            /* old smaller image height ~160-180 */
      object-fit:cover;
      display:block;
      border-bottom:1px solid rgba(0,0,0,0.03);
    }

    /* card body */
    .card-body{ padding:14px 16px; text-align:center; flex:1; display:flex; flex-direction:column; justify-content:space-between; }
    .category{ display:inline-block;background:#fff0f5;color:var(--accent);font-size:12px;padding:6px 10px;border-radius:14px;margin:6px auto 8px; }
    .card h3{ margin:6px 0 8px;font-size:16px;color:#222; font-weight:600; }
    .stock-status{ font-size:13px; margin-bottom:8px; }
    .in-stock{ color:#28a745; }
    .out-stock{ color:#e63946; }
    .price{ color:var(--accent); font-weight:700; font-size:15px; margin-bottom:10px; }

    .btn-row{ margin-top:8px; }
    .btn{
      display:inline-block;padding:10px 18px;border-radius:22px;background:var(--accent);color:#fff;font-weight:600;
      text-decoration:none;font-size:14px;
      box-shadow:0 6px 18px rgba(255,76,139,0.12);
    }
    .btn.disabled{ background:#ccc; pointer-events:none; box-shadow:none; }

    /* wishlist tiny heart (overlay) */
    .wishlist-btn{
      position:absolute;
      top:10px; right:10px;
      width:36px; height:36px; border-radius:50%;
      background:#fff; display:flex; align-items:center; justify-content:center;
      color:var(--accent); font-size:18px; box-shadow:0 4px 12px rgba(0,0,0,0.08); z-index:3;
    }
    .wishlist-btn.active{ background:var(--accent); color:#fff; }

    /* responsiveness: keep PC layout fixed until narrow screens */
    @media (max-width:1100px){
      .grid{ grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width:700px){
      .hero{ padding:60px 18px; }
      .grid{ grid-template-columns: 1fr; gap:18px; }
      .card .thumb{ height:200px; } /* slightly taller on mobile for better cropping */
    }
  </style>
</head>
<body>

<div class="hero" data-aos="fade-down">
  <?php
  $icons = ['Painting'=>'🎨','Gardening'=>'🌿','Photography'=>'📸','Crochet'=>'🧶','Sports'=>'⚽','Cars'=>'🚗','Reading'=>'📚'];
  $emoji = ($mode==="single_hobby" && isset($icons[$hobby['hobby_name']])) ? $icons[$hobby['hobby_name']] : '✨';
  ?>
  <h1><?php echo ($mode==="single_hobby" ? htmlspecialchars($hobby['hobby_name']) : 'Explore Hobbies'); ?></h1>
  <p><?php echo ($mode==="single_hobby" ? "Find products for ".strtolower($hobby['hobby_name']) : "Discover the things that make you happy."); ?></p>
</div>

<div class="sort-bar">
  <form method="GET" action="products.php">
    <?php if ($mode==="single_hobby"): ?>
      <input type="hidden" name="hobby_id" value="<?php echo $hobby_id; ?>">
    <?php endif; ?>
    <select name="sort" onchange="this.form.submit()">
      <option value="default" <?php if($sort=='default') echo 'selected'; ?>>Sort By</option>
      <option value="price_asc" <?php if($sort=='price_asc') echo 'selected'; ?>>Price: Low → High</option>
      <option value="price_desc" <?php if($sort=='price_desc') echo 'selected'; ?>>Price: High → Low</option>
      <option value="newest" <?php if($sort=='newest') echo 'selected'; ?>>Newest</option>
    </select>
  </form>
</div>

<div class="section" data-aos="fade-up">
  <div class="grid">
    <?php
    if ($mode === "single_hobby") {
        $result = mysqli_query($conn, "SELECT * FROM products WHERE hobby_id=$hobby_id ORDER BY $order_by");
        if (mysqli_num_rows($result) == 0) {
            echo "<div style='grid-column:1/-1;text-align:center;color:var(--muted)'>No products found.</div>";
        }
        while ($p = mysqli_fetch_assoc($result)) {
            // wishlist check
            $isFav = false;
            if (isset($_SESSION['user_id'])) {
                $isFav = isInWishlist($conn, $_SESSION['user_id'], $p['product_id']);
            }
            $favClass = $isFav ? 'active' : '';

            $stock = isset($p['stock']) ? intval($p['stock']) : 0;
            $in_stock = $stock > 0;
            $status_class = $in_stock ? 'in-stock' : 'out-stock';
            $status_text = $in_stock ? 'In Stock' : 'Out of Stock';

            // *** IMPORTANT: keep the image path exactly as saved in DB (no changes) ***
            $imgSrc = $p['image']; // e.g. "https://i.pinimg.com/..." or "images/xxx.jpg"
            ?>
            <div class="card" data-aos="zoom-in" style="position:relative;">
                <a class="wishlist-btn <?php echo $favClass ?>" href="wishlist_action.php?id=<?php echo $p['product_id'] ?>">❤</a>
                <img class="thumb" src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>">
                <div class="card-body">
                    <div>
                      <span class="category"><?php echo htmlspecialchars($hobby['hobby_name']); ?></span>
                      <h3><?php echo htmlspecialchars($p['product_name']); ?></h3>
                      <div class="stock-status <?php echo $status_class; ?>"><?php echo ($in_stock ? '✅ ' : '❌ ') . $status_text; ?></div>
                      <div class="price">₹<?php echo number_format($p['price'], 2); ?></div>
                    </div>

                    <div class="btn-row">
                      <a class="btn <?php echo ($in_stock ? '' : 'disabled'); ?>" href="<?php echo ($in_stock ? "add_to_cart.php?id={$p['product_id']}" : '#'); ?>">
                        <?php echo ($in_stock ? 'Add to Cart' : 'Unavailable'); ?>
                      </a>
                    </div>
                </div>
            </div>
        <?php
        }
    } else {
        $hobbies = mysqli_query($conn, "SELECT * FROM hobbies");
        while ($h = mysqli_fetch_assoc($hobbies)) {
            $img = $h['hobby_image'] ?: 'https://via.placeholder.com/400x300?text=Hobby';
            ?>
            <div class="card" data-aos="zoom-in">
              <img class="thumb" src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($h['hobby_name']); ?>">
              <div class="card-body">
                <h3><?php echo htmlspecialchars($h['hobby_name']); ?></h3>
                <div style="margin-top:10px"><a class="btn" href="products.php?hobby_id=<?php echo $h['hobby_id']; ?>">Explore</a></div>
              </div>
            </div>
        <?php
        }
    }
    ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:700, once:true});</script>

</body>
</html>
<?php include 'footer.php'; ?>
