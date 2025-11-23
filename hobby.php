<?php
include 'db_connect.php';
include 'header.php';

// find hobby
$hobby_id = isset($_GET['hobby_id']) ? intval($_GET['hobby_id']) : 0;
$hobby = null;

if ($hobby_id > 0) {
    $hobby = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hobbies WHERE hobby_id=$hobby_id"));
}

$mode = $hobby ? "single" : "all";

// emoji per hobby
$icons = [
  'Painting'   => '🎨',
  'Gardening'  => '🌿',
  'Photography'=> '📸',
  'Crochet'    => '🧶',
  'Sports'     => '⚽',
  'Cars'       => '🚗',
  'Reading'    => '📚'
];

$emoji = ($mode == "single" && isset($icons[$hobby['hobby_name']])) 
         ? $icons[$hobby['hobby_name']] 
         : "✨";

// wishlist helper
function isFav($conn, $uid, $pid) {
    $q = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    return mysqli_num_rows($q) > 0;
}
?>

<style>
/* --- Hobby Page Styling (Soft / Minimal) --- */
.hobby-hero{
  max-width:1200px;
  margin:20px auto 30px;
  padding:40px 30px;
  border-radius:20px;
  background:linear-gradient(135deg,#fff5f8 0%, #ffe9f1 100%);
  box-shadow:0 10px 30px rgba(0,0,0,0.06);
  text-align:center;
}
.hobby-hero-title{
  font-size:32px;
  font-weight:600;
  margin-bottom:8px;
  color:#ff6f91;
}
.hobby-hero-sub{
  font-size:15px;
  color:#777;
  max-width:620px;
  margin:0 auto;
}

.hobby-grid-wrap{
  max-width:1200px;
  margin:0 auto 60px;
  padding:0 20px 10px;
}
.hobby-grid{
  display:grid;
  grid-template-columns:repeat(3,minmax(260px,1fr));
  gap:22px;
}

/* cards */
.hobby-card{
  background:#fff;
  border-radius:18px;
  box-shadow:0 6px 20px rgba(0,0,0,.06);
  overflow:hidden;
  position:relative;
  transition:transform .2s ease-out, box-shadow .2s ease-out;
}
.hobby-card:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 30px rgba(0,0,0,.10);
}

.hobby-img{
  width:100%;
  height:220px;
  object-fit:cover;
  display:block;
}

.hobby-card-body{
  padding:14px 16px 16px;
}
.hobby-name{
  font-size:17px;
  font-weight:600;
  margin-bottom:4px;
}
.hobby-desc{
  font-size:13px;
  color:#777;
  min-height:34px;
}
.hobby-price{
  margin-top:10px;
  font-weight:600;
  color:#ff4c8b;
}

/* buttons */
.btn-soft{
  display:inline-block;
  padding:8px 16px;
  border-radius:20px;
  border:none;
  font-size:14px;
  cursor:pointer;
  text-decoration:none;
  transition:.2s;
}
.btn-primary{
  background:#ff4c8b;
  color:#fff;
  box-shadow:0 6px 16px rgba(255,76,139,0.18);
}
.btn-primary:hover{
  background:#e53b79;
  transform:translateY(-1px);
}
.btn-ghost{
  background:#fff0f6;
  color:#ff4c8b;
}

