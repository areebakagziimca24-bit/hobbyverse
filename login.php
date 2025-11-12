<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // ✅ Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "❌ Invalid password!";
        }
    } else {
        $error = "❌ No account found with that email!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login - Hobbyverse</title>
  <style>
    body { font-family:'Poppins', sans-serif; background:#f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh; }
    .box { background:#fff; padding:40px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:320px; text-align:center; }
    input { width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:8px; }
    button { width:100%; padding:12px; background:#ff4c8b; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    button:hover { background:#e63972; }
    .error { color:red; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="box">
    <h2>🔑 Login</h2>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="registration.php">Register</a></p>
  </div>
</body>
</html>
