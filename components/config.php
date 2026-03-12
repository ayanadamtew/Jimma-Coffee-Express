<?php
// Database configuration
// Using getenv() to support Cloud Platforms like Railway
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'coffee');
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '');