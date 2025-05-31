<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_ids'])) {
    $ids = $_POST['user_ids'];

    // Sanitize and prepare placeholders for SQL IN clause
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    header('Location: index.php?msg=deleted');
    exit;
} else {
    header('Location: index.php?msg=noselection');
    exit;
}