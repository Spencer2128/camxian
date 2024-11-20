<?php
// Database connection
include('tb_connection.php');

// Fetch the search query and table name from the form (GET parameters)
$search_query = isset($_GET['query']) ? trim($_GET['query']) : ''; // Trim whitespace
$table_name = isset($_GET['table']) ? $_GET['table'] : '';         // Get the selected table

// Check if a search query is provided
if (empty($search_query)) {
    die("Error: No search query provided.");
}

// Escape the search query to prevent SQL injection
$search_query = $conn->real_escape_string($search_query);

// Function to display table headers
function displayHeaders($columns) {
    echo "<thead><tr>";
    foreach ($columns as $column) {
        echo "<th>" . ucfirst(str_replace('_', ' ', $column)) . "</th>";
    }
    echo "</tr></thead>";
}

// Function to display search results in a table format
function displayResults($result, $columns) {
    echo "<table border='1'>";
    displayHeaders($columns);
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}

// Search function that accepts table name and search query
function searchTable($conn, $table_name, $search_query) {
    // Verify that the table exists
    $table_check_result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($table_check_result->num_rows == 0) {
        echo "<p>Error: Table '$table_name' does not exist.</p>";
        return;
    }

    // Fetch columns for the table
    $columns_result = $conn->query("SHOW COLUMNS FROM `$table_name`");
    if (!$columns_result) {
        echo "<p>Error fetching columns for table: " . $conn->error . "</p>";
        return;
    }

    // Build the WHERE clause dynamically for each column
    $search_conditions = [];
    $columns = [];
    while ($column_row = $columns_result->fetch_assoc()) {
        $column = $column_row['Field'];
        $columns[] = $column;

        // Exact match for 'id' column if numeric, partial match for others
        if ($column === 'id' && is_numeric($search_query)) {
            $search_conditions[] = "`$column` = '$search_query'";
        } else {
            $search_conditions[] = "`$column` LIKE '%$search_query%'";
        }
    }

    if (!empty($search_conditions)) {
        $where_clause = implode(" OR ", $search_conditions);
        $sql = "SELECT * FROM `$table_name` WHERE $where_clause";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<h2>Results in Table: $table_name</h2>";
            displayResults($result, $columns);
        } else {
            echo "<p>No results found in table: $table_name</p>";
        }
    }
}

// Main logic to handle single or multi-table search
if (empty($table_name)) {
    // Fetch all tables in the database
    $tables_result = $conn->query("SHOW TABLES");
    if (!$tables_result) {
        die("Error fetching tables: " . $conn->error);
    }

    // Iterate through each table and search
    while ($table_row = $tables_result->fetch_array()) {
        searchTable($conn, $table_row[0], $search_query);
    }
} else {
    // If a specific table is provided, search only within that table
    searchTable($conn, $table_name, $search_query);
}

// Close the database connection
$conn->close();
?>
