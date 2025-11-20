<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/guard.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

function bad($m,$c=400){ http_response_code($c); echo json_encode(['ok'=>false,'error'=>$m]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') bad('Method not allowed',405);
require_csrf($_POST['csrf'] ?? '');

$car_id = (int)($_POST['car_id'] ?? 0);
$place  = trim((string)($_POST['place'] ?? ''));
$start  = $_POST['start'] ?? '';
$end    = $_POST['end']   ?? '';
if ($car_id<=0) bad('Не выбрано авто');
if ($place==='') bad('Укажите место');
$start_dt = date_create($start);
$end_dt   = date_create($end);
if (!$start_dt || !$end_dt) bad('Неверный формат дат');
if ($start_dt >= $end_dt)  bad('Дата окончания должна быть позже начала');
if ($start_dt < new DateTime()) bad('Время начала уже прошло');

try{
  // есть ли авто
  $s=$pdo->prepare("SELECT 1 FROM cars WHERE id=?"); $s->execute([$car_id]);
  if(!$s->fetch()) bad('Автомобиль не найден',404);

  // занятость
  $s=$pdo->prepare("SELECT COUNT(*) FROM bookings
                    WHERE car_id=:c AND status IN ('pending','approved')
                      AND NOT (end_at<=:s OR start_at>=:e)");
  $s->execute([
    ':c'=>$car_id,
    ':s'=>$start_dt->format('Y-m-d H:i:s'),
    ':e'=>$end_dt->format('Y-m-d H:i:s'),
  ]);
  if((int)$s->fetchColumn()>0) bad('На выбранные даты авто занято');

  $userId = $_SESSION['user']['id'] ?? null;
  $ins=$pdo->prepare("INSERT INTO bookings (user_id,car_id,place,start_at,end_at,status)
                      VALUES (:u,:c,:p,:s,:e,'pending')");
  $ins->execute([
    ':u'=>$userId, ':c'=>$car_id, ':p'=>$place,
    ':s'=>$start_dt->format('Y-m-d H:i:s'),
    ':e'=>$end_dt->format('Y-m-d H:i:s'),
  ]);

  echo json_encode(['ok'=>true,'booking_id'=>$pdo->lastInsertId()]);
}catch(Throwable $e){
  bad('Ошибка сервера',500);
}
