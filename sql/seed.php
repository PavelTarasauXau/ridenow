<?php
require __DIR__.'/../config/db.php';

$cars = [
  ['Kia',   'Rio',        'механика', 'бензин', 5, 59.00, '/pics/kia_rio_4.jpg'],
  ['Geely', 'Coolray',    'автомат',  'бензин', 5, 89.00, '/pics/novyy_geely_coolray_2_c0b.webp'],
  ['Renault','Scenic',    'механика', 'дизель', 5, 99.00, '/pics/renaultscenic.jpeg'],
];

$stmt = $pdo->prepare("
  INSERT INTO cars (make, model, transmission, fuel, seats, daily_price, image_url)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");

foreach ($cars as $c) {
  $stmt->execute($c);
}

echo "OK\n";
