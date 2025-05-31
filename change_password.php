<?php
session_start();
include 'db.php';

// Check if user is logged in (simple example; adjust according to your auth system)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$current_password || !$new_password || !$confirm_password) {
        $error = "Please fill all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Fetch current hashed password from DB
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } else {
            // Hash new password and update DB
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->execute([$new_hashed, $_SESSION['user_id']]);
            $success = "Password changed successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Change Password</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include 'header.php'; ?>

<div class="form-container">
    <h2>Change Password</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="current_password">Current Password *</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password *</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm New Password *</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
