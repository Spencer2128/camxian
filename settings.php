<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Restrict settings page access to only admins
if ($_SESSION['role'] !== 'admin') {
    $isEmployee = true; // Flag to indicate employee is accessing the page
    $errorMessage = "You are not authorized to access this page.";
} else {
    $isEmployee = false; // Admin is allowed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Dashboard</title>
    <!-- Linking Google Font Link For Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
    <!-- Linking Material Symbols for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="css/dashboard.css?v=1">
    <style>
        /* Styles for the smaller modal */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent background */
            padding-top: 100px; /* Space from top */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 300px; /* Smaller width */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content p {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .close-modal {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 8px 16px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .close-modal:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="img/logo.png" alt="Logo" />
                <h2>Inventory System</h2>
            </div>
        
            <ul class="sidebar-links">
                <h4>
                    <span>Main Menu</span>
                    <div class="menu-separator"></div>
                </h4>
                <li><a href="dashboard.php" class="sidebar-link">
                    <span class="material-symbols-outlined">dashboard</span> Dashboard</a>
                </li>
                <li><a href="users.php" class="sidebar-link">
                    <span class="material-symbols-outlined">person</span> Users</a>
                </li>
                <li><a href="brands.php" class="sidebar-link">
                    <span class="material-symbols-outlined">business</span> Brands</a>
                </li>
                <li><a href="orders.php" class="sidebar-link">
                    <span class="material-symbols-outlined">shopping_cart</span> Orders</a>
                </li>
                <li><a href="reports.php" class="sidebar-link">
                    <span class="material-symbols-outlined">assessment</span> Reports</a>
                </li>
                <li><a href="https://camxian.com/" target="_blank" class="sidebar-link">
                    <span class="material-symbols-outlined">business_center</span> Company</a>
                </li>
                <li><a href="settings.php" class="sidebar-link">
                    <span class="material-symbols-outlined">settings</span> Settings</a>
                </li>
                <li><a href="javascript:void(0);" onclick="confirmLogout()" class="sidebar-link">
                    <span class="material-symbols-outlined">logout</span> Logout</a>
                </li>
            </ul>
        </nav>
        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Settings</h1>
            </header>
            
            <?php if ($isEmployee): ?>
                <!-- Employee Access Denied Modal -->
                <div id="accessDeniedModal" class="modal" style="display: block;">
                    <div class="modal-content">
                        <p><?php echo $errorMessage; ?></p>
                        <button class="close-modal" onclick="closeModal1()">Close</button>
                    </div>
                </div>
            <?php else: ?>
                <!-- Admin Settings Section -->
                <section class="settings-section">
                    <!-- Email Report Settings -->
                    <div class="settings-card">
                        <h2>Email Report Settings</h2>
                        <form action="update_account.php" method="POST">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="" required>

                            <label for="notifications">Report Notifications</label>
                            <select id="notifications" name="notifications">
                                <option value="enabled">Enabled</option>
                                <option value="disabled">Disabled</option>
                            </select>
                            

                            <button type="submit" class="save-btn">Save Changes</button>
                        </form>
                    </div>
                
                    <!-- Password Management -->
                    <div class="settings-card">
                        <h2>Password Management</h2>
                        <form action="update_password.php" method="POST">
                            <label for="current-password">Current Password:</label>
                            <input type="password" id="current-password" name="current_password" required>
                
                            <label for="new-password">New Password:</label>
                            <input type="password" id="new-password" name="new_password" required>
                
                            <label for="confirm-password">Confirm New Password:</label>
                            <input type="password" id="confirm-password" name="confirm_password" required>
                            

                            <button type="submit" class="save-btn">Update Password</button>
                        </form>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logoutmodal" style="display: none;">
        <div class="modal-content">
           <br>
            <p>Are you sure you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeModal()">No</button>
        </div>
    </div>

    <script>
        // Close the modal when clicking the close button
        function closeModal1() {
            var modal = document.getElementById('accessDeniedModal');
            modal.style.display = 'none'; // Hide the modal
        }

        // Function to open the modal
        function showModal() {
            var modal = document.getElementById('accessDeniedModal');
            modal.style.display = 'block'; // Show the modal
        }

        // Trigger showModal() if you want the modal to appear initially (if employee)
        <?php if ($isEmployee): ?>
            showModal();
        <?php endif; ?>
    </script>

    <script src="js/script.js" defer></script>
</body>
</html>
