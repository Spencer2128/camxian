<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Restrict delete operation to admin users only
if ($_SESSION['role'] !== 'admin') {
    // Send a response to show a message on the dashboard page
    echo json_encode(['error' => 'You do not have permission to delete tables.']);
    exit();
}

// Connect to the product database (camxian_table)
$conn_product = new mysqli('localhost', 'root', '', 'camxian_table');
if ($conn_product->connect_error) {
    die("Connection failed: " . $conn_product->connect_error);
}

// Check if 'table' parameter is set in the URL
if (isset($_GET['table'])) {
    $table_name = $_GET['table'];

    // Sanitize the table name to prevent SQL injection
    $table_name = $conn_product->real_escape_string($table_name);

    // Check if the table exists
    $checkTableSql = "SHOW TABLES LIKE '$table_name'";
    $tableExists = $conn_product->query($checkTableSql);

    if ($tableExists && $tableExists->num_rows > 0) {
        // Table exists, proceed with deletion
        $dropTableSql = "DROP TABLE `$table_name`";
        if ($conn_product->query($dropTableSql) === TRUE) {
            // Success, no need to redirect; the table is deleted
            echo json_encode(['success' => 'Table deleted successfully.']);
        } else {
            echo json_encode(['error' => 'Error deleting table: ' . $conn_product->error]);
        }
    } else {
        echo json_encode(['error' => 'Error: Table does not exist.']);
    }
} else {
    echo json_encode(['error' => 'Error: No table specified.']);
}
?>
