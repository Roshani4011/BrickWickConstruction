<?php
// db_config.php
// Cleaned: No stray output, strict error handling.

// SQL Server configuration
$servername = "ROSHNI\SQLEXPRESS";
$database = "construction_db";
$uid = "Roshani";
$pwd = "Roshani@4011";

// Initialize $conn to null
$conn = null;

try {
    // SQL Server connection using PDO
    $conn = new PDO("sqlsrv:Server=$servername;Database=$database", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // !!! CRITICAL: NO output (echo, print, or blank space) on success !!!
    
} catch (PDOException $e) {
    // If the CONNECTION fails, output a strict text error and stop.
    // This allows the JS to read a clear error string.
    die("FATAL_ERROR: Database Connection Failed: " . $e->getMessage()); 
}
?>