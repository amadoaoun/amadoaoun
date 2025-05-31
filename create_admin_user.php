<?php
include 'db.php'; // your database connection file

// Define admin user data
$name = 'Admin User';
$email = 'amado@ariaatech.com';
$password_plain = 'aoun'; // Choose a strong password here
$role = 'admin';

// Hash the password securely
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

try {
    // Check if email already exists to avoid duplicates
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo "Admin user with this email already exists.";
        exit;
    }
    
    // Insert new admin user
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $password_hashed, $role]);

    echo "Admin user created successfully!";

} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage();
}
