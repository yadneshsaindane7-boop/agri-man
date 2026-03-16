<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'agriman_db');
define('DB_CHARSET', 'utf8mb4');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;">
         <strong>Database connection failed:</strong> ' . mysqli_connect_error() . '</div>');
}

mysqli_set_charset($conn, DB_CHARSET);

function esc($conn, $val) {
    return mysqli_real_escape_string($conn, trim((string)$val));
}

function qry($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        die('<pre style="color:#dc2626">Query Error: ' . mysqli_error($conn) . "\n\nSQL: $sql</pre>");
    }
    return $result;
}


function fetch_all($conn, $sql) {
    $result = qry($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function fetch_one($conn, $sql) {
    $result = qry($conn, $sql);
    return mysqli_fetch_assoc($result);
}
