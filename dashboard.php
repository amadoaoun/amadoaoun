<?php
include 'header.php';
include 'db.php';
// Users added in last 7 days
$stmt7days = $db->prepare("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt7days->execute();
$users_last_7_days = $stmt7days->fetchColumn();

// Get top 5 professions with counts
$stmt_professions = $db->prepare("SELECT profession, COUNT(*) as count FROM users GROUP BY profession ORDER BY count DESC LIMIT 5");
$stmt_professions->execute();
$top_professions = $stmt_professions->fetchAll(PDO::FETCH_ASSOC);
// Fetch total users
$total_stmt = $db->query("SELECT COUNT(*) FROM users");
$total_users = $total_stmt->fetchColumn();
$stmt_avg_year = $db->query("SELECT AVG(year_of_birth) AS avg_year FROM users WHERE year_of_birth IS NOT NULL");
$avg_year_of_birth = round($stmt_avg_year->fetchColumn());
// Fetch last registration date
$last_stmt = $db->query("SELECT MAX(created_at) FROM users");
$last_registration = $last_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  
  body {
    
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
      background: linear-gradient(135deg, #e2e8f0 0%, #f0f2f5 100%);

    margin: 40px;
    color: #333;
  }
  
  .charts-container {
    
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto 40px auto;
    
      padding: 20px 10px;
      
  }
  .dashboard-box {
    
    background: linear-gradient(145deg, #ffffff, #e6e6e6);
    padding: 25px 30px;
    border-radius: 15px;
    box-shadow:
      8px 8px 15px rgba(0, 0, 0, 0.1),
      -8px -8px 15px rgba(255, 255, 255, 0.7),
      inset 1px 1px 2px rgba(255, 255, 255, 0.8);
    margin-bottom: 30px;
    flex: 1 1 450px;
    max-width: 600px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: default;
    position: relative;
    
    
  }
  .dashboard-box {
    
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s forwards;
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
  .dashboard-box:hover {
    
    transform: translateY(-8px);
    box-shadow:
      12px 12px 20px rgba(0, 0, 0, 0.15),
      -12px -12px 20px rgba(255, 255, 255, 0.8);
  }
  .dashboard-box:hover {
    
  box-shadow:
    0 0 15px rgba(0, 123, 255, 0.5),
    12px 12px 20px rgba(0, 0, 0, 0.15),
    -12px -12px 20px rgba(255, 255, 255, 0.8);
}
  .dashboard-box h3 {
    
    margin-top: 0;
    font-weight: 700;
    font-size: 1.5rem;
    color: #222;
    margin-bottom: 20px;
      border-bottom: 2px solid #007bff;
  padding-bottom: 8px;
  }
  .dashboard-box .stat {
    font-size: 2.5rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 10px;
    
    
  }
  .dashboard-box .stat {
    
  font-size: 3rem;
  color: #007bff;
  letter-spacing: 1.5px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
  
}
.summary-cards .card h4 {
  
    margin-bottom: 15px;
    font-weight: 800;
    font-size: 1,5rem;
      border-bottom: 2px solid #007bff;
  padding-bottom: 8px;
}
  .dashboard-box canvas {
    
    width: 100% !important;
    height: 300px !important;
    user-select: none;
     box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
  border-radius: 12px;
    
  }
  
  @media (max-width: 768px) {
    .charts-container {
      flex-direction: column;
      align-items: center;
    }
    .dashboard-box {
      max-width: 100%;
      flex: unset;
    }
    
  }
  .card {
    
  background: linear-gradient(145deg, #ffffff, #e6e6e6);
  padding: 25px 30px;
  border-radius: 15px;
  box-shadow:
    8px 8px 15px rgba(0, 0, 0, 0.1),
    -8px -8px 15px rgba(255, 255, 255, 0.7),
    inset 1px 1px 2px rgba(255, 255, 255, 0.8);
  margin-bottom: 30px;
  cursor: default;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
}

.card:hover {
  box-shadow:
    0 0 15px rgba(0, 123, 255, 0.5),
    12px 12px 20px rgba(0, 0, 0, 0.15),
    -12px -12px 20px rgba(255, 255, 255, 0.8);
    
}

</style>
</head>
<body>
<div class="summary-cards" style="display:flex; gap:30px; flex-wrap: wrap; margin-bottom: 30px; max-width: 1200px; margin-left:auto; margin-right:auto;">
  <div class="dashboard-box" style="flex: 1 1 200px; text-align:center;">
    <h3>Users Added Last 7 Days</h3>
    <div class="stat" style="font-size: 3rem; color: #007bff; font-weight: 700;"><?= $users_last_7_days ?></div>
  </div>

  <div class="dashboard-box" style="flex: 1 1 400px;">
    <h3>Top 5 Professions</h3>
    <ul>
      <?php foreach ($top_professions as $prof): ?>
        <li><?= htmlspecialchars($prof['profession'] ?: 'Others') ?> (<?= $prof['count'] ?>)</li>
      <?php endforeach; ?>
    </ul>
  </div>


<div class="charts-container">

  <div class="dashboard-box">
    <h3>Total Users</h3>
    <div class="stat"><?= htmlspecialchars($total_users) ?></div>
  </div>
<div class="card">
  <h4>Average Year of Birth</h4>
  <p class="stat"><?= $avg_year_of_birth ?: 'N/A' ?></p>
</div>
 

  <div class="dashboard-box">
    <h3>Registrations Over Time</h3>
    <canvas id="registrationsChart"></canvas>
  </div>

  <div class="dashboard-box">
    <h3>Users by Nationality</h3>
    <canvas id="nationalityChart"></canvas>
  </div>
 <div class="dashboard-box">
    <h3>Last Registration</h3>
    <div class="stat"><?= htmlspecialchars($last_registration) ?></div>
  </div>
  <div class="dashboard-box">
    <h3>Registrations Over Time (Pie Chart)</h3>
    <canvas id="registrationsPieChart"></canvas>
  </div>

  <div class="dashboard-box">
    <h3>Users by Nationality (Pie Chart)</h3>
    <canvas id="nationalityPieChart"></canvas>
  </div>
    <div class="dashboard-box">
    <h3>Users by Profession</h3>
    <canvas id="professionChart"></canvas>
  </div>

</div>

<script>
fetch('chart.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.date);
    const counts = data.map(item => parseInt(item.count));

    const ctx = document.getElementById('registrationsChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Users Registered',
          data: counts,
          backgroundColor: 'rgba(75, 192, 192, 0.7)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1,
          borderRadius: 4,
          maxBarThickness: 25
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } },
          x: {
            ticks: { maxRotation: 45, minRotation: 30, maxTicksLimit: 10 },
            grid: { display: false }
          }
        },
        plugins: {
          legend: { display: true, position: 'top' },
          tooltip: { enabled: true }
        }
      }
    });
  })
  .catch(console.error);

