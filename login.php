<?php
// Start the session
session_start();

// Initialize the error message variable
$error = "";

// Include the database connection
include('db_connection.php');

// Check if "Remember Me" cookies exist and pre-fill the form
$savedEmail = isset($_COOKIE['email']) ? $_COOKIE['email'] : "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role from the form

    // Prepare the SQL statement to validate email, role, and password
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ? AND role = ?");
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error); // Display error if preparation fails
    }
    $stmt->bind_param("ss", $email, $role); // Bind email and role
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with that email and role exists
    if ($row = $result->fetch_assoc()) {
        // Verify the password
        if (password_verify($password, $row['password_hash'])) {
            // Set session variable to indicate that the user is logged in
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            // Store the user's username in the session
            $_SESSION['username'] = $row['username'];  // Store the username in the session

            // Check if the "Remember Me" checkbox is checked
            if (isset($_POST['remember'])) {
                // Set cookies for email (but not password for security reasons)
                setcookie('email', $email, time() + (30 * 24 * 60 * 60), "/"); // Cookie expires in 30 days
            } else {
                // Clear cookies if "Remember Me" is not checked
                setcookie('email', '', time() - 3600, "/");
            }

            // Redirect to the dashboard upon successful login
            header("Location: dashboard.php");
            exit(); // Stop script execution after redirect
        } else {
            // Set the error message if the password is invalid
            $error = "Invalid email, role, or password!";
        }
    } else {
        // Set the error message if the email and role combination is not found
        $error = "Invalid email, role, or password!";
    }

    // Close the statement if it was prepared
    if (isset($stmt)) {
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="css/login.css?v=1">
</head>

<body style="background-image: url('img/background.png');">
    <div class="login-box">
        <div class="form-container">
            <img src="img/logo.png" alt="Company Logo" class="logo">
            <form action="login.php" method="post">
                <!-- Role selection as dropdown with a placeholder option -->
                <div class="form-group">
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>

                <!-- Email input -->
                <div class="form-group">
                    <input type="text" id="email" name="email" placeholder="Enter Username" value="<?php echo htmlspecialchars($savedEmail); ?>" required>
                </div>

                <!-- Password input -->
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Enter Password" value="" required>
                </div>

                <!-- Remember me checkbox -->
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember" name="remember" <?php if ($savedEmail) echo 'checked'; ?>>
                    <label for="remember">Remember Me</label>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn">LOGIN</button>
            </form>
            
            <!-- Error message display -->
            <?php if ($error): ?>
                <div style="color: red; font-size: 12px; margin-top: 10px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
