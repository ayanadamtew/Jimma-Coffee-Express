<?php
/**
 * Database Configuration
 * Priority: DB_* > MYSQL_* > MYSQL* > Localhost
 */

// Host
$host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';

// Database Name
$name = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'coffee';

// User
$user = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';

// Password - careful with empty password
$pass = getenv('DB_PASS');
if ($pass === false) $pass = getenv('MYSQLPASSWORD');
if ($pass === false) $pass = getenv('MYSQL_ROOT_PASSWORD');
if ($pass === false) $pass = getenv('MYSQL_PASSWORD');
if ($pass === false) $pass = ''; // Local fallback

define('DB_HOST', $host);
define('DB_NAME', $name);
define('DB_USER', $user);
define('DB_PASS', $pass);