<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="header">
  <div class="header-container">
    <div class="logo"><a href="/">RideNow</a></div>
    <nav class="nav">
      <a href="/" class="navlink">Главная</a>
      <a href="/pages/fleet.php" class="navlink">Автопарк</a>
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="/auth/logout.php" class="navlink">
          Выйти (<?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'user') ?>)
        </a>
      <?php else: ?>
        <a href="/auth/login.php" class="navlink">Войти</a>
        <a href="/auth/register.php" class="navlink">Регистрация</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
