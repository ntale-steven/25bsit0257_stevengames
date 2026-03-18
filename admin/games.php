<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
$db = getDB();

$msg = '';

// Toggle game status
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $cur = $db->query("SELECT status FROM games WHERE id=$id")->fetch_assoc();
    $new = $cur['status'] === 'active' ? 'inactive' : 'active';
    $db->query("UPDATE games SET status='$new' WHERE id=$id");
    $msg = "Game status updated to $new.";
}

// Delete game
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $db->query("DELETE FROM games WHERE id=$id");
    $msg = "Game deleted.";
}

// Add new game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $title  = $db->real_escape_string(trim($_POST['title']));
    $genre  = $db->real_escape_string(trim($_POST['genre']));
    $icon   = $db->real_escape_string(trim($_POST['icon']));
    $desc   = $db->real_escape_string(trim($_POST['description']));
    if ($title && $genre) {
        $db->query("INSERT INTO games (title, genre, icon, description) VALUES ('$title','$genre','$icon','$desc')");
        $msg = "Game '$title' added successfully.";
    }
}

$games = $db->query("SELECT * FROM games ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Games – Admin – Steven Games</title>
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
      <a href="games.php" class="active"><span class="nav-icon">🎮</span> Games</a>
      <a href="reports.php"><span class="nav-icon">📈</span> Reports</a>
      <a href="settings.php"><span class="nav-icon">⚙️</span> Settings</a>
      <a href="../logout.php" style="color:var(--accent2)"><span class="nav-icon">🚪</span> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>🎮 Games Management</h1>
      <span style="font-size:0.85rem;color:var(--text-muted)"><?= count($games) ?> games in catalogue</span>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-success" style="margin-bottom:1.5rem"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <!-- Add Game Form -->
    <div class="data-table" style="margin-bottom:2rem;padding:1.5rem;border-radius:var(--radius-lg)">
      <div class="section-title" style="margin-bottom:1rem">➕ Add New Game</div>
      <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 80px 1fr auto;gap:1rem;align-items:end">
        <div class="form-group" style="margin:0">
          <label>Title</label>
          <input type="text" name="title" placeholder="Game title" required>
        </div>
        <div class="form-group" style="margin:0">
          <label>Genre</label>
          <input type="text" name="genre" placeholder="e.g. Action, RPG">
        </div>
        <div class="form-group" style="margin:0">
          <label>Icon</label>
          <input type="text" name="icon" placeholder="🎮" maxlength="4">
        </div>
        <div class="form-group" style="margin:0">
          <label>Description</label>
          <input type="text" name="description" placeholder="Short description">
        </div>
        <button type="submit" name="add_game" class="btn-full" style="width:auto;padding:0.85rem 1.5rem;margin-top:1.5rem">Add</button>
      </form>
    </div>

    <!-- Games Table -->
    <div class="data-table">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Icon</th><th>Title</th><th>Genre</th>
            <th>Rating</th><th>Plays</th><th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($games as $g): ?>
          <tr>
            <td style="color:var(--text-muted)">#<?= $g['id'] ?></td>
            <td style="font-size:1.5rem"><?= $g['icon'] ?></td>
            <td><strong><?= htmlspecialchars($g['title']) ?></strong></td>
            <td style="color:var(--text-muted)"><?= htmlspecialchars($g['genre']) ?></td>
            <td>⭐ <?= $g['rating'] ?></td>
            <td style="color:var(--accent);font-family:var(--font-display)"><?= number_format($g['plays']) ?></td>
            <td><span class="status-badge <?= $g['status']==='active' ? 'status-active' : 'status-banned' ?>"><?= strtoupper($g['status']) ?></span></td>
            <td style="display:flex;gap:6px">
              <a href="?action=toggle&id=<?= $g['id'] ?>" class="action-btn btn-view"><?= $g['status']==='active' ? 'Disable' : 'Enable' ?></a>
              <a href="?action=delete&id=<?= $g['id'] ?>" class="action-btn btn-ban" onclick="return confirm('Delete this game?')">Del</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>
