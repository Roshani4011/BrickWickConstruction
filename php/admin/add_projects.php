<?php // add_projects.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
var_dump($_SERVER['REQUEST_METHOD']); // Check request method
echo "<pre>"; print_r($_POST); echo "</pre>"; // Check POST data

include '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'];
    $clientName = $_POST['client_name'];
    $projectType = $_POST['project_type'];
    $location = $_POST['location'];
    $budget = $_POST['budget'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "Invalid image type.";
        }
    }
    var_dump($_FILES['image']); //check image upload

    try {
        // Use the correct column name 'title'
        $sql = "INSERT INTO projects (title, client_name, project_type, location, budget, start_date, end_date, status, description, images) VALUES (:project_name, :client_name, :project_type, :location, :budget, :start_date, :end_date, :status, :description, :image)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':project_name', $projectName);
        $stmt->bindParam(':client_name', $clientName);
        $stmt->bindParam(':project_type', $projectType);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $imagePath);
        $stmt->execute();

        header('Location: projects.php');
        exit;
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage() . "<br>";
        echo "SQL: " . $sql;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Project</title>
    <link rel="stylesheet" href="projects.css">
</head>
<body>
    <div class="container">
        <h2>Add New Project</h2>
        <form method="post" action="add_projects.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" required>
            </div>
            <div class="form-group">
                <label for="client_name">Client Name:</label>
                <input type="text" id="client_name" name="client_name" required>
            </div>
            <div class="form-group">
                <label for="project_type">Project Type:</label>
                <select id="project_type" name="project_type">
                    <option value="Construction">Construction</option>
                    <option value="Interior">Interior</option>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="budget">Budget:</label>
                <input type="number" id="budget" name="budget">
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" id="image" name="image">
            </div>
            <button type="submit" class="btn">Add Project</button>
        </form>
    </div>
</body>
</html>