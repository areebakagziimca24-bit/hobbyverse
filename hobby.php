<?php
include 'db_connect.php';
include 'header.php';

$hobby_id = isset($_GET['hobby_id']) ? intval($_GET['hobby_id']) : 0;
$hobby = null;

// Fetch hobby if provided
if ($hobby_id > 0) {
    $hobby = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hobbies WHERE hobby_id=$hobby_id"));
}
$mode = $hobby ? "single_hobby" : "all_hobbies";

// 🎨 Backgrounds, emojis, and accent colors per hobby
$heroBackgrounds = [
  'Painting' => 'https://images.unsplash.com/photo-1500336624523-d727130c3328?auto=format&fit=crop&w=1600&q=80',
  'Gardening' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1600&q=80',
  'Photography' => 'https://images.unsplash.com/photo-1499084732479-de2c02d45fc4?auto=format&fit=crop&w=1600&q=80',
  'Crochet' => 'https://images.unsplash.com/photo-1616469829482-9e7f27b01ee9?auto=format&fit=crop&w=1600&q=80',
  'Sports' => 'https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&fit=crop&w=1600&q=80',
  'Cars' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1600&q=80',
  'Reading' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=1600&q=80'
];

$hobbyIcons = [
  'Painting' => '🎨',
  'Gardening' => '🌿',
  'Photography' => '📸',
  'Crochet' => '🧶',
  'Sports' => '⚽',
  'Cars' => '🚗',
  'Reading' => '📚'
];

$accentColors = [
  'Painting' => '#FFB6C1',
  'Gardening' => '#7ED957',
  'Photography' => '#B39DDB',
  'Crochet' => '#FF9A8B',
  'Sports' => '#5CB6FF',
  'Cars' => '#A6A6A6',
  'Reading' => '#FFD369'
];

$bgImage = ($mode === "single_hobby" && isset($heroBackgrounds[$hobby['hobby_name']])) 
    ? $heroBackgrounds[$hobby['hobby_name']] 
    : 'https://cdn.pixabay.com/photo/2020/07/13/10/37/hobby-5398884_1280.png';

$emoji = ($mode === "single_hobby" && isset($hobbyIcons[$hobby['hobby_name']])) 
    ? $hobbyIcons[$hobby['hobby_name']] 
    : '✨';

$accent = ($mode === "single_hobby" && isset($accentColors[$hobby['hobby_name']])) 
    ? $accentColors[$hobby['hobby_name']] 
    : '#ff4c8b';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hobbyverse | <?php echo ($mode === "single_hobby") ? htmlspecialchars($hobby['hobby_name']) : "Discover Hobbies"; ?></title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/809/809052.png">

<!-- Fonts + AOS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: #fffafc;
  color: #333;
}

/* 🌈 Hero Section */
.hero {
  position: relative;
  background: 
    linear-gradient(180deg, rgba(255,255,255,0.8), rgba(255,255,255,0.9)),
    url('<?php echo $bgImage; ?>') center/cover no-repeat;
  padding: 130px 20px;
  text-align: center;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  margin: 30px auto;
  max-width: 1200px;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 850px;
  margin: 0 auto;
  backdrop-filter: blur(8px);
  background: rgba(255,255,255,0.3);
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 4px 16px rgba(255,255,255,0.3);
}

.hero h1 {
  font-size: 52px;
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
  text-shadow: 0 2px 10px rgba(255,255,255,0.6);
}

.hero p {
  font-size: 20px;
  color: #555;
  margin-bottom: 25px;
}

.back-btn {
  display: inline-block;
  background: <?php echo $accent; ?>;
  color: #fff;
  padding: 12px 28px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}
.back-btn:hover {
  background: #e63972;
  transform: scale(1.05);
}

