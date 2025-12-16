<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function csrf(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function require_csrf_json(?string $token): void {
  $sessionToken = $_SESSION['csrf'] ?? '';
  if (!$token || !hash_equals($sessionToken, $token)) {
    http_response_code(419);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'CSRF mismatch (обновите страницу)'], JSON_UNESCAPED_UNICODE);
    exit;
  }
}

function isLoggedIn(): bool {
  return !empty($_SESSION['user']);
}

function require_auth_json(): void {
  if (!isLoggedIn()) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Требуется авторизация'], JSON_UNESCAPED_UNICODE);
    exit;
  }
}
