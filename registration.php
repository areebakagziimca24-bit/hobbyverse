<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $role = $_POST['role']; // 'user' or 'admin'

    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        $message = "✅ Registration successful! You can now login.";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - Hobbyverse</title>
  <style>
    body { font-family:'Poppins', sans-serif; background:#f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh; }
    .box { background:#fff; padding:40px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:320px; text-align:center; }
    input, select { width:100%; padding:12px; margin:10px 0; border:1px solid #ccc; border-radius:8px; }
    button { width:100%; padding:12px; background:#ff4c8b; color:#fff; border:none; border-radius:8px; cursor:pointer; }
    button:hover { background:#e63972; }
    .msg { margin-bottom:10px; color:green; }
  </style>
</head>
<body>
  <div class="box">
    <h2>📝 Register</h2>
    <?php if($message) echo "<p class='msg'>$message</p>"; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
