<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css?v=1">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Inventory System</h2>
            <div class="user-profile">
                <img src="img/User.png" alt="User Picture" class="user-picture">
                <p class="username">
    <?php
        // Ensure the session username is checked first
        if (isset($_SESSION['username'])) {
            echo "Hello, " . htmlspecialchars($_SESSION['username']);
        } else {
            echo "Hello, Guest";
        }
    ?>
</p>
            </div>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="brands.php">Brands</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="company.php">Company</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="javascript:void(0);" onclick="confirmLogout()">Logout</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <!-- Header -->
            <header>
                <h1>Company Info</h1>
                <img src="img/logo_solo.png" alt="Company Logo" class="Picture">
            </header>

                <!-- Logout Confirmation Modal -->
                <div id="logoutModal" class="logoutmodal" style="display: none;">
        <div class="modal-content">
           <br>
            <p>Are you sure you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeModal()">No</button>
        </div>
    </div>

    <script src="js/script.js" defer></script>
</body>
</html>
