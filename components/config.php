<?php
// Function to check multiple sources for an environment variable
function get_db_var($name, $fallback = '') {
    return getenv($name) ?: ($_ENV[$name] ?? ($_SERVER[$name] ?? $fallback));
}

// Database configuration
// Priority: 
// 1. Manually set DB_HOST/DB_NAME...
// 2. Railway's MYSQLHOST/MYSQLDATABASE...
// 3. Localhost (XAMPP/MAMP)
define('DB_HOST', get_db_var('DB_HOST', get_db_var('MYSQLHOST', 'localhost')));
define('DB_NAME', get_db_var('DB_NAME', get_db_var('MYSQLDATABASE', 'coffee')));
define('DB_USER', get_db_var('DB_USER', get_db_var('MYSQLUSER', 'root')));
define('DB_PASS', get_db_var('DB_PASS', get_db_var('MYSQLPASSWORD', '')));