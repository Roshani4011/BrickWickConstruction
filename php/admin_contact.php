<?php
include 'db_config.php'; // Path to database config

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $plotLocation = $_POST['plotLocation'];
    $plotSizeSqft = $_POST['plotSizeSqft'];

    try {
        $sql = "INSERT INTO contacts (name, phone, plot_location, plot_size_sqft) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $phone, $plotLocation, $plotSizeSqft]);
        echo "success";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

} else {
    echo "error: Invalid request.";
}
?>