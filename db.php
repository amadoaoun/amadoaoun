
<?php

try {
    $db = new PDO('mysql:host=localhost;dbname=user_management_db;charset=utf8mb4', 'root', ''); 
    // Replace 'your_database' with your actual MySQL database name
    // Replace 'root' and '' with your MySQL username and password if different

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

?>


