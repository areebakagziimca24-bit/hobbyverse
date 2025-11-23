<?php
session_start();

// ✅ HARD-CODED ADMIN CREDENTIALS
$ADMIN_EMAIL = "admin@hobbyverse.com";
$ADMIN_PASS  = "admin123";

$error = "";

// If already logged in as admin → go to dashboard
if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if ($email === $ADMIN_EMAIL && $pass === $ADMIN_PASS) {
        // ✅ Set admin session flags
        $_SESSION['is_admin']    = true;
        $_SESSION['admin_email'] = $ADMIN_EMAIL;
        $_SESSION['admin_name']  = "Administrator";

        header("Location: admin.php");
        exit;
    } else {
        $error = "❌ Invalid admin credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Hobbyverse</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body{
            margin:0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#fff7fa;
            font-family:'Poppins',sans-serif;
        }
        .box{
            background:white;
            padding:40px 35px;
            border-radius:20px;
            width:340px;
            box-shadow:0 10px 28px rgba(0,0,0,0.1);
            text-align:center;
            border:2px solid #ffe1ea;
        }
        .box h2{
            color:#ff6f91;
            font-size:26px;
            margin-bottom:10px;
        }
        .subtitle{
            font-size:13px;
            color:#777;
            margin-bottom:15px;
        }
        input{
            width:100%;
            padding:12px;
            border:1px solid #ffd1df;
            border-radius:12px;
            margin:10px 0;
            outline:none;
            font-size:14px;
        }
        button{
            width:100%;
            padding:12px;
            border:none;
            background:#ff6f91;
            color:white;
            font-size:16px;
            font-weight:600;
            border-radius:14px;
            cursor:pointer;
            margin-top:10px;
            transition:0.3s;
        }
        button:hover{
            background:#ff4d80;
        }
        .error{
            color:#e63946;
            margin-bottom:10px;
            font-weight:600;
            font-size:13px;
        }
        .back-link{
            margin-top:12px;
            font-size:13px;
        }
        .back-link a{
            color:#ff6f91;
            text-decoration:none;
            font-weight:600;
        }
        .back-link a:hover{
            text-decoration:underline;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🔐 Admin Login</h2>
    <div class="subtitle">Hobbyverse control panel access</div>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="back-link">
        <a href="index.php">← Back to site</a>
    </div>
</div>

</body>
</html>
