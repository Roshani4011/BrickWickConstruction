<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../db_config.php';

if (isset($_GET['id'])) {
    $projectId = $_GET['id'];

    try {
        $sql = "DELETE FROM projects WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $projectId);
        $stmt->execute();

        header('Location: projects.php');
        exit;
    } catch (PDOException $e) {
        echo "Error deleting project: " . $e->getMessage();
        exit;
    }
} else {
    echo "Project ID not provided.";
    exit;
}
?>