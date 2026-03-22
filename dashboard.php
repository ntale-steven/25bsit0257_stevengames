<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = getDB();
$username = $_SESSION['username'];
$uid = $_SESSION['user_id'];

// Get user info
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user_row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get games
$games_result = $db->query("SELECT * FROM games WHERE status = 'active' ORDER BY rating DESC");

// Helper function for icons
function getIconEmoji($icon) {
    $icons = [
        'rocket' => '🚀', 'dragon' => '🐉', 'car' => '🏎️',
        'puzzle' => '🧩', 'sword' => '⚔️', 'world' => '🌍'
    ];
    return $icons[$icon] ?? '🎮';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Steven Games</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .game-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 20px rgba(0,240,255,0.3);
        }
        .play-link {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background: var(--accent);
            color: #000;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .section-title {
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            color: var(--accent);
        }
        .dash-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .dash-stat {
            background: var(--bg-card);
            padding: 1rem;
            text-align: center;
            border-radius: 10px;
        }
        .value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent);
        }
    </style>
</head>
<body>
<div class="bg-grid"></div>

<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="logo">⚡ STEVENGAMES</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="dashboard" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <h1>Welcome back, <?= htmlspecialchars($username) ?>! 👋</h1>
    
    <div class="dash-stats">
        <div class="dash-stat">
            <div class="value"><?= number_format($user_row['score']) ?></div>
            <div>Total Score</div>
        </div>
        <div class="dash-stat">
            <div class="value"><?= $user_row['games_played'] ?></div>
            <div>Games Played</div>
        </div>
        <div class="dash-stat">
            <div class="value"><?= $user_row['win_streak'] ?></div>
            <div>Win Streak</div>
        </div>
    </div>
    
    <div class="section-title">🎮 Available Games</div>
    <div class="games-grid">
        <?php while ($game = $games_result->fetch_assoc()): 
            $slug = strtolower(str_replace(' ', '-', $game['title']));
        ?>
        <div class="game-card">
            <div style="font-size: 3rem;"><?= getIconEmoji($game['icon']) ?></div>
            <h3><?= htmlspecialchars($game['title']) ?></h3>
            <p><?= htmlspecialchars($game['description']) ?></p>
            <div>⭐ <?= $game['rating'] ?> | 🎮 <?= number_format($game['plays']) ?> plays</div>
            <a href="test_game.php?game=<?= urlencode($slug) ?>" class="play-link">▶ Play Now</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>