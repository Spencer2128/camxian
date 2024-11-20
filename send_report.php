<?php
include 'db_connection.php';

// Get emails of users with notifications enabled
$email_query = "SELECT email FROM users WHERE notifications = 'enabled'";
$email_result = $conn->query($email_query);

// Query low-stock products
$product_query = "SELECT product_name, quantity FROM products WHERE quantity <= 10";
$product_result = $conn->query($product_query);

// Prepare the report content
$report_content = "Low Stock Report:\n\n";
if ($product_result->num_rows > 0) {
    while($product = $product_result->fetch_assoc()) {
        $report_content .= "Product: " . $product['product_name'] . " - Quantity: " . $product['quantity'] . "\n";
    }
} else {
    $report_content .= "All items are sufficiently stocked.";
}

// Send email to each registered user
if ($email_result->num_rows > 0) {
    while ($user = $email_result->fetch_assoc()) {
        $to = $user['email'];
        $subject = "Low Stock Report";
        $headers = "From: no-reply@inventorysystem.com";
        
        mail($to, $subject, $report_content, $headers);
    }
}

$conn->close();
?>
