<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$msg = "";
$error = "";

// ---------------- HANDLE ACTIONS ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Delete user
    if ($action === 'delete_user') {
        $uid = (int)($_POST['user_id'] ?? 0);

        // (optional) prevent deleting yourself
        if ($uid === ($_SESSION['user_id'] ?? 0)) {
            $error = "You cannot delete the currently logged-in admin.";
        } else {
            mysqli_query($conn, "DELETE FROM users WHERE user_id = $uid");
            $msg = "User deleted successfully.";
        }
    }

    // Update user info
    if ($action === 'update_user') {
        $uid   = (int)($_POST['user_id'] ?? 0);
        $name  = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
        $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
        $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
        $city  = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
        $addr  = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
        $role  = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'user'));

        if ($name === '' || $email === '') {
            $error = "Name and Email are required.";
        } else {
            $q = "
                UPDATE users SET
                    username = '$name',
                    email    = '$email',
                    phone    = '$phone',
                    city     = '$city',
                    address  = '$addr',
                    role     = '$role'
                WHERE user_id = $uid
            ";
            if (mysqli_query($conn, $q)) {
                $msg = "User updated successfully.";
            } else {
                $error = "Error updating user: " . mysqli_error($conn);
            }
        }
    }

    // Reset password
    if ($action === 'reset_password') {
        $uid     = (int)($_POST['user_id'] ?? 0);
        $newpass = $_POST['new_password'] ?? '';

        if (strlen($newpass) < 4) {
            $error = "New password must be at least 4 characters.";
        } else {
            $hash = password_hash($newpass, PASSWORD_DEFAULT);
            $q = "UPDATE users SET password='$hash' WHERE user_id=$uid";
            if (mysqli_query($conn, $q)) {
                $msg = "Password reset successfully.";
            } else {
                $error = "Error resetting password: " . mysqli_error($conn);
            }
        }
    }

    // avoid resubmit on refresh
    header("Location: admin_users.php");
    exit;
}

// ---------------- FETCH USERS (with search) ----------------
$search = trim($_GET['q'] ?? '');
$where = '';
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where = "WHERE username LIKE '%$s%' OR email LIKE '%$s%' OR phone LIKE '%$s%'";
}

$users_q = "SELECT * FROM users $where ORDER BY user_id DESC";
$users_res = mysqli_query($conn, $users_q);

$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Users - Hobbyverse</title>

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
.card-title{
    font-weight:600;
    color:#ff4c8b;
}
.badge-role{
    padding:4px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:600;
}
.badge-admin{background:#ffe0f0;color:#c2185b;}
.badge-user{background:#e0f7ff;color:#0288d1;}
.table-avatar{
    width:34px;
    height:34px;
    border-radius:50%;
    background:#ffe3ee;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    font-weight:600;
    color:#ff4c8b;
}
.btn-pink{
    background:#ff4c8b;
    color:#fff;
    border-radius:999px;
}
.btn-pink:hover{
    background:#e63b7b;
    color:#fff;
}
.form-control, .form-select{
    border-radius:10px;
    font-size:14px;
}
.search-box{
    max-width:280px;
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
            <h2 class="page-title">Users Management</h2>
            <p class="text-muted mb-0">Total users: <strong><?= (int)$total_users ?></strong></p>
        </div>

        <form class="d-flex search-box" method="get">
            <input type="text" class="form-control form-control-sm"
                   name="q" placeholder="Search by name, email, phone"
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-sm btn-pink ms-2" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </form>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success py-2"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

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
                            <th>Role</th>
                            <th style="width:220px;">Actions</th>
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
                                            <div class="text-muted" style="font-size:12px;">
                                                ID: <?= (int)$u['user_id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['phone']) ?></td>
                                <td><?= htmlspecialchars($u['city']) ?></td>
                                <td>
                                    <?php $role = strtolower($u['role'] ?? 'user'); ?>
                                    <span class="badge-role <?= $role === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                        <?= ucfirst($role) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- EDIT BUTTON -->
                                    <button class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUser<?= (int)$u['user_id'] ?>">
                                        Edit
                                    </button>

                                    <!-- RESET PASSWORD BUTTON -->
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#passUser<?= (int)$u['user_id'] ?>">
                                        Reset Pass
                                    </button>

                                    <!-- DELETE FORM -->
                                    <form method="POST" style="display:inline"
                                          onsubmit="return confirm('Delete user <?= htmlspecialchars($u['username'],ENT_QUOTES) ?>?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- EDIT USER MODAL -->
                            <div class="modal fade" id="editUser<?= (int)$u['user_id'] ?>" tabindex="-1">
                              <div class="modal-dialog">
                                <div class="modal-content p-3">
                                  <div class="modal-header border-0">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_user">
                                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">

                                        <div class="mb-2">
                                            <label class="form-label">Username</label>
                                            <input class="form-control" name="username"
                                                   value="<?= htmlspecialchars($u['username']) ?>" required>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                   value="<?= htmlspecialchars($u['email']) ?>" required>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Phone</label>
                                            <input class="form-control" name="phone"
                                                   value="<?= htmlspecialchars($u['phone']) ?>">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">City</label>
                                            <input class="form-control" name="city"
                                                   value="<?= htmlspecialchars($u['city']) ?>">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address"
                                                      rows="2"><?= htmlspecialchars($u['address']) ?></textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Role</label>
                                            <select class="form-select" name="role">
                                                <option value="user"  <?= ($u['role'] === 'user')  ? 'selected':''; ?>>User</option>
                                                <option value="admin" <?= ($u['role'] === 'admin') ? 'selected':''; ?>>Admin</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-pink mt-2" type="submit">Save Changes</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- RESET PASSWORD MODAL -->
                            <div class="modal fade" id="passUser<?= (int)$u['user_id'] ?>" tabindex="-1">
                              <div class="modal-dialog">
                                <div class="modal-content p-3">
                                  <div class="modal-header border-0">
                                    <h5 class="modal-title">Reset Password</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    <p class="text-muted" style="font-size:13px;">
                                      Enter a new password for <strong><?= htmlspecialchars($u['username']) ?></strong>.
                                    </p>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="reset_password">
                                        <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">

                                        <div class="mb-2">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>

                                        <button class="btn btn-pink mt-2" type="submit">Reset Password</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No users found.</td>
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