fetch('chart_nationality.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.nationality || 'Unknown');
    const counts = data.map(item => item.count);

    const ctx = document.getElementById('nationalityChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Number of Users',
          data: counts,
          backgroundColor: 'rgba(0, 123, 255, 0.6)',
          borderColor: 'rgba(0, 123, 255, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    });
  })
  .catch(console.error);

// Helper function to generate colors for pie charts
function generateColors(num) {
  const palette = [
    '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
    '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6610f2'
  ];
  const colors = [];
  for (let i = 0; i < num; i++) {
    colors.push(palette[i % palette.length]);
  }
  return colors;
}

// Pie chart: Registrations Over Time
fetch('chart.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.date);
    const counts = data.map(item => parseInt(item.count));
    const colors = generateColors(counts.length);

    const ctxPie = document.getElementById('registrationsPieChart').getContext('2d');
    new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: counts,
          backgroundColor: colors,
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'right' },
          tooltip: { enabled: true }
        }
      }
    });
  });

// Pie chart: Users by Nationality
fetch('chart_nationality.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.nationality || 'Unknown');
    const counts = data.map(item => item.count);
    const colors = generateColors(counts.length);

    const ctxPieNat = document.getElementById('nationalityPieChart').getContext('2d');
    new Chart(ctxPieNat, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: counts,
          backgroundColor: colors,
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'right' },
          tooltip: { enabled: true }
        }
      }
    });
  });
  fetch('chart_profession.php')
  .then(res => res.json())
  .then(data => {
    const labels = data.map(item => item.profession || 'Unknown');
    const counts = data.map(item => item.count);

    const ctx = document.getElementById('professionChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Number of Users',
          data: counts,
          backgroundColor: 'rgba(255, 99, 132, 0.6)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } },
        responsive: true,
        maintainAspectRatio: false
      }
    });
  })
</script>

<?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
