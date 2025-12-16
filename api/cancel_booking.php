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

require_csrf_json($_POST['csrf'] ?? null);
require_auth_json();

$bookingId = (int)($_POST['booking_id'] ?? 0);
if ($bookingId <= 0) bad('Некорректный booking_id');

$userId = (int)$_SESSION['user']['id'];

try {
  // Проверяем, что бронь принадлежит пользователю и не отменена
  $s = $pdo->prepare("
    SELECT status
    FROM bookings
    WHERE id = :id AND user_id = :u
    LIMIT 1
  ");
  $s->execute([':id' => $bookingId, ':u' => $userId]);
  $row = $s->fetch();

  if (!$row) bad('Бронирование не найдено', 404);

  if ($row['status'] === 'cancelled') {
    echo json_encode(['ok' => true, 'status' => 'cancelled'], JSON_UNESCAPED_UNICODE);
    exit;
  }

  // Отменяем
  $u = $pdo->prepare("
    UPDATE bookings
    SET status = 'cancelled'
    WHERE id = :id AND user_id = :u
    LIMIT 1
  ");
  $u->execute([':id' => $bookingId, ':u' => $userId]);

  echo json_encode(['ok' => true, 'status' => 'cancelled'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  bad('Ошибка сервера', 500);
}
