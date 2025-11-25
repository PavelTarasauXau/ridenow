<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/guard.php';

// Читаем параметры из GET
$q    = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? '';

// Базовый SQL
$sql = "SELECT id, make, model, transmission, fuel, seats, daily_price, image_url
        FROM cars
        WHERE 1";
$params = [];

// Если есть строка поиска — фильтруем по марке или модели
if ($q !== '') {
  $sql .= " AND (
      make LIKE :q1
      OR model LIKE :q2
      OR CONCAT(make, ' ', model) LIKE :q3
  )";

  $like = '%'.$q.'%';
  $params[':q1'] = $like;
  $params[':q2'] = $like;
  $params[':q3'] = $like;
}



// Сортировка по цене
if ($sort === 'price_asc') {
    $sql .= " ORDER BY daily_price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY daily_price DESC";
} else {
    // сортировка по умолчанию (как было раньше)
    $sql .= " ORDER BY id DESC";
}

// можно ограничить, чтобы не выводить тонну
$sql .= " LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Наш автопарк</title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>

<section class="fleet-booking">
  <div class="fb-grid">
    <div class="fb-photo">
      <img src="/pics/office.png" alt="">
    </div>

    <form id="bookingForm" class="booking-form booking-form-fleet" action="/api/book.php" method="post">
      <legend><p class="booking-slogan">Выбери, забронируй, поезжай<br>Без очереди и бумаг</p></legend>
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf()) ?>">
      <input type="hidden" name="car_id" id="bf_car_id">

      <div class="form-group">
        <label>Откуда</label>
        <input type="text" name="place" placeholder="Введите место" required>
      </div>
      <div class="form-group">
        <label>Дата и время начала</label>
        <input type="datetime-local" name="start" required>
      </div>
      <div class="form-group">
        <label>Дата и время окончания</label>
        <input type="datetime-local" name="end" required>
      </div>

      <button type="submit" id="bf_submit" class="findauto-btn">Забронировать</button>
      <div id="bf_hint" class="muted" style="margin-top:6px;"></div>
      <div id="bf_msg" class="muted" style="margin-top:6px;"></div>
    </form>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!--<script>
  const startPicker = flatpickr("#start", {
    enableTime: true,
    dateFormat: "d.m.Y H:i",
    minDate: "today",
    time_24hr: true,
    onChange: ([date]) => endPicker.set('minDate', date || "today")
  });
  const endPicker = flatpickr("#end", {
    enableTime: true,
    dateFormat: "d.m.Y H:i",
    minDate: "today",
    time_24hr: true
  });
</script>-->

<!--added-->
<section class="fleet section">
  <h2>Наш автопарк</h2>
  <p class="fleet-subtitle">Популярные автомобили</p>

  <!-- ФИЛЬТРЫ -->
  <form class="fleet-filters" method="get">
    <input
      type="text"
      name="q"
      placeholder="Поиск по марке или модели..."
      value="<?= htmlspecialchars($q) ?>"
    >

    <select name="sort">
      <option value="">Сортировка по цене</option>
      <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Сначала дешёвые</option>
      <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Сначала дорогие</option>
    </select>

    <button type="submit" class="findauto-btn">Применить</button>
    <button type="button" class="viewall-btn" onclick="window.location='/pages/fleet.php'">Показать все</button>

  </form>

  <div class="cars-grid">
    <?php foreach ($cars as $c): 
      $img = $c['image_url'] ?: '/pics/kia_rio_4.jpg';
    ?>
      <div class="car-card">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($c['make'].' '.$c['model']) ?>">
        <h3><?= htmlspecialchars($c['make'].' '.$c['model']) ?></h3>
        <div class="car-details">
          <span><?= htmlspecialchars($c['transmission']) ?></span>
          <span><?= htmlspecialchars($c['fuel']) ?></span>
          <span><?= (int)$c['seats'] ?> мест</span>
        </div>
        <p class="car-price"><?= htmlspecialchars($c['daily_price']) ?> р/сутки</p>
        <button class="rent-btn bf-select"
                data-car="<?= (int)$c['id'] ?>"
                data-price="<?= htmlspecialchars($c['daily_price']) ?>">
          Забронировать
        </button>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Это наверно надо будет убрать
  <div class="cars-grid">
    <?php
    // простая витрина из таблицы cars
    $cars = $pdo->query("SELECT id, make, model, transmission, fuel, seats, daily_price, image_url
                         FROM cars ORDER BY id DESC LIMIT 12")->fetchAll();
    foreach ($cars as $c):
      $img = $c['image_url'] ?: '/pics/kia_rio_4.jpg';
    ?>
    <div class="car-card">
      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($c['make'].' '.$c['model']) ?>">
      <h3><?= htmlspecialchars($c['make'].' '.$c['model']) ?></h3>
      <div class="car-details">
        <span><?= htmlspecialchars($c['transmission']) ?></span>
        <span><?= htmlspecialchars($c['fuel']) ?></span>
        <span><?= (int)$c['seats'] ?> мест</span>
      </div>
      <p class="car-price"><?= htmlspecialchars($c['daily_price']) ?> р/сутки</p>
      <button class="rent-btn bf-select"
              data-car="<?= (int)$c['id'] ?>"
              data-price="<?= htmlspecialchars($c['daily_price']) ?>">
        Забронировать
      </button>
    </div>
    <?php endforeach; ?>
  </div>
</section>
    -->
<?php require __DIR__ . '/../includes/footer.php'; ?>
<script type="module">
  import { initBooking } from '/js/booking.js';
  initBooking();
</script>
</body>
</html>
