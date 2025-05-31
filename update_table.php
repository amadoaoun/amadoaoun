<?php
include 'db.php';

// Start transaction for safety
$db->beginTransaction();

try {
    // Create a new temporary table with the password column
    $db->exec("
        CREATE TABLE users_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT UNIQUE,
            mobile TEXT,
            nationality TEXT,
            profession TEXT,
            password TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Copy data from old users to new users_new
    $db->exec("
        INSERT INTO users_new (id, name, email, mobile, nationality, profession, created_at)
        SELECT id, name, email, mobile, nationality, profession, created_at FROM users;
    ");

    // Drop the old table
    $db->exec("DROP TABLE users;");

    // Rename new table to old table name
    $db->exec("ALTER TABLE users_new RENAME TO users;");

    // Commit changes
    $db->commit();

    echo "Table updated successfully!";
} catch (Exception $e) {
    $db->rollBack();
    echo "Failed to update table: " . $e->getMessage();
}
?>
