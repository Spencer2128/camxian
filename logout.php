<?php
session_start();

// Destroy the session
session_unset();
session_destroy();

// Clear the "Remember Me" cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/'); // Expire the cookie
}

header("Location: login.php");
exit();
?>
