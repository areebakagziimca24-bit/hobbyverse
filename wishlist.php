<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=wishlist.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT products.product_id, products.product_name, products.price, products.image, products.stock
    FROM wishlist 
    JOIN products ON wishlist.product_id = products.product_id
    WHERE wishlist.user_id = $user_id
";

$items = mysqli_query($conn, $query);
?>

<style>
body{font-family:'Poppins',sans-serif;background:#fff8fa;margin:0;}

.container{
  max-width:1100px;margin:40px auto;background:#fff;padding:30px;
  border-radius:18px;box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

h1{text-align:center;color:#ff4c8b;margin-bottom:25px;}

.grid{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:25px;
}

.card{
  background:#fff;border-radius:18px;overflow:hidden;
  box-shadow:0 6px 20px rgba(0,0,0,0.08);
  transition:.4s;
}
.card:hover{transform:translateY(-10px);}

.card img{width:100%;height:200px;object-fit:cover;}

.card-body{padding:15px;}

.price{color:#ff4c8b;font-weight:700;margin:4px 0;}

.btn{
  display:inline-block;padding:8px 14px;background:#ff4c8b;color:#fff;
  border-radius:20px;text-decoration:none;font-size:14px;font-weight:600;
  transition:.2s;
}
.btn:hover{background:#ff3a79;}

.remove{
  background:#ffd1dc;color:#333;margin-left:10px;
}
.remove:hover{background:#ffb8c7;}

.stock{font-weight:600;font-size:14px;margin-bottom:5px;}
.in-stock{color:#28a745;}
.low-stock{color:#e67e22;}
.out-stock{color:#d9534f;}

.empty{text-align:center;font-size:18px;color:#777;padding:40px;}
</style>

<div class="container" data-aos="fade-up">
  <h1>❤️ My Wishlist</h1>

  <?php if(mysqli_num_rows($items)==0): ?>
    <div class="empty" data-aos="fade-up" data-aos-delay="150">
      Your wishlist is empty 😢<br><br>
      <a href="products.php" class="btn">Explore Products</a>
    </div>
  <?php else: ?>

    <div class="grid">

      <?php 
      $delay = 0;
      while($p = mysqli_fetch_assoc($items)): 
        $delay += 120;

        $stock = $p['stock'];
        if ($stock == 0)      { $sclass = "out-stock"; $slabel="✖ Out of Stock"; }
        elseif ($stock <=10)  { $sclass = "low-stock"; $slabel="⚠ Low Stock"; }
        else                  { $sclass = "in-stock"; $slabel="✔ In Stock"; }
      ?>

        <div class="card" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
          <img src="<?= $p['image']; ?>">
          <div class="card-body">
            <h3><?= $p['product_name']; ?></h3>

            <div class="stock <?= $sclass; ?>"><?= $slabel; ?></div>
            <div class="price">₹<?= $p['price']; ?></div>

            <?php if($stock > 0): ?>
              <a href="cart.php?add=<?= $p['product_id']; ?>" class="btn">Add to Cart</a>
            <?php else: ?>
              <span class="btn" style="background:#ccc;cursor:not-allowed;">Unavailable</span>
            <?php endif; ?>

            <a href="wishlist_action.php?id=<?= $p['product_id']; ?>" class="btn remove">Remove</a>
          </div>
        </div>

      <?php endwhile; ?>

    </div>

  <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
