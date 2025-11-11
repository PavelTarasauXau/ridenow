<?php
require __DIR__.'/config/db.php';
echo 'Версия MySQL: '.$pdo->query('SELECT VERSION()')->fetchColumn()."<br>";
echo 'Пользователей: '.$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()."<br>";
echo 'Авто: '.$pdo->query('SELECT COUNT(*) FROM cars')->fetchColumn();
