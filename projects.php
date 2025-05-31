
<?php
include 'db.php'; // make sure this path is correct relative to your file structure

$projects = $db->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$projects = $db->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$grouped = [];

foreach ($projects as $project) {
  $grouped[$project['sector']][] = $project;
}
?>


<?php foreach ($grouped as $sector => $sectorProjects): ?>
  <h3><?= htmlspecialchars($sector) ?></h3>
  <div style="display: flex; flex-wrap: wrap;    gap: 20px; margin-bottom: 30px;">
    <?php foreach ($sectorProjects as $proj): ?>
      <div style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; width: 280px;">
        <?php if ($proj['photo']): ?>
          <img src="<?= htmlspecialchars($proj['photo']) ?>" style="max-width: 40%; height: auto; border-radius: 6px;" />
        <?php endif; ?>
        <h4><?= htmlspecialchars($proj['title']) ?></h4>
        <p><?= nl2br(htmlspecialchars(substr($proj['description'], 0, 100))) ?>...</p>
        <p><strong>Email:</strong> <?= htmlspecialchars($proj['contact_email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($proj['contact_phone']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
<?php endforeach; ?>
<?php
session_start();
include 'header.php';
include 'db.php';

// Filter
$sector = $_GET['sector'] ?? '';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 50;
$start = ($page - 1) * $records_per_page;

// Build WHERE clause
$whereSql = '';
$params = [];

if (!empty($sector)) {
    $whereSql = 'WHERE sector = ?';
    $params[] = $sector;
}

// Count total
$count_stmt = $db->prepare("SELECT COUNT(*) FROM projects $whereSql");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Fetch paginated projects
$stmt = $db->prepare("SELECT * FROM projects $whereSql ORDER BY id DESC LIMIT ? OFFSET ?");
foreach ($params as $k => $param) {
    $stmt->bindValue($k + 1, $param);
}
$stmt->bindValue(count($params) + 1, $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $start, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ariaa Tech - Project List</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>

<h1>Project List</h1>

<!-- Filters -->
<form method="GET" class="filters-container" autocomplete="off" style="margin-bottom: 20px; gap: 15px; display: flex; flex-wrap: wrap; max-width: 400px;">
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

  <div style="display: flex; gap: 10px; width: 55%;">
    <button type="submit" style="flex: 1; padding: 10px 0; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer;">Filter</button>
    <a href="projects.php" style="flex: 1; padding: 10px 0; border-radius: 6px; background-color: #dc3545; color: white; text-decoration: none; text-align: center;">Reset</a>
  </div>
</form>

<a href="projects-add.php" class="btn" style="margin-bottom: 10px;">➕ Add New Project</a>

<table border="1" style="margin-top:10px; width: 100%; border-collapse: collapse;">
  <thead>
    <tr>
      <th style="padding: 8px;">ID</th>
      <th style="padding: 8px;">Title</th>
      <th style="padding: 8px;">Sector</th>
      <th style="padding: 8px;">Email</th>
      <th style="padding: 8px;">Phone</th>
      <th style="padding: 8px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($projects)): ?>
      <tr><td colspan="7" style="text-align:center; padding:20px;">No projects found.</td></tr>
    <?php else: ?>
      <?php foreach ($projects as $project): ?>
        <tr>
          <td style="padding:8px;"><?= $project['id'] ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($project['title']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($project['sector']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($project['contact_email']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($project['contact_phone']) ?></td>
        
          </td>
          <td style="padding:8px;">
            <a href="projects-edit.php?id=<?= $project['id'] ?>" class="btn-action btn-edit">✏️ Edit</a>
            <a href="projects-delete.php?id=<?= $project['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Delete this project?')">🗑 Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<!-- Pagination -->
<div style="margin-top: 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
  <?php if ($page > 1): ?>
    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="btn-pagination">⬅ Previous</a>
  <?php endif; ?>

  <span>Page <?= $page ?> of <?= $total_pages ?></span>

  <?php if ($page < $total_pages): ?>
    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="btn-pagination">Next ➡</a>
  <?php endif; ?>

  <span style="margin-left:auto; font-weight:600;">Total Projects: <?= $total_records ?></span>
  
</div>

<?php include 'footer.php'; ?>

</body>
</html>
