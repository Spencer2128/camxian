<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'camxian_inventory');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the table name and category name from the form
    $table_name = $_POST['table_name']; // Table name passed via hidden input
    $category_name = $_POST['category_name']; // Category name from user input
    
    // Check if category name is empty
    if (empty($category_name)) {
        echo "Error: Category name cannot be empty.";
    } else {
        // Escape values to prevent SQL injection
        $category_name = $conn->real_escape_string($category_name);
        
        // SQL query to insert the category into the table
        $insertCategorySql = "INSERT INTO `$table_name` (name, color) VALUES ('$category_name', 'blue')"; // You can customize color logic
        
        if ($conn->query($insertCategorySql) === TRUE) {
            echo "Category added successfully to table '$table_name'.";
            header("Location: dashboard.php"); // Redirect after successful insert
            exit();
        } else {
            echo "Error adding category: " . $conn->error;
        }
    }
}

$conn->close();
?>
