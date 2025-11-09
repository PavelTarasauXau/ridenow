<?php
require __DIR__.'/config/db.php';
$ver = $pdo->query('SELECT VERSION()')->fetchColumn();
echo "Соединение успешно! Версия MySQL: $ver";
