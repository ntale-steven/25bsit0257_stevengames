<?php
// ===== STEVEN GAMES - play.php (FIXED VERSION) =====
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Login check
if (!isset($_SESSION['user_id'])) {
    $current_url = $_SERVER['REQUEST_URI'];
    header('Location: login.php?redirect=' . urlencode($current_url));
    exit;
}

require_once 'includes/db.php';
$db = getDB();

$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];
$game_slug = isset($_GET['game']) ? trim($_GET['game']) : '';

// Debug log
error_log("play.php called with game slug: " . $game_slug);

// If no game slug, redirect to dashboard
if (empty($game_slug)) {
    header('Location: dashboard.php');
    exit;
}

// Try to find the game by slug
$game = null;
$game_title_from_slug = str_replace('-', ' ', $game_slug);
$game_title_from_slug = ucwords($game_title_from_slug);

// Method 1: Try to find by exact title
$stmt = $db->prepare("SELECT * FROM games WHERE title = ? AND status = 'active'");
$stmt->bind_param("s", $game_title_from_slug);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $game = $result->fetch_assoc();
    error_log("Found game by title: " . $game['title']);
}
$stmt->close();

// Method 2: If not found, try by comparing slug
if (!$game) {
    $stmt = $db->prepare("SELECT * FROM games WHERE LOWER(REPLACE(title, ' ', '-')) = ? AND status = 'active'");
    $stmt->bind_param("s", $game_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
        error_log("Found game by slug: " . $game['title']);
    }
    $stmt->close();
}

// Method 3: Try partial match (for debugging)
if (!$game) {
    $like_term = '%' . $game_slug . '%';
    $stmt = $db->prepare("SELECT * FROM games WHERE LOWER(title) LIKE LOWER(?) AND status = 'active'");
    $stmt->bind_param("s", $like_term);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
        error_log("Found game by partial match: " . $game['title']);
    }
    $stmt->close();
}

