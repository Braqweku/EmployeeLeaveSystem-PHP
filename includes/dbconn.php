<?php

// Check and define constants only if not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost'); // Replace with your database host
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root'); // Replace with your database username
}
if (!defined('DB_PASS')) {
    define('DB_PASS', ''); // Replace with your database password
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'employeeleavedb'); // Replace with your database name
}

try {
    // Establish database connection
    $dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>