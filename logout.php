<?php
session_start();

// ✅ Destroy all session data (logout)
session_unset();
session_destroy();

// Optional: Delay redirect (2 seconds)
header("refresh:2; url=index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out | Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root{
      --accent:#ff6f91;
      --accent2:#ffc1cf;
      --bg:linear-gradient(135deg,#fff5f8 0%,#ffe7ef 100%);
    }
    body{
      font-family:'Poppins',sans-serif;
      background:var(--bg);
      color:#333;
      display:flex;
      justify-content:center;
      align-items:center;
      height:100vh;
      flex-direction:column;
      text-align:center;
      overflow:hidden;
    }
    h1{
      font-size:42px;
      font-weight:700;
      color:var(--accent);
      animation:fadeIn 1s ease;
    }
    p{
      font-size:18px;
      color:#555;
      margin-top:10px;
      animation:fadeInUp 1.3s ease;
    }
    .loader{
      margin-top:30px;
      width:50px;
      height:50px;
      border:5px solid #ffe3eb;
      border-top:5px solid var(--accent);
      border-radius:50%;
      animation:spin 1s linear infinite;
    }
    @keyframes spin{100%{transform:rotate(360deg);}}
    @keyframes fadeIn{from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
  </style>
</head>
<body data-aos="fade-in">

  <h1>Logging you out... 🌙</h1>
  <p>We hope you had a creative time at Hobbyverse 💖<br>You’ll be redirected to the homepage shortly.</p>
  <div class="loader"></div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>AOS.init({duration:800, once:true});</script>

</body>
</html>
