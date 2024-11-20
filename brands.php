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

        <div class="main-content">
            <!-- Header -->
            <header>
                <h1>Brands</h1>
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

