<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = getDB();
$username = $_SESSION['username'];
$game_slug = isset($_GET['game']) ? $_GET['game'] : '';

// Get game info
$stmt = $db->prepare("SELECT * FROM games WHERE LOWER(REPLACE(title, ' ', '-')) = ?");
$stmt->bind_param("s", $game_slug);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$game) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($game['title']) ?> - Steven Games</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .game-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
        }
        .score-box {
            font-size: 24px;
            margin: 20px;
            padding: 20px;
            background: var(--bg-card);
            border-radius: 10px;
        }
        .click-btn {
            font-size: 30px;
            padding: 20px 40px;
            margin: 20px;
            background: var(--accent);
            color: #000;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .click-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div class="bg-grid"></div>
<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="logo">⚡ STEVENGAMES</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">← Back to Lobby</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="game-container">
    <h1><?= htmlspecialchars($game['title']) ?> 🎮</h1>
    <p>Welcome, <?= htmlspecialchars($username) ?>!</p>
    
    <div class="score-box">
        <h2>Score: <span id="score">0</span></h2>
    </div>
    
    <button class="click-btn" onclick="addPoint()">Click Me! (+1 point)</button>
    <button class="click-btn" onclick="saveAndExit()" style="background: #ff3c78;">Save & Exit</button>
</div>

<script>
let currentScore = 0;

function addPoint() {
    currentScore++;
    document.getElementById('score').innerText = currentScore;
}

function saveAndExit() {
    if (currentScore > 0) {
        // Save score via AJAX
        fetch('save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                game_id: <?= $game['id'] ?>,
                score: currentScore
            })
        }).then(() => {
            window.location.href = 'dashboard.php';
        });
    } else {
        window.location.href = 'dashboard.php';
    }
}
</script>
</body>
</html>