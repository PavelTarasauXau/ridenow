<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

/* CSRF */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $errors[] = 'Сессия истекла. Обновите страницу.';
  }

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = !empty($_POST['remember']);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email';
  if ($password === '') $errors[] = 'Введите пароль';

  if (!$errors) {
    // ищем пользователя по email
    $stmt = $pdo->prepare("SELECT id, email, full_name, password_hash, role FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
      // логин успешен
      $_SESSION['user'] = [
        'id'        => (int)$user['id'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'] ?? 'user',
      ];

      // простая «запомнить меня» (оставь как есть или убери — без БД-токена это не безопасно)
      if ($remember) {
        setcookie('ridenow_remember', $user['email'], [
          'expires'  => time() + 60*60*24*30,
          'path'     => '/',
          'httponly' => true,
          'samesite' => 'Lax',
        ]);
      }

      header('Location: /index.php'); exit;
    } else {
      $errors[] = 'Неверный email или пароль';
    }
  }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RideNow — вход</title>
  <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].'/css/style.css') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <header class="header">
    <div class="header-container">
      <div class="logo"><a href="/">RideNow</a></div>
      <nav class="nav">
        <a href="/" class="navlink">Главная</a>
        <a href="/fleet.php" class="navlink">Автопарк</a>
        <a href="/auth/register.php" class="navlink">Регистрация</a>
      </nav>
    </div>
  </header>

  <main class="auth-wrap">
    <form class="auth-card" method="post" autocomplete="off">
      <h1 class="auth-title">Войти</h1>
      <p class="auth-desc">Добро пожаловать в RideNow</p>

      <?php if ($errors): ?>
        <div class="auth-error">
          <?php foreach ($errors as $e) echo "<div>• ".htmlspecialchars($e)."</div>"; ?>
        </div>
      <?php endif; ?>

      <div class="field">
        <label class="visually-hidden" for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
        <img class="icon" src="/pics/people.png" alt="">
      </div>

      <div class="field">
        <label class="visually-hidden" for="password">Пароль</label>
        <input id="password" name="password" type="password" placeholder="Пароль" required>
        <img class="icon" src="/pics/unlock.png" alt="">
      </div>

      <div class="auth-actions">
        <label class="auth-remember">
          <input type="checkbox" name="remember"> Запомнить меня
        </label>
        <a class="auth-link" href="#">Забыли пароль?</a>
      </div>

      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

      <button class="auth-submit" type="submit">Войти</button>

      <div class="auth-register">
        Нет аккаунта? <a href="/auth/register.php">Зарегистрироваться</a>
      </div>
    </form>
  </main>

  <footer class="footer">
    <p>&copy; 2025 RideNow. Все права защищены.</p>
  </footer>
</body>
</html>
