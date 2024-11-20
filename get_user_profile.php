<?php
// Start the session


// Redirect to login if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Connect to the user database (camxian_inventory)
$conn_user = new mysqli('localhost', 'root', '', 'camxian_inventory');
if ($conn_user->connect_error) {
    die("Connection failed: " . $conn_user->connect_error);
}

// Fetch the user's profile picture if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Query to fetch the profile picture from the database
    $sql = "SELECT profile_picture FROM users WHERE username = ?";
    $stmt = $conn_user->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if a profile picture exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($profile_picture);
        $stmt->fetch();
        $_SESSION['profile_picture'] = $profile_picture; // Store it in the session
    } else {
        // If no profile picture found, use a default picture
        $_SESSION['profile_picture'] = 'uploads/profile_pictures/default.jpg';
    }

    $stmt->close();
} else {
    // Default profile picture in case the username is not set
    $_SESSION['profile_picture'] = 'uploads/profile_pictures/default.jpg';
}
?>
