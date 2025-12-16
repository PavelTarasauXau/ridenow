<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/guard.php';

header('Content-Type: application/json; charset=utf-8');

function bad(string $m, int $c = 400): void {
  http_response_code($c);
  echo json_encode(['ok' => false, 'error' => $m], JSON_UNESCAPED_UNICODE);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') bad('Method not allowed', 405);

// CSRF + auth
require_csrf_json($_POST['csrf'] ?? null);
require_auth_json();

// данные
$car_id = (int)($_POST['car_id'] ?? 0);
$place  = trim((string)($_POST['place'] ?? ''));
$start  = trim((string)($_POST['start'] ?? ''));
$end    = trim((string)($_POST['end'] ?? ''));

if ($car_id <= 0) bad('Не выбрано авто');
if ($place === '') bad('Укажите место');
if ($start === '' || $end === '') bad('Укажите даты');

$start_dt = date_create($start);
$end_dt   = date_create($end);

if (!$start_dt || !$end_dt) bad('Неверный формат дат');
if ($start_dt >= $end_dt) bad('Дата окончания должна быть позже начала');
if ($start_dt < new DateTime()) bad('Время начала уже прошло');

try {
  // авто есть?
  $s = $pdo->prepare("SELECT 1 FROM cars WHERE id = ? LIMIT 1");
  $s->execute([$car_id]);
  if (!$s->fetchColumn()) bad('Автомобиль не найден', 404);

  // занятость (пересечение интервалов)
  $s = $pdo->prepare("
    SELECT COUNT(*) FROM bookings
    WHERE car_id = :c
      AND status IN ('pending','approved')
      AND NOT (end_at <= :s OR start_at >= :e)
  ");
  $s->execute([
    ':c' => $car_id,
    ':s' => $start_dt->format('Y-m-d H:i:s'),
    ':e' => $end_dt->format('Y-m-d H:i:s'),
  ]);

  if ((int)$s->fetchColumn() > 0) bad('На выбранные даты авто занято');

  $userId = (int)($_SESSION['user']['id'] ?? 0);
  if ($userId <= 0) bad('Требуется авторизация', 401);

  $ins = $pdo->prepare("
    INSERT INTO bookings (user_id, car_id, place, start_at, end_at, status)
    VALUES (:u, :c, :p, :s, :e, 'pending')
  ");
  $ins->execute([
    ':u' => $userId,
    ':c' => $car_id,
    ':p' => $place,
    ':s' => $start_dt->format('Y-m-d H:i:s'),
    ':e' => $end_dt->format('Y-m-d H:i:s'),
  ]);

  echo json_encode(['ok' => true, 'booking_id' => (int)$pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  bad('Ошибка сервера: ' . $e->getMessage(), 500);
}
