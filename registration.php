<?php
session_start();
include 'db_connect.php';

$message = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Clean inputs
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);
    $role     = "user"; // Force user role (secure!)

    // ---------------- VALIDATION ----------------

    if ($name === "" || $email === "" || $phone === "" || $password === "" || $confirm === "") {
        $message = "❌ All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Enter a valid email address.";
    }
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "❌ Phone number must be 10 digits.";
    }
    elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    }
    elseif ($password !== $confirm) {
        $message = "❌ Passwords do not match.";
    }
    else {
        // Check if email already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "❌ Email already registered.";
        } else {
            // Save user
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, phone, password, role)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $name, $email, $phone, $hashed, $role);

            if ($stmt->execute()) {
                $message = "✅ Registration successful! You can now login.";
            } else {
                $message = "❌ Something went wrong. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - Hobbyverse</title>
  <style>
    body {
      font-family:'Poppins', sans-serif;
      background:#f9f9f9;
      display:flex;
      justify-content:center;
      align-items:center;
      height:100vh;
      margin:0;
    }
    .box {
      background:#fff;
      padding:40px;
      border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,0.1);
      width:320px;
      text-align:center;
    }
    input {
      width:100%;
      padding:12px;
      margin:10px 0;
      border:1px solid #ccc;
      border-radius:8px;
    }
    button {
      width:100%;
      padding:12px;
      background:#ff4c8b;
      color:#fff;
      border:none;
      border-radius:8px;
      cursor:pointer;
    }
    button:hover { background:#e63972; }
    .msg { margin-bottom:10px; color:#d60000; font-weight:600; }
  </style>
</head>
<body>
  <div class="box">
    <h2>📝 Register</h2>

    <?php if ($message): ?>
        <p class='msg'><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>

      <input type="email" name="email" placeholder="Email" required>

      <input type="text" name="phone" placeholder="Phone Number (10 digits)" required>

      <input type="password" name="password" placeholder="Password" required>

      <input type="password" name="confirm_password" placeholder="Confirm Password" required>

      <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
