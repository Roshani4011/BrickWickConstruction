<?php
// services.php
session_start(); // Start session for CSRF token
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$file_path = '../db_config.php';
if (file_exists($file_path)) {
    include $file_path;
} else {
    die("db_config.php does NOT exist!");
}

$recordsPerPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;

try {
    $totalRecordsQuery = "SELECT COUNT(*) FROM cost_estimations";
    $totalRecordsStmt = $conn->query($totalRecordsQuery);
    $totalRecords = $totalRecordsStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);

    $sql = "SELECT * FROM cost_estimations ORDER BY id ASC OFFSET $offset ROWS FETCH NEXT $recordsPerPage ROWS ONLY";
    $stmt = $conn->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging (remove for production)
    // print_r($results);

} catch (PDOException $e) {
    die("Error retrieving data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cost Estimations Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f8f8f8; }
        h2 { text-align: center; margin-bottom: 30px; color: #d9534f; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); }
        table { border-collapse: collapse; width: 100%; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); background-color: white; border-radius: 8px; overflow: hidden; }
        th, td { border: 1px solid #ddd; padding: 15px; text-align: left; }
        th { background-color: #d9534f; color: white; font-weight: bold; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        @media (max-width: 768px) { table, thead, tbody, th, td, tr { display: block; } thead tr { position: absolute; top: -9999px; left: -9999px; } tr { border: 1px solid #ccc; margin-bottom: 10px; } td { border: none; border-bottom: 1px solid #eee; position: relative; padding-left: 50%; } td:before { position: absolute; top: 6px; left: 6px; width: 45%; padding-right: 10px; white-space: nowrap; font-weight: bold; content: attr(data-label); color: #d9534f; } }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a { display: inline-block; padding: 8px 16px; text-decoration: none; border: 1px solid #ddd; background-color: white; color: black; }
        .pagination a.active { background-color: #d9534f; color: white; border: 1px solid #d9534f; }
        .pagination a:hover:not(.active) { background-color: #f0f0f0; }
        .delete-button { background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Cost Estimations Records</h2>
    <?php if (!empty($results)) { ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($results[0]) as $columnName) { echo "<th>" . $columnName . "</th>"; } ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <?php foreach ($row as $key => $value) { echo "<td data-label='" . $key . "'>" . $value . "</td>"; } ?>
                        <td>
                            <form action='delete_record.php' method='post'>
                                <input type='hidden' name='id' value='<?php echo $row['id']; ?>'>
                                <?php $csrf_token = bin2hex(random_bytes(32)); $_SESSION['csrf_token'] = $csrf_token; ?>
                                <input type='hidden' name='csrf_token' value='<?php echo $csrf_token; ?>'>
                                <button type='submit' class='delete-button' onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No records found.</p>
    <?php } ?>
    <div class="pagination">
        <?php if ($totalPages > 1) { ?>
            <?php if ($page > 1) { echo "<a href='services.php?page=" . ($page - 1) . "'>&laquo;</a>"; } ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) { $activeClass = ($i == $page) ? 'active' : ''; echo "<a href='services.php?page=" . $i . "' class='" . $activeClass . "'>" . $i . "</a>"; } ?>
            <?php if ($page < $totalPages) { echo "<a href='services.php?page=" . ($page + 1) . "'>&raquo;</a>"; } ?>
        <?php } ?>
    </div>
</body>
</html>