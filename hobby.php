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
  'Painting'=>'🎨','Gardening'=>'🌿','Photography'=>'📸',
  'Crochet'=>'🧶','Sports'=>'⚽','Cars'=>'🚗','Reading'=>'📚'
];

$emoji = ($mode=="single" && isset($icons[$hobby['hobby_name']])) ? 
         $icons[$hobby['hobby_name']] : "✨";

// wishlist helper
function isFav($conn,$uid,$pid){
    $q = mysqli_query($conn,"SELECT id FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    return mysqli_num_rows($q)>0;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Hobbyverse</title>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<style>
body{font-family:Poppins;background:#fff7fa;margin:0;}

.hero{
  text-align:center;
  padding:100px 20px;
  background:#ffeaf1;
  border-radius:20px;
  margin:20px;
}

.grid{
  max-width:1200px;margin:auto;
  display:grid;gap:25px;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
}

.card{
  background:#fff;
  border-radius:18px;
  box-shadow:0 6px 20px rgba(0,0,0,.08);
  overflow:hidden;
  position:relative;
  transition:.3s;
}
.card:hover{transform:translateY(-6px)}

.card img{
  width:100%;height:230px;object-fit:cover;
}

.card-body{padding:15px;}
.price{color:#ff4c8b;font-weight:600;}

.add-btn{
  display:inline-block;
  padding:8px 12px;background:#ff4c8b;color:#fff;
  border-radius:20px;text-decoration:none;
}

.wish-btn{
  position:absolute;top:12px;right:12px;
  width:40px;height:40px;border-radius:50%;
  background:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;cursor:pointer;
  box-shadow:0 3px 10px rgba(0,0,0,.15);
}
.wish-active{background:#ff4c8b;color:#fff;}
</style>
</head>

<body>

<div class="hero">
  <h1>
    <?php echo $mode=="single" ? $hobby['hobby_name']." $emoji" : "Explore All Hobbies ✨"; ?>
  </h1>
</div>

<div class="grid">

<?php
// SINGLE HOBBY MODE
if ($mode=="single") {

  $p = mysqli_query($conn,"SELECT * FROM products WHERE hobby_id=$hobby_id");
  if(mysqli_num_rows($p)==0){
      echo "<h3>No products found.</h3>";
  }

  while($row=mysqli_fetch_assoc($p)){

      $pid=$row['product_id'];
      $isFav = isset($_SESSION['user_id']) ? isFav($conn,$_SESSION['user_id'],$pid) : false;

      echo "
      <div class='card'>
        <a href=\"wishlist_action.php?id=$pid\" 
           class='wish-btn ".($isFav?'wish-active':'')."'>❤</a>

        <img src=\"{$row['image']}\" alt=\"product\">

        <div class='card-body'>
          <h3>{$row['product_name']}</h3>
          <p>".substr($row['description'],0,60)."...</p>
          <div class='price'>₹ {$row['price']}</div>

          <a href='cart.php?add=$pid' class='add-btn'>Add to Cart</a>
        </div>
      </div>";
  }

// ALL HOBBIES MODE
} else {

  $h = mysqli_query($conn,"SELECT * FROM hobbies");
  while($hb=mysqli_fetch_assoc($h)){
      echo "
      <div class='card'>
        <img src=\"{$hb['hobby_image']}\" alt='img'>
        <div class='card-body'>
          <h3>{$hb['hobby_name']}</h3>
          <a class='add-btn' href='hobby.php?hobby_id={$hb['hobby_id']}'>Explore</a>
        </div>
      </div>";
  }
}
?>

</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>

</body>
</html>

<?php include 'footer.php'; ?>
