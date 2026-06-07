<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'animethai');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Demo');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/animethai');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-errors.log');

session_start();
date_default_timezone_set('Asia/Bangkok');
