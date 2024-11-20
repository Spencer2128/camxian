<?php
// Connect to the database
include 'tb_connection.php';

// Function to fetch all tables with low stock data
function fetchLowStockData($conn) {
    $lowStockData = [];

    // Step 1: Get a list of all tables in the database
    $tablesResult = $conn->query("SHOW TABLES");

    // Check if query was successful
    if ($tablesResult === false) {
        echo "Error fetching tables: " . $conn->error;
        return [];
    }

    // Step 2: Loop through each table
    while ($tableRow = $tablesResult->fetch_array()) {
        $tableName = $tableRow[0];

        // Step 3: Check if the table has the 'quantity' column
        $columnCheckQuery = "DESCRIBE `$tableName`";
        $columnResult = $conn->query($columnCheckQuery);

        if ($columnResult === false) {
            echo "Error checking columns of table `$tableName`: " . $conn->error;
            continue;
        }

        $hasQuantityColumn = false;
        while ($column = $columnResult->fetch_assoc()) {
            if ($column['Field'] === 'quantity') {
                $hasQuantityColumn = true;
                break;
            }
        }

        // If table contains 'quantity' column, fetch the data
        if ($hasQuantityColumn) {
            $tableDataResult = $conn->query("SELECT * FROM `$tableName` WHERE quantity <= 10 ORDER BY quantity ASC");

            if ($tableDataResult === false) {
                echo "Error fetching data from `$tableName`: " . $conn->error;
                continue;
            }

            // Store the data from the table
            $tableData = [];
            while ($row = $tableDataResult->fetch_assoc()) {
                $tableData[] = $row;
            }

            // Store table data if there is relevant data
            if (!empty($tableData)) {
                $lowStockData[$tableName] = $tableData;
            }
        }
    }

    return $lowStockData;
}

// Fetch low stock data from all tables
$lowStockData = fetchLowStockData($conn);

// Handle sending the report if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_report'])) {
    // Get emails of all users
    $email_query = "SELECT email FROM users";
    $email_result = $conn->query($email_query);

    // Prepare the report content for email
    $report_content = "Low Stock Report:\n\n";
    if (!empty($lowStockData)) {
        foreach ($lowStockData as $tableName => $tableRows) {
            // Only use the part of the table name that doesn't include numeric prefixes
            $tableName = preg_replace('/^\d+\s/', '', $tableName);  // Remove numeric prefixes like '1 '
            
            $report_content .= "$tableName\n"; // Removed "Table: " label
            foreach ($tableRows as $product) {
                // Ensure keys exist before accessing
                $series = isset($product['series']) ? $product['series'] : 'N/A';
                $model = isset($product['model']) ? $product['model'] : 'N/A';
                $resolution = isset($product['resolution']) ? $product['resolution'] : 'N/A';
                $description = isset($product['description']) ? $product['description'] : 'N/A';
                $quantity = isset($product['quantity']) ? $product['quantity'] : 'N/A';

                $report_content .= "Series: $series - Model: $model - Resolution: $resolution - Description: $description - Quantity: $quantity\n";
            }
        }
    } else {
        $report_content .= "All items are sufficiently stocked.";
    }

    // Send email to each registered user
    if ($email_result->num_rows > 0) {
        while ($user = $email_result->fetch_assoc()) {
            $to = $user['email'];
            $subject = "Low Stock Report";
            $headers = "From: no-reply@inventorysystem.com";
            mail($to, $subject, $report_content, $headers);
        }
    }
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
        <!-- Main content -->
        <div class="main-content">
            <!-- Header -->
            <header>
                <h1>Low Stock Report</h1>
                <img src="img/logo_solo.png" alt="Company Logo" class="Picture">
            </header>

            <!-- Report content -->
            <div class="report-content">
                <?php if (!empty($lowStockData)): ?>
                    <?php foreach ($lowStockData as $tableName => $tableRows): ?>
                        <?php
                        // Clean up table name to remove numbers or extra prefixes
                        $tableName = preg_replace('/^\d+\s/', '', $tableName);
                        ?>
                        <h2><?php echo htmlspecialchars($tableName); ?></h2> <!-- Display only the table name -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Series</th>
                                    <th>Model</th>
                                    <th>Resolution</th>
                                    <th>Description</th>
                                    <th>QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tableRows as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row["series"] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row["model"] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row["resolution"] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row["description"] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row["quantity"] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>All items are sufficiently stocked.</p>
                <?php endif; ?>
            </div>

            <!-- Send Report Section -->
            <div class="send-report">
                <form action="reports.php" method="POST">
                    <button type="submit" name="send_report" class="btn btn-primary">Send Low Stock Report</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
 <!-- Logout Confirmation Modal -->
 <div id="logoutModal" class="logoutmodal" style="display: none;">
        <div class="modal-content">
            <br>
            <p>Are you sure you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeModal()">No</button>
        </div>
    </div>

    <script src="js/script.js"></script>
<?php
// Close the database connection after everything is done
$conn->close();
?>
