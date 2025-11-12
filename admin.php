<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC") 
          or die("❌ Orders query failed: " . mysqli_error($conn));


$products = mysqli_query($conn, "SELECT p.*, h.hobby_name 
                                 FROM products p 
                                 JOIN hobbies h ON p.hobby_id=h.hobby_id 
                                 ORDER BY p.product_id DESC") 
            or die("❌ Products query failed: " . mysqli_error($conn));


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $hobby_id = intval($_POST['hobby_id']);
    $image = mysqli_real_escape_string($conn, $_POST['image']); // filename

    $sql = "INSERT INTO products (hobby_id, product_name, description, price, image) 
            VALUES ($hobby_id, '$name', '$desc', $price, '$image')";
    mysqli_query($conn, $sql) or die("❌ Insert failed: " . mysqli_error($conn));

    header("Location: admin.php");
    exit;
}


$hobbies = mysqli_query($conn, "SELECT * FROM hobbies") 
           or die("❌ Hobbies query failed: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', sans-serif; margin:0; background:#f9f9f9; }
    header { background:#333; color:#fff; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
    header h1 { margin:0; font-size:22px; }
    header a { color:#ff4c8b; text-decoration:none; font-weight:600; }
    main { padding:30px; }
    section { margin-bottom:50px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    h2 { margin-top:0; color:#333; }
    table { border-collapse:collapse; width:100%; margin-top:15px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:left; }
    th { background:#f1f1f1; }
    form { margin-top:20px; }
    input, textarea, select { padding:10px; margin:8px 0; width:100%; max-width:400px; border:1px solid #ccc; border-radius:8px; }
    button { padding:10px 20px; background:#ff4c8b; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    button:hover { background:#e63972; }
  </style>
</head>
<body>

<header>
  <h1>📊 Admin Dashboard - Hobbyverse</h1>
  <a href="logout.php">Logout</a>
</header>

<main>


  <section>
    <h2>🛒 Orders</h2>
    <table>
      <tr>
        <th>ID</th><th>Customer</th><th>Email</th><th>Total</th><th>Payment</th><th>Date</th>
      </tr>
      <?php while($order = mysqli_fetch_assoc($orders)): ?>
      <tr>
        <td><?php echo $order['order_id']; ?></td>
        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
        <td><?php echo htmlspecialchars($order['email']); ?></td>
        <td>₹<?php echo $order['total_amount']; ?></td>
        <td><?php echo ucfirst($order['payment_method']); ?></td>
        <td><?php echo $order['order_date']; ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </section>


  <section>
    <h2>📦 Products</h2>
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Hobby</th><th>Price</th><th>Image</th>
      </tr>
      <?php while($p = mysqli_fetch_assoc($products)): ?>
      <tr>
        <td><?php echo $p['product_id']; ?></td>
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><?php echo htmlspecialchars($p['hobby_name']); ?></td>
        <td>₹<?php echo $p['price']; ?></td>
        <td><?php echo htmlspecialchars($p['image']); ?></td>
      </tr>
      <?php endwhile; ?>
    </table>

    <h3>Add New Product</h3>
    <form method="POST">
      <input type="text" name="product_name" placeholder="Product Name" required>
      <textarea name="description" placeholder="Description"></textarea>
      <input type="number" name="price" step="0.01" placeholder="Price" required>
      <select name="hobby_id" required>
        <option value="">-- Select Hobby --</option>
        <?php while($h = mysqli_fetch_assoc($hobbies)): ?>
          <option value="<?php echo $h['hobby_id']; ?>"><?php echo htmlspecialchars($h['hobby_name']); ?></option>
        <?php endwhile; ?>
      </select>
      <input type="text" name="image" placeholder="Image filename (e.g. guitar.jpg)">
      <button type="submit" name="add_product">Add Product</button>
    </form>
  </section>

</main>

</body>
</html>
