<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// allowed statuses
$allowed_statuses = ['pending','processing','shipped','delivered','cancelled'];

// ---------------- HANDLE ACTIONS (POST) ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? '';
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

    if ($order_id > 0 && $action === 'update_status') {
        $new_status = $_POST['status'] ?? 'pending';
        if (in_array($new_status, $allowed_statuses, true)) {
            $status_esc = mysqli_real_escape_string($conn, $new_status);
            mysqli_query($conn, "UPDATE orders SET status='$status_esc' WHERE order_id=$order_id");
        }
    }

    if ($order_id > 0 && $action === 'cancel_order') {
        mysqli_query($conn, "UPDATE orders SET status='cancelled' WHERE order_id=$order_id");
    }

    // avoid resubmission
    header("Location: admin_orders.php");
    exit;
}

// ---------------- FILTER ----------------
$current_filter = $_GET['status'] ?? 'all';
$filter_sql = "";
if (in_array($current_filter, $allowed_statuses, true)) {
    $filter_esc = mysqli_real_escape_string($conn, $current_filter);
    $filter_sql = "WHERE o.status = '$filter_esc'";
}

// ---------------- FETCH ORDERS ----------------
$orders_q = "
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    $filter_sql
    ORDER BY o.order_id DESC
";
$orders_res = mysqli_query($conn, $orders_q);

// small helper for counts
function status_count($conn, $status) {
    $status = mysqli_real_escape_string($conn, $status);
    $q = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status='$status'");
    $row = mysqli_fetch_row($q);
    return (int)$row[0];
}

$pending_count   = status_count($conn,'pending');
$processing_count= status_count($conn,'processing');
$shipped_count   = status_count($conn,'shipped');
$delivered_count = status_count($conn,'delivered');
$cancelled_count = status_count($conn,'cancelled');

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Orders - Hobbyverse</title>

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
.filter-chip{
    display:inline-block;
    padding:6px 12px;
    border-radius:999px;
    font-size:13px;
    margin-right:8px;
    background:#ffe3ee;
    color:#ff4c8b;
    text-decoration:none;
}
.filter-chip.active{
    background:#ff4c8b;
    color:#fff;
}
.actions-form{
    display:flex;
    gap:6px;
    align-items:center;
}
.actions-form select{
    font-size:12px;
    padding:4px 6px;
}
.actions-form button{
    font-size:12px;
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

    <h2 class="page-title">Orders Management</h2>
    <p class="text-muted">View and manage all customer orders.</p>

    <!-- FILTER CHIPS -->
    <div class="mb-3">
        <?php
        $filters = [
            'all'        => "All",
            'pending'    => "Pending ($pending_count)",
            'processing' => "Processing ($processing_count)",
            'shipped'    => "Shipped ($shipped_count)",
            'delivered'  => "Delivered ($delivered_count)",
            'cancelled'  => "Cancelled ($cancelled_count)",
        ];
        foreach ($filters as $key => $label):
            $active = ($current_filter === $key) ? 'active' : '';
            $link = 'admin_orders.php';
            if ($key !== 'all') $link .= '?status='.$key;
        ?>
            <a href="<?= $link ?>" class="filter-chip <?= $active ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- ORDERS TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">All Orders</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#Order</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($orders_res && mysqli_num_rows($orders_res) > 0): ?>
                        <?php while($o = mysqli_fetch_assoc($orders_res)): 
                            $status = strtolower($o['status'] ?? 'pending');
                            $badgeClass = 'badge-pending';
                            if ($status === 'processing') $badgeClass = 'badge-processing';
                            elseif ($status === 'shipped') $badgeClass = 'badge-shipped';
                            elseif ($status === 'delivered') $badgeClass = 'badge-delivered';
                            elseif ($status === 'cancelled') $badgeClass = 'badge-cancelled';
                        ?>
                        <tr>
                            <td>#<?= (int)$o['order_id'] ?></td>
                            <td><?= htmlspecialchars($o['username'] ?? $o['customer_name'] ?? 'Guest') ?></td>
                            <td>₹<?= number_format($o['total_amount'],2) ?></td>
                            <td><?= htmlspecialchars($o['payment_method'] ?? 'COD') ?></td>
                            <td>
                                <span class="badge-status <?= $badgeClass ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
                            <td>
                                <div class="actions-form">
                                    <!-- Update status -->
                                    <form method="POST" class="d-flex gap-1">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?= (int)$o['order_id'] ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <?php foreach ($allowed_statuses as $st): ?>
                                                <option value="<?= $st ?>" <?= $st === $status ? 'selected':'' ?>>
                                                    <?= ucfirst($st) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" type="submit">
                                            Save
                                        </button>
                                    </form>

                                    <!-- Cancel -->
                                    <?php if ($status !== 'cancelled' && $status !== 'delivered'): ?>
                                    <form method="POST"
                                          onsubmit="return confirm('Cancel order #<?= (int)$o['order_id'] ?>?');">
                                        <input type="hidden" name="action" value="cancel_order">
                                        <input type="hidden" name="order_id" value="<?= (int)$o['order_id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            Cancel
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- View details -->
                                    <a href="admin_order_details.php?order_id=<?= (int)$o['order_id'] ?>"
                                       class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No orders found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>
