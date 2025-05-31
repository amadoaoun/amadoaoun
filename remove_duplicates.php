<?php
include 'db.php';  // make sure path is correct

try {
    $db->exec("
        DELETE FROM users
        WHERE id NOT IN (
            SELECT MIN(id)
            FROM users
            GROUP BY email
        );
    ");
    echo "Duplicate emails removed successfully.";
} catch (PDOException $e) {
    echo "Error removing duplicates: " . $e->getMessage();
}
?>