/* wishlist heart */
.wish-btn{
  position:absolute;top:12px;right:12px;
  width:40px;height:40px;border-radius:50%;
  background:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;cursor:pointer;
  box-shadow:0 3px 10px rgba(0,0,0,.15);
  transition:.2s;
}
.wish-btn:hover{transform:scale(1.05);}
.wish-active{background:#ff4c8b;color:#fff;}

.stock-pill{
  display:inline-block;
  padding:4px 10px;
  border-radius:999px;
  font-size:12px;
  margin-top:6px;
}
.stock-pill.ok{background:#e6f8ec;color:#1b7737;}
.stock-pill.low{background:#fff4d6;color:#a66a00;}
.stock-pill.out{background:#ffe2e5;color:#b3261e;}

.btn-disabled{
  background:#ddd !important;
  color:#888 !important;
  box-shadow:none !important;
  cursor:not-allowed !important;
}

/* responsive */
@media (max-width:1100px){
  .hobby-grid{grid-template-columns:repeat(2,minmax(240px,1fr));}
}
@media (max-width:720px){
  .hobby-hero{margin:15px 16px;padding:26px 18px;}
  .hobby-hero-title{font-size:24px;}
  .hobby-grid{grid-template-columns:1fr;}
}
</style>

<!-- HERO -->
<section class="hobby-hero" data-aos="fade-up">
  <?php if ($mode == "single"): ?>
    <h1 class="hobby-hero-title">
      <?= htmlspecialchars($hobby['hobby_name']) . " " . $emoji ?>
    </h1>
    <p class="hobby-hero-sub">
      Curated products to make your <?= strtolower(htmlspecialchars($hobby['hobby_name'])) ?> journey more fun and inspiring.
    </p>
  <?php else: ?>
    <h1 class="hobby-hero-title">Explore All Hobbies ✨</h1>
    <p class="hobby-hero-sub">
      Discover new passions, from painting and crochet to reading, gardening and more. Pick a hobby and start exploring.
    </p>
  <?php endif; ?>
</section>

<!-- GRID -->
<section class="hobby-grid-wrap">
  <div class="hobby-grid">

    <?php
    // SINGLE HOBBY MODE: show products for that hobby
    if ($mode == "single") {

      $p = mysqli_query($conn, "SELECT * FROM products WHERE hobby_id=$hobby_id");
      if (mysqli_num_rows($p) == 0) {
          echo "<div style='grid-column:1/-1;text-align:center;color:#777;' data-aos='fade-up'>
                  No products found for this hobby yet.
                </div>";
      }

      $delay = 0;
      while ($row = mysqli_fetch_assoc($p)) {

          $pid  = $row['product_id'];
          $name = htmlspecialchars($row['product_name']);
          $desc = htmlspecialchars(substr($row['description'] ?? '', 0, 80));
          $img  = $row['image'];
          $price = number_format($row['price'], 2);

          $isFav = isset($_SESSION['user_id']) ? isFav($conn, $_SESSION['user_id'], $pid) : false;

          // stock
          $stock = isset($row['stock']) ? intval($row['stock']) : 0;
          if ($stock > 10) {
              $stockClass = 'ok';
              $stockLabel = "In stock ($stock left)";
          } elseif ($stock > 0) {
              $stockClass = 'low';
              $stockLabel = "Only $stock left";
          } else {
              $stockClass = 'out';
              $stockLabel = "Out of stock";
          }

          $delay += 80;  // stagger animation
          ?>
          <div class="hobby-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <a href="wishlist_action.php?id=<?= $pid ?>" 
               class="wish-btn <?= $isFav ? 'wish-active' : '' ?>">❤</a>

            <img src="<?= htmlspecialchars($img) ?>" alt="product image" class="hobby-img">

            <div class="hobby-card-body">
              <div class="hobby-name"><?= $name ?></div>
              <div class="hobby-desc"><?= $desc ?><?= strlen($row['description'] ?? '') > 80 ? '…' : '' ?></div>

              <div class="stock-pill <?= $stockClass ?>"><?= $stockLabel ?></div>

              <div class="hobby-price">₹ <?= $price ?></div>

              <div style="margin-top:10px;">
                <a 
                  href="<?= $stock > 0 ? 'cart.php?add='.$pid : 'javascript:void(0);' ?>" 
                  class="btn-soft btn-primary <?= $stock > 0 ? '' : 'btn-disabled' ?>">
                  <?= $stock > 0 ? 'Add to Cart' : 'Unavailable' ?>
                </a>
              </div>
            </div>
          </div>
      <?php
      }

    // ALL HOBBIES MODE: show hobbies
    } else {

      $h = mysqli_query($conn, "SELECT * FROM hobbies");
      $delay = 0;

      while ($hb = mysqli_fetch_assoc($h)) {
          $delay += 80;
          $hname = htmlspecialchars($hb['hobby_name']);
          $hdesc = htmlspecialchars(substr($hb['description'] ?? '', 0, 80));
          $himg  = $hb['hobby_image'] ?: 'https://via.placeholder.com/400x260?text=Hobby';
          ?>
          <div class="hobby-card" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
            <img src="<?= htmlspecialchars($himg) ?>" alt="hobby image" class="hobby-img">
            <div class="hobby-card-body">
              <div class="hobby-name"><?= $hname ?></div>
              <div class="hobby-desc"><?= $hdesc ?><?= strlen($hb['description'] ?? '') > 80 ? '…' : '' ?></div>
              <div style="margin-top:12px;">
                <a class="btn-soft btn-primary" href="hobby.php?hobby_id=<?= intval($hb['hobby_id']) ?>">Explore</a>
              </div>
            </div>
          </div>
      <?php
      }
    }
    ?>

  </div>
</section>

<!-- AOS JS (if not already in footer) -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 700,
    once: true,
    easing: 'ease-out'
  });
</script>

<?php include 'footer.php'; ?>
