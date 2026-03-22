<?php
session_start();
require_once 'includes/db.php';   // ← separate config file (Milestone Req #2)

$logged_in = isset($_SESSION['user_id']);
$username  = $logged_in ? $_SESSION['username'] : '';
$db        = getDB();

// ── 1. Fetch games from DB (Milestone Req #3 — dynamic loop) ──────────────
$games_result = $db->query("SELECT * FROM games WHERE status = 'active' ORDER BY rating DESC");

// ── 2. Fetch leaderboard from DB ──────────────────────────────────────────
$lb_result = $db->query("SELECT username, score FROM users WHERE status = 'active' ORDER BY score DESC LIMIT 5");

// ── 3. Fetch tbl_content rows (Milestone Req #1 — the content table) ──────
$content_result = $db->query("SELECT * FROM tbl_content WHERE status = 'published' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Steven Games – Play. Win. Dominate.</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    /* ── Content / News section ─────────────────────────── */
    .content-section { padding: 5rem 2rem; max-width: 1200px; margin: 0 auto; }
    .content-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
    .content-card {
      background: var(--bg-card); border: 1px solid var(--border);
      border-radius: var(--radius-lg); overflow: hidden;
      transition: transform .3s, box-shadow .3s;
    }
    .content-card:hover { transform: translateY(-6px); box-shadow: var(--glow); }
    .content-card img { width: 100%; height: 160px; object-fit: cover; }
    .content-card-body { padding: 1.2rem; }
    .content-cat {
      display: inline-block; background: var(--accent3);
      color: #fff; font-size: .7rem; font-weight: 700;
      padding: .2rem .7rem; border-radius: 20px; margin-bottom: .6rem;
      text-transform: uppercase; letter-spacing: 1px;
    }
    .content-card-body h3 { color: var(--accent); font-size: 1rem; margin-bottom: .5rem; }
    .content-card-body p  { font-size: .9rem; color: var(--text-muted); line-height: 1.5; }
    .content-date { font-size: .75rem; color: var(--text-muted); margin-top: .8rem; }
    .no-records {
      text-align: center; padding: 3rem 1rem;
      color: var(--text-muted); font-size: 1.1rem;
      border: 2px dashed var(--border); border-radius: var(--radius-lg);
    }
    .no-records span { font-size: 2.5rem; display: block; margin-bottom: .8rem; }
  </style>
</head>
<body>

<div class="bg-grid"></div>
<div class="particles" id="particles"></div>

<!-- NAV -->
<nav class="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <span class="logo-icon">⚡</span>
      <span class="logo-text">STEVEN<span class="logo-accent">GAMES</span></span>
    </a>
    <ul class="nav-links">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="#games">Games</a></li>
      <li><a href="#leaderboard">Leaderboard</a></li>
      <li><a href="#news">News</a></li>
      <?php if ($logged_in): ?>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php" class="btn-nav">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php" class="btn-nav">Login</a></li>
        <li><a href="register.php" class="btn-nav btn-nav-outline">Sign Up</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">🎮 #1 Gaming Platform</div>
    <h1 class="hero-title">Play.<br><span class="glow-text">Win.</span><br>Dominate.</h1>
    <p class="hero-sub">Join thousands of gamers on Steven Games. Compete, climb the leaderboard, and claim your glory.</p>
    <div class="hero-btns">
      <?php if (!$logged_in): ?>
        <a href="register.php" class="btn-primary">Start Playing Free</a>
        <a href="login.php"    class="btn-ghost">Sign In</a>
      <?php else: ?>
        <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
        <a href="#games"        class="btn-ghost">Browse Games</a>
      <?php endif; ?>
    </div>
    <div class="hero-stats">
      <div class="stat"><span class="stat-num">12K+</span><span class="stat-label">Players</span></div>
      <div class="stat"><span class="stat-num">50+</span> <span class="stat-label">Games</span></div>
      <div class="stat"><span class="stat-num">99%</span> <span class="stat-label">Uptime</span></div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="floating-card card1">🏆 Daily Champion</div>
    <div class="floating-card card2">🎯 New High Score!</div>
    <div class="floating-card card3">🔥 On Fire Streak</div>
    <div class="hero-orb"></div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     GAMES SECTION — data-driven (Milestone Req #3)
     Static HTML blocks removed; loop injects DB rows.
════════════════════════════════════════════════════════ -->
<section class="games-section" id="games">
  <div class="section-header">
    <span class="section-tag">FEATURED</span>
    <h2>Top Games</h2>
    <p>Handpicked titles for the ultimate gaming experience</p>
  </div>

  <div class="games-grid">
    <?php if ($games_result && $games_result->num_rows > 0): ?>
      <?php while ($game = $games_result->fetch_assoc()): ?>
      <!-- Single HTML template — rendered once per DB row -->
      <div class="game-card">
        <div class="game-icon"><?= htmlspecialchars($game['icon']) ?></div>
        <div class="game-info">
          <h3><?= htmlspecialchars($game['title']) ?></h3>
          <span class="game-genre"><?= htmlspecialchars($game['genre']) ?></span>
          <div class="game-rating">⭐ <?= htmlspecialchars($game['rating']) ?></div>
          <p style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem">
            <?= htmlspecialchars($game['description']) ?>
          </p>
        </div>
        <?php
          // Always link directly to play.php — it handles auth itself
          $slug = strtolower(str_replace(' ', '-', $game['title']));
        ?>
        <a href="play.php?game=<?= urlencode($slug) ?>" class="game-btn">Play Now</a>
      </div>
      <?php endwhile; ?>

    <?php else: ?>
      <!-- No records found (Milestone Success Criteria) -->
      <div class="no-records" style="grid-column:1/-1">
        <span>🎮</span>
        No games found in the database. Add some rows to the <code>games</code> table in phpMyAdmin and they will appear here automatically!
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- LEADERBOARD — data-driven -->
<section class="leaderboard-section" id="leaderboard">
  <div class="section-header">
    <span class="section-tag">RANKINGS</span>
    <h2>Top Players</h2>
    <p>The legends who dominate the arena</p>
  </div>
  <div class="leaderboard-table">
    <div class="lb-header">
      <span>Rank</span><span>Player</span><span>Score</span><span>Badge</span>
    </div>
    <?php
    $badges = ['👑','🥈','🥉','🔥','⚡'];
    $rank   = 1;
    if ($lb_result && $lb_result->num_rows > 0):
      while ($player = $lb_result->fetch_assoc()):
        $badge = $badges[$rank - 1] ?? '🎮';
    ?>
    <div class="lb-row rank-<?= $rank ?>">
      <span class="rank">#<?= $rank ?></span>
      <span class="player-name"><?= htmlspecialchars($player['username']) ?></span>
      <span class="score"><?= number_format($player['score']) ?></span>
      <span class="badge"><?= $badge ?></span>
    </div>
    <?php $rank++; endwhile; else: ?>
    <div class="no-records" style="padding:2rem">
      <span>🏆</span> No players yet. Sign up to be the first on the leaderboard!
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
     NEWS / CONTENT SECTION — powered by tbl_content
     (Milestone Req #1 — the required table)
════════════════════════════════════════════════════════ -->
<section id="news" style="background:rgba(13,17,23,.6);padding:5rem 0">
  <div class="content-section" style="padding:0 2rem">
    <div class="section-header">
      <span class="section-tag">LATEST</span>
      <h2>News &amp; Updates</h2>
      <p>Stay up to date with everything happening on Steven Games</p>
    </div>

    <?php if ($content_result && $content_result->num_rows > 0): ?>
      <div class="content-grid">
        <?php while ($item = $content_result->fetch_assoc()): ?>
        <!-- One HTML template — repeated per tbl_content row -->
        <div class="content-card">
          <img src="<?= htmlspecialchars($item['image_url']) ?>"
               alt="<?= htmlspecialchars($item['title']) ?>"
               onerror="this.src='https://placehold.co/400x160/0d1117/00f0ff?text=No+Image'">
          <div class="content-card-body">
            <span class="content-cat"><?= htmlspecialchars($item['category']) ?></span>
            <h3><?= htmlspecialchars($item['title']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <div class="content-date">
              🗓 <?= date('M j, Y', strtotime($item['created_at'])) ?>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>

    <?php else: ?>
      <!-- Empty state (Milestone Success Criteria — handle no records) -->
      <div class="no-records">
        <span>📰</span>
        No news articles yet. Add a row to <code>tbl_content</code> in phpMyAdmin — it will appear here instantly!
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-logo">⚡ STEVEN<span>GAMES</span></div>
    <p>© 2025 Steven Games. All rights reserved. Built with passion for gamers.</p>
    <div class="footer-links">
      <a href="#">Privacy</a><a href="#">Terms</a><a href="#">Support</a>
    </div>
  </div>
</footer>

<?php $db->close(); ?>
<script src="js/main.js"></script>
</body>
</html>
