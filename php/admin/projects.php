<?php // projects.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
echo "<pre>"; print_r($_SESSION); echo "</pre>"; //check session.

include '../db_config.php';
try {
    $sql = "SELECT * FROM projects";
    $stmt = $conn->query($sql);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>"; print_r($projects); echo "</pre>"; // Check fetched data
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
    echo "SQL: " . $sql;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Projects</title>
    <link rel="stylesheet" href="projects.css">
</head>
<body>
    <div class="container">
        <h2>Manage Projects</h2>
        <table class="projects-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Budget</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo $project['title']; ?></td>
                            <td><?php echo $project['client_name']; ?></td>
                            <td><?php echo $project['project_type']; ?></td>
                            <td><?php echo $project['location']; ?></td>
                            <td><?php echo $project['budget']; ?></td>
                            <td><?php echo $project['start_date']; ?></td>
                            <td><?php echo $project['end_date']; ?></td>
                            <td>
                                <span class="status <?php echo strtolower($project['status']); ?>">
                                    <?php echo $project['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_project.php?id=<?php echo $project['id']; ?>">Edit</a>
                                <a href="delete_project.php?id=<?php echo $project['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">No projects found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <h3>Add New Project</h3>
        <a href="add_projects.php" class="btn btn-primary">Add Project</a>
    </div>
</body>
</html>