<?php
// submit_contact.php
// Corrected: Uses clear text prefixes for success/error.

// Set error reporting for development (remove on production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// The file path assumes db_config.php is in a subdirectory named 'php'
require_once 'php/db_config.php'; 

// Set the header to ensure the browser knows the response type
header('Content-Type: text/plain'); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('ERROR: Method Not Allowed'); 
}

// Get and sanitize form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$plotLocation = isset($_POST['plotLocation']) ? trim($_POST['plotLocation']) : '';
// Use 0 if plotSizeSqft is missing or invalid
$plotSizeSqft = isset($_POST['plotSizeSqft']) ? intval($_POST['plotSizeSqft']) : 0; 

// Validation
if (empty($name) || empty($phone)) {
    die('ERROR: Name and phone are required.');
}

// Check if the connection object is available from db_config.php
if (!isset($conn) || $conn === null) {
    die('FATAL_ERROR: System configuration failed - DB object is missing.');
}

try {
    // !!! CRITICAL: Double-check 'contacts' table and all column names !!!
   $sql = "INSERT INTO contacts (name, phone, plot_location, plot_size_sqft) 
         VALUES (:name, :phone, :plot_location, :plot_size_sqft)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([
        ':name' => $name,
        ':phone' => $phone,
        ':plot_location' => $plotLocation,
        ':plot_size_sqft' => $plotSizeSqft
    ])) {
        // Success response
        die('SUCCESS: Inquiry submitted successfully.'); // New prefix for JS to check
    } else {
        // Fallback for execution failure
        $errorInfo = $stmt->errorInfo();
        error_log("DB execution failed: " . print_r($errorInfo, true));
        die('ERROR: Database execution failed. Check server logs.');
    }
    
} catch (PDOException $e) {
    // !!! THIS IS THE CATCH BLOCK THAT WILL FINALLY REVEAL THE SQL ERROR !!!
    $detailed_error = $e->getMessage();
    error_log("PDO Exception: " . $detailed_error);

    // Output the detailed SQL error to the client
    die("ERROR: SQL Query Failed: " . $detailed_error); 
}
?>