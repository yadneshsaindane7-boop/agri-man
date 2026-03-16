<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'mr'])) {
    $_SESSION['user_lang'] = $_GET['lang'];
}
$GLOBALS['_lang'] = $_SESSION['user_lang'] ?? 'en';

$_lang_file = __DIR__ . '/../lang/' . $GLOBALS['_lang'] . '.php';
if (!file_exists($_lang_file)) {
    $_lang_file = __DIR__ . '/../lang/en.php';
}
$GLOBALS['_translations'] = require $_lang_file;

function __($key) {
    return $GLOBALS['_translations'][$key] ?? $key;
}

function current_lang() {
    return $GLOBALS['_lang'];
}

function lang_toggle_url() {
    $target = (current_lang() === 'en') ? 'mr' : 'en';
    $params = array_merge($_GET, ['lang' => $target]);
    unset($params['lang']); // will re-add below
    return '?lang=' . $target;
}
