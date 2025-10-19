<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Page is loading...<br>";

session_start();

echo "Session started...<br>";

// Check admin login - REMOVED for testing
// if (!isset($_SESSION['admin_id'])) {
//     echo "Not logged in. Continuing anyway for debug...<br>";
// }

echo "Trying to include db_config.php...<br>";

// Try to include database config
if (file_exists('../db_config.php')) {
    include '../db_config.php';
    echo "db_config.php found and included!<br>";
} elseif (file_exists('db_config.php')) {
    include 'db_config.php';
    echo "db_config.php found in current directory!<br>";
} elseif (file_exists('php/db_config.php')) {
    include 'php/db_config.php';
    echo "db_config.php found in php directory!<br>";
} else {
    die("ERROR: Cannot find db_config.php. Please check the path.<br>");
}

echo "<h2>Database Connection Test</h2>";

// Check if connection works
if (isset($conn) && $conn) {
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection object not found</p>";
    die("Cannot proceed without database connection.");
}

// Check table structure
try {
    echo "<h3>Contacts Table Structure:</h3>";
    
    // For MySQL/MariaDB
    $sql = "DESCRIBE contacts";
    $stmt = $conn->query($sql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo "<p style='color: red;'>✗ Contacts table does not exist!</p>";
    } else {
        echo "<p style='color: green;'>✓ Contacts table exists!</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #333; color: white;'><th>Column Name</th><th>Data Type</th></tr>";
        foreach ($columns as $col) {
            $field = $col['Field'] ?? $col['COLUMN_NAME'] ?? 'Unknown';
            $type = $col['Type'] ?? $col['DATA_TYPE'] ?? 'Unknown';
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($field) . "</strong></td>";
            echo "<td>" . htmlspecialchars($type) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error checking table structure: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Check if there are any contacts
try {
    echo "<h3>Contacts in Database:</h3>";
    $sql = "SELECT * FROM contacts LIMIT 5";
    $stmt = $conn->query($sql);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total contacts found: <strong>" . count($contacts) . "</strong></p>";
    
    if (!empty($contacts)) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        // Print headers
        echo "<tr style='background: #333; color: white;'>";
        foreach (array_keys($contacts[0]) as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
        echo "<th>DELETE ACTION</th>";
        echo "</tr>";
        
        // Print rows with delete button
        foreach ($contacts as $contact) {
            echo "<tr>";
            foreach ($contact as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            // Add delete button
            $id = $contact['id'] ?? 'NO_ID';
            echo "<td>";
            echo '<a href="contact.php?delete_id=' . $id . '" ';
            echo 'style="background-color: #dc3545; color: white; padding: 5px 10px; ';
            echo 'text-decoration: none; border-radius: 4px; display: inline-block;">';
            echo 'DELETE</a>';
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No contacts in the database yet.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error fetching contacts: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>✓ Debug Complete</h3>";
echo "<p><a href='contact.php'>Go to contact.php</a></p>";
?>