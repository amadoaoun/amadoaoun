<?php
include 'db.php';

// Query count grouped by profession
$stmt = $db->query("
    SELECT profession, COUNT(*) AS count
    FROM users
    GROUP BY profession
    ORDER BY count DESC
");

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'profession' => $row['profession'] ?: 'Unknown',
        'count' => $row['count']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);