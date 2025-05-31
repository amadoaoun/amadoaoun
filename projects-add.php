<?php
session_start();
include 'header.php';
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ariaa Tech - Add Project</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>

<h1>Add New Project</h1>

<form method="POST" enctype="multipart/form-data" class="filters-container" autocomplete="off" style="margin-bottom: 20px; gap: 15px; display: flex; flex-direction: column; flex-wrap: wrap; max-width: 900px;">

  <input
    type="text"
    name="title"
    placeholder="Project Title"
    required
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <textarea
    name="description"
    placeholder="Describe the project"
    required
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; min-height: 100px;"
  ></textarea>

<select name="sector" required style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
  <option value="">Select Sector</option>
  <option value="Tourism">Tourism</option>
  <option value="Manufacturing">Manufacturing</option>
  <option value="Technology">Technology</option>
  <option value="Food & Beverage">Food & Beverage</option>
  <option value="Healthcare">Healthcare</option>
  <option value="Education">Education</option>
  <option value="Agriculture">Agriculture</option>
  <option value="Real Estate">Real Estate</option>
  <option value="Transportation">Transportation</option>
  <option value="Energy">Energy</option>
  <option value="Retail">Retail</option>
  <option value="Finance & Banking">Finance & Banking</option>
  <option value="Construction">Construction</option>
  <option value="Media & Advertising">Media & Advertising</option>
  <option value="Textile & Fashion">Textile & Fashion</option>
  <option value="Legal Services">Legal Services</option>
  <option value="Telecommunications">Telecommunications</option>
  <option value="E-commerce">E-commerce</option>
  <option value="Environmental & Sustainability">Environmental & Sustainability</option>
  <option value="Automotive">Automotive</option>
  <option value="Logistics & Supply Chain">Logistics & Supply Chain</option>
  <option value="Beauty & Wellness">Beauty & Wellness</option>
  <option value="Aerospace & Defense">Aerospace & Defense</option>
  <option value="Sports & Entertainment">Sports & Entertainment</option>
  <option value="Non-Profit & NGOs">Non-Profit & NGOs</option>
  <option value="Government & Public Sector">Government & Public Sector</option>
  <option value="Arts & Culture">Arts & Culture</option>
  <option value="Import & Export">Import & Export</option>
  <option value="Handicrafts & Artisans">Handicrafts & Artisans</option>
  <option value="Hospitality & Event Management">Hospitality & Event Management</option>
</select>

  <input
    type="email"
    name="contact_email"
    placeholder="Contact Email"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <input
    type="text"
    name="contact_phone"
    placeholder="Contact Phone"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <input
    type="file"
    name="photo"
    accept="image/*"
    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <div style="display: flex; gap: 10px; width: 100%;">
    <button type="submit" style="flex: 1; padding: 10px 0; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer;">
      ➕ Add Project
    </button>
    <a href="projects.php" style="flex: 1; 
      padding: 10px 0; 
      border-radius: 6px; 
      background-color: #6c757d; 
      color: white; 
      text-align: center; 
      text-decoration: none;">
      ⬅ Back
    </a>
  </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $sector = $_POST['sector'];
  $email = $_POST['contact_email'];
  $phone = $_POST['contact_phone'];

  $photoPath = null;
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    $photoName = time() . '_' . basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $photoName);
    $photoPath = 'uploads/' . $photoName;
  }

  $stmt = $db->prepare("INSERT INTO projects (title, description, sector, contact_email, contact_phone, photo) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$title, $description, $sector, $email, $phone, $photoPath]);

  echo "<p style='color: green; font-weight: bold;'>✅ Project added successfully.</p>";
}
?>

<?php include 'footer.php'; ?>
</body>
</html>
