<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// ---------------- Fetch Statistics ----------------

// Total users
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));

// Total orders
$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"));

// Total products
$products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"));

// Low stock count
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock <= 5"));

// Recent Orders (latest 6)
$recent_orders = mysqli_query($conn, "
    SELECT o.order_id, o.total_amount, o.status, o.created_at, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC LIMIT 6
");

// Top Selling Products
$top_products = mysqli_query($conn, "
    SELECT p.product_id, p.product_name, p.image, 
           SUM(oi.quantity) AS sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    GROUP BY oi.product_id
    ORDER BY sold DESC
    LIMIT 5
");

// Low Stock Products
$low_stock_products = mysqli_query($conn, "
    SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC
");

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard - Hobbyverse</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#fff7fa;
    overflow-x:hidden;
}
.sidebar{
    width:230px;
    height:100vh;
    position:fixed;
    left:0;top:0;
    background:#ff4c8b;
    color:white;
    padding:20px 15px;
}
.sidebar h2{
    font-size:20px;
    font-weight:600;
    margin-bottom:20px;
}
.sidebar a{
    display:block;
    padding:10px 12px;
    margin-bottom:8px;
    color:white;
    text-decoration:none;
    border-radius:8px;
}
.sidebar a:hover{
    background:#ff6ea4;
}
.content{
    margin-left:250px;
    padding:30px;
}
.card-stat{
    background:white;
    border-radius:15px;
    padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}
.card-title{
    font-weight:600;
    color:#ff4c8b;
}
.table img{
    width:45px;
    height:45px;
    border-radius:6px;
    object-fit:cover;
}
.section-title{
    font-weight:600;
    color:#ff4c8b;
    margin-bottom:15px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Hobbyverse Admin</h2>
    <a href="admin.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="admin_products.php"><i class="fa fa-box"></i> Products</a>
    <a href="admin_orders.php"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="admin_users.php"><i class="fa fa-users"></i> Users</a>
    <a href="admin_hobbies.php"><i class="fa fa-heart"></i> Hobbies</a>
    <hr style="border-color:#ffcadd;">
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

<h2 style="font-weight:600; color:#ff4c8b;">Dashboard Overview</h2>
<p class="text-muted">Welcome back, Admin 👋</p>

<!-- TOP STAT CARDS -->
<div class="row g-4 mt-1">

    <div class="col-md-3">
        <div class="card-stat">
            <h5 class="card-title"><i class="fa fa-users"></i> Total Users</h5>
            <h2><?= $users['total'] ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <h5 class="card-title"><i class="fa fa-shopping-bag"></i> Orders</h5>
            <h2><?= $orders['total'] ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <h5 class="card-title"><i class="fa fa-box"></i> Products</h5>
            <h2><?= $products['total'] ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-stat">
            <h5 class="card-title"><i class="fa fa-triangle-exclamation"></i> Low Stock</h5>
            <h2><?= $low_stock['total'] ?></h2>
        </div>
    </div>

</div>

<!-- RECENT ORDERS -->
<div class="mt-5">
    <h4 class="section-title">🛒 Recent Orders</h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($o = mysqli_fetch_assoc($recent_orders)): ?>
            <tr>
                <td><?= $o['order_id'] ?></td>
                <td><?= $o['username'] ?></td>
                <td>₹<?= number_format($o['total_amount'],2) ?></td>
                <td><?= ucfirst($o['status']) ?></td>
                <td><?= $o['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- TOP SELLING PRODUCTS -->
<div class="mt-5">
    <h4 class="section-title">🏆 Top Selling Products</h4>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Sold</th>
            </tr>
        </thead>
        <tbody>
        <?php while($p = mysqli_fetch_assoc($top_products)): ?>
            <tr>
                <td><img src="<?= $p['image'] ?>"></td>
                <td><?= $p['product_name'] ?></td>
                <td><b><?= $p['sold'] ?></b></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- LOW STOCK ITEMS -->
<div class="mt-5">
    <h4 class="section-title">⚠️ Low Stock Items</h4>

    <table class="table table-danger table-striped">
        <thead>
            <tr>
                <th>Product</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
        <?php while($ls = mysqli_fetch_assoc($low_stock_products)): ?>
            <tr>
                <td><?= $ls['product_name'] ?></td>
                <td><b><?= $ls['stock'] ?></b></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>

</body>
</html>
