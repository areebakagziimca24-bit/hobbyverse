<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Search filter
$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where = "WHERE username LIKE '%$s%' OR email LIKE '%$s%' OR phone LIKE '%$s%' OR city LIKE '%$s%'";
}

$users_res = mysqli_query($conn, "SELECT * FROM users $where ORDER BY user_id DESC");
$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Users - Hobbyverse</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#fff7fa;
    overflow-x:hidden;
}
.sidebar{
    width:230px;height:100vh;position:fixed;left:0;top:0;
    background:#ff4c8b;color:white;padding:20px 15px;
}
.sidebar h2{font-size:20px;font-weight:600;margin-bottom:20px;}
.sidebar a{
    display:block;padding:10px 12px;margin-bottom:8px;
    color:white;text-decoration:none;border-radius:8px;font-size:14px;
}
.sidebar a:hover,.sidebar a.active{background:#ff6ea4;}

.content{margin-left:250px;padding:30px;}
.page-title{font-weight:600;color:#ff4c8b;}

.table td, .table th { text-align: center; }

.table-avatar{
    width:34px;height:34px;border-radius:50%;
    background:#ffe3ee;display:flex;align-items:center;
    justify-content:center;font-size:15px;font-weight:600;color:#ff4c8b;
}

.search-box{max-width:280px;}
.btn-pink{
    background:#ff4c8b;color:#fff;border-radius:999px;
}
.btn-pink:hover{background:#e63b7b;color:#fff;}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Hobbyverse Admin</h2>
    <a href="admin.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="admin_products.php"><i class="fa fa-box"></i> Products</a>
    <a href="admin_orders.php"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="admin_users.php" class="active"><i class="fa fa-users"></i> Users</a>
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
            <h2 class="page-title">Users Overview</h2>
            <p class="text-muted mb-0">Total users: <strong><?= (int)$total_users ?></strong></p>
        </div>

        <form class="d-flex search-box" method="get">
            <input type="text" class="form-control form-control-sm"
                   name="q" placeholder="Search by name, email, phone, city"
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-sm btn-pink ms-2" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>

    <!-- USERS TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">All Users</h5>

            <div class="table-responsive">
                <table class="table align-middle table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($users_res && mysqli_num_rows($users_res) > 0): ?>
                        <?php while($u = mysqli_fetch_assoc($users_res)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="table-avatar">
                                        <?= strtoupper(substr($u['username'] ?? 'U',0,1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;">
                                            <?= htmlspecialchars($u['username']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size:12px;">ID: <?= (int)$u['user_id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td><?= htmlspecialchars($u['city']) ?></td>
                            <td><?= htmlspecialchars($u['address']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
