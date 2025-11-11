<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Удаление данных пользователя из сессии
unset($_SESSION['user']);

// Удаление remember cookie
if (isset($_COOKIE['ridenow_remember'])) {
  setcookie('ridenow_remember', '', time() - 3600, '/');
}

// Редирект на главную страницу
header('Location: /index.php');
exit;

