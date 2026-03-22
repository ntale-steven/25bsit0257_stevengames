<?php
session_start();
require_once 'includes/auth.php';
requireLogin();

$db = getDB();
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$game_slug = isset($_GET['game']) ? $_GET['game'] : '';

// Find the game
$stmt = $db->prepare("SELECT * FROM games WHERE LOWER(REPLACE(title, ' ', '-')) = ? AND status = 'active'");
$stmt->bind_param("s", $game_slug);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$game) {
    header('Location: dashboard.php');
    exit;
}

// Get user's best score for this game
$stmt = $db->prepare("SELECT MAX(score) as best_score FROM scores WHERE user_id = ? AND game_id = ?");
$stmt->bind_param("ii", $user_id, $game['id']);
$stmt->execute();
$best = $stmt->get_result()->fetch_assoc();
$best_score = $best['best_score'] ?? 0;
$stmt->close();

// Update play count
$stmt = $db->prepare("UPDATE games SET plays = plays + 1 WHERE id = ?");
$stmt->bind_param("i", $game['id']);
$stmt->execute();
$stmt->close();

// Handle score saving
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['score'])) {
    $score = intval($_POST['score']);
    
    // Save to scores table
    $stmt = $db->prepare("INSERT INTO scores (user_id, game_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $game['id'], $score);
    $stmt->execute();
    $stmt->close();
    
    // Update user's total score
    $stmt = $db->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Update games played count
    $stmt = $db->prepare("UPDATE users SET games_played = games_played + 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true]);
    exit;
}

// Map icon to emoji
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($game['title']) ?> - Steven Games</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #0a0a0f;
            font-family: 'Orbitron', monospace;
            color: #fff;
        }
        
        .game-container {
            max-width: 900px;
            margin: 80px auto;
            padding: 20px;
        }
        
        .game-header {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #00f0ff;
        }
        
        .game-icon {
            font-size: 4rem;
            margin-bottom: 10px;
        }
        
        .game-stats {
            display: flex;
            justify-content: space-between;
            background: #0f0f1a;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            text-align: center;
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #888;
            text-transform: uppercase;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00f0ff;
        }
        
        .game-area {
            background: #000;
            border: 2px solid #00f0ff;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .click-button {
            background: linear-gradient(135deg, #00f0ff, #0099ff);
            color: #000;
            font-size: 3rem;
            font-weight: bold;
            padding: 30px 60px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-family: 'Orbitron', monospace;
            transition: transform 0.1s, box-shadow 0.1s;
            margin: 20px;
        }
        
        .click-button:active {
            transform: scale(0.95);
        }
        
        .click-button:hover {
            box-shadow: 0 0 30px rgba(0,240,255,0.5);
        }
        
        .controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            font-size: 1rem;
            font-family: 'Orbitron', monospace;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #00f0ff;
            color: #000;
        }
        
        .btn-danger {
            background: #ff3c78;
            color: #fff;
        }
        
        .btn-secondary {
            background: #333;
            color: #fff;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        
        .score-display {
            font-size: 4rem;
            font-weight: bold;
            color: #00f0ff;
            margin: 20px;
            text-shadow: 0 0 10px rgba(0,240,255,0.5);
        }
        
        .game-over {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .game-over-box {
            background: #1a1a2e;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            border: 2px solid #ff3c78;
        }
        
        .hidden {
            display: none;
        }
        
        .timer {
            font-size: 2rem;
            color: #ff3c78;
            margin: 10px;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .combo {
            animation: pulse 0.3s ease;
            color: #ffd700;
        }
    </style>
</head>
<body>
<div class="bg-grid"></div>

<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon">⚡</span>
            <span class="logo-text">STEVEN<span class="logo-accent">GAMES</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="dashboard.php">← Dashboard</a></li>
            <li><a href="logout.php" class="btn-nav">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="game-container">
    <div class="game-header">
        <div class="game-icon"><?= getIconEmoji($game['icon']) ?></div>
        <h1><?= htmlspecialchars($game['title']) ?></h1>
        <p><?= htmlspecialchars($game['description']) ?></p>
    </div>
    
    <div class="game-stats">
        <div class="stat-box">
            <div class="stat-label">Player</div>
            <div class="stat-value"><?= htmlspecialchars($username) ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Best Score</div>
            <div class="stat-value" id="best-score"><?= number_format($best_score) ?></div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Genre</div>
            <div class="stat-value"><?= htmlspecialchars($game['genre']) ?></div>
        </div>
    </div>
    
    <div class="game-area" id="game-area">
        <div class="score-display">
            Score: <span id="current-score">0</span>
        </div>
        
        <div class="timer" id="timer">
            Time: 30s
        </div>
        
        <button class="click-button" id="click-button">
            👆 CLICK ME! 👆
        </button>
        
        <div id="combo" style="font-size: 1.2rem; color: #ffd700;"></div>
    </div>
    
    <div class="controls">
        <button class="btn btn-primary" id="restart-btn">🔄 Restart Game</button>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Lobby</a>
    </div>
</div>

<div id="game-over-modal" class="game-over hidden">
    <div class="game-over-box">
        <h2>🎮 GAME OVER! 🎮</h2>
        <div style="font-size: 3rem; margin: 20px;">Final Score: <span id="final-score">0</span></div>
        <div id="new-record" style="color: #ffd700; margin: 10px;"></div>
        <button class="btn btn-primary" id="play-again-btn">Play Again</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script>
let gameActive = true;
let score = 0;
let timeLeft = 30;
let combo = 0;
let comboTimeout = null;
let timerInterval = null;

const gameId = <?= $game['id'] ?>;
const userId = <?= $user_id ?>;
let bestScore = <?= $best_score ?>;

// DOM elements
const clickButton = document.getElementById('click-button');
const currentScoreSpan = document.getElementById('current-score');
const timerSpan = document.getElementById('timer');
const comboDiv = document.getElementById('combo');
const gameOverModal = document.getElementById('game-over-modal');
const finalScoreSpan = document.getElementById('final-score');
const newRecordDiv = document.getElementById('new-record');
const bestScoreSpan = document.getElementById('best-score');

// Save score to database
async function saveScoreToDatabase(finalScore) {
    if (finalScore === 0) return;
    
    const formData = new FormData();
    formData.append('score', finalScore);
    
    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            console.log('Score saved successfully!');
            if (finalScore > bestScore) {
                bestScore = finalScore;
                bestScoreSpan.textContent = bestScore.toLocaleString();
            }
        }
    } catch (error) {
        console.error('Error saving score:', error);
    }
}

// Update combo
function updateCombo() {
    if (comboTimeout) clearTimeout(comboTimeout);
    
    if (combo > 1) {
        comboDiv.textContent = `${combo}x COMBO! +${combo} points!`;
        comboDiv.classList.add('combo');
        setTimeout(() => comboDiv.classList.remove('combo'), 300);
    } else {
        comboDiv.textContent = '';
    }
    
    comboTimeout = setTimeout(() => {
        combo = 0;
        comboDiv.textContent = '';
    }, 2000);
}

// Add points
function addPoints() {
    if (!gameActive) return;
    
    // Add points with combo multiplier
    const pointsToAdd = 1 + Math.floor(combo / 5);
    score += pointsToAdd;
    combo++;
    
    currentScoreSpan.textContent = score;
    updateCombo();
    
    // Visual feedback
    clickButton.style.transform = 'scale(0.95)';
    setTimeout(() => {
        clickButton.style.transform = 'scale(1)';
    }, 100);
}

// End game
async function endGame() {
    if (!gameActive) return;
    
    gameActive = false;
    
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
    
    // Show game over modal
    finalScoreSpan.textContent = score;
    
    if (score > bestScore) {
        newRecordDiv.innerHTML = '🎉 NEW RECORD! 🎉';
        newRecordDiv.style.animation = 'pulse 0.5s ease';
    } else {
        newRecordDiv.innerHTML = '';
    }
    
    gameOverModal.classList.remove('hidden');
    
    // Save score
    await saveScoreToDatabase(score);
}

// Start timer
function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    
    timerInterval = setInterval(() => {
        if (!gameActive) return;
        
        timeLeft--;
        timerSpan.textContent = `Time: ${timeLeft}s`;
        
        if (timeLeft <= 0) {
            endGame();
        }
    }, 1000);
}

// Reset game
function resetGame() {
    gameActive = true;
    score = 0;
    timeLeft = 30;
    combo = 0;
    
    currentScoreSpan.textContent = '0';
    timerSpan.textContent = 'Time: 30s';
    comboDiv.textContent = '';
    gameOverModal.classList.add('hidden');
    
    startTimer();
}

// Event listeners
clickButton.addEventListener('click', addPoints);
document.getElementById('restart-btn').addEventListener('click', resetGame);
document.getElementById('play-again-btn').addEventListener('click', resetGame);

// Start the game
resetGame();

// Keyboard support
document.addEventListener('keydown', (e) => {
    if (e.code === 'Space' && gameActive) {
        e.preventDefault();
        addPoints();
    }
});
</script>
</body>
</html>