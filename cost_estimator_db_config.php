<?php
// cost_estimator_db_config.php - FINAL PRODUCTION CODE (SQL Server PDO)

// Database Parameters (CHANGE THESE)
$serverName = "ROSHNI\SQLEXPRESS"; // Your SQL Server Instance Name
$database = "construction_db";      // Your database name
$uid = "Roshani";                  // Your database user
$pwd = "Roshani@4011";             // Your database password

try {
    // CRITICAL: Ensure PDO_SQLSRV driver is installed on your PHP server
    $dsn = "sqlsrv:server=$serverName;Database=$database";
    
    $conn = new PDO($dsn, $uid, $pwd);
    
    // Set professional PDO attributes
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Throw exceptions on error
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);        // CRITICAL: Use native prepared statements
    
} catch (PDOException $e) {
    // Log the actual connection error details to a secure server log (not displayed to user)
    error_log(date('Y-m-d H:i:s') . " - Database connection error: " . $e->getMessage());

    // Display only a generic error to the user for security
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error. Database connection failure.']);
    die();
}
?>