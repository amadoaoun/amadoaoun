<?php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/crud.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add year_of_birth column
    $db->exec("ALTER TABLE users ADD COLUMN year_of_birth INTEGER;");

    echo "Column 'year_of_birth' added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>