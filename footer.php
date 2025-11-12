<?php
// footer.php - include at bottom of every page (after page content)
if (session_status() === PHP_SESSION_NONE) session_start();
?>
  </main> <!-- /.site-main -->

  <footer style="background:#222;color:#fff;padding:48px 36px;margin-top:40px">
    <div style="max-width:1200px;margin:0 auto;display:flex;flex-wrap:wrap;gap:30px;justify-content:space-between;align-items:flex-start">
      <div style="flex:1;min-width:220px">
        <h3 style="font-family: 'Baloo 2', cursive;margin:0 0 8px;color:#fff">Hobbyverse</h3>
        <p style="color:#ccc;margin:0 0 12px;max-width:320px">Curated tools and ideas for every hobby. Discover, create, share and shop.</p>
        <small style="color:#999">© <?php echo date('Y'); ?> Hobbyverse</small>
      </div>

      <div style="flex:1;min-width:180px">
        <h4 style="margin:0 0 8px;color:#fff">Explore</h4>
        <a href="products.php" style="color:#ddd;text-decoration:none;display:block;margin:6px 0">Shop</a>
        <a href="hobby.php" style="color:#ddd;text-decoration:none;display:block;margin:6px 0">Hobbies</a>
        <a href="about.php" style="color:#ddd;text-decoration:none;display:block;margin:6px 0">About</a>
      </div>

      <div style="flex:1;min-width:240px">
        <h4 style="margin:0 0 8px;color:#fff">Newsletter</h4>
        <form id="footer-news" onsubmit="event.preventDefault(); alert('Thanks — demo subscribe!')">
          <input type="email" placeholder="Your email" required style="padding:10px;width:100%;border-radius:8px;border:none;margin-bottom:8px">
          <button type="submit" style="padding:10px 14px;border-radius:8px;border:none;background:var(--accent);color:white;cursor:pointer">Join</button>
        </form>
      </div>
    </div>
  </footer>

  <!-- Scroll to top -->
  <button id="scrollTop" aria-label="Scroll to top" style="position:fixed;right:24px;bottom:24px;width:44px;height:44px;border-radius:50%;border:none;background:var(--accent);color:#fff;font-size:18px;display:none;box-shadow:0 8px 20px rgba(0,0,0,.12);cursor:pointer">↑</button>

  <!-- Scripts -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 900, once: true });

    // scroll top
    const st = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) st.style.display = 'block'; else st.style.display = 'none';
    });
    st.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // small accessibility: close mobile menus or capture ESC
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { /* future: close modals if any */ } });
  </script>
</body>
</html>
