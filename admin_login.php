<?php
session_start();


if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin.php");
    exit;
}

// Handle login form
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $admin_user = "admin";
    $admin_pass = "12345"; 

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['is_admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "❌ Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Login - Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family:'Poppins', sans-serif; background:#f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh; }
    .login-box { background:#fff; padding:40px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:300px; text-align:center; }
    h2 { margin-bottom:20px; }
    input { width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:8px; }
    button { width:100%; padding:12px; background:#ff4c8b; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    button:hover { background:#e63972; }
    .error { color:red; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>🔐 Admin Login</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
