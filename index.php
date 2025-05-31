<?php
session_start();
include 'header.php';
include 'db.php';

// Get all filter inputs from GET
$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$nationality = $_GET['nationality'] ?? '';
$profession = $_GET['profession'] ?? '';
$year_min = $_GET['year_min'] ?? '';
$year_max = $_GET['year_max'] ?? '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Sorting
$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'asc';

// Allowed columns for sorting to prevent SQL injection
$allowed_columns = ['id', 'name', 'email', 'mobile', 'nationality', 'profession', 'year_of_birth', 'role', 'created_at'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}
$sort_order = strtolower($sort_order) === 'desc' ? 'DESC' : 'ASC';

$records_per_page = 20;
$start = ($page - 1) * $records_per_page;

// Build WHERE clause dynamically
$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(name LIKE ? OR email LIKE ? OR mobile LIKE ? OR nationality LIKE ? OR profession LIKE ? OR role LIKE ?)";
    $likeSearch = "%$search%";
    array_push($params, $likeSearch, $likeSearch, $likeSearch, $likeSearch, $likeSearch, $likeSearch);
}

if ($role !== '') {
    $whereClauses[] = "role = ?";
    $params[] = $role;
}

if ($nationality !== '') {
    $whereClauses[] = "nationality = ?";
    $params[] = $nationality;
}

if ($profession !== '') {
    $whereClauses[] = "profession = ?";
    $params[] = $profession;
}

if ($year_min !== '' && is_numeric($year_min)) {
    $whereClauses[] = "year_of_birth >= ?";
    $params[] = (int)$year_min;
}

if ($year_max !== '' && is_numeric($year_max)) {
    $whereClauses[] = "year_of_birth <= ?";
    $params[] = (int)$year_max;
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get total records count
$count_stmt = $db->prepare("SELECT COUNT(*) FROM users $whereSql");
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Get paginated data
$stmt = $db->prepare("SELECT * FROM users $whereSql ORDER BY $sort_column $sort_order LIMIT ? OFFSET ?");
foreach ($params as $k => $param) {
    $stmt->bindValue($k + 1, $param);
}
// Bind limit and offset at the end
$stmt->bindValue(count($params) + 1, $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $start, PDO::PARAM_INT);

$stmt->execute();

// Helper for sortable column headers
function sort_link($column, $current_sort, $current_order, $label, $extra_params = []) {
    $order = 'asc';
    $arrow = '';
    if ($current_sort === $column) {
        if (strtolower($current_order) === 'asc') {
            $order = 'desc';
            $arrow = ' ↑';
        } else {
            $order = 'asc';
            $arrow = ' ↓';
        }
    }

    // Build query params including filters
    $params = array_merge($_GET, ['sort' => $column, 'order' => $order, 'page' => 1]);
    $query = http_build_query($params);

    return "<a href=\"?{$query}\" style='text-decoration:none; color:inherit;'>$label$arrow</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ariaa Tech - User List</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>

<h1>User List</h1>

<!-- Filter Form -->
<form method="GET" class="filters-container" autocomplete="off" style="margin-bottom: 20px; gap: 15px; display: flex; flex-wrap: wrap; max-width: 900px;">

  <input
    type="text"
    name="search"
    placeholder="Search (name, email, mobile...)"
    value="<?= htmlspecialchars($search) ?>"
    style="flex-grow: 2; min-width: 220px; padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;"
  />

  <select name="role" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
    <option value="">All Roles</option>
    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
    <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
    <option value="viewer" <?= $role === 'viewer' ? 'selected' : '' ?>>Viewer</option>
  </select>

  <select name="nationality" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
    <option value="">All Nationalities</option>
    <?php
    $nats = $db->query("SELECT DISTINCT nationality FROM users ORDER BY nationality")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($nats as $nat) {
        $sel = ($nationality === $nat) ? 'selected' : '';
        echo "<option value=\"" . htmlspecialchars($nat) . "\" $sel>" . htmlspecialchars($nat) . "</option>";
    }
    ?>
  </select>

  <select name="profession" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc;">
    <option value="">All Professions</option>
    <?php
    $profs = $db->query("SELECT DISTINCT profession FROM users ORDER BY profession")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($profs as $prof) {
        $sel = ($profession === $prof) ? 'selected' : '';
        echo "<option value=\"" . htmlspecialchars($prof) . "\" $sel>" . htmlspecialchars($prof) . "</option>";
    }
    ?>
  </select>

  <!-- Button row with full width -->
  <div style="display: flex; gap: 10px; width: 55%; margin-top: -5px;">
    <button type="submit" style="flex: 1; padding: 10px 0; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer;">
      Filter
    </button>
    <a href="index.php" style="flex: 1; 
    padding: 10px 0; 
    border-radius: 6px; 
    background-color: #dc3545; 
    color: white; 
    text-decoration: none; 
    text-align: center;">
      Reset
    </a>
  </div>

</form>


<a href="add.php" class="btn" style="margin-bottom: 10px;">➕ Add New User</a> | 
<a href="export_pdf.php" target="_blank" class="btn" style="margin-bottom: 10px;">📄 Export to PDF</a> | 
<a href="import_csv.php" class="btn" style="margin-bottom: 10px;">📥 Import CSV</a>

<table border="1" style="margin-top:10px; width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th style="padding:8px;"><?= sort_link('id', $sort_column, $sort_order, 'ID') ?></th>
            <th style="padding:8px;"><?= sort_link('name', $sort_column, $sort_order, 'Name') ?></th>
            <th style="padding:8px;"><?= sort_link('email', $sort_column, $sort_order, 'Email') ?></th>
            <th style="padding:8px;"><?= sort_link('mobile', $sort_column, $sort_order, 'Mobile') ?></th>
            <th style="padding:8px;"><?= sort_link('nationality', $sort_column, $sort_order, 'Nationality') ?></th>
            <th style="padding:8px;"><?= sort_link('profession', $sort_column, $sort_order, 'Profession') ?></th>
            <th style="padding:8px;"><?= sort_link('year_of_birth', $sort_column, $sort_order, 'Year of Birth') ?></th>
            <th style="padding:8px;"><?= sort_link('role', $sort_column, $sort_order, 'Role') ?></th>
            <th style="padding:8px;">Profile Picture</th>
            <th style="padding:8px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($total_records == 0): ?>
      <tr><td colspan="10" style="text-align:center; padding:20px;">No records found.</td></tr>
    <?php else: ?>
      <?php foreach ($stmt as $row): ?>
        <tr>
          <td style="padding:8px;"><?= $row['id'] ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['name']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['email']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['mobile']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['nationality']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['profession']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['year_of_birth']) ?></td>
          <td style="padding:8px;"><?= htmlspecialchars($row['role']) ?></td>
          <td style="padding:8px;">
            <?php if (!empty($row['photo'])): ?>
              <img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="User Photo" style="max-width: 60px; max-height: 60px; border-radius: 50%;">
            <?php else: ?>
              <span>No Photo</span>
            <?php endif; ?>
          </td>
          <td style="padding:8px;">
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Edit User">✏️ Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>" class="btn-action btn-delete" title="Delete User" onclick="return confirm('Delete this user?')">🗑 Delete</a>
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
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="btn-pagination">Next</a>
    <?php endif; ?>

    <span style="margin-left:auto; font-weight:600;">Total Records: <?= $total_records ?></span>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
