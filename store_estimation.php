<?php
// store_estimation.php - FINAL PRODUCTION CODE (Secure PDO SQL Server)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration & Logging Setup
$log_file = 'logs/form_submissions.log';
if (!file_exists('logs')) {
    mkdir('logs', 0755, true); // Create directory if it doesn't exist
}

function write_log($message) {
    global $log_file;
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

header('Content-Type: application/json');

// 1. CRITICAL SECURITY: Check HTTP Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    write_log("Method Not Allowed: Received " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Only POST requests are allowed.']);
    die();
}

try {
    write_log("Processing form submission...");

    // Include database configuration (assuming it defines a PDO object named $conn)
    require_once 'cost_estimator_db_config.php';

    if (!isset($conn) || !($conn instanceof PDO)) {
        throw new Exception("Database connection failed or is not available.");
    }

    // 2. CRITICAL SECURITY: Data Retrieval and Validation (Sanitize All Inputs)
    // Use filter_var for type validation (INT/FLOAT/EMAIL) and htmlspecialchars for string sanitization
    $plot_size      = filter_var($_POST['plot_size'] ?? 0, FILTER_VALIDATE_INT);
    $plot_location  = htmlspecialchars($_POST['plot_location'] ?? '', ENT_QUOTES, 'UTF-8');
    $package        = htmlspecialchars($_POST['package'] ?? '0', ENT_QUOTES, 'UTF-8');
    $floors         = filter_var($_POST['floors'] ?? 0, FILTER_VALIDATE_INT);
    $name           = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email          = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone          = htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $lift           = ($_POST['lift'] ?? 'no') === 'yes' ? 'yes' : 'no'; 
    $estimated_cost = filter_var($_POST['estimated_total'] ?? 0, FILTER_VALIDATE_FLOAT); // CRITICAL: Retrieving client-calculated cost
    
    $send_email = 'yes'; // Default value, modify if a checkbox is used

    // 3. Final Validation Check
    if (!$plot_size || !$plot_location || !$package || !$floors || !$name || !$email || !$phone || !$estimated_cost) {
        throw new Exception("Required fields or the estimated cost are missing or invalid.");
    }
    
    write_log("Data validated: Name=$name, Email=$email, Cost=$estimated_cost");

    // 4. CRITICAL SECURITY: Prepared Statement
    // Ensure 'estimated_cost' column exists in your 'cost_estimations' table (DECIMAL/NUMERIC type)
    $sql = "INSERT INTO cost_estimations (plot_size, plot_location, package, floors, name, email, phone, lift, estimated_cost, send_email, submission_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())"; // GETDATE() is common in SQL Server
    $stmt = $conn->prepare($sql);

    // 5. Execute with Parameters
    $success = $stmt->execute([
        $plot_size, $plot_location, $package, $floors, $name, $email, $phone, $lift, $estimated_cost, $send_email
    ]);

    if (!$success) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Database error: " . ($errorInfo[2] ?? "Unknown error. SQLSTATE: " . ($errorInfo[0] ?? 'N/A')));
    }

    write_log("Successfully inserted data for: $name ($email) with cost: $estimated_cost");

    // 6. Success Response
    echo json_encode(['status' => 'success', 'message' => 'Thank you! Your project estimate has been submitted successfully.', 'estimated_cost' => $estimated_cost]);
    die();

} catch (Exception $e) {
    // 7. Robust Error Handling
    $errorMsg = "Error processing form: " . $e->getMessage();
    write_log("ERROR: $errorMsg");

    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'There was a problem submitting your form.']);
    die();
}