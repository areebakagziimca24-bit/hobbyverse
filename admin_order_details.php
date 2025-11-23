<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    die("Invalid order ID.");
}

// Fetch order + user
$order_q = "
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = $order_id
";
$order_res = mysqli_query($conn, $order_q);
$order = mysqli_fetch_assoc($order_res);

if (!$order) {
    die("Order not found.");
}

// Fetch items
$items_q = "
    SELECT oi.*, p.product_name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
";
$items_res = mysqli_query($conn, $items_q);

?>
<!DOCTYPE html>
<html>
<head>
<title>Order #<?= $order_id ?> Details - Hobbyverse Admin</title>

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
    font-size:14px;
}
.sidebar a:hover{
    background:#ff6ea4;
}
.sidebar a.active{
    background:#ff6ea4;
}
.content{
    margin-left:250px;
    padding:30px;
}
.page-title{
    font-weight:600;
    color:#ff4c8b;
}
.badge-status{
    padding:4px 9px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}
.badge-pending{background:#fff0c2;color:#b08500;}
.badge-processing{background:#d9ecff;color:#0f53a3;}
.badge-shipped{background:#d7f6ff;color:#00718f;}
.badge-delivered{background:#d5ffd6;color:#15803d;}
.badge-cancelled{background:#ffd5d5;color:#b42318;}
.table img{
    width:50px;height:50px;border-radius:6px;object-fit:cover;
}
.info-box{
    background:#fff;
    border-radius:14px;
    padding:18px 18px;
    box-shadow:0 4px 14px rgba(0,0,0,0.06);
    margin-bottom:20px;
}
.info-title{
    font-weight:600;
    color:#ff4c8b;
    margin-bottom:8px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Hobbyverse Admin</h2>
    <a href="admin.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="admin_products.php"><i class="fa fa-box"></i> Products</a>
    <a href="admin_orders.php" class="active"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="admin_users.php"><i class="fa fa-users"></i> Users</a>
    <a href="admin_hobbies.php"><i class="fa fa-heart"></i> Hobbies</a>
    <hr style="border-color:#ffcadd;">
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    <a href="index.php" style="background:#ffb6c9;margin-top:10px;">
        <i class="fa fa-door-open"></i> Exit Dashboard
    </a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="page-title">Order #<?= $order_id ?> Details</h2>
            <p class="text-muted mb-0">
                Placed on <?= htmlspecialchars($order['created_at'] ?? '') ?>
            </p>
        </div>

        <a href="admin_orders.php" class="btn btn-outline-secondary">
            ← Back to Orders
        </a>
    </div>

    <?php
        $status = strtolower($order['status'] ?? 'pending');
        $badgeClass = 'badge-pending';
        if ($status === 'processing') $badgeClass = 'badge-processing';
        elseif ($status === 'shipped') $badgeClass = 'badge-shipped';
        elseif ($status === 'delivered') $badgeClass = 'badge-delivered';
        elseif ($status === 'cancelled') $badgeClass = 'badge-cancelled';
    ?>

    <!-- ORDER SUMMARY -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-box">
                <div class="info-title">Order Summary</div>
                <p class="mb-1"><strong>Order ID:</strong> #<?= $order_id ?></p>
                <p class="mb-1"><strong>User:</strong> <?= htmlspecialchars($order['username'] ?? 'Guest') ?></p>
                <p class="mb-1"><strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'],2) ?></p>
                <p class="mb-1"><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></p>
                <p class="mb-0">
                    <strong>Status:</strong>
                    <span class="badge-status <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                </p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <div class="info-title">Customer & Address</div>
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? '') ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? '') ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? '') ?></p>
                <p class="mb-1"><strong>City:</strong> <?= htmlspecialchars($order['city'] ?? '') ?></p>
                <p class="mb-0"><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['address'] ?? '')) ?></p>
            </div>
        </div>
    </div>

    <!-- ORDER ITEMS -->
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Items in this Order</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $grand_total = 0;
                        while($it = mysqli_fetch_assoc($items_res)):
                            $grand_total += (float)$it['subtotal'];
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($it['image'])): ?>
                                        <img src="<?= htmlspecialchars($it['image']) ?>" alt="">
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($it['product_name']) ?></span>
                                </div>
                            </td>
                            <td>₹<?= number_format($it['price'],2) ?></td>
                            <td><?= (int)$it['quantity'] ?></td>
                            <td>₹<?= number_format($it['subtotal'],2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                            <td><strong>₹<?= number_format($grand_total,2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
