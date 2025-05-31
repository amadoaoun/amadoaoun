<?php
include __DIR__ . '/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $nationality = trim($_POST['nationality']);
    $profession = trim($_POST['profession']);
    $year_of_birth = trim($_POST['year_of_birth']);
    $role = $_POST['role'] ?? 'viewer';

    // Handle photo upload
    $photo = $user['photo']; // keep current photo if no new upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['photo']['tmp_name'];
        $originalName = basename($_FILES['photo']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowedExt)) {
            $newFileName = uniqid('photo_', true) . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                // Delete old photo if exists and is different from new
                if ($photo && file_exists($uploadDir . $photo)) {
                    unlink($uploadDir . $photo);
                }
                $photo = $newFileName;
            } else {
                $error = "Failed to upload photo.";
            }
        } else {
            $error = "Invalid photo format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (!$error) {
        // Check if email changed and is unique
        if ($email !== $user['email']) {
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$email, $id]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = "This email is already registered by another user.";
            }
        }
    }

    if (!$error && $name && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, nationality = ?, profession = ?, year_of_birth = ?, role = ?, photo = ? WHERE id = ?");
        $stmt->execute([$name, $email, $mobile, $nationality, $profession, $year_of_birth, $role, $photo, $id]);
        header("Location: index.php");
        exit;
    } elseif (!$error) {
        $error = "Invalid name or email.";
    }
}

$countries = [
     "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia",
    "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus",
    "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil",
    "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada",
    "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)",
    "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Democratic Republic of the Congo",
    "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador",
    "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. Swaziland)", "Ethiopia", "Fiji", "Finland",
    "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
    "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia",
    "Iran", "Iraq", "Ireland", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya",
    "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya",
    "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali",
    "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco",
    "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru",
    "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia",
    "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay",
    "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis",
    "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe",
    "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia",
    "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan",
    "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste",
    "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda",
    "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan",
    "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css" />
    <style>
      /* Optional inline style for photo preview */
      #photoPreview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 1px solid #ccc;
      }
    </style>
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<div class="form-container">
    <h2>Edit User</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="mobile">Mobile Number</label>
        <input type="text" id="mobile" name="mobile" value="<?= htmlspecialchars($user['mobile']) ?>">

        <label for="nationality">Nationality</label>
        <select id="nationality" name="nationality" required>
            <option value="">Select your nationality</option>
            <?php foreach ($countries as $country): ?>
                <option value="<?= htmlspecialchars($country) ?>" <?= $user['nationality'] === $country ? 'selected' : '' ?>><?= htmlspecialchars($country) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="profession">Profession</label>
        <input type="text" id="profession" name="profession" value="<?= htmlspecialchars($user['profession']) ?>">

        <label for="year_of_birth">Year of Birth</label>
        <input type="text" id="year_of_birth" name="year_of_birth" placeholder="Select Year" autocomplete="off" value="<?= htmlspecialchars($user['year_of_birth']) ?>" readonly>

        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
          <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>Editor</option>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <label for="photo">Profile Photo</label>
        <?php if (!empty($user['photo']) && file_exists(__DIR__ . '/uploads/' . $user['photo'])): ?>
          <img id="photoPreview" src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Current Photo" />
        <?php else: ?>
          <img id="photoPreview" src="https://via.placeholder.com/80?text=No+Photo" alt="No Photo" />
        <?php endif; ?>
        <input type="file" id="photo" name="photo" accept="image/*" />

        <button type="submit">Update</button>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
// Year picker popup same as in add.php
document.addEventListener('DOMContentLoaded', () => {
  const yearInput = document.getElementById('year_of_birth');
  if (!yearInput) return;

  yearInput.readOnly = true;

  yearInput.addEventListener('click', () => {
    // Remove existing popup
    const existingPopup = document.getElementById('yearPickerPopup');
    if (existingPopup) existingPopup.remove();

    // Create popup container
    let popup = document.createElement('div');
    popup.id = 'yearPickerPopup';
    popup.style.position = 'absolute';
    popup.style.background = 'white';
    popup.style.border = '1px solid #ccc';
    popup.style.padding = '10px';
    popup.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    popup.style.zIndex = 1000;
    popup.style.maxHeight = '200px';
    popup.style.overflowY = 'auto';
    popup.style.width = '120px';

    // Position popup below input
    const rect = yearInput.getBoundingClientRect();
    popup.style.left = rect.left + 'px';
    popup.style.top = rect.bottom + window.scrollY + 'px';

    // Generate years from currentYear down to 1900
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= 1900; year--) {
      let yearOption = document.createElement('div');
      yearOption.textContent = year;
      yearOption.style.padding = '6px 8px';
      yearOption.style.cursor = 'pointer';

      yearOption.addEventListener('mouseenter', () => {
        yearOption.style.backgroundColor = '#007bff';
        yearOption.style.color = 'white';
      });
      yearOption.addEventListener('mouseleave', () => {
        yearOption.style.backgroundColor = '';
        yearOption.style.color = '';
      });
      yearOption.addEventListener('click', () => {
        yearInput.value = yearOption.textContent;
        document.body.removeChild(popup);
      });

      popup.appendChild(yearOption);
    }

    document.body.appendChild(popup);

    // Close popup on outside click
    const closePopup = (e) => {
      if (!popup.contains(e.target) && e.target !== yearInput) {
        popup.remove();
        document.removeEventListener('click', closePopup);
      }
    };

    setTimeout(() => {
      document.addEventListener('click', closePopup);
    }, 100);
  });
});
</script>

</body>
</html>