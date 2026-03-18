<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Steven Games – Play. Win. Dominate.</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- Animated background particles -->
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
    <h1 class="hero-title">
      Play.<br>
      <span class="glow-text">Win.</span><br>
      Dominate.
    </h1>
    <p class="hero-sub">Join thousands of gamers on Steven Games. Compete, climb the leaderboard, and claim your glory.</p>
    <div class="hero-btns">
      <?php if (!$logged_in): ?>
        <a href="register.php" class="btn-primary">Start Playing Free</a>
        <a href="login.php" class="btn-ghost">Sign In</a>
      <?php else: ?>
        <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
        <a href="#games" class="btn-ghost">Browse Games</a>
      <?php endif; ?>
    </div>
    <div class="hero-stats">
      <div class="stat"><span class="stat-num">12K+</span><span class="stat-label">Players</span></div>
      <div class="stat"><span class="stat-num">50+</span><span class="stat-label">Games</span></div>
      <div class="stat"><span class="stat-num">99%</span><span class="stat-label">Uptime</span></div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="floating-card card1">🏆 Daily Champion</div>
    <div class="floating-card card2">🎯 New High Score!</div>
    <div class="floating-card card3">🔥 On Fire Streak</div>
    <div class="hero-orb"></div>
  </div>
</section>

<!-- GAMES SECTION -->
<section class="games-section" id="games">
  <div class="section-header">
    <span class="section-tag">FEATURED</span>
    <h2>Top Games</h2>
    <p>Handpicked titles for the ultimate gaming experience</p>
  </div>
  <div class="games-grid">
    <?php
    $games = [
      ['🚀','Space Blaster','Action','Play Now','4.9'],
      ['🐉','Dragon Quest','RPG','Play Now','4.8'],
      ['🏎️','Turbo Race','Racing','Play Now','4.7'],
      ['🧩','Mind Matrix','Puzzle','Play Now','4.6'],
      ['⚔️','Battle Arena','Fighting','Play Now','4.9'],
      ['🌍','World Builder','Strategy','Play Now','4.5'],
    ];
    foreach($games as $g): ?>
    <div class="game-card">
      <div class="game-icon"><?= $g[0] ?></div>
      <div class="game-info">
        <h3><?= $g[1] ?></h3>
        <span class="game-genre"><?= $g[2] ?></span>
        <div class="game-rating">⭐ <?= $g[4] ?></div>
      </div>
      <a href="<?= $logged_in ? 'dashboard.php' : 'login.php' ?>" class="game-btn"><?= $g[3] ?></a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- LEADERBOARD -->
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
    $players = [
      ['1','StevenX_Pro','128,450','👑'],
      ['2','NightHawk99','115,200','🥈'],
      ['3','PixelKing','98,780','🥉'],
      ['4','GamerQueen','87,560','🔥'],
      ['5','SwiftBlade','76,340','⚡'],
    ];
    foreach($players as $p): ?>
    <div class="lb-row rank-<?= $p[0] ?>">
      <span class="rank">#<?= $p[0] ?></span>
      <span class="player-name"><?= $p[1] ?></span>
      <span class="score"><?= $p[2] ?></span>
      <span class="badge"><?= $p[3] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-logo">⚡ STEVEN<span>GAMES</span></div>
    <p>© 2024 Steven Games. All rights reserved. Built with passion for gamers.</p>
    <div class="footer-links">
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
      <a href="#">Support</a>
    </div>
  </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
