<?php
session_start();
require_once '../includes/auth.php';
requireAdmin();
$db = getDB();

// Stats
$total_users   = $db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_games   = $db->query("SELECT COUNT(*) as c FROM games")->fetch_assoc()['c'];
$total_scores  = $db->query("SELECT COUNT(*) as c FROM scores")->fetch_assoc()['c'];
$top_scores    = $db->query("SELECT u.username, g.title, s.score, s.played_at FROM scores s JOIN users u ON s.user_id=u.id JOIN games g ON s.game_id=g.id ORDER BY s.score DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
$top_players   = $db->query("SELECT username, score, games_played FROM users WHERE role='user' ORDER BY score DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports – Admin – Steven Games</title>
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
      <a href="reports.php" class="active"><span class="nav-icon">📈</span> Reports</a>
      <a href="settings.php"><span class="nav-icon">⚙️</span> Settings</a>
      <a href="../logout.php" style="color:var(--accent2)"><span class="nav-icon">🚪</span> Logout</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>📈 Reports & Analytics</h1>
    </div>

    <!-- Summary Cards -->
    <div class="admin-cards">
      <div class="admin-card highlight">
        <div class="card-icon">👥</div>
        <div class="card-val"><?= $total_users ?></div>
        <div class="card-lbl">Total Users</div>
      </div>
      <div class="admin-card">
        <div class="card-icon">🎮</div>
        <div class="card-val"><?= $total_games ?></div>
        <div class="card-lbl">Games Available</div>
      </div>
      <div class="admin-card">
        <div class="card-icon">🏆</div>
        <div class="card-val"><?= $total_scores ?></div>
        <div class="card-lbl">Total Scores Recorded</div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
      <!-- Top Players -->
      <div>
        <div class="section-title">🥇 Top Players by Score</div>
        <div class="data-table">
          <table>
            <thead><tr><th>Rank</th><th>Player</th><th>Score</th><th>Games</th></tr></thead>
            <tbody>
              <?php foreach($top_players as $i => $p): ?>
              <tr>
                <td style="font-family:var(--font-display);color:<?= $i===0?'var(--gold)':($i===1?'#c0c0c0':($i===2?'#cd7f32':'var(--text-muted)')) ?>">#<?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($p['username']) ?></strong></td>
                <td style="color:var(--accent);font-family:var(--font-display)"><?= number_format($p['score']) ?></td>
                <td style="color:var(--text-muted)"><?= $p['games_played'] ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($top_players)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:1.5rem">No player data yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Scores -->
      <div>
        <div class="section-title">🎯 Highest Individual Scores</div>
        <div class="data-table">
          <table>
            <thead><tr><th>Player</th><th>Game</th><th>Score</th><th>Date</th></tr></thead>
            <tbody>
              <?php foreach($top_scores as $s): ?>
              <tr>
                <td><strong><?= htmlspecialchars($s['username']) ?></strong></td>
                <td style="color:var(--text-muted);font-size:0.85rem"><?= htmlspecialchars($s['title']) ?></td>
                <td style="color:var(--accent);font-family:var(--font-display)"><?= number_format($s['score']) ?></td>
                <td style="color:var(--text-muted);font-size:0.8rem"><?= date('M j', strtotime($s['played_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($top_scores)): ?>
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:1.5rem">No scores yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="../js/main.js"></script>
</body>
</html>