/* ✨ Floating Emoji */
.floating-emoji {
  position: absolute;
  top: 35%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 120px;
  opacity: 0.15;
  z-index: 1;
  animation: floaty 6s ease-in-out infinite;
  user-select: none;
}
@keyframes floaty {
  0%,100% { transform: translate(-50%, -50%) scale(1); opacity: 0.15; }
  50% { transform: translate(-50%, -60%) scale(1.1); opacity: 0.25; }
}

/* 🌼 Grid & Cards */
.section {
  padding: 60px 80px;
  max-width: 1200px;
  margin: auto;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}
.card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: 0.3s;
  position: relative;
}
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}
.card h3 {
  margin: 15px;
  font-size: 20px;
  color: #222;
}
.card p {
  margin: 0 15px 15px;
  color: #666;
  font-size: 14px;
}
.price {
  color: <?php echo $accent; ?>;
  font-weight: 700;
  margin: 10px 15px 20px;
}
.add-btn {
  display: inline-block;
  margin: 0 15px 20px;
  background: <?php echo $accent; ?>;
  color: #fff;
  padding: 10px 16px;
  border-radius: 20px;
  text-decoration: none;
  transition: 0.3s;
}
.add-btn:hover {
  background: #e63972;
}

/* Responsive */
@media (max-width: 768px) {
  .hero h1 { font-size: 36px; }
  .hero p { font-size: 18px; }
  .section { padding: 30px 20px; }
}
</style>
</head>
<body>

<!-- 🌟 HERO SECTION -->
<div class="hero" data-aos="fade-down">
  <div class="floating-emoji"><?php echo $emoji; ?></div>
  <div class="hero-content" data-aos="zoom-in">
    <?php if ($mode === "single_hobby"): ?>
      <h1><?php echo htmlspecialchars($hobby['hobby_name']); ?></h1>
      <p>Discover the best tools and ideas for your <?php echo strtolower($hobby['hobby_name']); ?> journey <?php echo $emoji; ?></p>
      <a href="hobby.php" class="back-btn">← Back to all hobbies</a>
    <?php else: ?>
      <h1>Discover Your Next Passion ✨</h1>
      <p>Explore all hobbies and find something that makes your soul happy.</p>
    <?php endif; ?>
  </div>
</div>

<!-- 🌿 CONTENT GRID -->
<div class="section" data-aos="fade-up">
  <div class="grid">
    <?php
    if ($mode === "single_hobby") {
      $result = mysqli_query($conn, "SELECT * FROM products WHERE hobby_id=$hobby_id");
      if (mysqli_num_rows($result) == 0) {
          echo "<p style='text-align:center;width:100%;font-size:18px;'>No products found for this hobby yet.</p>";
      }
      while ($p = mysqli_fetch_assoc($result)) {
          $img = !empty($p['image']) ? $p['image'] : 'https://via.placeholder.com/400x300?text=No+Image';
          echo "
          <div class='card' data-aos='zoom-in'>
            <img src='$img' alt='{$p['product_name']}'>
            <h3>{$p['product_name']}</h3>
            <p>".substr($p['description'],0,70)."...</p>
            <div class='price'>₹{$p['price']}</div>
            <a href='cart.php?add={$p['product_id']}' class='add-btn'>Add to Cart</a>
          </div>";
      }
    } else {
      $hobbies = mysqli_query($conn, "SELECT * FROM hobbies");
      while ($h = mysqli_fetch_assoc($hobbies)) {
          $img = !empty($h['hobby_image']) ? $h['hobby_image'] : 'https://via.placeholder.com/400x300?text=Hobby';
          echo "
          <div class='card' data-aos='zoom-in'>
            <img src='$img' alt='{$h['hobby_name']}'>
            <h3>{$h['hobby_name']}</h3>
            <a href='hobby.php?hobby_id={$h['hobby_id']}' class='add-btn'>Explore</a>
          </div>";
      }
    }
    ?>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 1000, once: true });</script>

</body>
</html>
<?php include 'footer.php'; ?>
