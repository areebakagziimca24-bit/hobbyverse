<?php
session_start();
include 'db_connect.php';

// 🔐 Require login BEFORE any HTML
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=account.php");
    exit;
}

$uid = (int)$_SESSION['user_id'];

$success = '';
$error   = '';

// Fetch user info
$userRes = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $uid");
$user    = mysqli_fetch_assoc($userRes);

if (!$user) {
    die("User not found.");
}

// -------------------- HANDLE PROFILE UPDATE --------------------
if ($_SERVER['REQUEST_METHOD'] === "POST") {

   $name    = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
$email   = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));   // <-- ADD THIS
$phone   = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$city    = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));


    // --- Validations ---
    if ($name === "") {
        $error = "Username cannot be empty.";
    }
  

    elseif ($phone !== "" && !preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be 10 digits.";
    }
    else {
        // 👉 Ensure users table has phone, city, address fields

        $sql = "
          UPDATE users SET 
    username = '$name',
    phone    = '$phone',
    city     = '$city',
    address  = '$address'
WHERE user_id = $uid

        ";

        if (mysqli_query($conn, $sql)) {
            $success          = "Profile updated successfully!";
            $_SESSION['name'] = $name;

            // Refresh user data after update
            $userRes = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $uid");
            $user    = mysqli_fetch_assoc($userRes);
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// 🧾 Get order history
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$uid ORDER BY order_id DESC");

// include header AFTER all redirects
include 'header.php';
?>

<style>
.account-page{
  max-width:1100px;
  margin:30px auto 60px;
  padding:0 5px;
}
.account-grid{
  display:grid;
  grid-template-columns:minmax(0,2fr) minmax(0,1.5fr);
  gap:24px;
  margin-bottom:24px;
}
@media(max-width:900px){
  .account-grid{grid-template-columns:1fr;}
}
.card{
  background:#fff;
  border-radius:20px;
  padding:24px;
  box-shadow:0 10px 25px rgba(0,0,0,0.08);
}
.card-header{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:10px;
}
.card-header h2{
  margin:0;
  color:#ff4c8b;
}
.pill{
  display:inline-block;
  padding:3px 10px;
  border-radius:999px;
  font-size:12px;
  background:#ffe3ee;
  color:#ff4c8b;
}
.form-group{margin-bottom:12px;}
.form-group label{
  display:block;
  font-size:13px;
  margin-bottom:4px;
  font-weight:600;
}
.input, .textarea{
  width:100%;
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #e3e3e3;
  font-family:'Poppins',sans-serif;
  font-size:14px;
}
.textarea{min-height:80px;resize:vertical;}
.btn-primary{
  padding:10px 24px;
  border:none;
  border-radius:999px;
  background:#ff4c8b;
  color:#fff;
  font-weight:600;
  cursor:pointer;
  margin-top:6px;
}
.btn-primary:hover{background:#e63b7b;}
.alert{
  padding:10px 12px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:12px;
}
.alert-success{background:#d4ffe6;color:#0a8a4a;}
.alert-error{background:#ffe3e3;color:#c0392b;}

.orders-table{
  width:100%;
  border-collapse:collapse;
  font-size:14px;
}
.orders-table th,
.orders-table td{
  padding:10px 8px;
  border-bottom:1px solid #f0f0f0;
  text-align:left;
}
.orders-table th{
  background:#fff0f6;
  color:#ff4c8b;
  font-weight:600;
}

.badge-status{
  display:inline-block;
  padding:3px 9px;
  border-radius:999px;
  font-size:12px;
}
.badge-status.pending{background:#fff3cd;color:#856404;}
.badge-status.completed{background:#d4edda;color:#155724;}
.badge-status.cancelled{background:#f8d7da;color:#721c24;}

.quick-links .btn-primary{
  margin-right:10px;
  margin-top:10px;
}
.btn-secondary{
  padding:8px 16px;
  border-radius:999px;
  border:1px solid #ccc;
  background:#fff;
  font-size:13px;
  cursor:pointer;
}
.btn-secondary:hover{border-color:#ff4c8b;color:#ff4c8b;}

a.link{
  color:#ff4c8b;
  text-decoration:none;
  font-weight:600;
}
a.link:hover{text-decoration:underline;}
</style>

<div class="account-page" data-aos="fade-up">

  <div class="account-grid">

    <!-- 👤 Profile -->
    <div class="card" data-aos="fade-right">
      <div class="card-header">
        <span style="font-size:26px">👤</span>
        <div>
          <h2>My Profile</h2>
          <span class="pill">Manage your account details</span>
        </div>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="input"
                 value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
        </div>


        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" class="input"
                 value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                 placeholder="10-digit phone number">
        </div>

        <div class="form-group">
          <label>City</label>
          <input type="text" name="city" class="input"
                 value="<?= htmlspecialchars($user['city'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Address</label>
          <textarea name="address" class="textarea"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn-primary">Update Profile</button>
      </form>
    </div>

    <!-- 📦 Order history -->
    <div class="card" data-aos="fade-left">
      <div class="card-header">
        <span style="font-size:24px">📦</span>
        <div>
          <h2>Order History</h2>
          <span class="pill">Recent orders placed on Hobbyverse</span>
        </div>
      </div>

      <?php if (!$orders || mysqli_num_rows($orders) == 0): ?>
        <p style="font-size:14px;color:#777;margin-top:8px;">
          You don't have any orders yet.
          <a href="products.php" class="link">Start shopping →</a>
        </p>
      <?php else: ?>
        <table class="orders-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php while ($o = mysqli_fetch_assoc($orders)): ?>
            <?php
              $status      = strtolower($o['status'] ?? 'pending');
              $statusClass = 'pending';
              if ($status === 'completed' || $status === 'delivered') $statusClass = 'completed';
              elseif ($status === 'cancelled') $statusClass = 'cancelled';
            ?>
            <tr>
              <td><?= (int)$o['order_id'] ?></td>
              <td><?= htmlspecialchars($o['created_at'] ?? '') ?></td>
              <td>₹<?= number_format($o['total_amount'] ?? 0, 2) ?></td>
              <td><span class="badge-status <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
              <td><a class="link" href="order_details.php?order_id=<?= (int)$o['order_id'] ?>">View</a></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  </div>

  <!-- ⭐ Quick links -->
  <div class="card quick-links" data-aos="fade-up">
    <div class="card-header">
      <span style="font-size:22px">⭐</span>
      <div>
        <h2>Quick Links</h2>
        <span class="pill">Jump back into your favourite sections</span>
      </div>
    </div>

    <button class="btn-primary" onclick="location.href='wishlist.php'">❤️ My Wishlist</button>
    <button class="btn-secondary" onclick="location.href='cart.php'">🛒 View Cart</button>
    <button class="btn-secondary" onclick="location.href='logout.php'">Logout</button>
  </div>

</div>

<?php include 'footer.php'; ?>
