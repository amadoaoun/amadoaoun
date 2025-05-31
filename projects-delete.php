<?php
session_start();
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Optional: fetch project and delete image
$stmt = $db->prepare("SELECT photo FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if ($project && !empty($project['photo']) && file_exists($project['photo'])) {
  unlink($project['photo']);
}

// Delete the project
$delete = $db->prepare("DELETE FROM projects WHERE id = ?");
$delete->execute([$id]);

header("Location: projects.php");
exit;
