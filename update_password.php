<?php
// update_password.php

// Include the database connection
include('db_connection.php'); // Ensure this file exists and contains your DB connection

session_start(); // Start a session to use session variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email']; // Assuming the email is stored in session after login

    // Fetch the current password_hash from the database (use password_hash column)
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Verify the current password
    if ($row && password_verify($current_password, $row['password_hash'])) {
        // Check if new password matches confirm password
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_new_password, $email);

            if ($update_stmt->execute()) {
                echo "Password updated successfully!";
            } else {
                echo "Error updating password. Please try again.";
            }
        } else {
            echo "New password and confirmation do not match.";
        }
    } else {
        echo "Current password is incorrect.";
    }

    // Close the statement and connection
    $stmt->close();
    if (isset($update_stmt)) {
        $update_stmt->close(); // Close if it was created
    }
    $conn->close();
}
?>
