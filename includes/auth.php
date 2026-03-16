<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /agriman/login.php');
        exit;
    }
}

function redirect_if_logged_in() {
    if (!empty($_SESSION['user_id'])) {
        header('Location: /agriman/dashboard.php');
        exit;
    }
}

function base_url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'];

    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    $module_dirs = ['fields','plantings','tasks','harvests','rotation','reports','includes','lang','assets'];
    $parts = array_filter(explode('/', $script_dir), 'strlen');
    $cleaned = [];
    foreach (array_reverse(array_values($parts)) as $segment) {
        if (in_array(strtolower($segment), $module_dirs)) continue;
        array_unshift($cleaned, $segment);
    }
    $root = '/' . implode('/', $cleaned);

    return $protocol . '://' . $host . rtrim($root, '/') . '/' . ltrim($path, '/');
}

function login_user($user) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_lang'] = $user['lang'] ?? 'en';
    session_regenerate_id(true);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name() {
    return $_SESSION['user_name'] ?? '';
}
