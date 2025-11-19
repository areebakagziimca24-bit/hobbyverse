<?php
// Safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Hobbyverse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Fonts + AOS -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --accent: #ff6f91;
            --accent-light: #ffc1cf;
            --text: #333;
            --muted: #777;
            --bg: linear-gradient(135deg, #fffaf8 0%, #ffe7ef 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); }

        /* NAVBAR */
        .nav-wrapper {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(6px);
            padding: 14px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .navbar {
            max-width: 1200px;
            margin: auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
        }

        /* Logo */
        .logo {
            font-family: 'Baloo 2', cursive;
            font-weight: 700;
            font-size: 28px;
            background: linear-gradient(90deg, var(--accent), var(--accent-light));
            -webkit-background-clip: text;
            color: transparent;
            cursor: pointer;
        }

        /* Center Nav Links */
        .nav-center {
            display: flex;
            gap: 28px;
            justify-content: center;
        }

        .nav-center a {
            text-decoration: none;
            font-weight: 600;
            color: var(--text);
            transition: 0.25s;
        }

        .nav-center a:hover {
            color: var(--accent);
        }

        /* Right Icons */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-self: end;
        }

        .icon-btn {
            font-size: 22px;
            text-decoration: none;
            color: var(--text);
            position: relative;
            transition: 0.25s;
        }

        .icon-btn:hover {
            color: var(--accent);
        }

        .badge {
            position: absolute;
            top: -6px;
            right: -10px;
            background: var(--accent);
            color: white;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            background: white;
            padding: 6px 10px;
            border-radius: 30px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0,0,0,0.04);
        }

        .search-bar input {
            border: none;
            outline: none;
            padding: 8px 10px;
            width: 180px;
        }

        .search-bar button {
            background: var(--accent);
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
        }

        @media(max-width: 850px) {
            .nav-center { display: none; }
            .search-bar input { width: 100px; }
        }

        main.site-main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="nav-wrapper">
    <div class="navbar">

        <!-- Logo -->
        <div class="logo" onclick="location.href='index.php'">Hobbyverse</div>

        <!-- Center Menu -->
        <div class="nav-center">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="hobby.php">Hobbies</a>
            <a href="about.php">About</a>
        </div>

        <!-- Right Section -->
        <div class="nav-right">

            <!-- Search -->
            <form class="search-bar" method="GET" action="index.php">
                <input type="search" name="search" placeholder="Search hobbies...">
                <button type="submit">Go</button>
            </form>

            <!-- User -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="font-weight:600;">Hi, <?= htmlspecialchars($_SESSION['name']); ?></span>
                <a href="logout.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Logout</a>
            <?php else: ?>
                <a href="login.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Login</a>
                <a href="registration.php" class="icon-btn" style="font-size:16px;color:var(--muted)">Register</a>
            <?php endif; ?>

            <!-- Wishlist -->
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

            <!-- Cart -->
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

<!-- Page Wrapper -->
<main class="site-main">
