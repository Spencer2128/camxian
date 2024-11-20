<?php
// Include database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $notifications = $_POST['notifications'];

    // Check if the email already exists in the user_settings table
    $check_query = "SELECT * FROM user_settings WHERE email = '$email'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        // Email exists, update the notification setting
        $update_query = "UPDATE user_settings SET notifications = '$notifications' WHERE email = '$email'";
        if ($conn->query($update_query) === TRUE) {
            echo "Settings updated successfully!";
        } else {
            echo "Error updating settings: " . $conn->error;
        }
    } else {
        // Email doesn't exist, insert a new record
        $insert_query = "INSERT INTO user_settings (email, notifications) VALUES ('$email', '$notifications')";
        if ($conn->query($insert_query) === TRUE) {
            echo "Settings saved successfully!";
        } else {
            echo "Error saving settings: " . $conn->error;
        }
    }

    // Close the connection
    $conn->close();
}
?>
