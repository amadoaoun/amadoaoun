<?php
include __DIR__ . '/db.php';

$q = $_GET['q'] ?? '';
$results = [];

if ($q !== '') {
    $stmt = $db->prepare("SELECT name, email FROM users WHERE name LIKE ? OR email LIKE ? OR mobile LIKE ? OR nationality LIKE ? LIMIT 10");
    $stmt->execute(["%$q%", "%$q%", "%$q%", "%$q%"]);
    while ($row = $stmt->fetch()) {
        $results[] = $row['name'] . ' (' . $row['email'] . ')';
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>
