<?php
// Database configuration
// Using simple getenv with fallbacks

$host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
$name = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'coffee';
$user = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$pass = getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';

define('DB_HOST', $host);
define('DB_NAME', $name);
define('DB_USER', $user);
define('DB_PASS', $pass);