<?php
require __DIR__.'/config/db.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Параметры для простого пагина и поиска
$limit  = max(1, min(100, (int)($_GET['limit']  ?? 20)));
$offset = max(0, (int)($_GET['offset'] ?? 0));
$q      = trim((string)($_GET['q'] ?? ''));

// Общая инфа
$version = $pdo->query('SELECT VERSION()')->fetchColumn();
$dbName  = $pdo->query('SELECT DATABASE()')->fetchColumn();
$dbUser  = $pdo->query('SELECT CURRENT_USER()')->fetchColumn();

// Счётчики (аккуратно: если таблицы ещё не созданы — ловим ошибку)
$usersCount = $carsCount = null;
try { $usersCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); } catch (Throwable $e) {}
try { $carsCount  = (int)$pdo->query('SELECT COUNT(*) FROM cars')->fetchColumn(); }  catch (Throwable $e) {}

// --- Пользователи
$users = [];
if ($usersCount !== null) {
    if ($q !== '') {
        $stmt = $pdo->prepare("
            SELECT id, email, full_name, role, password_hash, created_at
            FROM users
            WHERE email LIKE :q OR full_name LIKE :q
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");
        $like = "%$q%";
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT id, email, full_name, role, password_hash, created_at
            FROM users
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $users = $stmt->fetchAll();
}

// --- Авто
$cars = [];
if ($carsCount !== null) {
    if ($q !== '') {
        $stmt = $pdo->prepare("
            SELECT id, make, model, transmission, fuel, seats, daily_price, image_url, created_at
            FROM cars
            WHERE make LIKE :q OR model LIKE :q
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");
        $like = "%$q%";
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT id, make, model, transmission, fuel, seats, daily_price, image_url, created_at
            FROM cars
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $cars = $stmt->fetchAll();
}

// Маскирование хеша (покажем куски начала/конца, середину скроем)
function mask_hash($hash) {
    $hash = (string)$hash;
    $len = strlen($hash);
    if ($len <= 12) return $hash; // короткие не трогаем (на всякий)
    return substr($hash, 0, 10) . '…' . substr($hash, -8);
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>RideNow — тест БД</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/style.css">
  <style>
    .dbpage{ width:min(var(--container), 100% - 48px); margin:24px auto 64px; }
    .db-head{ display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
    .db-badge{ background:#EEF0FF; color:var(--accent); font-weight:700; padding:6px 10px; border-radius:999px; }
    .db-panel{ background:#fff; border:1px solid #E3E6EF; border-radius:16px; box-shadow:var(--shadow); padding:16px; margin-top:16px; }
    .db-grid{ overflow:auto; }
    table.db{ border-collapse:collapse; width:100%; min-width:720px; }
    .db th, .db td{ padding:10px 12px; border-bottom:1px solid #ECEFF6; text-align:left; font-size:14px; }
    .db th{ color:var(--primary); background:#FAFAFE; position:sticky; top:0; }
    .db img.thumb{ width:68px; height:48px; object-fit:cover; border-radius:10px; box-shadow:0 4px 14px rgba(0,0,0,.08); }
    .db-actions{ display:flex; gap:8px; align-items:center; }
    .db-form{ display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .db-form input[type="text"], .db-form input[type="number"]{
      height:38px; border:1px solid #E3E6EF; border-radius:10px; padding:0 10px; outline:none;
    }
    .db-form input:focus{ border-color:#B8BDF2; box-shadow:0 0 0 4px rgba(43,42,134,.1); }
    .btn{ background:var(--bg); color:#fff; border-radius:10px; padding:10px 14px; font-weight:700; border:0; cursor:pointer; }
    .muted{ color:var(--muted); }
    .section-title{ margin:24px 0 8px; }
    .sep{ height:1px; background:#ECEFF6; margin:20px 0; }
    .nowrap{ white-space:nowrap; }
  </style>
</head>
<body>

<header class="header">
  <div class="header-container">
    <div class="logo"><a href="/">RideNow</a></div>
    <nav class="nav">
      <a class="navlink" href="/">Главная</a>
      <a class="navlink" href="/scripts/fleet.php">Автопарк</a>
      <a class="navlink" href="/auth/login.php">Вход</a>
    </nav>
  </div>
</header>

<main class="dbpage">
  <h1 class="section-title">Состояние базы данных</h1>
  <div class="db-panel db-head">
    <span class="db-badge">MySQL: <?= h($version) ?></span>
    <span class="db-badge">База: <?= h($dbName ?: '(не выбрана)') ?></span>
    <span class="db-badge">Пользователь БД: <?= h($dbUser) ?></span>
    <?php if ($usersCount !== null): ?>
      <span class="db-badge">Пользователей: <?= (int)$usersCount ?></span>
    <?php endif; ?>
    <?php if ($carsCount !== null): ?>
      <span class="db-badge">Авто: <?= (int)$carsCount ?></span>
    <?php endif; ?>
  </div>

  <form class="db-form" method="get">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Поиск: email / ФИО / марка / модель" style="min-width:260px">
    <input type="number" name="limit" value="<?= (int)$limit ?>" min="1" max="100" title="limit">
    <input type="number" name="offset" value="<?= (int)$offset ?>" min="0" title="offset">
    <button class="btn" type="submit">Применить</button>
    <a class="navlink" href="?">Сбросить</a>
  </form>

  <div class="sep"></div>

  <h2 class="section-title">Пользователи</h2>
  <?php if ($usersCount === null): ?>
    <div class="muted">Таблица <code>users</code> ещё не создана.</div>
  <?php elseif (!$users): ?>
    <div class="muted">Записей нет.</div>
  <?php else: ?>
    <div class="db-grid">
      <table class="db">
        <thead>
          <tr>
            <th class="nowrap">ID</th>
            <th>Email</th>
            <th>ФИО</th>
            <th>Роль</th>
            <th>Хеш пароля</th>
            <th class="nowrap">Создан</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="nowrap"><?= (int)$u['id'] ?></td>
              <td><?= h($u['email']) ?></td>
              <td><?= h($u['full_name']) ?></td>
              <td class="nowrap"><?= h($u['role']) ?></td>
              <td class="nowrap"><code><?= h(mask_hash($u['password_hash'])) ?></code></td>
              <td class="nowrap"><?= h($u['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="sep"></div>

  <h2 class="section-title">Автомобили</h2>
  <?php if ($carsCount === null): ?>
    <div class="muted">Таблица <code>cars</code> ещё не создана.</div>
  <?php elseif (!$cars): ?>
    <div class="muted">Записей нет.</div>
  <?php else: ?>
    <div class="db-grid">
      <table class="db">
        <thead>
          <tr>
            <th class="nowrap">ID</th>
            <th>Марка</th>
            <th>Модель</th>
            <th class="nowrap">КПП</th>
            <th class="nowrap">Топливо</th>
            <th class="nowrap">Места</th>
            <th class="nowrap">Цена/сутки</th>
            <th>Фото</th>
            <th class="nowrap">Добавлен</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cars as $c): ?>
            <tr>
              <td class="nowrap"><?= (int)$c['id'] ?></td>
              <td><?= h($c['make']) ?></td>
              <td><?= h($c['model']) ?></td>
              <td class="nowrap"><?= h($c['transmission']) ?></td>
              <td class="nowrap"><?= h($c['fuel']) ?></td>
              <td class="nowrap"><?= (int)$c['seats'] ?></td>
              <td class="nowrap"><?= number_format((float)$c['daily_price'], 2, '.', ' ') ?> р</td>
              <td>
                <?php if (!empty($c['image_url'])): ?>
                  <img class="thumb" src="<?= h($c['image_url']) ?>" alt="">
                <?php endif; ?>
              </td>
              <td class="nowrap"><?= h($c['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</main>

<footer class="footer">
  <p>&copy; 2025 RideNow. Все права защищены.</p>
</footer>
</body>
</html>
