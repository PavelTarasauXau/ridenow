<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/guard.php';

// -------- GET параметры --------
$q    = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? '';

$place = trim($_GET['place'] ?? '');
$start = trim($_GET['start'] ?? '');
$end   = trim($_GET['end'] ?? '');

// index.php отдаёт d.m.Y H:i, а fleet форма отдаёт datetime-local (Y-m-d\TH:i)
function parse_dt(string $s): ?DateTime {
  $s = trim($s);
  if ($s === '') return null;

  // 1) d.m.Y H:i (с index.php)
  $dt = DateTime::createFromFormat('d.m.Y H:i', $s);
  if ($dt instanceof DateTime) return $dt;

  // 2) datetime-local: 2025-12-20T20:00
  $dt = DateTime::createFromFormat('Y-m-d\TH:i', $s);
  if ($dt instanceof DateTime) return $dt;

  // 3) fallback
  $dt = date_create($s);
  return ($dt instanceof DateTime) ? $dt : null;
}

$start_dt = parse_dt($start);
$end_dt   = parse_dt($end);
$hasRange = ($start_dt && $end_dt && $start_dt < $end_dt);

// -------- SQL --------
$sql = "SELECT id, make, model, transmission, fuel, seats, daily_price, image_url
        FROM cars
        WHERE 1";
$params = [];

// поиск
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

// только свободные на интервал
if ($hasRange) {
  $sql .= " AND NOT EXISTS (
      SELECT 1 FROM bookings b
      WHERE b.car_id = cars.id
        AND b.status IN ('pending','approved')
        AND NOT (b.end_at <= :s OR b.start_at >= :e)
  )";
  $params[':s'] = $start_dt->format('Y-m-d H:i:s');
  $params[':e'] = $end_dt->format('Y-m-d H:i:s');
}

// сортировка
if ($sort === 'price_asc') {
  $sql .= " ORDER BY daily_price ASC";
} elseif ($sort === 'price_desc') {
  $sql .= " ORDER BY daily_price DESC";
} else {
  $sql .= " ORDER BY id DESC";
}

$sql .= " LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

// значения для заполнения datetime-local
$start_local = $hasRange ? $start_dt->format('Y-m-d\TH:i') : '';
$end_local   = $hasRange ? $end_dt->format('Y-m-d\TH:i')   : '';
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
        <input type="text" name="place" placeholder="Введите место" required
               value="<?= htmlspecialchars($place) ?>">
      </div>

      <div class="form-group">
        <label>Дата и время начала</label>
        <input type="datetime-local" name="start" required
               value="<?= htmlspecialchars($start_local) ?>">
      </div>

      <div class="form-group">
        <label>Дата и время окончания</label>
        <input type="datetime-local" name="end" required
               value="<?= htmlspecialchars($end_local) ?>">
      </div>

      <button type="submit" id="bf_submit" class="findauto-btn">Забронировать</button>
      <div id="bf_hint" class="muted" style="margin-top:6px;"></div>
      <div id="bf_msg" class="muted" style="margin-top:6px;"></div>
    </form>
  </div>
</section>

<section class="fleet section">
  <h2>Наш автопарк</h2>
  <p class="fleet-subtitle">
    <?= $hasRange ? 'Доступные автомобили на выбранное время' : 'Популярные автомобили' ?>
  </p>

  <!-- фильтры: сохраняем place/start/end, чтобы при сортировке не потерялись даты -->
  <form class="fleet-filters" method="get">
    <input type="hidden" name="place" value="<?= htmlspecialchars($place) ?>">
    <input type="hidden" name="start" value="<?= htmlspecialchars($start) ?>">
    <input type="hidden" name="end"   value="<?= htmlspecialchars($end) ?>">

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

  <?php if ($hasRange && empty($cars)): ?>
    <div class="container">
      <div class="contracts-empty">
        <div class="contracts-empty-title">Нет доступных автомобилей</div>
        <div class="contracts-empty-text">Попробуйте выбрать другой интервал или снять фильтры.</div>
      </div>
    </div>
  <?php endif; ?>

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

<?php require __DIR__ . '/../includes/footer.php'; ?>
<script type="module">
  import { initBooking } from '/js/booking.js';
  initBooking();
</script>
</body>
</html>
