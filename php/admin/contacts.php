<?php
session_start();

// Security check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include '../db_config.php';

$error = '';
$success_message = '';

// Handle DELETE
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id_to_delete = intval($_GET['delete_id']);
    
    try {
        $sql = "DELETE FROM contacts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_to_delete]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Contact deleted successfully!";
        } else {
            $_SESSION['error'] = "Contact not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    
    header("Location: contact.php");
    exit;
}

// Get messages from session
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Fetch contacts
try {
    $sql = "SELECT id, name, phone, plot_location, plot_size_sqft, timestamp FROM contacts ORDER BY timestamp DESC";
    $stmt = $conn->query($sql);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching contacts: " . $e->getMessage();
    $contacts = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contacts Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { padding: 20px; background-color: #ffffffff; }
        .container { background: white; padding: 30px; border-radius: 8px; }
        .delete-btn { 
            background-color: #dc3545 !important; 
            color: white !important;
            border: none !important;
            padding: 5px 15px !important;
            border-radius: 4px !important;
            text-decoration: none !important;
            display: inline-block !important;
            font-size: 14px !important;
        }
        .delete-btn:hover { 
            background-color: #c82333 !important; 
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Contacts Management</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($contacts)): ?>
            <p><strong>Total Contacts: <?php echo count($contacts); ?></strong></p>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Plot Location</th>
                        <th>Plot Size (Sqft)</th>
                        <th>Timestamp</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contact['id']); ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                        <td><?php echo htmlspecialchars($contact['plot_location'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($contact['plot_size_sqft'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($contact['timestamp']); ?></td>
                        <td>
                            <a href="contact.php?delete_id=<?php echo $contact['id']; ?>" 
                               class="delete-btn"
                               onclick="return confirm('Delete contact: <?php echo htmlspecialchars($contact['name']); ?>?');">
                                DELETE
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">No contacts found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>