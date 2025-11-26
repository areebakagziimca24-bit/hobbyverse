<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';
?>
<!-- NAVIGATION ONLY (NO <html> / <head> / <body> TAGS HERE) -->

<style>
:root {
    --accent:#ff6f91;
    --accent-light:#ffc1cf;
    --text:#333;
    --muted:#777;
    --bg:linear-gradient(135deg,#fffaf8 0%,#ffe7ef 100%);
    --shadow:0 10px 30px rgba(0,0,0,0.08);
}
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; background:var(--bg); color:var(--text); }

.nav-wrapper {
    position:sticky;
    top:0;
    z-index:1000;
    background:rgba(255,255,255,0.9);
    backdrop-filter:blur(8px);
    padding:12px 0;
    border-bottom:1px solid rgba(0,0,0,0.05);
}

.navbar {
    max-width:1300px;
    margin:auto;
    padding:0 20px;
    display:grid;
    grid-template-columns: 180px 1fr auto auto;
    align-items:center;
    gap:20px;
}

/* LOGO */
.logo {
    font-family:'Baloo 2',cursive;
    font-size:30px;
    font-weight:700;
    background:linear-gradient(90deg,var(--accent),var(--accent-light));
    -webkit-background-clip:text;
    color:transparent;
    cursor:pointer;
}

/* SEARCH */
.search-bar {
    display:flex;
    align-items:center;
    background:white;
    padding:6px 12px;
    border-radius:30px;
    border:1px solid #eee;
    box-shadow:var(--shadow);
    width:100%;
}
.search-bar input {
    width:100%;
    border:none;
    outline:none;
    padding:8px 10px;
    font-size:15px;
}
.search-bar button {
    background:var(--accent);
    color:white;
    border:none;
    padding:8px 18px;
    border-radius:20px;
    cursor:pointer;
    font-weight:600;
}

/* MENU */
.nav-center {
    display:flex;
    gap:26px;
}
.nav-center a {
    text-decoration:none;
    font-weight:600;
    font-size:15px;
    color:var(--text);
    transition:.25s;
}
.nav-center a:hover { color:var(--accent); }

/* RIGHT ICONS */
.nav-right {
    display:flex;
    align-items:center;
    gap:18px;
}

.icon-btn {
    font-size:22px;
    color:var(--text);
    text-decoration:none;
    position:relative;
    transition:.25s;
}
.icon-btn:hover { color:var(--accent); }

.badge {
    position:absolute;
    top:-6px;
    right:-10px;
    background:var(--accent);
    color:white;
    padding:2px 7px;
    border-radius:12px;
    font-size:12px;
    font-weight:700;
}

@media(max-width:950px){
    .nav-center{ display:none; }
    .navbar { grid-template-columns:150px 1fr auto; }
}
</style>

<!-- NAVBAR HTML -->
<div class="nav-wrapper">
    <div class="navbar">

        <!-- LOGO -->
        <div class="logo" onclick="location.href='index.php'">Hobbyverse</div>

        <!-- SEARCH -->
        <form class="search-bar" method="GET" action="search.php">
            <input type="search" name="search" placeholder="Search hobbies...">
            <button type="submit">Go</button>
        </form>

        <!-- MENU -->
        <div class="nav-center">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="hobby.php">Hobbies</a>
            <a href="about.php">About</a>
        </div>

        <!-- RIGHT SIDE -->
    <!-- RIGHT ICONS -->
<div class="nav-right">

    <?php if (isset($_SESSION['user_id'])): ?>
        <span style="font-weight:600;">Hi, <?= htmlspecialchars($_SESSION['name']); ?></span>

        <!-- ACCOUNT PAGE LINK -->
        <a href="account.php" class="icon-btn" title="My Account">👤</a>

        <a href="logout.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Logout</a>
    <?php else: ?>
        <a href="login.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Login</a>
        <a href="registration.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Register</a>
    <?php endif; ?>

    <!-- WISHLIST -->
    <a href="wishlist.php" class="icon-btn">❤️
        <?php
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                $wq = mysqli_query($conn, "SELECT COUNT(*) AS c FROM wishlist WHERE user_id=$uid");
                $wcount = mysqli_fetch_assoc($wq)['c'];
                if ($wcount > 0) echo "<span class='badge'>$wcount</span>";
            }
        ?>
    </a>

    <!-- CART -->
    <a href="cart.php" class="icon-btn">🛒
        <?php
            $cart_count = 0;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cart_count += $item['quantity'] ?? 0;
                }
            }
            if ($cart_count > 0) echo "<span class='badge'>$cart_count</span>";
        ?>
    </a>
</div>

    </div>
</div>

