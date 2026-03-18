<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();

$db = getDB();
$msg = '';

// Handle ban/unban
if (isset($_GET['action']) && $_GET['action'] === 'ban' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id != $_SESSION['user_id']) {
        $current = $db->query("SELECT status FROM users WHERE id = $id")->fetch_assoc();
        $new_status = ($current['status'] === 'active') ? 'banned' : 'active';
        $db->query("UPDATE users SET status = '$new_status' WHERE id = $id");
        $msg = "User status updated to $new_status.";
    } else {
        $msg = "You cannot ban yourself.";
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id != $_SESSION['user_id']) {
        $db->query("DELETE FROM users WHERE id = $id");
        $msg = "User deleted.";
    }
}

// Handle role change
if (isset($_GET['action']) && $_GET['action'] === 'promote' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $current = $db->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
    $new_role = ($current['role'] === 'admin') ? 'user' : 'admin';
    $db->query("UPDATE users SET role = '$new_role' WHERE id = $id");
    $msg = "User role updated to $new_role.";
}

// Search/filter
$search = trim($_GET['search'] ?? '');
$where = $search ? "WHERE username LIKE '%".addslashes($search)."%' OR email LIKE '%".addslashes($search)."%'" : '';
$users = $db->query("SELECT * FROM users $where ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users – Admin – Steven Games</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="bg-grid"></div>
<div class="particles" id="particles"></div>

<div class="admin-layout">
  <aside class="admin-sidebar">
    <div class="admin-logo">
      <a href="../index.php">⚡ STEVEN<span>GAMES</span></a>
      <div class="admin-badge">ADMIN PANEL</div>
    </div>
    <nav class="admin-nav">
      <a href="index.php"><span class="nav-icon">📊</span> Dashboard</a>
      <a href="users.php" class="active"><span class="nav-icon">👥</span> Users</a>
      <a href="games.php"><span class="nav-icon">🎮</span> Games</a>
      <a href="reports.php"><span class="nav-icon">📈</span> Reports</a>
      <a href="settings.php"><span class="nav-icon">⚙️</span> Settings</a>
      <a href="../logout.php" style="color: var(--accent2)"><span class="nav-icon">🚪</span> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>👥 User Management</h1>
      <span style="font-size:0.85rem;color:var(--text-muted)"><?= count($users) ?> users found</span>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-success" style="margin-bottom:1.5rem"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Search -->
    <form method="GET" style="margin-bottom:1.5rem;display:flex;gap:1rem;align-items:center">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
             placeholder="Search username or email..."
             style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:0.7rem 1rem;color:var(--text);font-family:var(--font-body);font-size:0.95rem;flex:1;outline:none">
      <button type="submit" class="btn-full" style="width:auto;padding:0.7rem 1.5rem">Search</button>
      <?php if ($search): ?><a href="users.php" style="color:var(--text-muted);font-size:0.9rem">Clear</a><?php endif; ?>
    </form>

    <div class="data-table">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Role</th>
            <th>Status</th><th>Joined</th><th>Last Login</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($users as $u): ?>
          <tr>
            <td style="color:var(--text-muted)">#<?= $u['id'] ?></td>
            <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
            <td style="color:var(--text-muted);font-size:0.85rem"><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="status-badge <?= $u['role']==='admin' ? 'status-banned' : 'status-active' ?>"><?= strtoupper($u['role']) ?></span></td>
            <td><span class="status-badge <?= $u['status']==='active' ? 'status-active' : 'status-banned' ?>"><?= strtoupper($u['status']) ?></span></td>
            <td style="font-size:0.85rem;color:var(--text-muted)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
            <td style="font-size:0.85rem;color:var(--text-muted)"><?= $u['last_login'] ? date('M j H:i', strtotime($u['last_login'])) : '–' ?></td>
            <td style="display:flex;gap:4px;flex-wrap:wrap">
              <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <a href="?action=ban&id=<?= $u['id'] ?>" class="action-btn btn-ban"
                   onclick="return confirm('Toggle ban?')"><?= $u['status']==='active' ? 'Ban' : 'Unban' ?></a>
                <a href="?action=promote&id=<?= $u['id'] ?>" class="action-btn btn-view"
                   onclick="return confirm('Toggle admin role?')"><?= $u['role']==='admin' ? 'Demote' : 'Promote' ?></a>
                <a href="?action=delete&id=<?= $u['id'] ?>" class="action-btn btn-ban"
                   onclick="return confirm('Permanently delete this user?')" style="background:rgba(255,60,120,0.2)">Del</a>
              <?php else: ?>
                <span style="color:var(--text-muted);font-size:0.8rem">(You)</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($users)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script src="../js/main.js"></script>
</body>
</html>
