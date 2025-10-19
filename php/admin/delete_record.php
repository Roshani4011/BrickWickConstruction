<?php
// delete_record.php

include '../db_config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM cost_estimations WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo 'success';
    } catch (PDOException $e) {
        echo 'failure';
    }
} else {
    echo 'failure';
}
?>