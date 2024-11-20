<?php
// Start the session
session_start();

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

// Connect to the product database (camxian_table)
$conn_product = new mysqli('localhost', 'root', '', 'camxian_table');
if ($conn_product->connect_error) {
    die("Connection failed: " . $conn_product->connect_error);
}

// Fetch all tables from the camxian_table database
$tables = $conn_product->query("SHOW TABLES");

// Handle new table creation for the product database (camxian_table)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $card_name = $_POST['card_name'];
    $fields = $_POST['fields'];
    if (!empty($card_name)) {
        $table_name = "table_" . strtolower(preg_replace('/\s+/', '_', $card_name));
        $display_name = ucfirst(strtolower(preg_replace('/table_/', '', $table_name)));

        // Check if the table name already exists in the camxian_table database
        $checkTableSql = "SHOW TABLES LIKE '$table_name'";  
        $tableExists = $conn_product->query($checkTableSql);

        if ($tableExists && $tableExists->num_rows > 0) {
            echo "Error: Table with this name already exists.";
        } else {
            // Create the initial table with only the id field in the camxian_table database
            $createTableSql = "CREATE TABLE `$table_name` (
                id INT AUTO_INCREMENT PRIMARY KEY
            )";

            if ($conn_product->query($createTableSql) === TRUE) {
                // Define the predefined fields with their corresponding types
                $predefinedFields = [
                    'barcode' => 'INT',
                    'series' => 'VARCHAR(255)',
                    'model' => 'VARCHAR(255)',
                    'resolution' => 'VARCHAR(255)',
                    'description' => 'TEXT',
                    'quantity' => 'INT'
                ];

                $fields_sql = [];
                if (!empty($fields)) {
                    $fields = array_unique($fields);
                    foreach ($fields as $field) {
                        if (isset($predefinedFields[$field])) {
                            $field_name = preg_replace("/[^a-zA-Z0-9_]/", "", $field);
                            $field_type = $predefinedFields[$field];

                            $fields_sql[] = "ADD COLUMN `$field_name` $field_type"; 
                        }
                    }

                    if (!empty($fields_sql)) {
                        $fields_sql_str = implode(", ", $fields_sql); 
                        $alterTableSql = "ALTER TABLE `$table_name` $fields_sql_str"; 

                        if ($conn_product->query($alterTableSql) === TRUE) {
                            header("Location: dashboard.php");
                            exit();
                        } else {
                            echo "Error adding fields: " . $conn_product->error;
                        }
                    } else {
                        echo "No valid fields selected.";
                    }
                } else {
                    echo "Table created with only the ID field.";
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                echo "Error creating table: " . $conn_product->error;
            }
        }
    } else {
        echo "Card name cannot be empty.";
    }

    // Send the response as JSON
    echo json_encode($response);
    exit();
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
        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
                <img src="img/logo_solo.png" alt="Company Logo" class="Picture">
            </header>
            <section class="stats" id="card-section">
                <?php if ($tables->num_rows > 0) : ?>
                    <?php while ($table = $tables->fetch_array()) : ?>
                        <div class="card">
                            <!-- Link to view_table.php with the table name as a query parameter -->
                            <a class="link" href="view_table.php?table=<?php echo urlencode($table[0]); ?>">
                                <?php echo htmlspecialchars(ucfirst(str_replace('table_', '', $table[0]))); ?>
                            </a>
                            <button onclick="deleteTable('<?php echo $table[0]; ?>')">Delete</button>
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>No tables found.</p>
                <?php endif; ?>

                <a href="javascript:void(0);" onclick="showModal()" class="card add-card">
                    <p>ADD</p>
                </a>
            </section>
        </div>
    </div>

    <!-- Modal for adding a new card -->
    <div id="addCardModal" class="addmodal" style="display: none;">
        <div class="modal-content">
            <h2>Enter Table Name and Fields</h2>
            <form method="POST">
                <input type="text" name="card_name" placeholder="Table Name" required>
                <div id="fields-container">
                    <div class="field-item">
                        <select name="fields[]" required>
                            <option value="barcode">Barcode</option>
                            <option value="series">Series</option>
                            <option value="model">Model</option>
                            <option value="resolution">Resolution</option>
                            <option value="description">Description</option>
                            <option value="quantity">Quantity</option>
                        </select>
                        <button type="button" onclick="removeField(this)">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addField()">Add More Fields</button>
                <button type="submit" name="add_card" class="save-btn">Create Table</button>
                <button type="button" onclick="closeModal1()" class="cancel-btn">Cancel</button>
            </form>
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

    <script src="js/script.js"></script>

    <script>
        function showModal() {
            document.getElementById('addCardModal').style.display = 'block';
        }

        function closeModal1() {
            document.getElementById('addCardModal').style.display = 'none';
        }

        function addField() {
            const fieldsContainer = document.getElementById('fields-container');
            const newField = document.createElement('div');
            newField.classList.add('field-item');
            newField.innerHTML = ` 
                <select name="fields[]" required>
                    <option value="barcode">Barcode</option>
                    <option value="series">Series</option>
                    <option value="model">Model</option>
                    <option value="resolution">Resolution</option>
                    <option value="description">Description</option>
                    <option value="quantity">Quantity</option>
                </select>
                <button type="button" onclick="removeField(this)">Remove</button>
            `;
            fieldsContainer.appendChild(newField);
        }

        function removeField(button) {
            button.parentElement.remove();
        } 

        
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        

        // Handle table creation via AJAX
        document.getElementById('addTableForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'dashboard.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const feedback = document.getElementById('formFeedback');

                    if (response.success) {
                        feedback.innerHTML = `<p style="color: green;">${response.success}</p>`;
                        setTimeout(() => {
                            location.reload(); // Reload to show new table
                        }, 1000);
                    } else {
                        feedback.innerHTML = `<p style="color: red;">${response.error}</p>`;
                    }
                }
            };
            xhr.send(formData);
        });


        function deleteTable(tableName) {
            // Send an AJAX request to delete_table.php with the table name as a query parameter
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete_table.php?table=' + encodeURIComponent(tableName), true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        // Show error message on the dashboard
                        alert(response.error);
                    } else if (response.success) {
                        // Refresh the page or handle the successful deletion
                        alert(response.success);
                        location.reload(); // This will reload the page to reflect changes
                    }
                }
            };
            xhr.send();
        }

    </script>
</body>
</html>
