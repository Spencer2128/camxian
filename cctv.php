<?php
// Database connection
include('db_connection.php');

// Handle form submission via AJAX (if adding or updating a product)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $series = $_POST['series'];
    $model = $_POST['model'];
    $image_url = $_POST['image_url'];
    $resolution = $_POST['resolution'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    // Check if the product already exists in the database
    $check_sql = "SELECT * FROM products WHERE series = '$series' AND model = '$model'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Product exists, update the quantity
        $row = $check_result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;

        // Update the existing product's quantity
        $update_sql = "UPDATE products SET quantity = '$new_quantity' WHERE series = '$series' AND model = '$model'";
        if ($conn->query($update_sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Product quantity updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update product quantity']);
        }
    } else {
        // Product does not exist, insert a new product
        $insert_sql = "INSERT INTO products (series, model, image_url, resolution, description, quantity)
                       VALUES ('$series', '$model', '$image_url', '$resolution', '$description', '$quantity')";

        if ($conn->query($insert_sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'New product added']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add product']);
        }
    }
    exit();
}


// Fetch existing product data
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System Dashboard</title>
    <link rel="stylesheet" href="./css/cctv.css?v=1"> <!-- Link to external CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Inventory System</h2>
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
            <!-- Search Bar -->
            <header>
                <input type="text" id="searchQuery" placeholder="Search..." class="search-input">
            </header>

            <!-- Listings Section -->
            <div class="listing-section">
                <h2>Listings</h2>
                <button class="create-draft" onclick="openModal()">Add</button>

                <!-- Table -->
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Series</th>
                            <th>Model</th>
                            <th>Product Image</th>
                            <th>Resolution</th>
                            <th>Description</th>
                            <th>QTY</th>
                        </tr>
                    </thead>
                    <tbody id="product-list">
                        <?php
                        // Loop through and display existing products
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['series'] . "</td>";
                                echo "<td><img src='" . $row['image_url'] . "' alt='Product Image'></td>";
                                echo "<td>" . $row['model'] . "</td>";
                                echo "<td>" . $row['resolution'] . "</td>";
                                echo "<td>" . $row['description'] . "</td>";
                                echo "<td>" . $row['quantity'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal for Adding a Product -->
<div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add New Product</h2>
            <form id="add-product-form" enctype="multipart/form-data">
                <label for="series">Series:</label>
                <input type="text" name="series" id="series" required><br>

                <label for="model">Model:</label>
                <input type="text" name="model" id="model" required><br>

                <label for="image_url">Image URL:</label>
                <input type="text" name="image_url" id="image_url" required><br>

                <label for="resolution">Resolution:</label>
                <input type="text" name="resolution" id="resolution" required><br>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea><br>

                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" required><br>

                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
  <div id="logoutModal" class="modal">
        <div class="modal-content">
           <br>
            <p>Are you sure you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeModal()">No</button>
        </div>
    </div>



    <!-- Link to external JS file -->
    <script src="js/script.js" defer></script>
    <script src="js/search.js" defer></script>
    <script src="js/modalscript.js" defer></script>
</body>
</html>
