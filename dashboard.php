<?php
session_start();
require_once 'includes/auth.php';
requireLogin();
// db loaded via auth.php → includes/db.php   // ← separate config file

$db       = getDB();
$username = $_SESSION['username'];
$uid      = $_SESSION['user_id'];

// User row
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user_row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ── Fetch games dynamically from DB (Milestone Req #3) ────────────────────
$games_result = $db->query("SELECT * FROM games WHERE status = 'active' ORDER BY rating DESC");

// ── Leaderboard ───────────────────────────────────────────────────────────
$lb_result = $db->query("SELECT username, score FROM users WHERE status='active' ORDER BY score DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – Steven Games</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    .no-records {
      text-align:center;padding:3rem 1rem;color:var(--text-muted);
      border:2px dashed var(--border);border-radius:var(--radius-lg);
      grid-column:1/-1;
    }
    .no-records span{font-size:2.5rem;display:block;margin-bottom:.8rem;}
    .play-link {
      display:inline-block;padding:.55rem 1.3rem;
      background:var(--accent);color:#000;border-radius:8px;
      font-weight:700;font-family:var(--font-display);font-size:.8rem;
      text-decoration:none;transition:box-shadow .2s;
    }
    .play-link:hover{box-shadow:var(--glow);}
  </style>
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
        <li><a href="admin/index.php" style="color:var(--accent2)">Admin Panel</a></li>
      <?php endif; ?>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="dashboard">
  <div class="dash-header">
    <h1>Welcome back, <span><?= htmlspecialchars($username) ?></span> 👋</h1>
    <p>Your gaming stats &amp; recent activity</p>
  </div>

  <div class="dash-stats">
    <div class="dash-stat">
      <div class="icon">🏆</div>
      <div class="value"><?= number_format($user_row['score']) ?></div>
      <div class="label">Total Score</div>
    </div>
    <div class="dash-stat">
      <div class="icon">🎮</div>
      <div class="value"><?= $user_row['games_played'] ?></div>
      <div class="label">Games Played</div>
    </div>
    <div class="dash-stat">
      <div class="icon">🔥</div>
      <div class="value"><?= $user_row['win_streak'] ?></div>
      <div class="label">Win Streak</div>
    </div>
    <div class="dash-stat">
      <div class="icon">⭐</div>
      <div class="value">#–</div>
      <div class="label">Global Rank</div>
    </div>
  </div>

  <!-- ══ GAME LOBBY — data-driven (Milestone Req #3) ══════════════════════
       Static game array removed. One HTML template per DB row.
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="section-title">🎯 Game Lobby</div>
  <div class="games-grid" style="margin-bottom:3rem">
    <?php if ($games_result && $games_result->num_rows > 0): ?>
      <?php while ($game = $games_result->fetch_assoc()):
        $slug = strtolower(str_replace(' ', '-', $game['title']));
      ?>
      <!-- HTML template rendered once per row from the games table -->
      <div class="game-card">
        <div class="game-icon"><?= htmlspecialchars($game['icon']) ?></div>
        <div class="game-info">
          <h3><?= htmlspecialchars($game['title']) ?></h3>
          <span class="game-genre"><?= htmlspecialchars($game['genre']) ?></span>
          <div class="game-rating">⭐ <?= htmlspecialchars($game['rating']) ?></div>
          <p style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem;line-height:1.4">
            <?= htmlspecialchars($game['description']) ?>
          </p>
        </div>
        <a href="play.php?game=<?= urlencode($slug) ?>" class="play-link">▶ Play</a>
      </div>
      <?php endwhile; ?>

    <?php else: ?>
      <!-- Empty state (Milestone Success Criteria) -->
      <div class="no-records">
        <span>🎮</span>
        No games in the database yet. Add rows to the <code>games</code> table in phpMyAdmin — they will appear here automatically!
      </div>
    <?php endif; ?>
  </div>

  <!-- ── Account Info ─────────────────────────────────────────────────── -->
  <div class="section-title">👤 Account Info</div>
  <div class="data-table">
    <table>
      <tr><td style="color:var(--text-muted);font-weight:600;width:160px">Username</td><td><?= htmlspecialchars($user_row['username']) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Email</td>   <td><?= htmlspecialchars($user_row['email']) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Role</td>    <td><span class="status-badge status-active"><?= strtoupper($user_row['role']) ?></span></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Member Since</td><td><?= date('F j, Y', strtotime($user_row['created_at'])) ?></td></tr>
      <tr><td style="color:var(--text-muted);font-weight:600">Last Login</td><td><?= $user_row['last_login'] ? date('M j, Y H:i', strtotime($user_row['last_login'])) : 'Just now' ?></td></tr>
    </table>
  </div>
</div>

<?php $db->close(); ?>
<script src="js/main.js"></script>
</body>
</html>
