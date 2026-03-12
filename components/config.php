<?php
// Function to check multiple sources for an environment variable
function get_db_var($name, $fallback = '') {
    // Check getenv, $_ENV, and $_SERVER
    $val = getenv($name);
    if ($val !== false) return $val;
    if (isset($_ENV[$name])) return $_ENV[$name];
    if (isset($_SERVER[$name])) return $_SERVER[$name];
    return $fallback;
}

// Database configuration
$host = get_db_var('DB_HOST', get_db_var('MYSQLHOST', 'localhost'));
$name = get_db_var('DB_NAME', get_db_var('MYSQLDATABASE', 'coffee'));
$user = get_db_var('DB_USER', get_db_var('MYSQLUSER', 'root'));
$pass = get_db_var('DB_PASS', get_db_var('MYSQLPASSWORD', ''));

define('DB_HOST', $host);
define('DB_NAME', $name);
define('DB_USER', $user);
define('DB_PASS', $pass);