<?php
include 'php\db_config.php'; // Use relative path

try {
    $conn->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>