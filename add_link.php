<?php
// Include the database connection
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $linkText = $conn->real_escape_string($_POST['linkText']);
    $linkHref = $conn->real_escape_string($_POST['linkHref']);
    $linkColor = $conn->real_escape_string($_POST['linkColor']);

    // Insert the new link into the database
    $sql = "INSERT INTO links (text, href, color) VALUES ('$linkText', '$linkHref', '$linkColor')";

    if ($conn->query($sql) === TRUE) {
        echo "Link added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>
