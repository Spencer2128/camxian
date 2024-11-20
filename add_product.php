<?php
// Start session and check user login
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Connect to the product database
$conn_product = new mysqli('localhost', 'root', '', 'camxian_table');
if ($conn_product->connect_error) {
    die("Connection failed: " . $conn_product->connect_error);
}

// Get the table name from the form data
$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';

// If no table name is provided, exit with error
if (empty($table_name)) {
    echo json_encode(['status' => 'error', 'message' => 'No table specified.']);
    exit();
}

// Sanitize and fetch the form data
$columns = [];
foreach ($_POST as $key => $value) {
    if ($key != 'add_product' && $key != 'table_name') {  // Exclude the flag and table name
        $columns[$key] = $conn_product->real_escape_string($value);
    }
}

// Build the SQL query to insert the new product data
$columns_keys = implode(", ", array_keys($columns));
$columns_values = "'" . implode("', '", array_values($columns)) . "'";

$sql = "INSERT INTO `$table_name` ($columns_keys) VALUES ($columns_values)";

if ($conn_product->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Product added successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error adding product: ' . $conn_product->error]);
}

// Close the database connection
$conn_product->close();
?>
