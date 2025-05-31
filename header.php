
<?php
// header.php
?>
<link rel="icon" href="/images/favicon.ico" type="image/x-icon" />
<header class="site-header">
  
  <div class="logo-container">
    <img src="images/logo.jpg" alt="Site Logo" class="logo" />
    <h1 class="site-title">Damour Municipality</h1>
    
  </div>
  <nav class="site-nav">
  <a href="index.php" class="btn">Home</a>
  <a href="dashboard.php" class="btn">Dashboard</a>

  <a href="projects.php" class="btn">Projects</a>

  <a href="logout.php" class="btn">Logout</a>
 

</nav>

<script>
  <?php if ($_SESSION['user_role'] === 'admin'): ?>
  <a href="delete.php?id=<?= $row['id'] ?>">🗑 Delete</a>
<?php endif; ?>
  requireRole(['admin']);
  window.addEventListener('scroll', () => {
    const header = document.querySelector('.site-header');
    if(window.scrollY > 50) {
      header.classList.add('shrink');
    } else {
      header.classList.remove('shrink');
    }
  });
  function requireRole($roles = []) {
    session_start();
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles)) {
        // Redirect to login or unauthorized page
        header("Location: unauthorized.php");
        exit;
    }
}
</script>

</header>
