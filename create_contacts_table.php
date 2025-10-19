<?php
include __DIR__ . '/php/db_config.php';

try {
    // Check if table already exists
    $tableCheck = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'contacts'");
    $tableExists = $tableCheck->fetchColumn() > 0;
    
    if ($tableExists) {
        echo "The contacts table already exists. Do you want to drop it and recreate? <br>";
        echo "<a href='?action=drop'>Yes, drop and recreate</a> | <a href='?action=no'>No, keep it</a>";
        
        if (isset($_GET['action']) && $_GET['action'] === 'drop') {
            $conn->exec("DROP TABLE contacts");
            echo "<br>Table dropped successfully.<br>";
            $tableExists = false;
        } else if (isset($_GET['action']) && $_GET['action'] === 'no') {
            echo "<br>Table kept unchanged.<br>";
            exit;
        } else {
            exit;
        }
    }
    
    if (!$tableExists) {
        // Create the table with the structure you provided
        $createTable = "CREATE TABLE contacts (
            id INT PRIMARY KEY IDENTITY(1,1),
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            plot_location VARCHAR(255),
            plot_size_sqft INT,
            timestamp DATETIME DEFAULT GETDATE()
        )";
        
        $conn->exec($createTable);
        echo "Contacts table created successfully!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>