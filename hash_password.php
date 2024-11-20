<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include('db_connection.php'); // Ensure this file exists and contains your DB connection

// Add this line to test if the script is running
echo "Script started.<br>";

// Fetch all users from the database
$stmt = $conn->prepare("SELECT email, password FROM users");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if users exist
if ($result->num_rows === 0) {
    echo "No users found in the database.<br>";
} else {
    // Loop through each user and hash the password
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $plain_password = $row['password']; // This should be the plain text password

        // Hash the password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Update the user record with the hashed password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);

        if ($update_stmt->execute()) {
            echo "Password for $email updated successfully!<br>";
        } else {
            echo "Error updating password for $email: " . $update_stmt->error . "<br>";
        }

        // Close the update statement
        $update_stmt->close();
    }
}

// Close the original statement and connection
$stmt->close();
$conn->close();
?>
