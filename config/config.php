<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('APP_NAME', $_ENV['APP_NAME']);
define('APP_URL', rtrim($_ENV['APP_URL'], '/'));
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID']);

$isHttps = !empty($_SERVER['HTTPS'])
    && $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
