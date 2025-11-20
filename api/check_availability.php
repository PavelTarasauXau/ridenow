<?php
require __DIR__.'/../config/db.php';
header('Content-Type: application/json; charset=utf-8');

$car_id = (int)($_GET['car_id'] ?? 0);
$start  = $_GET['start'] ?? '';
$end    = $_GET['end'] ?? '';
if($car_id<=0 || !$start || !$end){
  echo json_encode(['ok'=>false,'error'=>'bad params']); exit;
}

try{
  $stmt=$pdo->prepare("
    SELECT COUNT(*) FROM bookings
     WHERE car_id=:c AND status IN ('pending','approved')
       AND NOT (end_at <= :s OR start_at >= :e)
  ");
  $stmt->execute([
    ':c'=>$car_id, ':s'=>date('Y-m-d H:i:s', strtotime($start)),
    ':e'=>date('Y-m-d H:i:s', strtotime($end))
  ]);
  $busy = (int)$stmt->fetchColumn() > 0;
  echo json_encode($busy ? ['ok'=>false,'error'=>'busy'] : ['ok'=>true]);
}catch(Throwable $e){
  echo json_encode(['ok'=>false,'error'=>'server']); 
}
