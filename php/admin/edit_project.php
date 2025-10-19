<?php // edit_project.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit; 
}
include '../db_config.php';

if (isset($_GET['id'])) {
    $projectId = $_GET['id'];

    // Fetch the project data for editing
    try {
        $sql = "SELECT * FROM projects WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $projectId);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            echo "Project not found.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error fetching project: " . $e->getMessage();
        exit;
    }

    // Handle form submission for updating the project
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

        // Image upload handling (similar to add_projects.php)
        $imagePath = $project['images']; // Keep the existing image if no new one is uploaded
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

        try {
            $sql = "UPDATE projects SET 
                    title = :project_name,
                    client_name = :client_name,
                    project_type = :project_type,
                    location = :location,
                    budget = :budget,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    description = :description,
                    images = :image 
                    WHERE id = :id";
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
            $stmt->bindParam(':id', $projectId);

            var_dump($stmt); // Debugging: Print the prepared statement

            $stmt->execute();

            header('Location: projects.php');
            exit;
        } catch (PDOException $e) {
            echo "Error updating project: " . $e->getMessage();
            exit;
        }
    }
} else {
    echo "Project ID not provided.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link rel="stylesheet" href="projects.css">
</head>
<body>
    <div class="container">
        <h2>Edit Project</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" value="<?php echo $project['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="client_name">Client Name:</label>
                <input type="text" id="client_name" name="client_name" value="<?php echo $project['client_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="project_type">Project Type:</label>
                <select id="project_type" name="project_type">
                    <option value="Construction" <?php if ($project['project_type'] === 'Construction') echo 'selected'; ?>>Construction</option>
                    <option value="Interior" <?php if ($project['project_type'] === 'Interior') echo 'selected'; ?>>Interior</option>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo $project['location']; ?>">
            </div>
            <div class="form-group">
                <label for="budget">Budget:</label>
                <input type="number" id="budget" name="budget" value="<?php echo $project['budget']; ?>">
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $project['end_date']; ?>">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Not Started" <?php if ($project['status'] === 'Not Started') echo 'selected'; ?>>Not Started</option>
                    <option value="In Progress" <?php if ($project['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="On Hold" <?php if ($project['status'] === 'On Hold') echo 'selected'; ?>>On Hold</option>
                    <option value="Completed" <?php if ($project['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo $project['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" id="image" name="image">
            </div>
            <button type="submit" class="btn">Update Project</button>
        </form>
    </div>
</body>
</html>