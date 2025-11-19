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
<!DOCTYPE html>
<html>
<head>
<title>My Wishlist - Hobbyverse</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

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
  box-shadow:0 6px 20px rgba(0,0,0,0.08);transition:.3s;
}

.card:hover{transform:translateY(-8px);}

.card img{width:100%;height:180px;object-fit:cover;}

.card-body{padding:15px;}

.price{color:#ff4c8b;font-weight:700;margin:4px 0;}

.btn{
  display:inline-block;padding:8px 14px;background:#ff4c8b;color:#fff;
  border-radius:20px;text-decoration:none;font-size:14px;font-weight:600;
}

.remove{
  background:#ffd1dc;color:#333;margin-left:10px;
}

.stock{font-weight:600;font-size:14px;margin-bottom:5px;}
.in-stock{color:#28a745;}
.low-stock{color:#e67e22;}
.out-stock{color:#d9534f;}

.empty{text-align:center;font-size:18px;color:#777;padding:40px;}
</style>
</head>

<body>

<div class="container">
  <h1>❤️ My Wishlist</h1>

  <?php if(mysqli_num_rows($items)==0): ?>
    <div class="empty">
      Your wishlist is empty 😢<br><br>
      <a href="products.php" class="btn">Explore Products</a>
    </div>
  <?php else: ?>

    <div class="grid">

      <?php while($p = mysqli_fetch_assoc($items)): ?>

        <?php
        $stock = $p['stock'];
        if ($stock == 0)      { $sclass = "out-stock"; $slabel="✖ Out of Stock"; }
        elseif ($stock <=10)  { $sclass = "low-stock"; $slabel="⚠ Low Stock"; }
        else                  { $sclass = "in-stock"; $slabel="✔ In Stock"; }
        ?>

        <div class="card">
          <img src="<?php echo $p['image']; ?>">
          <div class="card-body">
            <h3><?php echo $p['product_name']; ?></h3>
            <div class="stock <?php echo $sclass; ?>"><?php echo $slabel; ?></div>
            <div class="price">₹<?php echo $p['price']; ?></div>

            <a href="cart.php?add=<?php echo $p['product_id']; ?>" class="btn">Add to Cart</a>
            <a href="wishlist_action.php?id=<?php echo $p['product_id']; ?>" class="btn remove">Remove</a>
          </div>
        </div>

      <?php endwhile; ?>

    </div>

  <?php endif; ?>

</div>

</body>
</html>

<?php include 'footer.php'; ?>
