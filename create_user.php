<?php
include 'db.php';

// User details to create
$name = 'Admin User';
$email = 'admin@example.com';
$password_plain = 'securepassword';
$mobile = '1234567890';          // Optional, can be empty
$nationality = 'Lebanese';       // Optional, can be empty
$profession = 'Administrator';   // Optional, can be empty

// Hash the password securely
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

try {
    // Adjust SQL to include password and all fields properly
    $stmt = $db->prepare("INSERT INTO users (name, email, password, mobile, nationality, profession, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

    // Execute with all parameters in order
    $stmt->execute([$name, $email, $password_hashed, $mobile, $nationality, $profession]);

    echo "User created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>