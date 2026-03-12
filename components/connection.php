<?php
require_once __DIR__ . '/config.php';

try {
	$conn = new PDO(
		'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
		DB_USER,
		DB_PASS
		);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
	die('Database connection failed. Please check your config.php settings.');
}

if (!function_exists('unique_id')) {
	function unique_id()
	{
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charLength = strlen($chars);
		$randomString = '';
		for ($i = 0; $i < 20; $i++) {
			$randomString .= $chars[mt_rand(0, $charLength - 1)];
		}
		return $randomString;
	}
}