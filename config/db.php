<?php

$DB_HOST = '127.127.126.31';   
$DB_PORT = 3306;               
$DB_NAME = 'ridenow';
$DB_USER = 'root';
$DB_PASS = '';

// Подключение к БД
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Ошибка подключения к БД: ' . $e->getMessage());
}

// Сессии — для авторизации и т.п.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
