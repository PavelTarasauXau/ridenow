<?php
function csrf(): string {
  if (session_status() === PHP_SESSION_NONE) session_start();
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['csrf'];
}
function require_csrf($token): void {
  if (!hash_equals($_SESSION['csrf'] ?? '', $token ?? '')) {
    http_response_code(419); die('CSRF mismatch');
  }
}
function isLoggedIn(): bool { return !empty($_SESSION['user']); }
