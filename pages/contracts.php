<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/guard.php';

// Требуем авторизацию (HTML-страница)
if (!isLoggedIn()) {
  header('Location: /auth/login.php');
  exit;
}

$userId = (int)$_SESSION['user']['id'];

// Достаём контракты (бронирования) пользователя + данные авто
$stmt = $pdo->prepare("
  SELECT
    b.id,
    b.place,
    b.start_at,
    b.end_at,
    b.status,
    b.created_at,
    c.make,
    c.model,
    c.transmission,
    c.fuel,
    c.seats,
    c.daily_price,
    c.image_url
  FROM bookings b
  JOIN cars c ON c.id = b.car_id
  WHERE b.user_id = :u
  ORDER BY b.created_at DESC
  LIMIT 200
");
$stmt->execute([':u' => $userId]);
$items = $stmt->fetchAll();

function status_label(string $s): string {
  return match ($s) {
    'pending'   => 'В ожидании',
    'approved'  => 'Подтверждено',
    'cancelled' => 'Отменено',
    default     => $s
  };
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RideNow — Мои контракты</title>
  <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'].'/css/style.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="contracts-page">
  <div class="container">
    <h1 class="contracts-title">Мои контракты</h1>
    <p class="contracts-subtitle">Здесь отображаются ваши бронирования (контракты) и их статус.</p>

    <?php if (!$items): ?>
      <div class="contracts-empty">
        <div class="contracts-empty-title">Пока нет контрактов</div>
        <div class="contracts-empty-text">Перейдите в автопарк и создайте бронирование.</div>
        <a class="contracts-empty-btn" href="/pages/fleet.php">Перейти в автопарк</a>
      </div>
    <?php else: ?>

      <div class="contracts-list" id="contractsList">
        <?php foreach ($items as $it):
          $img = $it['image_url'] ?: '/pics/kia_rio_4.jpg';
          $status = (string)$it['status'];
        ?>
          <article class="contract-card" data-booking-id="<?= (int)$it['id'] ?>">
            <div class="contract-photo">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($it['make'].' '.$it['model']) ?>">
            </div>

            <div class="contract-body">
              <div class="contract-top">
                <div>
                  <h3 class="contract-name"><?= htmlspecialchars($it['make'].' '.$it['model']) ?></h3>
                  <div class="contract-meta">
                    <span><?= htmlspecialchars($it['transmission']) ?></span>
                    <span><?= htmlspecialchars($it['fuel']) ?></span>
                    <span><?= (int)$it['seats'] ?> мест</span>
                    <span><?= htmlspecialchars($it['daily_price']) ?> р/сутки</span>
                  </div>
                </div>

                <div class="contract-status contract-status--<?= htmlspecialchars($status) ?>">
                  <?= htmlspecialchars(status_label($status)) ?>
                </div>
              </div>

              <div class="contract-info">
                <div class="contract-row">
                  <div class="contract-label">Откуда</div>
                  <div class="contract-value"><?= htmlspecialchars($it['place']) ?></div>
                </div>
                <div class="contract-row">
                  <div class="contract-label">Период</div>
                  <div class="contract-value">
                    <?= htmlspecialchars($it['start_at']) ?> → <?= htmlspecialchars($it['end_at']) ?>
                  </div>
                </div>
                <div class="contract-row">
                  <div class="contract-label">Создан</div>
                  <div class="contract-value"><?= htmlspecialchars($it['created_at']) ?></div>
                </div>
              </div>

              <div class="contract-actions">
                <button type="button" class="contract-btn contract-btn--pay" disabled title="Пока заглушка">
                  Оплатить
                </button>

                <button
                  type="button"
                  class="contract-btn contract-btn--cancel"
                  data-cancel
                  <?= $status === 'cancelled' ? 'disabled' : '' ?>
                >
                  Отменить бронь
                </button>

                <div class="contract-msg" data-msg></div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <input type="hidden" id="csrfToken" value="<?= htmlspecialchars(csrf()) ?>">

    <?php endif; ?>
  </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>

<script type="module">
  import { initContracts } from '/js/contracts.js';
  initContracts();
</script>
</body>
</html>
