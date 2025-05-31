<?php
include 'header.php';
include 'db.php';


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== FALSE) {
        // Skip header row
        fgetcsv($handle);

        $count = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Assuming CSV columns: name,email,mobile,nationality,profession
            list($name, $email, $mobile, $nationality, $profession) = $data;

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue; // skip invalid email
            }

            // Check if user exists by email
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                // Update existing user
                $update = $db->prepare("UPDATE users SET name = ?, mobile = ?, nationality = ?, profession = ? WHERE email = ?");
                $update->execute([$name, $mobile, $nationality, $profession, $email]);
            } else {
                // Insert new user
                $insert = $db->prepare("INSERT INTO users (name, email, mobile, nationality, profession, created_at) VALUES (?, ?, ?, ?, ?, NOW())
");
                $insert->execute([$name, $email, $mobile, $nationality, $profession]);
            }
            $count++;
        }
        fclose($handle);
        $message = "Successfully imported/updated $count users.";
    } else {
        $message = "Failed to open the uploaded CSV file.";
    }
}
?>

<div class="form-container" style="max-width: 500px; margin-top: 50px;">
    <h2>Import Users from CSV</h2>

    <?php if ($message): ?>
        <div class="error-msg" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>

        <button type="submit" style="margin-top: 15px;">Import CSV</button>
    </form>

    <p style="margin-top: 20px; font-size: 14px; color: #555;">
        CSV format: <strong>name,email,mobile,nationality,profession</strong><br>
        Existing users will be updated by email; new users will be added.
    </p>
</div>

<?php include 'footer.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Site Title</title>
  <link rel="stylesheet" href="style.css" />
  <!-- add any other meta tags or scripts you use -->
</head>
<body>