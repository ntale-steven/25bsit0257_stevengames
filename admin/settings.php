<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
$db = getDB();

$msg = '';

// Change own password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $uid  = $_SESSION['user_id'];
    $row  = $db->query("SELECT password FROM users WHERE id=$uid")->fetch_assoc();

    if (!password_verify($current, $row['password'])) {
        $msg = ['type' => 'error', 'text' => 'Current password is incorrect.'];
    } elseif (strlen($new) < 6) {
        $msg = ['type' => 'error', 'text' => 'New password must be at least 6 characters.'];
    } elseif ($new !== $confirm) {
        $msg = ['type' => 'error', 'text' => 'New passwords do not match.'];
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $db->query("UPDATE users SET password='$hash' WHERE id=$uid");
        $msg = ['type' => 'success', 'text' => 'Password updated successfully.'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings – Admin – Steven Games</title>
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
      <a href="users.php"><span class="nav-icon">👥</span> Users</a>
      <a href="games.php"><span class="nav-icon">🎮</span> Games</a>
      <a href="reports.php"><span class="nav-icon">📈</span> Reports</a>
      <a href="settings.php" class="active"><span class="nav-icon">⚙️</span> Settings</a>
      <a href="../logout.php" style="color:var(--accent2)"><span class="nav-icon">🚪</span> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>⚙️ Admin Settings</h1>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-<?= $msg['type'] === 'error' ? 'error' : 'success' ?>" style="max-width:480px;margin-bottom:1.5rem">
        <?= htmlspecialchars($msg['text']) ?>
      </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:start">

      <!-- Change Password -->
      <div>
        <div class="section-title">🔒 Change Admin Password</div>
        <div class="data-table" style="padding:1.5rem;border-radius:var(--radius-lg)">
          <form method="POST">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" name="current_password" placeholder="Current password" required>
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_password" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" name="confirm_password" placeholder="Repeat new password" required>
            </div>
            <button type="submit" name="change_password" class="btn-full">Update Password</button>
          </form>
        </div>
      </div>

      <!-- Account Info -->
      <div>
        <div class="section-title">👤 Account Details</div>
        <div class="data-table" style="border-radius:var(--radius-lg)">
          <table>
            <?php
            $me = $db->query("SELECT * FROM users WHERE id={$_SESSION['user_id']}")->fetch_assoc();
            ?>
            <tr><td style="color:var(--text-muted);font-weight:600;width:130px;padding:1rem">Username</td><td><?= htmlspecialchars($me['username']) ?></td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">Email</td><td><?= htmlspecialchars($me['email']) ?></td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">Role</td><td><span class="status-badge status-banned">ADMIN</span></td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">Member Since</td><td><?= date('F j, Y', strtotime($me['created_at'])) ?></td></tr>
          </table>
        </div>

        <div class="section-title" style="margin-top:1.5rem">🌐 Site Info</div>
        <div class="data-table" style="border-radius:var(--radius-lg)">
          <table>
            <tr><td style="color:var(--text-muted);font-weight:600;width:130px;padding:1rem">Site Name</td><td>Steven Games</td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">PHP Version</td><td><?= phpversion() ?></td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">Server</td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache' ?></td></tr>
            <tr><td style="color:var(--text-muted);font-weight:600;padding:1rem">Database</td><td>MySQL – steven_games</td></tr>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>
