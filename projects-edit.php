<?php
session_start();
include 'header.php';
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the project
$stmt = $db->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    echo "<p style='color:red;'>Project not found.</p>";
    include 'footer.php';
    exit;
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $sector = $_POST['sector'];
    $email = $_POST['contact_email'];
    $phone = $_POST['contact_phone'];

    $photoPath = $project['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $photoName = time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
        $photoPath = 'uploads/' . $photoName;
    }

    $update = $db->prepare("UPDATE projects SET title = ?, description = ?, sector = ?, contact_email = ?, contact_phone = ?, photo = ? WHERE id = ?");
    $update->execute([$title, $description, $sector, $email, $phone, $photoPath, $id]);

    echo "<p style='color: green;'>✅ Project updated successfully.</p>";
    // Refresh project
    $stmt->execute([$id]);
    $project = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ariaa Tech - Edit Project</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>

<h1>Edit Project</h1>

<form method="POST" enctype="multipart/form-data" class="filters-container" autocomplete="off" style="margin-bottom: 20px; gap: 15px; display: flex; flex-direction: column; flex-wrap: wrap; max-width: 900px;">

  <input
    type="text"
    name="title"
    placeholder="Project Title"
    value="<?= htmlspecialchars($project['title']) ?>"
    required
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <textarea
    name="description"
    placeholder="Describe the project"
    required
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; min-height: 100px;"
  ><?= htmlspecialchars($project['description']) ?></textarea>

  <select name="sector" required style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
    <option value="">Select Sector</option>
    <?php
    $sectors = ['Tourism',
  'Manufacturing',
  'Technology',
  'Food & Beverage',
  'Healthcare',
  'Education',
  'Agriculture',
  'Real Estate',
  'Transportation',
  'Energy',
  'Retail',
  'Finance & Banking',
  'Construction',
  'Media & Advertising',
  'Textile & Fashion',
  'Legal Services',
  'Telecommunications',
  'E-commerce',
  'Environmental & Sustainability',
  'Automotive',
  'Logistics & Supply Chain',
  'Beauty & Wellness',
  'Aerospace & Defense',
  'Sports & Entertainment',
  'Non-Profit & NGOs',
  'Government & Public Sector',
  'Arts & Culture',
  'Import & Export',
  'Handicrafts & Artisans',
  'Hospitality & Event Management'];
    foreach ($sectors as $sec) {
        $sel = ($project['sector'] === $sec) ? 'selected' : '';
        echo "<option value=\"$sec\" $sel>$sec</option>";
    }
    ?>
  </select>

  <input
    type="email"
    name="contact_email"
    placeholder="Contact Email"
    value="<?= htmlspecialchars($project['contact_email']) ?>"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <input
    type="text"
    name="contact_phone"
    placeholder="Contact Phone"
    value="<?= htmlspecialchars($project['contact_phone']) ?>"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <input
    type="file"
    name="photo"
    class="form-control"
    accept="image/*"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <?php if (!empty($project['photo'])): ?>
    <img src="<?= htmlspecialchars($project['photo']) ?>" alt="Project Image" style="max-width: 120px; margin-top: 10px; border-radius: 6px;" />
  <?php endif; ?>

  <div style="display: flex; gap: 10px; width: 100%;">
    <button type="submit" style="flex: 1; padding: 10px 0; border: none; border-radius: 6px; background-color: #28a745; color: white; cursor: pointer;">💾 Save Changes</button>
    <a href="projects.php" style="flex: 1; padding: 10px 0; border-radius: 6px; background-color: #6c757d; color: white; text-align: center; text-decoration: none;">⬅ Back</a>
  </div>

</form>

<?php include 'footer.php'; ?>
</body>
</html>
