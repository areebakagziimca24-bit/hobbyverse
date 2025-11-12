<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us | Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root{
      --accent:#ff6f91;
      --accent2:#ffc1cf;
      --bg:linear-gradient(135deg,#fffaf8 0%,#ffe7ef 100%);
      --text:#2b2b2b;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden;}
    
    /* HERO */
    .hero{
      text-align:center;
      padding:120px 20px 80px;
      background:linear-gradient(180deg,#fff5f8,#fff);
      border-radius:0 0 80px 80px;
      position:relative;
      box-shadow:0 8px 24px rgba(0,0,0,0.05);
    }
    .hero h1{
      font-family:'Baloo 2',cursive;
      font-size:60px;
      color:var(--accent);
      margin-bottom:10px;
    }
    .hero p{
      font-size:20px;
      color:#555;
      max-width:700px;
      margin:auto;
    }
    .hero img{
      width:220px;
      position:absolute;
      right:50px;
      bottom:-60px;
      opacity:0.9;
    }

    /* SECTION */
    .section{
      max-width:1100px;
      margin:80px auto;
      padding:0 30px;
      text-align:center;
    }
    .section h2{
      color:var(--accent);
      font-family:'Baloo 2';
      font-size:36px;
      margin-bottom:20px;
    }
    .section p{
      color:#555;
      font-size:17px;
      line-height:1.8;
      max-width:850px;
      margin:auto;
    }

    /* MISSION / STORY */
    .mission{
      display:grid;
      grid-template-columns:1fr 1fr;
      align-items:center;
      gap:50px;
      margin-top:60px;
    }
    .mission img{
      width:100%;
      border-radius:20px;
      box-shadow:0 8px 24px rgba(0,0,0,0.1);
    }

    /* TEAM */
    .team{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
      gap:30px;
      margin-top:50px;
    }
    .member{
      background:#fff;
      border-radius:20px;
      box-shadow:0 6px 18px rgba(0,0,0,0.08);
      padding:20px;
      transition:.3s;
    }
    .member:hover{transform:translateY(-8px) scale(1.03);}
    .member img{
      width:100%;
      height:240px;
      object-fit:cover;
      border-radius:16px;
      margin-bottom:15px;
    }
    .member h3{
      font-family:'Baloo 2';
      color:var(--accent);
      margin-bottom:5px;
    }
    .member p{color:#666;font-size:14px;}

    /* CTA */
    .cta{
      text-align:center;
      margin:100px 0;
    }
    .cta button{
      background:linear-gradient(90deg,var(--accent),var(--accent2));
      color:#fff;
      border:none;
      border-radius:30px;
      padding:15px 30px;
      font-weight:600;
      cursor:pointer;
      transition:.3s;
      box-shadow:0 6px 16px rgba(0,0,0,0.1);
    }
    .cta button:hover{transform:scale(1.08);}
  </style>
</head>
<body>

  <!-- HERO -->
  <section class="hero" data-aos="fade-down">
    <h1>About Hobbyverse 🎨</h1>
    <p>Where creativity meets community — we help you discover and grow the hobbies you love!</p>
    <img src="https://cdn-icons-png.flaticon.com/512/5303/5303003.png" alt="Art Illustration">
  </section>

  <!-- OUR STORY -->
  <section class="section" data-aos="fade-up">
    <h2>Our Story</h2>
    <div class="mission">
      <div data-aos="fade-right">
        <p>
          Hobbyverse was born from a simple belief — everyone deserves to have a creative outlet.  
          Whether it’s painting on a lazy Sunday, tending to your garden, or picking up photography,  
          we wanted to create a place where passions thrive and creativity blossoms. 🌱  
          <br><br>
          Since our beginning, we’ve been connecting enthusiasts, providing quality tools,  
          and spreading joy through every hobby we touch.  
        </p>
      </div>
      <div data-aos="fade-left">
        <img src="https://images.unsplash.com/photo-1503602642458-232111445657?auto=format&fit=crop&w=800&q=80" alt="Hobby workspace">
      </div>
    </div>
  </section>

  <!-- OUR MISSION -->
  <section class="section" data-aos="fade-up">
    <h2>Our Mission 💫</h2>
    <p>
      We aim to inspire people to try something new, explore their creativity, and feel the joy of making.  
      Hobbyverse brings together a vibrant community where inspiration and learning never stop.  
      Together, we’re crafting a happier, more creative world — one hobby at a time. ✨
    </p>
  </section>

  <!-- TEAM -->
  <section class="section" data-aos="fade-up">
    <h2>Meet the Makers 👩‍🎨</h2>
    <div class="team">
      <div class="member" data-aos="zoom-in">
        <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=800&q=80" alt="Team Member">
        <h3>Ruhi Haveliwala</h3>
        
      </div>
      <div class="member" data-aos="zoom-in">
        <img src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=800&q=80" alt="Team Member">
        <h3>Areeba Kagzi</h3>
       
      </div>
      <div class="member" data-aos="zoom-in">
        <img src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=800&q=80" alt="Team Member">
        <h3>Kamya Malhotra</h3>
        
      </div>
    </div>
  </section>

  <!-- CTA -->
  <div class="cta" data-aos="fade-up">
    <h2>Start your creative journey today 🌻</h2>
    <p style="margin:10px 0 20px;color:#666;">Browse your favorite hobbies and find something you love.</p>
    <button onclick="window.location.href='index.php'">Explore Hobbies →</button>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>AOS.init({duration:1000, once:true});</script>
</body>
</html>
<?php include 'footer.php'; ?>
