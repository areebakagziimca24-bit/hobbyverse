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

    // Add hobby
    if ($action === 'add_hobby') {
        $name = mysqli_real_escape_string($conn, trim($_POST['hobby_name'] ?? ''));
        $desc = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
        $img  = mysqli_real_escape_string($conn, trim($_POST['hobby_image'] ?? ''));

        if ($name === "") {
            $error = "Hobby name is required.";
        } else {
            $q = "INSERT INTO hobbies (hobby_name, description, hobby_image)
                  VALUES ('$name', '$desc', '$img')";
            if (mysqli_query($conn, $q)) {
                $msg = "Hobby added successfully.";
            } else {
                $error = "Error adding hobby: " . mysqli_error($conn);
            }
        }
    }

    // Update hobby
    if ($action === 'update_hobby') {
        $hid  = (int)($_POST['hobby_id'] ?? 0);
        $name = mysqli_real_escape_string($conn, trim($_POST['hobby_name'] ?? ''));
        $desc = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
        $img  = mysqli_real_escape_string($conn, trim($_POST['hobby_image'] ?? ''));

        if ($name === "") {
            $error = "Hobby name cannot be empty.";
        } else {
            $q = "UPDATE hobbies
                  SET hobby_name='$name',
                      description='$desc',
                      hobby_image='$img'
                  WHERE hobby_id=$hid";
            if (mysqli_query($conn, $q)) {
                $msg = "Hobby updated successfully.";
            } else {
                $error = "Error updating hobby: " . mysqli_error($conn);
            }
        }
    }

    // Delete hobby
    if ($action === 'delete_hobby') {
        $hid = (int)($_POST['hobby_id'] ?? 0);

        // Optional: check if products exist under this hobby
        $check = mysqli_fetch_row(
            mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE hobby_id=$hid")
        )[0] ?? 0;

        if ($check > 0) {
            $error = "There are $check product(s) under this hobby. Please reassign or delete products first.";
        } else {
            mysqli_query($conn, "DELETE FROM hobbies WHERE hobby_id=$hid");
            $msg = "Hobby deleted.";
        }
    }

    // avoid resubmit on refresh
    header("Location: admin_hobbies.php");
    exit;
}

// ---------------- FETCH HOBBIES WITH PRODUCT COUNT ----------------
$hobbies_res = mysqli_query($conn, "
    SELECT h.*,
           COUNT(p.product_id) AS product_count
    FROM hobbies h
    LEFT JOIN products p ON p.hobby_id = h.hobby_id
    GROUP BY h.hobby_id
    ORDER BY h.hobby_name ASC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Hobbies - Hobbyverse</title>

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
.hobby-img{
    width:50px;
    height:50px;
    border-radius:10px;
    object-fit:cover;
    border:1px solid #f0c4d6;
    background:#fff;
}
.badge-count{
    padding:4px 10px;
    border-radius:999px;
    background:#ffe3ee;
    color:#ff4c8b;
    font-size:12px;
    font-weight:600;
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
    <a href="admin_hobbies.php" class="active"><i class="fa fa-heart"></i> Hobbies</a>
    <hr style="border-color:#ffcadd;">
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    <a href="index.php" style="background:#ffb6c9;margin-top:10px;">
        <i class="fa fa-door-open"></i> Exit Dashboard
    </a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <h2 class="page-title">Hobbies Management</h2>
    <p class="text-muted">Create and manage hobby categories for products.</p>

    <?php if ($msg): ?>
      <div class="alert alert-success py-2"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ADD HOBBY -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">➕ Add New Hobby</h5>

            <form method="POST">
                <input type="hidden" name="action" value="add_hobby">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Hobby Name</label>
                        <input type="text" name="hobby_name" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control"
                               placeholder="Short description (optional)">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Image URL</label>
                        <input type="text" name="hobby_image" class="form-control"
                               placeholder="hobby image path (optional)">
                    </div>
                    <div class="col-12 mt-1">
                        <button type="submit" class="btn btn-pink">Add Hobby</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- HOBBIES TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">🎯 All Hobbies</h5>

            <div class="table-responsive">
                <table class="table align-middle table-striped">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Hobby</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($hobbies_res && mysqli_num_rows($hobbies_res) > 0): ?>
                        <?php while ($h = mysqli_fetch_assoc($hobbies_res)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($h['hobby_image'])): ?>
                                    <img src="<?= htmlspecialchars($h['hobby_image']) ?>" class="hobby-img">
                                <?php else: ?>
                                    <div class="hobby-img d-flex align-items-center justify-content-center"
                                         style="font-size:20px;color:#ff4c8b;background:#ffe3ee;">
                                        <i class="fa fa-heart"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($h['hobby_name']) ?></div>
                                <div class="text-muted" style="font-size:12px;">ID: <?= (int)$h['hobby_id'] ?></div>
                            </td>
                            <td style="max-width:320px;">
                                <span style="font-size:13px;">
                                    <?= htmlspecialchars($h['description'] ?: '—') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-count">
                                    <?= (int)$h['product_count'] ?> product(s)
                                </span>
                            </td>
                            <td>
                                <!-- Edit -->
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editHobby<?= (int)$h['hobby_id'] ?>">
                                    Edit
                                </button>

                                <!-- Delete -->
                                <form method="POST" style="display:inline"
                                      onsubmit="return confirm('Delete hobby <?= htmlspecialchars($h['hobby_name'],ENT_QUOTES) ?>?');">
                                    <input type="hidden" name="action" value="delete_hobby">
                                    <input type="hidden" name="hobby_id" value="<?= (int)$h['hobby_id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- EDIT MODAL -->
                        <div class="modal fade" id="editHobby<?= (int)$h['hobby_id'] ?>" tabindex="-1">
                          <div class="modal-dialog">
                            <div class="modal-content p-3">
                              <div class="modal-header border-0">
                                <h5 class="modal-title">Edit Hobby</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_hobby">
                                    <input type="hidden" name="hobby_id" value="<?= (int)$h['hobby_id'] ?>">

                                    <div class="mb-2">
                                        <label class="form-label">Hobby Name</label>
                                        <input type="text" name="hobby_name" class="form-control"
                                               value="<?= htmlspecialchars($h['hobby_name']) ?>" required>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" rows="2"
                                                  class="form-control"><?= htmlspecialchars($h['description']) ?></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" name="hobby_image" class="form-control"
                                               value="<?= htmlspecialchars($h['hobby_image']) ?>">
                                        <div class="form-text">
                                            Keep same path style as product images.
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-pink mt-2">
                                        Save Changes
                                    </button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hobbies added yet.
                            </td>
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
