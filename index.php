<?php
include 'db_connect.php';
include 'header.php';

// fetch hobbies and featured products
$hobbies = mysqli_query($conn, "SELECT * FROM hobbies");
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC LIMIT 4");

// helper function to build image path
function getImageSrc($val) {
  if (!$val) return 'https://via.placeholder.com/400x300?text=No+Image';
  if (stripos($val, 'http') === 0) return $val;
  return 'images/' . $val;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hobbyverse – Your Friendly Neighborhood Hobby Store</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root{
      --accent:#ff6f91;
      --accent2:#ffc1cf;
      --bg:linear-gradient(135deg,#fffaf8 0%,#ffe7ef 100%);
      --dark:#2b2b2b;
      --muted:#777;
      --shadow:0 8px 20px rgba(0,0,0,0.08);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--dark);overflow-x:hidden}

    /* NAVBAR */
    nav{display:flex;justify-content:space-between;align-items:center;padding:20px 60px;position:sticky;top:0;z-index:50;background:rgba(255,255,255,0.8);backdrop-filter:blur(10px);box-shadow:0 2px 8px rgba(0,0,0,0.05)}
    .logo{font-family:'Baloo 2';font-size:28px;font-weight:700;background:linear-gradient(90deg,var(--accent),var(--accent2));-webkit-background-clip:text;color:transparent;cursor:pointer}
    .links a{margin-left:25px;text-decoration:none;color:#333;font-weight:600;position:relative;transition:.3s}
    .links a::after{content:'';position:absolute;bottom:-3px;left:0;width:0;height:2px;background:var(--accent);transition:width .3s}
    .links a:hover::after{width:100%}
    .links a:hover{color:var(--accent)}

    /* HERO */
    .hero{min-height:90vh;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;position:relative;overflow:hidden;background:var(--bg)}
    .hero-graphic{position:absolute;top:50%;left:50%;width:700px;transform:translate(-50%,-50%) scale(1.2);opacity:.25;filter:blur(2px) saturate(1.1);z-index:0;pointer-events:none;animation:floaty 10s ease-in-out infinite}
    @keyframes floaty{0%,100%{transform:translate(-50%,-50%) scale(1.2) rotate(0)}50%{transform:translate(-50%,-48%) scale(1.25) rotate(2deg)}}
    .hero h1{font-family:'Baloo 2';font-size:74px;color:var(--accent);text-shadow:0 2px 6px rgba(0,0,0,0.1);position:relative;z-index:1;animation:fadeInDown 1.3s ease}
    .hero h2{margin-top:10px;color:#444;font-weight:500;font-size:22px;position:relative;z-index:1;animation:fadeInUp 1.6s ease}
    .cta{padding:14px 28px;margin-top:25px;background:linear-gradient(90deg,var(--accent),var(--accent2));color:#fff;border:none;border-radius:30px;font-weight:600;cursor:pointer;box-shadow:var(--shadow);transition:.3s;z-index:1}
    .cta:hover{transform:scale(1.05)}
    @keyframes fadeInDown{0%{opacity:0;transform:translateY(-30px)}100%{opacity:1;transform:translateY(0)}}
    @keyframes fadeInUp{0%{opacity:0;transform:translateY(30px)}100%{opacity:1;transform:translateY(0)}}

    /* SEARCH */
    .search-wrap{text-align:center;margin:60px 0 20px}
    .search{display:inline-flex;align-items:center;background:#fff;padding:8px 12px;border-radius:50px;box-shadow:var(--shadow)}
    .search input{border:none;outline:none;padding:12px 16px;border-radius:50px;width:280px;font-size:15px}
    .search button{background:var(--accent);border:none;color:#fff;padding:10px 20px;border-radius:50px;cursor:pointer;transition:.3s}
    .search button:hover{background:var(--accent2);color:#333}

    /* SECTIONS */
    .section{padding:80px 60px;max-width:1200px;margin:auto}
    .section h2{text-align:center;font-size:32px;margin-bottom:40px;font-family:'Baloo 2';color:var(--accent)}

    /* CARD GRID */
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px}
    .card{background:#fff;border-radius:15px;overflow:hidden;box-shadow:var(--shadow);transition:.4s;text-align:center;position:relative}
    .card img{width:100%;height:200px;object-fit:cover;display:block;transition:transform 0.4s ease}
    .card:hover img{transform:scale(1.05)}
    .card h3{margin:15px 0 5px;font-size:18px;color:#222}
    .card p{color:#777;font-size:14px}
    .card:hover{transform:translateY(-10px)scale(1.02);box-shadow:0 12px 25px rgba(0,0,0,0.1)}

    /* ABOUT */
    .about{background:#fff8fb;padding:80px 40px;text-align:center;border-radius:20px;box-shadow:var(--shadow);margin:60px auto;max-width:1100px}
    .about h3{font-family:'Baloo 2';font-size:28px;color:var(--accent);margin-bottom:10px}
    .about p{color:#555;font-size:16px;margin-bottom:20px}

    /* FOOTER */
    footer{background:#222;color:#fff;padding:60px 30px;display:flex;justify-content:space-around;flex-wrap:wrap;gap:30px}
    footer h4{font-family:'Baloo 2';margin-bottom:10px}
    footer a{color:#fff;text-decoration:none;font-size:14px;display:block;margin:4px 0}
    footer a:hover{color:var(--accent2)}

    /* SCROLL TOP */
    #scrollTop{position:fixed;bottom:30px;right:30px;background:var(--accent);color:#fff;border:none;border-radius:50%;width:45px;height:45px;font-size:20px;cursor:pointer;opacity:0;transition:.3s;box-shadow:var(--shadow)}
    #scrollTop.show{opacity:1}

    @media(max-width:900px){.hero h1{font-size:54px}.section{padding:50px 20px}}
  </style>
</head>
<body>

  <!-- HERO -->
  <section class="hero" data-aos="fade-in">
    <img src="images/hero_illustration.jpg" alt="Hobbyverse Illustration" class="hero-graphic">
    <h1>Hobbyverse</h1>
    <h2>Your Friendly Neighborhood Hobby Store 🎨</h2>
    <button class="cta" onclick="document.getElementById('hobbies').scrollIntoView({behavior:'smooth'})">Explore Now</button>
  </section>

  <!-- SEARCH -->
  <div class="search-wrap" data-aos="fade-up">
    <form class="search" method="GET" action="search.php">
      <input type="text" name="q" placeholder="Search your next hobby... 🎶 🌿 ✍️">
      <button type="submit">Search</button>
    </form>
  </div>

  <!-- HOBBIES -->
  <section class="section" id="hobbies" data-aos="fade-up">
    <h2>✨ Explore Hobbies</h2>
    <div class="grid">
      <?php while($h = mysqli_fetch_assoc($hobbies)) { 
        $img = getImageSrc($h['hobby_image']);
      ?>
        <div class="card" data-aos="zoom-in">
          <img src="<?php echo $img; ?>" alt="<?php echo $h['hobby_name']; ?>">
          <h3><?php echo $h['hobby_name']; ?></h3>
          <a href="products.php?hobby_id=<?php echo $h['hobby_id']; ?>" style="color:var(--accent);font-weight:600;text-decoration:none;">Explore</a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- FEATURED -->
  <section class="section" data-aos="fade-up">
    <h2>🌟 Featured Products</h2>
    <div class="grid">
      <?php while($p = mysqli_fetch_assoc($products)) { 
        $img = getImageSrc($p['image']);
      ?>
        <div class="card" data-aos="zoom-in">
          <img src="<?php echo $img; ?>" alt="<?php echo $p['product_name']; ?>">
          <h3><?php echo $p['product_name']; ?></h3>
          <p>₹<?php echo $p['price']; ?></p>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ABOUT -->
  <div class="about" data-aos="fade-right">
    <h3>xo, Hobbyverse 💌</h3>
    <p>We believe hobbies are more than pastimes — they bring joy, creativity, and connection.<br>Find tools you love, explore your passions, and make something beautiful today.</p>
    <button class="cta" onclick="location.href='about.php'">Learn More</button>
  </div>

  <!-- FOOTER -->
  <?php include 'footer.php'; ?>

  <button id="scrollTop">↑</button>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({duration:1000,once:true});
    const scrollBtn=document.getElementById('scrollTop');
    window.addEventListener('scroll',()=>{window.scrollY>300?scrollBtn.classList.add('show'):scrollBtn.classList.remove('show')});
    scrollBtn.onclick=()=>window.scrollTo({top:0,behavior:'smooth'});
  </script>
</body>
</html>
