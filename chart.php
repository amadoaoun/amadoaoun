<?php
include 'db.php';

// Query registrations grouped by date
$stmt = $db->query("
    SELECT DATE(created_at) AS reg_date, COUNT(*) AS count
    FROM users
    GROUP BY reg_date
    ORDER BY reg_date ASC
");

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = [
        'date' => $row['reg_date'],
        'count' => $row['count']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
