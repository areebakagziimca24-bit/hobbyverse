<?php
// header.php - include at top of every page
// Usage: include 'header.php'; then page content, then include 'footer.php';

// start session safely
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Hobbyverse</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <!-- Fonts & AOS -->
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root{
      --accent:#ff6f91;
      --accent-2:#ffc1cf;
      --muted:#777;
      --bg: linear-gradient(135deg,#fffaf8 0%, #ffe7ef 100%);
      --card-shadow: 0 10px 30px rgba(30,30,30,0.08);
    }
    *{box-sizing:border-box}
    body{font-family:'Poppins',sans-serif;margin:0;background:var(--bg);color:#222;-webkit-font-smoothing:antialiased}
    a{color:inherit}
    /* Top navbar */
    .site-nav{display:flex;justify-content:space-between;align-items:center;padding:18px 40px;background:rgba(255,255,255,0.85);backdrop-filter:blur(6px);position:sticky;top:0;z-index:60;border-bottom:1px solid rgba(0,0,0,0.03)}
    .site-brand{font-family:'Baloo 2',cursive;font-weight:700;font-size:22px;background:linear-gradient(90deg,var(--accent),var(--accent-2));-webkit-background-clip:text;color:transparent;cursor:pointer}
    .nav-links{display:flex;align-items:center;gap:18px}
    .nav-links a{font-weight:600;text-decoration:none;padding:8px 6px;border-radius:6px;color:#3b3b3b;transition:color .22s}
    .nav-links a:hover{color:var(--accent)}
    /* search in nav */
    .nav-search{display:flex;align-items:center;background:#fff;padding:6px;border-radius:30px;box-shadow:var(--card-shadow);border:1px solid rgba(0,0,0,0.04)}
    .nav-search input{border:none;outline:none;padding:8px 10px;border-radius:20px;width:220px}
    .nav-search button{background:var(--accent);border:none;color:white;padding:8px 12px;border-radius:20px;cursor:pointer;font-weight:600}
    /* account/cart */
    .nav-right{display:flex;align-items:center;gap:12px}
    .badge{background:var(--accent);color:#fff;border-radius:12px;padding:2px 8px;font-size:13px;font-weight:700}
    /* small responsive */
    @media(max-width:880px){
      .nav-search input{width:120px}
      .nav-links a { display:none } /* keep nav simpler on small screens; customize later */
    }
    /* small helper: container for page content */
    main.site-main{max-width:1200px;margin:28px auto;padding:0 18px}
  </style>
</head>
<body>
  <header class="site-nav" role="banner" aria-label="Top Navigation">
    <div style="display:flex;align-items:center;gap:18px">
      <div class="site-brand" onclick="location.href='index.php'">Hobbyverse</div>
    </div>

    <div style="display:flex;align-items:center;gap:18px">
      <nav class="nav-links" aria-label="Main Links">
        <a href="index.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="hobby.php">Hobbies</a>
        <a href="about.php">About</a>
      </nav>

      <form class="nav-search" method="GET" action="index.php" role="search" style="margin-right:8px;">
        <input type="search" name="search" placeholder="Search hobbies..." aria-label="Search hobbies">
        <button type="submit">Search</button>
      </form>

      <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div style="font-weight:600;color:#333">Hi, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></div>
          <a href="logout.php" style="text-decoration:none;color:var(--muted)">Logout</a>
        <?php else: ?>
          <a href="login.php" style="text-decoration:none;color:var(--muted)">Log in</a>
          <a href="registration.php" style="text-decoration:none;color:var(--muted)">Register</a>
        <?php endif; ?>

        <a href="cart.php" aria-label="Cart">
          🛒
          <?php
            $count = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
              foreach ($_SESSION['cart'] as $v) $count += (int)$v;
            }
            if ($count > 0) echo "<span class='badge' style='margin-left:6px'>$count</span>";
          ?>
        </a>
      </div>
    </div>
  </header>

  <!-- open main container: include page-specific content inside main -->
  <main class="site-main" role="main">
