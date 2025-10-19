<?php
//session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '..\db_config.php';

// Fetch total projects
$sql = "SELECT COUNT(*) FROM projects";
$stmt = $conn->query($sql);
$totalProjects = $stmt->fetchColumn();

// Fetch new services (using the corrected query)
$sql = "SELECT COUNT(*) FROM services WHERE created_at >= GETDATE() - 7";
$stmt = $conn->query($sql);
$newServices = $stmt->fetchColumn();

// Fetch revenue (assuming you have a 'budget' column in your projects table)
$sql = "SELECT SUM(budget) FROM projects";
$stmt = $conn->query($sql);
$totalRevenue = $stmt->fetchColumn();

// Pass the data to dashboard.php (using session variables)
$_SESSION['total_projects'] = $totalProjects;
$_SESSION['new_services'] = $newServices;
$_SESSION['total_revenue'] = $totalRevenue;

// Redirect to dashboard.php
//header('Location: dashboard.php');
//exit;
?>
