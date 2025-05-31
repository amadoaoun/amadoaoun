<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['column'], $data['value'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$id = (int)$data['id'];
$column = $data['column'];
$value = trim($data['value']);

$allowed = ['name', 'email', 'mobile', 'nationality', 'profession'];

if (!in_array($column, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid column']);
    exit;
}

if ($column === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET $column = ? WHERE id = ?");
    $stmt->execute([$value, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}