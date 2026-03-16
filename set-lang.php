<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$lang = $_GET['lang'] ?? 'en';
if (in_array($lang, ['en', 'mr'])) {
    $_SESSION['user_lang'] = $lang;
}

$ref = $_GET['ref'] ?? '';

$ref = parse_url($ref, PHP_URL_PATH);
$ref = '/' . ltrim($ref, '/');

if (strpos($ref, '/agriman/') !== 0) {
    $ref = '/agriman/dashboard.php';
}

header('Location: ' . $ref);
exit;