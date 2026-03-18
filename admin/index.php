<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = getDB();
$total_users = $db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$active_users = $db->query("SELECT COUNT(*) as c FROM users WHERE status='active'")->fetch_assoc()['c'];
$banned_users = $db->query("SELECT COUNT(*) as c FROM users WHERE status='banned'")->fetch_assoc()['c'];
$admins = $db->query("SELECT COUNT(*) as c FROM users WHERE role='admin'")->fetch_assoc()['c'];
$recent_users = $db->query("SELECT id, username, email, role, status, created_at, last_login FROM users ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel – Steven Games</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="bg-grid"></div>
<div class="particles" id="particles"></div>

<div class="admin-layout">
  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <a href="../index.php">⚡ STEVEN<span>GAMES</span></a>
      <div class="admin-badge">ADMIN PANEL</div>
    </div>
    <nav class="admin-nav">
      <a href="index.php" class="active"><span class="nav-icon">📊</span> Dashboard</a>
      <a href="users.php"><span class="nav-icon">👥</span> Users</a>
      <a href="games.php"><span class="nav-icon">🎮</span> Games</a>
      <a href="reports.php"><span class="nav-icon">📈</span> Reports</a>
      <a href="settings.php"><span class="nav-icon">⚙️</span> Settings</a>
      <a href="../logout.php" style="margin-top: auto; color: var(--accent2)"><span class="nav-icon">🚪</span> Logout</a>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>📊 Dashboard Overview</h1>
      <div style="font-size:0.85rem; color:var(--text-muted)">
        Logged in as <strong style="color:var(--accent)"><?= htmlspecialchars($_SESSION['username']) ?></strong>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="admin-cards">
      <div class="admin-card highlight">
        <div class="card-icon">👥</div>
        <div class="card-val"><?= $total_users ?></div>
        <div class="card-lbl">Total Users</div>
      </div>
      <div class="admin-card">
        <div class="card-icon">✅</div>
        <div class="card-val"><?= $active_users ?></div>
        <div class="card-lbl">Active Users</div>
      </div>
      <div class="admin-card">
        <div class="card-icon">🚫</div>
        <div class="card-val"><?= $banned_users ?></div>
        <div class="card-lbl">Banned Users</div>
      </div>
      <div class="admin-card">
        <div class="card-icon">🛡️</div>
        <div class="card-val"><?= $admins ?></div>
        <div class="card-lbl">Admins</div>
      </div>
    </div>

    <!-- Recent Users Table -->
    <div class="section-title">🕒 Recent Registrations</div>
    <div class="data-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($recent_users as $u): ?>
          <tr>
            <td style="color:var(--text-muted)">#<?= $u['id'] ?></td>
            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
            <td style="color:var(--text-muted);font-size:0.85rem"><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="status-badge <?= $u['role']==='admin' ? 'status-banned' : 'status-active' ?>"><?= strtoupper($u['role']) ?></span></td>
            <td><span class="status-badge <?= $u['status']==='active' ? 'status-active' : 'status-banned' ?>"><?= strtoupper($u['status']) ?></span></td>
            <td style="font-size:0.85rem;color:var(--text-muted)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
            <td>
              <a href="users.php?action=ban&id=<?= $u['id'] ?>" class="action-btn btn-ban" onclick="return confirm('Toggle ban for this user?')">Ban/Unban</a>
              <a href="users.php?view=<?= $u['id'] ?>" class="action-btn btn-view">View</a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent_users)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:2rem">No users yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script src="../js/main.js"></script>
</body>
</html>