// If game not found, show error instead of redirecting
if (!$game) {
    // Get all games for debugging
    $all_games = $db->query("SELECT title, LOWER(REPLACE(title, ' ', '-')) as slug FROM games WHERE status = 'active'");
    $game_list = [];
    while ($row = $all_games->fetch_assoc()) {
        $game_list[] = $row['title'] . " (slug: " . $row['slug'] . ")";
    }
    die("
    <h1>Game Not Found</h1>
    <p>Requested game slug: <strong>" . htmlspecialchars($game_slug) . "</strong></p>
    <p>Available games in database:</p>
    <ul>
        <li>" . implode("</li><li>", array_map('htmlspecialchars', $game_list)) . "</li>
    </ul>
    <p><a href='dashboard.php'>← Back to Dashboard</a></p>
    ");
}

$game_id = $game['id'];
$game_title = $game['title'];
$game_icon = $game['icon'];
$game_genre = $game['genre'];
$game_description = $game['description'];

// Map icon to emoji
function getIconEmoji($icon_name) {
    $icon_map = [
        'rocket' => '🚀',
        'dragon' => '🐉',
        'car' => '🏎️',
        'puzzle' => '🧩',
        'sword' => '⚔️',
        'world' => '🌍'
    ];
    return $icon_map[$icon_name] ?? '🎮';
}

$icon_emoji = getIconEmoji($game_icon);

// Determine game type based on genre
$game_type = 'space'; // default
if ($game_genre == 'Racing') {
    $game_type = 'race';
} elseif ($game_genre == 'Puzzle' || $game_genre == 'Strategy') {
    $game_type = 'puzzle';
}

// Get user's best score for this game
$stmt = $db->prepare("SELECT MAX(score) as best_score FROM scores WHERE user_id = ? AND game_id = ?");
$stmt->bind_param("ii", $user_id, $game_id);
$stmt->execute();
$best_score_result = $stmt->get_result()->fetch_assoc();
$user_best_score = $best_score_result['best_score'] ?? 0;
$stmt->close();

// Update play count
$stmt = $db->prepare("UPDATE games SET plays = plays + 1 WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$stmt->close();

// Handle score saving
if (isset($_POST['action']) && $_POST['action'] === 'save_score') {
    $score = intval($_POST['score'] ?? 0);
    $stmt = $db->prepare("INSERT INTO scores (user_id, game_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $game_id, $score);
    $stmt->execute();
    $stmt->close();
    
    // Update user's total score
    $stmt = $db->prepare("UPDATE users SET score = score + ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $user_id);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($game_title) ?> – Steven Games</title>
<link rel="stylesheet" href="css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
  .play-page { max-width:860px; margin:90px auto 3rem; padding:0 1.5rem; }
  .play-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem; }
  .play-header h1 { font-family:var(--font-display); color:var(--accent); font-size:1.5rem; margin:0; }
  .hud {
    display:flex; justify-content:space-between; flex-wrap:wrap; gap:.5rem;
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:var(--radius); padding:.7rem 1.4rem;
    font-family:var(--font-display); font-size:.95rem; color:var(--accent);
    margin-bottom:.8rem;
  }
  #game-canvas {
    display:block; width:100%;
    border:2px solid var(--border); border-radius:12px;
    background:#000; box-shadow:0 0 30px rgba(0,240,255,.25);
    cursor:crosshair; touch-action:none;
  }
  .controls { display:flex; gap:.8rem; margin-top:1rem; flex-wrap:wrap; }
  .btn-play {
    padding:.55rem 1.5rem; border-radius:8px; border:none; cursor:pointer;
    font-family:var(--font-display); font-size:.82rem; font-weight:700;
    transition:all .2s; text-decoration:none; display:inline-block;
  }
  .btn-play.primary { background:var(--accent); color:#000; }
  .btn-play.primary:hover { box-shadow:0 0 20px rgba(0,240,255,.5); }
  .btn-play.outline { background:transparent; border:1px solid var(--border); color:var(--text); }
  .btn-play.outline:hover { border-color:var(--accent); color:var(--accent); }
  .hint { margin-top:.9rem; padding:.6rem 1.1rem; font-size:.82rem; color:var(--text-muted);
    background:rgba(0,240,255,.04); border:1px solid var(--border); border-radius:8px; }
  .debug-info { margin-top: 1rem; padding: 0.5rem; background: #f0f0f0; color: #333; border-radius: 4px; font-size: 0.7rem; }
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
      <li><a href="dashboard.php">← Back to Lobby</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="play-page">
  <div class="play-header">
    <span style="font-size:2.2rem"><?= $icon_emoji ?></span>
    <div>
      <h1><?= htmlspecialchars($game_title) ?></h1>
      <p style="color:var(--text-muted);font-size:.85rem;margin:0">
        Player: <strong><?= $username ?></strong> | 
        Genre: <?= htmlspecialchars($game_genre) ?>
      </p>
    </div>
  </div>

  <div class="hud">
    <span>SCORE: <span id="hud-score">0</span></span>
    <span>LEVEL: <span id="hud-level">1</span></span>
    <span>LIVES: <span id="hud-lives">❤️❤️❤️</span></span>
    <span>BEST: <span id="hud-best"><?= $user_best_score ?></span></span>
  </div>

  <canvas id="game-canvas" width="800" height="450"></canvas>

  <div class="controls">
    <button class="btn-play primary" id="btn-start">▶ Start / Restart</button>
    <button class="btn-play outline" id="btn-pause">⏸ Pause</button>
    <a href="dashboard.php" class="btn-play outline">← Back to Lobby</a>
  </div>

  <div class="hint" id="hint-text">
    <?php if ($game_type === 'space'): ?>
      🖱 Move mouse to aim · Click or Space to shoot · Arrow keys / WASD to move · P to pause
    <?php elseif ($game_type === 'race'): ?>
      🖱 Move mouse left/right to steer · Arrow Left/Right also work · Dodge all cars! · P to pause
    <?php else: ?>
      🖱 Click a gem · Click an adjacent gem to swap · Match 3 or more of the same colour · P to pause
    <?php endif; ?>
  </div>
  
  <!-- Debug info - remove in production -->
  <div class="debug-info">
    Debug: Game loaded successfully!<br>
    Game ID: <?= $game_id ?> | Title: <?= htmlspecialchars($game_title) ?> | Type: <?= $game_type ?> | Slug: <?= htmlspecialchars($game_slug) ?>
  </div>
</div>

<script>
"use strict";

// Game configuration
const GAME_TYPE = '<?= $game_type ?>';
const GAME_ID = <?= $game_id ?>;
const USER_ID = <?= $user_id ?>;

console.log('Game loaded:', GAME_TYPE, 'Game ID:', GAME_ID);

// Save score function
async function saveGameScore(finalScore) {
    if (finalScore <= 0) return;
    
    const formData = new FormData();
    formData.append('action', 'save_score');
    formData.append('score', finalScore);
    
    try {
        const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            console.log('Score saved successfully!');
        }
    } catch (error) {
        console.error('Error saving score:', error);
    }
}

// Canvas setup
const canvas = document.getElementById('game-canvas');
const ctx = canvas.getContext('2d');
const W = 800, H = 450;
canvas.width = W;
canvas.height = H;

// HUD helpers
let score = 0, level = 1, lives = 3, best = parseInt(document.getElementById('hud-best').textContent) || 0;

function setScore(v) { 
    score = v; 
    document.getElementById('hud-score').textContent = v.toLocaleString(); 
    if(v > best){
        best = v;
        document.getElementById('hud-best').textContent = v.toLocaleString();
    }
}
function setLevel(v) { level = v; document.getElementById('hud-level').textContent = v; }
function setLives(v) { lives = v; document.getElementById('hud-lives').textContent = '❤️'.repeat(Math.max(0,v)); }
function addScore(n) { setScore(score + n); }
function resetHUD(l) { setScore(0); setLevel(1); setLives(l); }

// Game state
let running = false, paused = false, raf = null;

function stopLoop() { 
    running = false; 
    if(raf){ 
        cancelAnimationFrame(raf); 
        raf=null; 
    }
    if (score > 0) {
        saveGameScore(score);
    }
}

function gameOver() {
    stopLoop();
    if (score > 0) {
        saveGameScore(score);
    }
    
    ctx.fillStyle = 'rgba(0,0,0,.8)';
    ctx.fillRect(0,0,W,H);
    ctx.textAlign = 'center';
    ctx.shadowColor='#ff3c78'; ctx.shadowBlur=40;
    ctx.fillStyle='#ff3c78'; ctx.font='bold 54px Orbitron,monospace';
    ctx.fillText('GAME OVER', W/2, H/2-24);
    ctx.shadowColor='#00f0ff'; ctx.shadowBlur=20;
    ctx.fillStyle='#00f0ff'; ctx.font='24px Orbitron,monospace';
    ctx.fillText('Score: '+score.toLocaleString(), W/2, H/2+24);
    ctx.shadowBlur=0;
    ctx.fillStyle='#9ca3af'; ctx.font='15px Rajdhani,sans-serif';
    ctx.fillText('Press  ▶ Start / Restart  to play again', W/2, H/2+58);
}

// Keyboard handling
const K = {};
window.addEventListener('keydown', e => {
    K[e.key] = true;
    if (e.key === ' ') e.preventDefault();
    if (e.key === 'p' || e.key === 'P') togglePause();
});
window.addEventListener('keyup', e => { K[e.key] = false; });

function togglePause() {
    if (!running) return;
    paused = !paused;
    if (!paused) tick();
}

// Buttons
document.getElementById('btn-start').addEventListener('click', startGame);
document.getElementById('btn-pause').addEventListener('click', togglePause);

// Draw idle screen
function drawIdle() {
    ctx.fillStyle='#050810'; ctx.fillRect(0,0,W,H);
    ctx.textAlign='center';
    ctx.shadowColor='#00f0ff'; ctx.shadowBlur=30;
    ctx.fillStyle='#00f0ff'; ctx.font='bold 28px Orbitron,monospace';
    ctx.fillText('Press  ▶ Start  to Play', W/2, H/2-8);
    ctx.shadowBlur=0;
    ctx.fillStyle='#6b7280'; ctx.font='15px Rajdhani,sans-serif';
    ctx.fillText('Use your mouse to control · Arrow keys also work', W/2, H/2+30);
}
drawIdle();

<?php if ($game_type === 'space'): ?>
// SPACE SHOOTER GAME
let player, bullets, enemies, eBullets, stars, frame;
let mouseX = W/2;

canvas.addEventListener('mousemove', e => {
    const r = canvas.getBoundingClientRect();
    mouseX = (e.clientX - r.left) * (W / r.width);
});
canvas.addEventListener('click', () => { if(running && !paused) shoot(); });

function shoot() {
    if (player.cd > 0) return;
    bullets.push({x:player.x, y:player.y-18, s:11});
    player.cd = 9;
}

function spawnWave() {
    const cols = Math.min(4+level, 9);
    const rows = Math.min(1+Math.floor(level/2), 4);
    for (let r=0; r<rows; r++)
        for (let c=0; c<cols; c++)
            enemies.push({x:70+c*((W-140)/Math.max(cols-1,1)), y:40+r*52,
                          w:28, h:26, hp:1+Math.floor(level/3),
                          t:60+Math.random()*80});
}

function initSpace() {
    resetHUD(3); frame=0;
    stars = Array.from({length:90},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.5+.4,s:Math.random()*.7+.2}));
    player = {x:W/2, y:H-70, w:32, h:32, cd:0, inv:0};
    bullets=[]; enemies=[]; eBullets=[];
    spawnWave();
}

function updateSpace() {
    frame++;
    if (K['ArrowLeft']||K['a']||K['A']) player.x -= 5;
    if (K['ArrowRight']||K['d']||K['D']) player.x += 5;
    if (K[' ']||K['ArrowUp']) shoot();
    player.x += (mouseX - player.x) * 0.1;
    player.x = Math.max(player.w/2, Math.min(W-player.w/2, player.x));
    if (player.cd>0) player.cd--;
    if (player.inv>0) player.inv--;

    for (let i=bullets.length-1; i>=0; i--) {
        bullets[i].y -= bullets[i].s;
        if (bullets[i].y < -20) { bullets.splice(i,1); continue; }
        for (let j=enemies.length-1; j>=0; j--) {
            const e=enemies[j], b=bullets[i];
            if (!b) break;
            if (b.x>e.x-e.w/2 && b.x<e.x+e.w/2 && b.y<e.y+e.h/2 && b.y>e.y-e.h/2) {
                e.hp--;
                bullets.splice(i,1);
                if (e.hp<=0) { enemies.splice(j,1); addScore(50+level*12); }
                break;
            }
        }
    }

    enemies.forEach(e => {
        e.t--;
        if (e.t<=0) {
            eBullets.push({x:e.x, y:e.y+14, s:3+level*.3});
            e.t = Math.max(28,85-level*7) + Math.random()*30;
        }
    });
    for (let i=eBullets.length-1; i>=0; i--) {
        eBullets[i].y += eBullets[i].s;
        if (eBullets[i].y > H+20) { eBullets.splice(i,1); continue; }
        if (player.inv<=0) {
            const b=eBullets[i];
            if (Math.abs(b.x-player.x)<player.w/2-4 && b.y>player.y-player.h/2 && b.y<player.y+player.h/2) {
                eBullets.splice(i,1);
                player.inv=100;
                setLives(lives-1);
                if (lives<=0) { gameOver(); return; }
            }
        }
    }

    if (enemies.length===0) { setLevel(level+1); addScore(300); spawnWave(); }
}

function drawSpace() {
    ctx.fillStyle='#000010'; ctx.fillRect(0,0,W,H);
    stars.forEach(s=>{ s.y+=s.s; if(s.y>H)s.y=0;
        ctx.fillStyle=`rgba(255,255,255,${.2+s.r*.2})`;
        ctx.beginPath(); ctx.arc(s.x,s.y,s.r,0,Math.PI*2); ctx.fill(); });
    if (!(player.inv>0 && Math.floor(frame/5)%2===0)) {
        ctx.save(); ctx.translate(player.x,player.y);
        ctx.shadowColor='#00f0ff'; ctx.shadowBlur=22; ctx.fillStyle='#00f0ff';
        ctx.beginPath(); ctx.moveTo(0,-player.h/2); ctx.lineTo(-player.w/2,player.h/2);
        ctx.lineTo(0,player.h/4); ctx.lineTo(player.w/2,player.h/2); ctx.closePath(); ctx.fill();
        ctx.shadowColor='#ff3c78'; ctx.shadowBlur=16; ctx.fillStyle='#ff3c78';
        ctx.beginPath(); ctx.ellipse(0,player.h/2,6,8+Math.sin(frame*.25)*4,0,0,Math.PI*2); ctx.fill();
        ctx.restore();
    }
    enemies.forEach(e=>{ ctx.save(); ctx.translate(e.x,e.y);
        ctx.shadowColor='#ff3c78'; ctx.shadowBlur=14; ctx.fillStyle=e.hp>1?'#ff6600':'#ff3c78';
        ctx.beginPath(); ctx.moveTo(0,e.h/2); ctx.lineTo(-e.w/2,-e.h/2); ctx.lineTo(e.w/2,-e.h/2); ctx.closePath(); ctx.fill();
        ctx.fillStyle='#fff'; ctx.shadowBlur=0; ctx.beginPath(); ctx.arc(0,0,4,0,Math.PI*2); ctx.fill();
        ctx.restore(); });
    ctx.shadowColor='#00f0ff'; ctx.shadowBlur=10; ctx.fillStyle='#00f0ff';
    bullets.forEach(b=>ctx.fillRect(b.x-2.5,b.y,5,14));
    ctx.shadowColor='#ff3c78'; ctx.shadowBlur=10; ctx.fillStyle='#ff3c78';
    eBullets.forEach(b=>ctx.fillRect(b.x-2.5,b.y,5,12));
    ctx.shadowBlur=0;
}

function startGame() { stopLoop(); initSpace(); running=true; tick(); }
function tick() { if(!running||paused)return; updateSpace(); if(running)drawSpace(); raf=requestAnimationFrame(tick); }

<?php else: ?>
// Simple placeholder for other game types
function startGame() {
    alert('Game type "' + GAME_TYPE + '" is coming soon!');
}
<?php endif; ?>

</script>
<script src="js/main.js"></script>
</body>
</html>