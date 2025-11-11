<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

/* CSRF */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$errors = [];
$email = '';
$full_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $errors[] = 'Сессия истекла. Обновите страницу.';
  }

  $email = trim($_POST['email'] ?? '');
  $full_name = trim($_POST['full_name'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';

  // Валидация
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Введите корректный email';
  }
  if (mb_strlen($full_name) < 2) {
    $errors[] = 'Имя должно содержать минимум 2 символа';
  }
  if (mb_strlen($password) < 6) {
    $errors[] = 'Пароль должен содержать минимум 6 символов';
  }
  if ($password !== $password_confirm) {
    $errors[] = 'Пароли не совпадают';
  }

  if (!$errors) {
    // проверяем, что email свободен
    $exists = (int)$pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?")->execute([$email]) ?: 0;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ((int)$stmt->fetchColumn() > 0) {
      $errors[] = 'Email уже зарегистрирован';
    } else {
      // создаём пользователя
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $ins = $pdo->prepare("INSERT INTO users (email, full_name, password_hash, role) VALUES (?, ?, ?, 'user')");
      $ins->execute([$email, $full_name, $hash]);

      // авторизуем сразу после регистрации
      $uid = (int)$pdo->lastInsertId();
      $_SESSION['user'] = [
        'id'        => $uid,
        'email'     => $email,
        'full_name' => $full_name,
        'role'      => 'user',
      ];

      header('Location: /index.php');
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RideNow — регистрация</title>
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
        <a href="/auth/login.php" class="navlink">Войти</a>
      </nav>
    </div>
  </header>

  <main class="auth-wrap">
    <form class="auth-card" method="post" autocomplete="off">
      <h1 class="auth-title">Регистрация</h1>
      <p class="auth-desc">Создайте аккаунт для аренды автомобилей</p>

      <?php if ($errors): ?>
        <div class="auth-error">
          <?php foreach ($errors as $e) echo "<div>• ".htmlspecialchars($e)."</div>"; ?>
        </div>
      <?php endif; ?>

      <div class="field">
        <label class="visually-hidden" for="full_name">Полное имя</label>
        <input id="full_name" name="full_name" type="text" placeholder="Полное имя" value="<?= htmlspecialchars($full_name) ?>" required>
        <img class="icon" src="/pics/people.png" alt="">
      </div>

      <div class="field">
        <label class="visually-hidden" for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
        <img class="icon" src="/pics/people.png" alt="">
      </div>

      <div class="field">
        <label class="visually-hidden" for="password">Пароль</label>
        <input id="password" name="password" type="password" placeholder="Пароль (минимум 6 символов)" required minlength="6">
        <img class="icon" src="/pics/unlock.png" alt="">
      </div>

      <div class="field">
        <label class="visually-hidden" for="password_confirm">Подтверждение пароля</label>
        <input id="password_confirm" name="password_confirm" type="password" placeholder="Подтвердите пароль" required minlength="6">
        <img class="icon" src="/pics/unlock.png" alt="">
      </div>

      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

      <button class="auth-submit" type="submit">Зарегистрироваться</button>

      <div class="auth-register">
        Уже есть аккаунт? <a href="/auth/login.php">Войти</a>
      </div>
    </form>
  </main>

  <footer class="footer">
    <p>&copy; 2025 RideNow. Все права защищены.</p>
  </footer>
</body>
</html>
