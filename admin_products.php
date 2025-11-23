<?php
session_start();
include "db_connect.php";

// ---------------- ADMIN CHECK ----------------
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// ---------------- HANDLE ACTIONS ----------------
$msg = "";

// Delete product
if (isset($_POST['delete_product'])) {
    $pid = intval($_POST['product_id']);
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $pid");
    $msg = "Product deleted successfully.";
}

// Update product
if (isset($_POST['edit_product'])) {
    $pid   = intval($_POST['product_id']);
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $hobby = intval($_POST['hobby_id']);
    $img   = mysqli_real_escape_string($conn, $_POST['image']);

    $update = "
        UPDATE products SET
        product_name='$name',
        price=$price,
        stock=$stock,
        hobby_id=$hobby,
        image='$img'
        WHERE product_id=$pid
    ";
    mysqli_query($conn, $update);
    $msg = "Product updated successfully.";
}

// Add new product
if (isset($_POST['add_product'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $hobby = intval($_POST['hobby_id']);
    $img   = mysqli_real_escape_string($conn, $_POST['image']);

    mysqli_query($conn,
        "INSERT INTO products (product_name, price, stock, hobby_id, image)
         VALUES ('$name', $price, $stock, $hobby, '$img')"
    );

    $msg = "Product added successfully.";
}

// Fetch all products
$products = mysqli_query($conn, "
    SELECT p.*, h.hobby_name 
    FROM products p
    LEFT JOIN hobbies h ON p.hobby_id = h.hobby_id
    ORDER BY p.product_id DESC
");

// Fetch hobbies for dropdown
$hobbies = mysqli_query($conn, "SELECT * FROM hobbies ORDER BY hobby_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Products - Hobbyverse</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family:'Poppins',sans-serif;
    background:#fff7fa;
}
.sidebar{
    width:230px;height:100vh;position:fixed;left:0;top:0;
    background:#ff4c8b;color:white;padding:20px 15px;
}
.sidebar a{
    display:block;padding:10px 12px;color:white;text-decoration:none;border-radius:8px;
}
.sidebar a:hover{background:#ff6ea4;}
.content{margin-left:250px;padding:30px;}

.card-title{color:#ff4c8b;font-weight:600;}
.table img{width:50px;height:50px;object-fit:cover;border-radius:8px;}
.btn-pink{background:#ff4c8b;color:white;border-radius:10px;}
.btn-pink:hover{background:#e63b7b;}
.form-control{border-radius:10px;}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Hobbyverse Admin</h2>
    <a href="admin.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="admin_products.php" style="background:#ff6ea4;"><i class="fa fa-box"></i> Products</a>
    <a href="admin_orders.php"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="admin_users.php"><i class="fa fa-users"></i> Users</a>
    <a href="admin_hobbies.php"><i class="fa fa-heart"></i> Hobbies</a>
    <hr style="border-color:#ffcadd;">
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>


<!-- MAIN CONTENT -->
<div class="content">

<h2 class="card-title">Products Management</h2>
<p class="text-muted">Manage all store products here.</p>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>


<!-- ADD PRODUCT -->
<div class="card p-4 my-4">
    <h5 class="card-title">➕ Add New Product</h5>

    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="name" placeholder="Product Name" required class="form-control">
            </div>

            <div class="col-md-2">
                <input type="number" name="price" placeholder="Price" required step="0.01" class="form-control">
            </div>

            <div class="col-md-2">
                <input type="number" name="stock" placeholder="Stock" required class="form-control">
            </div>

            <div class="col-md-3">
                <select name="hobby_id" class="form-control">
                    <option value="">Select Hobby</option>
                    <?php while($h = mysqli_fetch_assoc($hobbies)): ?>
                        <option value="<?= $h['hobby_id'] ?>"><?= $h['hobby_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-6 mt-2">
                <input type="text" name="image" placeholder="Image URL" class="form-control">
            </div>

            <div class="col-md-2 mt-2">
                <button name="add_product" class="btn btn-pink w-100">Add</button>
            </div>
        </div>
    </form>
</div>


<!-- ALL PRODUCTS TABLE -->
<div class="card p-4">
    <h5 class="card-title">📦 All Products</h5>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Hobby</th>
                <th>Price</th>
                <th>Stock</th>
                <th width="220">Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while($p = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><img src="<?= $p['image'] ?>"></td>
                <td><?= $p['product_name'] ?></td>
                <td><?= $p['hobby_name'] ?: "—" ?></td>
                <td>₹<?= number_format($p['price'],2) ?></td>
                <td><?= $p['stock'] ?></td>

                <td>
                    <!-- EDIT BUTTON OPENS A MODAL (Bootstrap) -->
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal" data-bs-target="#editModal<?= $p['product_id'] ?>">
                        Edit
                    </button>

                    <!-- DELETE -->
                    <form method="POST" style="display:inline-block">
                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                        <button name="delete_product" class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete this product?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="editModal<?= $p['product_id'] ?>">
                <div class="modal-dialog">
                    <div class="modal-content p-3">
                        <h4>Edit Product</h4>

                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">

                            <input class="form-control mt-2" name="name" value="<?= $p['product_name'] ?>">
                            <input class="form-control mt-2" name="price" type="number" step="0.01" value="<?= $p['price'] ?>">
                            <input class="form-control mt-2" name="stock" type="number" value="<?= $p['stock'] ?>">
                            <input class="form-control mt-2" name="image" value="<?= $p['image'] ?>">

                            <select class="form-control mt-2" name="hobby_id">
                                <option value="">None</option>
                                <?php
                                mysqli_data_seek($hobbies, 0);
                                while($h = mysqli_fetch_assoc($hobbies)):
                                ?>
                                <option value="<?= $h['hobby_id'] ?>"
                                    <?= ($p['hobby_id'] == $h['hobby_id']) ? "selected" : "" ?>>
                                    <?= $h['hobby_name'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>

                            <button class="btn btn-pink mt-3" name="edit_product">Save Changes</button>
                        </form>

                    </div>
                </div>
            </div>

        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
