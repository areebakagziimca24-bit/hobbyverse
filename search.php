<?php
include 'db_connect.php';
include 'header.php';

$q = mysqli_real_escape_string($conn, $_GET['q'] ?? '');

if ($q == "") {
    $hobby_results = [];
    $product_results = [];
} else {
    $hobby_results = mysqli_query($conn, 
        "SELECT * FROM hobbies WHERE hobby_name LIKE '%$q%' OR description LIKE '%$q%'");

    $product_results = mysqli_query($conn,
        "SELECT * FROM products 
         WHERE product_name LIKE '%$q%' OR description LIKE '%$q%'");
}

/* WISHLIST FUNCTION */
function isInWishlist($conn, $uid, $pid){
    $q = mysqli_query($conn,"SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    return mysqli_num_rows($q) > 0;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Search Results | Hobbyverse</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
:root{
  --accent:#ff4c8b;
  --bg:#fff8fa;
  --muted:#777;
  --card-shadow:0 8px 20px rgba(0,0,0,.06);
  --card-radius:12px;
}

body{font-family:'Poppins',sans-serif;background:var(--bg);margin:0;color:#222;}
a{text-decoration:none;color:inherit;}

.section{max-width:1100px;margin:20px auto 80px;padding:0 18px;}

h1{
  text-align:center;
  font-size:26px;
  color:#ff6f91;
  margin-top:20px;
}

/* GRID — same as products.php */
.grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:28px;
  margin-top:40px;
}

/* CARD — same size */
.card{
  background:#fff;
  border-radius:var(--card-radius);
  box-shadow:var(--card-shadow);
  overflow:hidden;
  transition:.2s;
  position:relative;
}
.card:hover{transform:translateY(-6px);}

.thumb{
  width:100%;
  height:160px;
  object-fit:cover;
  border-bottom:1px solid rgba(0,0,0,0.05);
}

.card-body{
  padding:14px 16px;
  text-align:center;
}

.card h3{
  font-size:16px;
  margin:6px 0 8px;
}

.category{
  display:inline-block;
  font-size:12px;
  background:#fff0f5;
  color:var(--accent);
  padding:6px 10px;
  border-radius:14px;
}

/* Wishlist Heart */
.wishlist-btn{
  position:absolute;
  top:10px;right:10px;
  width:36px;height:36px;background:#fff;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;color:var(--accent);
  box-shadow:0 4px 12px rgba(0,0,0,.08);
}
.wishlist-btn.active{background:var(--accent);color:#fff;}

/* Buttons */
.btn{
  display:inline-block;
  padding:10px 18px;
  background:var(--accent);
  color:#fff;
  border-radius:22px;
  font-weight:600;
  margin-top:10px;
}
.btn.disabled{background:#ccc;pointer-events:none;}

.price{color:var(--accent);font-weight:700;margin-top:6px;}
.stock-status{font-size:13px;margin-top:6px;}
.in-stock{color:#28a745;}
.out-stock{color:#e63946;}

@media(max-width:1100px){
  .grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:700px){
  .grid{grid-template-columns:1fr;}
  .thumb{height:200px;}
}
</style>
</head>

<body>

<h1>Search Results for "<?php echo htmlspecialchars($q); ?>"</h1>

<div class="section">

<!-- IF NOTHING FOUND -->
<?php if (!$q || (mysqli_num_rows($hobby_results)==0 && mysqli_num_rows($product_results)==0)): ?>
    <p style="text-align:center;margin-top:30px;color:var(--muted);">
        No results found. Try something else.
    </p>
<?php endif; ?>

<div class="grid">

<!-- ===================== HOBBY RESULTS ===================== -->
<?php if ($q && mysqli_num_rows($hobby_results) > 0): ?>
<?php while ($h = mysqli_fetch_assoc($hobby_results)): ?>

    <div class="card" data-aos="zoom-in">
        <img class="thumb" src="<?php echo $h['hobby_image']; ?>" alt="">
        <div class="card-body">
            <h3><?php echo htmlspecialchars($h['hobby_name']); ?></h3>
            <a class="btn" href="products.php?hobby_id=<?php echo $h['hobby_id']; ?>">Explore</a>
        </div>
    </div>

<?php endwhile; ?>
<?php endif; ?>

<!-- ===================== PRODUCT RESULTS ===================== -->
<?php if ($q && mysqli_num_rows($product_results) > 0): ?>
<?php while ($p = mysqli_fetch_assoc($product_results)):

    $isFav = false;
    if (isset($_SESSION['user_id'])) {
        $isFav = isInWishlist($conn, $_SESSION['user_id'], $p['product_id']);
    }
    $favClass = $isFav ? "active" : "";

    $stock = intval($p['stock']);
    $in_stock = $stock > 0;
?>

    <div class="card" data-aos="zoom-in">
        <a class="wishlist-btn <?php echo $favClass; ?>" href="wishlist_action.php?id=<?php echo $p['product_id']; ?>">❤</a>

        <img class="thumb" src="<?php echo $p['image']; ?>" alt="">

        <div class="card-body">
            <h3><?php echo htmlspecialchars($p['product_name']); ?></h3>

            <div class="price">₹<?php echo number_format($p['price'], 2); ?></div>
            <div class="stock-status <?php echo $in_stock?'in-stock':'out-stock'; ?>">
                <?php echo $in_stock ? 'In Stock' : 'Out of Stock'; ?>
            </div>

            <a class="btn <?php echo $in_stock ? '' : 'disabled'; ?>"
               href="<?php echo $in_stock ? 'add_to_cart.php?id='.$p['product_id'] : '#'; ?>">
               <?php echo $in_stock ? 'Add to Cart' : 'Unavailable'; ?>
            </a>
        </div>
    </div>

<?php endwhile; ?>
<?php endif; ?>

</div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:700, once:true});</script>

</body>
</html>

<?php include 'footer.php'; ?>
