<?php
include __DIR__ . '/db.php';

$error = '';
$selectedNationality = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $nationality = trim($_POST['nationality']);
    $profession = trim($_POST['profession']);
    $year_of_birth = trim($_POST['year_of_birth']);
    $role = $_POST['role'] ?? 'viewer';

    // Handle photo upload
    $photo = null;
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
                $photo = $newFileName;
            } else {
                $error = "Failed to upload photo.";
            }
        } else {
            $error = "Invalid photo format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (!$error) {
        // Check for duplicate email
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = "This email is already registered.";
        } else {
            $password_plain = 'defaultPassword123';
            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (name, email, mobile, nationality, profession, year_of_birth, role, password, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $mobile, $nationality, $profession, $year_of_birth, $role, $password_hashed, $photo]);
            header("Location: index.php");
            exit;
        }
    }
}

// List of countries for dropdown (use your full list)
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
    <title>Add New User</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<div class="form-container">
    <h2>Add New User</h2>

    <?php if ($error): ?>
      <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <label for="name">Name <span style="color:#b00020;">*</span></label>
        <input type="text" id="name" name="name" required autocomplete="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">

        <label for="email">Email <span style="color:#b00020;">*</span></label>
        <input type="email" id="email" name="email" required autocomplete="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label for="mobile">Mobile Number</label>
        <input type="text" id="mobile" name="mobile" autocomplete="tel" value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>">

        <label for="nationality">Nationality</label>
        <select id="nationality" name="nationality" required>
            <option value="">Select your nationality</option>
            <?php foreach ($countries as $country): ?>
                <option value="<?= htmlspecialchars($country) ?>" <?= (isset($_POST['nationality']) && $_POST['nationality'] === $country) ? 'selected' : '' ?>><?= htmlspecialchars($country) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="profession">Profession</label>
        <input type="text" id="profession" name="profession" value="<?= isset($_POST['profession']) ? htmlspecialchars($_POST['profession']) : '' ?>">

        <label for="year_of_birth">Year of Birth</label>
        <input type="text" id="year_of_birth" name="year_of_birth" placeholder="Select Year" autocomplete="off" value="<?= isset($_POST['year_of_birth']) ? htmlspecialchars($_POST['year_of_birth']) : '' ?>" readonly>

        <label for="role">Role</label>
        <select id="role" name="role" required>
          <option value="viewer" <?= (isset($_POST['role']) && $_POST['role'] === 'viewer') ? 'selected' : '' ?>>Viewer</option>
          <option value="editor" <?= (isset($_POST['role']) && $_POST['role'] === 'editor') ? 'selected' : '' ?>>Editor</option>
          <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
        </select>

        <label for="photo">Profile Photo</label>
        <input type="file" id="photo" name="photo" accept="image/*" />

        <button type="submit">Add User</button>
    </form>
    
</div>

<?php include __DIR__ . '/footer.php'; ?>

<script>
// Year picker popup same as before
document.addEventListener('DOMContentLoaded', () => {
  const yearInput = document.getElementById('year_of_birth');
  if (!yearInput) return;

  yearInput.readOnly = true;

  yearInput.addEventListener('click', () => {
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

    const rect = yearInput.getBoundingClientRect();
    popup.style.left = rect.left + 'px';
    popup.style.top = rect.bottom + window.scrollY + 'px';

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

    const existingPopup = document.getElementById('yearPickerPopup');
    if (existingPopup) existingPopup.remove();

    document.body.appendChild(popup);

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