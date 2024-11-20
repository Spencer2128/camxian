<?php
// db_connection.php

// Database configuration
$host = "localhost"; // Typically "localhost"
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "camxian_table"; // Your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

