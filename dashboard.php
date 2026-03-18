<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$username = $_SESSION['username'];
$db = getDB();

// Get user stats
$uid = $_SESSION['user_id'];
$user_row = $db->query("SELECT * FROM users WHERE id = $uid")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – Steven Games</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="bg-grid"></div>
<div class="particles" id="particles"></div>

<nav class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <span class="logo-icon">⚡</span>
      <span class="logo-text">STEVEN<span class="logo-accent">GAMES</span></span>
    </a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="dashboard.php" class="active">Dashboard</a></li>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <li><a href="admin/index.php" style="color: var(--accent2)">Admin Panel</a></li>
      <?php endif; ?>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="dashboard">
  <div class="dash-header">
    <h1>Welcome back, <span><?= htmlspecialchars($username) ?></span> 👋</h1>
    <p>Your gaming stats & recent activity</p>
  </div>

  <div class="dash-stats">
    <div class="dash-stat">
      <div class="icon">🏆</div>
      <div class="value">0</div>
      <div class="label">Total Score</div>
    </div>
    <div class="dash-stat">
      <div class="icon">🎮</div>
      <div class="value">0</div>
      <div class="label">Games Played</div>
    </div>
    <div class="dash-stat">
      <div class="icon">🔥</div>
      <div class="value">0</div>
      <div class="label">Win Streak</div>
    </div>
    <div class="dash-stat">
      <div class="icon">⭐</div>
      <div class="value">#–</div>
      <div class="label">Global Rank</div>
    </div>
  </div>

  <!-- Game Lobby -->
  <div class="section-title">🎯 Game Lobby</div>
  <div class="games-grid" style="margin-bottom: 3rem;">
    <?php
    $games = [
      ['🚀','Space Blaster','Action','Launch Game'],
      ['🐉','Dragon Quest','RPG','Launch Game'],
      ['🏎️','Turbo Race','Racing','Launch Game'],
      ['🧩','Mind Matrix','Puzzle','Launch Game'],
      ['⚔️','Battle Arena','Fighting','Launch Game'],
      ['🌍','World Builder','Strategy','Launch Game'],
    ];
    foreach($games as $g): ?>
    <div class="game-card">
      <div class="game-icon"><?= $g[0] ?></div>
      <div class="game-info">
        <h3><?= $g[1] ?></h3>
        <span class="game-genre"><?= $g[2] ?></span>
      </div>
      <a href="#" class="game-btn" onclick="alert('Game launching... (connect game engine here)');"><?= $g[3] ?></a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Account Info -->
  <div class="section-title">👤 Account Info</div>
  <div class="data-table">
    <table>
      <tr><td style="color:var(--text-muted);font-weight:600;width:160px">Username</td><td><?= htmlspecialchars($user_row['username']) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Email</td><td><?= htmlspecialchars($user_row['email']) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Role</td><td><span class="status-badge status-active"><?= strtoupper($user_row['role']) ?></span></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Member Since</td><td><?= date('F j, Y', strtotime($user_row['created_at'])) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Last Login</td><td><?= $user_row['last_login'] ? date('M j, Y H:i', strtotime($user_row['last_login'])) : 'Just now' ?></td></tr>
    </table>
  </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
