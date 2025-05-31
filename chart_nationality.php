<?php
include __DIR__ . '/db.php';

// Get count of users grouped by nationality
$stmt = $db->prepare("SELECT nationality, COUNT(*) as count FROM users GROUP BY nationality ORDER BY count DESC");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
