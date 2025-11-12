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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>
    Hobbyverse | <?php echo ($mode === "single_hobby") ? htmlspecialchars($hobby['hobby_name']) : "Discover Hobbies"; ?>
  </title>
  <link rel="icon" type="image/png"
        href="https://media.istockphoto.com/id/1968163891/vector/creative-professions-people-artisans-at-work-musician-graffiti-artist-sculptor-puppeteer.jpg?s=2048x2048&w=is&k=20&c=J9Wuc5d6s7iZKXmTEkcdp9AAiwSHocnshY3wuzUR_nU=">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root {
      --accent:#ff4c8b;
      --accent2:#ff9a9e;
      --bg:#fff8fa;
      --shadow:0 8px 24px rgba(0,0,0,0.08);
    }
    body { font-family:'Poppins',sans-serif; margin:0; background:var(--bg); color:#333; overflow-x:hidden; }

    /* ✅ Hero Section */
    .hero {
      text-align:center; padding:140px 20px;
      background:linear-gradient(180deg,rgba(255,255,255,0.85),rgba(255,255,255,0.95)),
                 url('images/hero_illustration.jpg') center/cover no-repeat;
      border-radius:0 0 60px 60px; box-shadow:0 6px 24px rgba(0,0,0,0.08);
      position:relative; overflow:hidden;
    }
    .hero h1 { font-size:52px; font-weight:700; margin-bottom:10px; color:#222; text-shadow:0 2px 8px rgba(255,255,255,0.7); }
    .hero p { font-size:18px; color:#555; margin-bottom:20px; }
    .back-btn {
      display:inline-block; text-decoration:none; color:#fff;
      background:var(--accent); padding:12px 26px; border-radius:30px;
      font-weight:600; transition:all 0.3s;
    }
    .back-btn:hover { background:#e63972; transform:scale(1.05); }

    /* Floating Emoji */
    .floating-emoji { position:absolute; top:25%; left:50%; transform:translate(-50%, -50%); font-size:130px; opacity:0.12; z-index:0; animation: floaty 6s ease-in-out infinite; }
    @keyframes floaty { 0%,100%{transform:translate(-50%,-50%) scale(1);opacity:0.12;}50%{transform:translate(-50%,-60%) scale(1.1);opacity:0.22;} }

    /* Sort Bar */
    .sort-bar { text-align:center; margin:30px 0 10px; }
    select {
      padding:10px 14px; border-radius:25px; border:1px solid #ddd; background:#fff;
      font-size:15px; transition:0.3s; cursor:pointer;
    }
    select:hover { border-color:var(--accent); }

    /* Section Layout */
    .section { padding:60px 80px; max-width:1200px; margin:auto; }
    .grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:30px; }

    /* ✅ Product Cards */
    .card {
      background:#fff; border-radius:18px; box-shadow:var(--shadow);
      overflow:hidden; position:relative;
      transition:transform .3s, box-shadow .3s;
    }
    .card:hover { transform:translateY(-8px); box-shadow:0 12px 30px rgba(0,0,0,0.12); }
    .card img { width:100%; height:200px; object-fit:cover; filter:blur(4px); opacity:0; transition:opacity 0.8s ease, filter 1s ease; }
    .card img.loaded { filter:blur(0); opacity:1; }
    .card-body { padding:15px; position:relative; background:linear-gradient(180deg,#fff,#fff8fa); text-align:center; }
    .category { background:#fff0f5; color:var(--accent); font-size:13px; border-radius:12px; padding:4px 10px; display:inline-block; margin-bottom:8px; }
    .card h3 { margin:8px 0; font-size:18px; color:#222; }
    .price { color:var(--accent); font-weight:700; font-size:16px; margin-bottom:6px; }
    .stock-status { font-size:14px; font-weight:600; margin-bottom:10px; }
    .in-stock { color:#28a745; }
    .out-stock { color:#e63972; }
    .explore {
      display:inline-block; padding:8px 16px; background:var(--accent); color:#fff;
      border-radius:20px; text-decoration:none; transition:all .3s ease;
    }
    .explore:hover { background:#e63972; transform:translateY(-3px) scale(1.05); box-shadow:0 8px 20px rgba(255,76,139,0.3); }
    .explore.disabled { background:#ccc; cursor:not-allowed; }
    .empty { text-align:center; margin:60px 0; color:#777; font-size:18px; }
    .empty img { width:160px; opacity:0.8; margin-bottom:10px; }

    /* ✅ Success Banner */
    .banner {
      background:#d4edda; color:#155724; padding:12px; text-align:center; font-weight:600;
      border-bottom:2px solid #c3e6cb; animation:slideDown .5s ease;
    }
    @keyframes slideDown { 0%{opacity:0; transform:translateY(-30px);} 100%{opacity:1; transform:translateY(0);} }

    @media(max-width:900px){ .section{padding:30px 20px;} .hero{padding:100px 20px;} .hero h1{font-size:36px;} }
  </style>
</head>
<body>

<!-- ✅ Success Message -->
<?php if (isset($_GET['added'])): ?>
  <div class="banner">✅ <?php echo htmlspecialchars($_GET['added']); ?> added to cart successfully!</div>
<?php endif; ?>

<?php
$hobbyIcons = [
  'Painting'=>'🎨','Gardening'=>'🌿','Photography'=>'📸','Crochet'=>'🧶','Sports'=>'⚽','Cars'=>'🚗','Reading'=>'📚'
];
$emoji = ($mode === "single_hobby" && isset($hobbyIcons[$hobby['hobby_name']])) ? $hobbyIcons[$hobby['hobby_name']] : '✨';
?>

<!-- ✅ HERO -->
<div class="hero" data-aos="fade-down">
  <div class="floating-emoji"><?php echo $emoji; ?></div>
  <div style="position:relative; z-index:2;">
    <?php if ($mode === "single_hobby"): ?>
      <h1><?php echo htmlspecialchars($hobby['hobby_name']); ?></h1>
      <p>Discover amazing products for your <?php echo strtolower($hobby['hobby_name']); ?> journey <?php echo $emoji; ?></p>
      <a href="products.php" class="back-btn">← Back to all hobbies</a>
    <?php else: ?>
      <h1>Discover Your Next Passion ✨</h1>
      <p>Explore all hobbies and find something that sparks your creativity.</p>
    <?php endif; ?>
  </div>
</div>

<!-- ✅ SORT BAR -->
<div class="sort-bar" data-aos="fade-up">
  <form method="GET" action="products.php">
    <?php if ($mode === "single_hobby"): ?>
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

<!-- ✅ PRODUCTS GRID -->
<div class="section" data-aos="fade-up">
  <div class="grid">
    <?php
    if ($mode === "single_hobby") {
        $query = "SELECT * FROM products WHERE hobby_id=$hobby_id ORDER BY $order_by";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            echo "<div class='empty'><img src='https://cdn-icons-png.flaticon.com/512/4076/4076509.png'><p>No products found for this hobby yet.</p></div>";
        }
        while ($p = mysqli_fetch_assoc($result)) {
            $img = $p['image'] ?: 'https://via.placeholder.com/400x300?text=No+Image';
            $stock = isset($p['stock']) ? intval($p['stock']) : 0;
            $in_stock = $stock > 0;
            $category = htmlspecialchars($hobby['hobby_name']);
            $status_class = $in_stock ? 'in-stock' : 'out-stock';
            $status_text = $in_stock ? '✅ In Stock' : '❌ Out of Stock';
            $btn_class = $in_stock ? 'explore' : 'explore disabled';
            $btn_text = $in_stock ? 'Add to Cart' : 'Unavailable';
            echo "
            <div class='card' data-aos='zoom-in'>
              <img src='$img' alt='{$p['product_name']}'>
              <div class='card-body'>
                <span class='category'>$category</span>
                <h3>{$p['product_name']}</h3>
                <div class='stock-status $status_class'>$status_text</div>
                <div class='price'>₹{$p['price']}</div>
                <a href='".($in_stock ? "add_to_cart.php?id={$p['product_id']}" : "#")."' class='$btn_class'>$btn_text</a>
              </div>
            </div>";
        }
    } else {
        $hobbies = mysqli_query($conn, "SELECT * FROM hobbies");
        while ($h = mysqli_fetch_assoc($hobbies)) {
            $img = $h['hobby_image'] ?: 'https://via.placeholder.com/400x300?text=Hobby';
            echo "
            <div class='card' data-aos='zoom-in'>
              <img src='$img' alt='{$h['hobby_name']}'>
              <div class='card-body'>
                <h3>{$h['hobby_name']}</h3>
                <a href='products.php?hobby_id={$h['hobby_id']}' class='explore'>Explore</a>
              </div>
            </div>";
        }
    }
    ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration:1000, once:true });

// ✅ Tilt hover animation
document.querySelectorAll('.card').forEach(card=>{
  card.addEventListener('mousemove',e=>{
    const rect=card.getBoundingClientRect();
    const x=(e.clientX-rect.left)-rect.width/2;
    const y=(e.clientY-rect.top)-rect.height/2;
    card.style.transform=`rotateX(${(-y/40)}deg) rotateY(${(x/40)}deg) scale(1.03)`;
  });
  card.addEventListener('mouseleave',()=>card.style.transform='');
});

// ✅ Smooth image reveal
document.querySelectorAll('.card img').forEach(img=>{
  img.onload=()=>img.classList.add('loaded');
});
</script>
</body>
</html>
<?php include 'footer.php'; ?>
