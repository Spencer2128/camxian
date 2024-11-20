<?php
// Start session and check user login
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Connect to the product database
$conn_product = new mysqli('localhost', 'root', '', 'camxian_table');
if ($conn_product->connect_error) {
    die("Connection failed: " . $conn_product->connect_error);
}

// Get the table name from the URL
$table_name = isset($_GET['table']) ? $_GET['table'] : '';

// Get the search query from GET if set
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// If no table name is provided, redirect back to the dashboard
if (empty($table_name)) {
    header("Location: dashboard.php");
    exit();
}

// Prepare SQL to get columns for the specified table
$columns_result = $conn_product->query("SHOW COLUMNS FROM `$table_name`");

if (!$columns_result) {
    die("Error fetching table columns: " . $conn_product->error);
}

// Fetch all the columns in the table
$columns = [];
while ($row = $columns_result->fetch_assoc()) {
    $columns[] = $row['Field'];
}
// Build the SQL query to fetch data based on search query
if (!empty($search_query)) {
    $search_query = $conn_product->real_escape_string($search_query);
    
    // Dynamically build the WHERE clause based on table columns
    $search_conditions = [];
    foreach ($columns as $column) {
        // Only search in relevant fields (adjust as needed for specific fields like 'description', 'model', etc.)
        $search_conditions[] = "$column LIKE '%$search_query%'";
    }
    
    // Combine all conditions with OR to allow searching across all columns
    $where_clause = implode(" OR ", $search_conditions);
    $sql = "SELECT * FROM `$table_name` WHERE $where_clause";
} else {
    $sql = "SELECT * FROM `$table_name`"; // No search, just get all rows
}

// Execute the query
$data_result = $conn_product->query($sql);

if (!$data_result) {
    die("Error fetching table data: " . $conn_product->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Table - <?php echo htmlspecialchars($table_name); ?></title>
    <link rel="stylesheet" href="css/view_table.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h2><?php echo htmlspecialchars(ucfirst(str_replace('table_', '', $table_name))); ?></h2>
                <input type="text" id="searchQuery" placeholder="Search..." class="search-input">
            </header>

            <button class="create-draft" onclick="openModal('<?php echo $table_name; ?>')">Add</button>

            <!-- Table Data -->
            <section class="table-data">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column) : ?>
                                <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $column))); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="product-list">
                        <?php
                        if ($data_result->num_rows > 0) {
                            while ($row = $data_result->fetch_assoc()) {
                                echo "<tr>";
                                foreach ($columns as $column) {
                                    if ($column == 'image_url') {
                                        echo "<td><img src='" . htmlspecialchars($row[$column]) . "' alt='Product Image' style='width:50px; height:50px;'></td>";
                                    } else {
                                        echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='" . count($columns) . "'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                window.location.href = 'logout.php';
            }
        }

        $(document).ready(function() {
            // Event listener for search input
            $('#searchQuery').on('keyup', function() {
                var query = $(this).val(); // Get the input value

                if (query.length >= 3 || query.length === 0) { // Search if 3 or more characters, or clear results if empty
                    $.ajax({
                        url: 'view_table.php', // Use the current page URL
                        type: 'GET',
                        data: {
                            search_query: query, // Pass the search query
                            table: '<?php echo $table_name; ?>' // Pass the current table name dynamically
                        },
                        success: function(response) {
                            // Extract only the table content from the response
                            var filteredData = $(response).find('#product-list').html();
                            $('#product-list').html(filteredData); // Update the table body with the filtered data
                        }
                    });
                }
            });
        });

        // Open the modal and pass the table name dynamically
        function openModal(tableName) {
            document.getElementById('tableName').value = tableName;  // Set the table name in the hidden input
            document.getElementById('addProductModal').style.display = "block";  // Open the modal
        }

        // Close the modal
        function closeModal() {
            document.getElementById('addProductModal').style.display = "none";
        }
    </script>

    <!-- Modal for Adding a Product -->
    <div id="addProductModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add New Product</h2>
            <form id="add-product-form" enctype="multipart/form-data">
                <?php
                // Prepopulate the modal form fields based on table columns
                foreach ($columns as $column) {
                    echo '<label for="' . $column . '">' . ucfirst(str_replace('_', ' ', $column)) . ':</label>';
                    if ($column === 'description') {
                        echo '<textarea name="' . $column . '" id="' . $column . '" required></textarea><br>';
                    } elseif ($column === 'image_url') {
                        echo '<input type="text" name="' . $column . '" id="' . $column . '" required><br>';
                    } else {
                        echo '<input type="text" name="' . $column . '" id="' . $column . '" required><br>';
                    }
                }
                ?>
                <input type="hidden" name="table_name" id="tableName">
                <button class="create-draft" type="submit">Add</button>
            </form>
        </div>
    </div>
</body>
</html>

<script src="js/script.js" defer></script>
<script src="js/search.js" defer></script>
<script src="js/modalscript.js" defer></script>

<?php
// Close the database connection
$conn_product->close();
?>
