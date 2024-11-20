<?php
session_start();

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "camxian_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch all user data (email and role)
function fetchAllUserData($conn) {
    $userData = [];

    // Fetch data from the 'users' table
    $sql = "SELECT id, email, role, username FROM users";  // Added 'username' field in the select query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result === false) {
        echo "Error executing query: " . $conn->error;
        return [];
    }

    // Store the data from the 'users' table
    while ($row = $result->fetch_assoc()) {
        $userData[] = $row;
    }

    return $userData;
}

// Handle form submission for new user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'], $_POST['role'], $_POST['username'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $username = $_POST['username']; // Get the username

    // Validate role
    if (!in_array($role, ['admin', 'employee'])) {
        die("Invalid role selected!");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, role, username) VALUES (?, ?, ?, ?)");  // Include username in the query
    $stmt->bind_param("ssss", $email, $hashed_password, $role, $username);  // Bind username as a parameter

    // Execute the query
    if ($stmt->execute()) {
        echo "<p>User registered successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection after processing
// $conn->close();  // Moved to the end of the script
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
            <h1>List of Users</h1>
            <button onclick="openModal()">Add User</button> <!-- Button to open modal -->
        </header>

        <!-- Users Table Section -->
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Username</th> <!-- Added Username column -->
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetching user data (username, email, and role)
                    $userData = fetchAllUserData($conn);
                    if (!empty($userData)) {
                        $counter = 1;
                        foreach ($userData as $row) {
                            $userId = $row['id']; // Get the user ID
                            echo "<tr onclick=\"window.location.href='users.php?action=details&id=$userId'\" style='cursor: pointer;'>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>"; // Display username
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                            echo "<td>
                                    <a href='users.php?action=edit&id=$userId'>Edit</a> |
                                    <a href='users.php?action=delete&id=$userId' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>No users found</td></tr>"; // Adjusted colspan for the new column
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for User Registration -->
<div id="registerModal" class="reguser" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="close2Modal()">&times;</span>
        <h2>Register New User</h2>
        <form action="users.php" method="POST">
            <input type="text" name="username" placeholder="Enter Username" required> <!-- Added input for username -->
            <input type="email" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
            </select>
            <button type="submit">Register</button>
        </form>
    </div>
</div>

<script>
    // Function to open the modal
    function openModal() {
        document.getElementById("registerModal").style.display = "block";
    }

    // Function to close the modal
    function close2Modal() {
        document.getElementById("registerModal").style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        var modal = document.getElementById("registerModal");
        // Close modal only if the click is outside the modal content
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

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

<?php
// Close the database connection
$conn->close();
?>
