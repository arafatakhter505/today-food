<?php
// db_connection.php

// Database connection parameters
$host = 'localhost';
$dbname = 'today_food';
$dbUsername = 'root';
$password = '';
$charset = 'utf8';

try {
    // Create a PDO instance
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $dbUsername, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Enable exception handling for errors
} catch (PDOException $e) {
    // In case of a connection error, display a message
    die("Connection failed: " . $e->getMessage());
}
?>
