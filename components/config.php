<?php
/**
 * Database Configuration with Deep Environment Search
 */

function fetch_env_var($name, $fallback = '') {
    // Check $_ENV first (most reliable for FPM/Nixpacks)
    if (isset($_ENV[$name]) && $_ENV[$name] !== '') return $_ENV[$name];
    // Check $_SERVER
    if (isset($_SERVER[$name]) && $_SERVER[$name] !== '') return $_SERVER[$name];
    // Check getenv
    $val = getenv($name);
    if ($val !== false && $val !== '') return $val;
    
    return $fallback;
}

// Hostname
define('DB_HOST', fetch_env_var('DB_HOST', fetch_env_var('MYSQLHOST', fetch_env_var('MYSQL_HOST', 'localhost'))));

// Database Name
define('DB_NAME', fetch_env_var('DB_NAME', fetch_env_var('MYSQLDATABASE', fetch_env_var('MYSQL_DATABASE', 'coffee'))));

// Username
define('DB_USER', fetch_env_var('DB_USER', fetch_env_var('MYSQLUSER', fetch_env_var('MYSQL_USER', 'root'))));

// Password
// We treat empty string differently here
$pass = fetch_env_var('DB_PASS', null);
if ($pass === null) $pass = fetch_env_var('MYSQLPASSWORD', null);
if ($pass === null) $pass = fetch_env_var('MYSQL_PASSWORD', null);
if ($pass === null) $pass = fetch_env_var('MYSQL_ROOT_PASSWORD', null);
if ($pass === null) $pass = ''; // Final local fallback

define('DB_PASS', $pass);