<?php
// ===== STEVEN GAMES - play.php =====
session_start();

// Manual login check — redirect to login with return URL so player
// lands back on this game after signing in (XAMPP subfolder safe)
if (!isset($_SESSION['user_id'])) {
    $base        = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $return_slug = isset($_GET['game']) ? urlencode(trim($_GET['game'])) : '';
    $return_url  = urlencode('play.php?game=' . rawurldecode($return_slug));
    header('Location: ' . $base . '/login.php?redirect=play.php%3Fgame%3D' . rawurldecode($return_slug));
    exit;
}

$username  = htmlspecialchars($_SESSION['username']);
$game_slug = isset($_GET['game']) ? trim($_GET['game']) : '';

$game_map = [
    'space-blaster' => ['title' => 'Space Blaster', 'icon' => '🚀', 'type' => 'space'],
    'dragon-quest'  => ['title' => 'Dragon Quest',  'icon' => '🐉', 'type' => 'space'],
    'battle-arena'  => ['title' => 'Battle Arena',  'icon' => '⚔️', 'type' => 'space'],
    'turbo-race'    => ['title' => 'Turbo Race',    'icon' => '🏎️', 'type' => 'race'],
    'mind-matrix'   => ['title' => 'Mind Matrix',   'icon' => '🧩', 'type' => 'puzzle'],
    'world-builder' => ['title' => 'World Builder', 'icon' => '🌍', 'type' => 'puzzle'],
];

// Unknown slug → back to dashboard
if (!array_key_exists($game_slug, $game_map)) {
    header('Location: dashboard.php');
    exit;
}

$game      = $game_map[$game_slug];
$game_type = $game['type'];
$game_title = $game['title'];
$game_icon  = $game['icon'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $game_title ?> – Steven Games</title>
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
      <li><a href="dashboard.php">← Lobby</a></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </div>
</nav>

<div class="play-page">
  <div class="play-header">
    <span style="font-size:2.2rem"><?= $game_icon ?></span>
    <div>
      <h1><?= $game_title ?></h1>
      <p style="color:var(--text-muted);font-size:.85rem;margin:0">Player: <strong><?= $username ?></strong></p>
    </div>
  </div>

  <div class="hud">
    <span>SCORE: <span id="hud-score">0</span></span>
    <span>LEVEL: <span id="hud-level">1</span></span>
    <span>LIVES: <span id="hud-lives">❤️❤️❤️</span></span>
    <span>BEST:  <span id="hud-best">0</span></span>
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
</div>

<script>
"use strict";

// ─── Canvas setup ────────────────────────────────────────────
const canvas = document.getElementById('game-canvas');
const ctx    = canvas.getContext('2d');
const W = 800, H = 450;           // internal resolution (never changes)
canvas.width  = W;
canvas.height = H;

// ─── HUD helpers ─────────────────────────────────────────────
let score = 0, level = 1, lives = 3, best = 0;
function setScore(v)  { score = v; document.getElementById('hud-score').textContent = v.toLocaleString(); if(v>best){best=v;document.getElementById('hud-best').textContent=v.toLocaleString();} }
function setLevel(v)  { level = v; document.getElementById('hud-level').textContent = v; }
function setLives(v)  { lives = v; document.getElementById('hud-lives').textContent = '❤️'.repeat(Math.max(0,v)); }
function addScore(n)  { setScore(score + n); }
function resetHUD(l)  { setScore(0); setLevel(1); setLives(l); }

// ─── State ───────────────────────────────────────────────────
let running = false, paused = false, raf = null;

function stopLoop() { running = false; if(raf){ cancelAnimationFrame(raf); raf=null; } }

function gameOver() {
    stopLoop();
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

// ─── Keyboard ────────────────────────────────────────────────
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

// ─── Buttons ─────────────────────────────────────────────────
document.getElementById('btn-start').addEventListener('click', startGame);
document.getElementById('btn-pause').addEventListener('click', togglePause);

// ─── Draw idle screen ────────────────────────────────────────
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

// ═══════════════════════════════════════════════════════════════
//  GAME TYPE: <?= $game_type ?>

// ═══════════════════════════════════════════════════════════════

<?php if ($game_type === 'space'): ?>
// ─────────────────────────────────────────────────────────────
//  SPACE SHOOTER
// ─────────────────────────────────────────────────────────────
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
    // Move player
    if (K['ArrowLeft']||K['a']||K['A']) player.x -= 5;
    if (K['ArrowRight']||K['d']||K['D']) player.x += 5;
    if (K[' ']||K['ArrowUp']) shoot();
    player.x += (mouseX - player.x) * 0.1;
    player.x = Math.max(player.w/2, Math.min(W-player.w/2, player.x));
    if (player.cd>0) player.cd--;
    if (player.inv>0) player.inv--;

    // Player bullets
    for (let i=bullets.length-1; i>=0; i--) {
        bullets[i].y -= bullets[i].s;
        if (bullets[i].y < -20) { bullets.splice(i,1); continue; }
        // vs enemies
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

    // Enemy fire & movement
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

    // Next wave
    if (enemies.length===0) { setLevel(level+1); addScore(300); spawnWave(); }
}

function drawSpace() {
    ctx.fillStyle='#000010'; ctx.fillRect(0,0,W,H);
    // Stars
    stars.forEach(s=>{ s.y+=s.s; if(s.y>H)s.y=0;
        ctx.fillStyle=`rgba(255,255,255,${.2+s.r*.2})`;
        ctx.beginPath(); ctx.arc(s.x,s.y,s.r,0,Math.PI*2); ctx.fill(); });
    // Player
    if (!(player.inv>0 && Math.floor(frame/5)%2===0)) {
        ctx.save(); ctx.translate(player.x,player.y);
        ctx.shadowColor='#00f0ff'; ctx.shadowBlur=22; ctx.fillStyle='#00f0ff';
        ctx.beginPath(); ctx.moveTo(0,-player.h/2); ctx.lineTo(-player.w/2,player.h/2);
        ctx.lineTo(0,player.h/4); ctx.lineTo(player.w/2,player.h/2); ctx.closePath(); ctx.fill();
        ctx.shadowColor='#ff3c78'; ctx.shadowBlur=16; ctx.fillStyle='#ff3c78';
        ctx.beginPath(); ctx.ellipse(0,player.h/2,6,8+Math.sin(frame*.25)*4,0,0,Math.PI*2); ctx.fill();
        ctx.restore();
    }
    // Enemies
    enemies.forEach(e=>{ ctx.save(); ctx.translate(e.x,e.y);
        ctx.shadowColor='#ff3c78'; ctx.shadowBlur=14; ctx.fillStyle=e.hp>1?'#ff6600':'#ff3c78';
        ctx.beginPath(); ctx.moveTo(0,e.h/2); ctx.lineTo(-e.w/2,-e.h/2); ctx.lineTo(e.w/2,-e.h/2); ctx.closePath(); ctx.fill();
        ctx.fillStyle='#fff'; ctx.shadowBlur=0; ctx.beginPath(); ctx.arc(0,0,4,0,Math.PI*2); ctx.fill();
        ctx.restore(); });
    // Bullets
    ctx.shadowColor='#00f0ff'; ctx.shadowBlur=10; ctx.fillStyle='#00f0ff';
    bullets.forEach(b=>ctx.fillRect(b.x-2.5,b.y,5,14));
    ctx.shadowColor='#ff3c78'; ctx.shadowBlur=10; ctx.fillStyle='#ff3c78';
    eBullets.forEach(b=>ctx.fillRect(b.x-2.5,b.y,5,12));
    ctx.shadowBlur=0;
}

function startGame() { stopLoop(); initSpace(); running=true; tick(); }
function tick() { if(!running||paused)return; updateSpace(); if(running)drawSpace(); raf=requestAnimationFrame(tick); }

<?php elseif ($game_type === 'race'): ?>
// ─────────────────────────────────────────────────────────────
//  TURBO RACE
// ─────────────────────────────────────────────────────────────
const RL=W*.15, RR=W*.85, RW=RR-RL;
const LANES=[RL+RW*.2, RL+RW*.5, RL+RW*.8];
let player, obs, dashes, frame, spd;
let mouseX = LANES[1];

canvas.addEventListener('mousemove', e=>{
    const r=canvas.getBoundingClientRect();
    mouseX=(e.clientX-r.left)*(W/r.width);
});

function initRace() {
    resetHUD(3); frame=0; spd=4;
    player={x:LANES[1], y:H-90, w:36, h:64};
    obs=[]; dashes=Array.from({length:10},(_,i)=>({y:i*(H/10)}));
}

function rr(x,y,w,h,r){
    ctx.beginPath();
    ctx.moveTo(x+r,y); ctx.lineTo(x+w-r,y); ctx.arcTo(x+w,y,x+w,y+r,r);
    ctx.lineTo(x+w,y+h-r); ctx.arcTo(x+w,y+h,x+w-r,y+h,r);
    ctx.lineTo(x+r,y+h); ctx.arcTo(x,y+h,x,y+h-r,r);
    ctx.lineTo(x,y+r); ctx.arcTo(x,y,x+r,y,r); ctx.closePath();
}

function drawCar(x,y,w,h,col) {
    ctx.save();
    ctx.shadowColor=col; ctx.shadowBlur=16; ctx.fillStyle=col;
    rr(x-w/2,y-h/2,w,h,8); ctx.fill();
    ctx.shadowBlur=0; ctx.fillStyle='rgba(0,0,0,.55)';
    ctx.fillRect(x-w/2+4,y-h/2+10,w-8,14);
    ctx.fillRect(x-w/2+4,y+h/2-24,w-8,14);
    ctx.fillStyle='#111';
    [[-w/2-2,-h/2+8],[w/2-4,-h/2+8],[-w/2-2,h/2-20],[w/2-4,h/2-20]].forEach(([ox,oy])=>{
        ctx.fillRect(x+ox,y+oy,6,12); });
    ctx.restore();
}

function updateRace() {
    frame++; addScore(1);
    if (frame%400===0) { setLevel(level+1); spd=Math.min(spd+.6,14); }
    dashes.forEach(d=>{ d.y+=spd*2.2; if(d.y>H)d.y-=H; });
    if (frame%Math.max(26,88-level*7)===0) {
        const lx=LANES[Math.floor(Math.random()*3)];
        obs.push({x:lx,y:-80,w:36,h:64,c:['#ff3c78','#ff6600','#7c3aed','#e53935'][Math.floor(Math.random()*4)]});
    }
    obs.forEach(o=>o.y+=spd);
    obs=obs.filter(o=>o.y<H+100);
    if (K['ArrowLeft']||K['a']||K['A']) player.x-=5.5;
    if (K['ArrowRight']||K['d']||K['D']) player.x+=5.5;
    player.x+=(mouseX-player.x)*.09;
    player.x=Math.max(RL+player.w/2, Math.min(RR-player.w/2, player.x));
    for (let i=obs.length-1;i>=0;i--) {
        const o=obs[i];
        if (Math.abs(o.x-player.x)<(o.w+player.w)/2-6 && Math.abs(o.y-player.y)<(o.h+player.h)/2-6) {
            obs.splice(i,1); setLives(lives-1);
            if (lives<=0) { gameOver(); return; }
        }
    }
}

function drawRace() {
    ctx.fillStyle='#0a1520'; ctx.fillRect(0,0,W,H);
    ctx.fillStyle='#0a1f0a'; ctx.fillRect(0,0,RL,H); ctx.fillRect(RR,0,W-RR,H);
    ctx.fillStyle='#1c1c2e'; ctx.fillRect(RL,0,RW,H);
    ctx.strokeStyle='#fff'; ctx.lineWidth=4;
    ctx.beginPath(); ctx.moveTo(RL,0); ctx.lineTo(RL,H); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(RR,0); ctx.lineTo(RR,H); ctx.stroke();
    ctx.strokeStyle='#ffd700'; ctx.lineWidth=3; ctx.setLineDash([26,18]);
    [LANES[0]+(LANES[1]-LANES[0])/2, LANES[1]+(LANES[2]-LANES[1])/2].forEach(lx=>{
        dashes.forEach(d=>{ ctx.beginPath(); ctx.moveTo(lx,d.y); ctx.lineTo(lx,d.y+26); ctx.stroke(); });
    });
    ctx.setLineDash([]);
    obs.forEach(o=>drawCar(o.x,o.y,o.w,o.h,o.c));
    drawCar(player.x,player.y,player.w,player.h,'#00f0ff');
    ctx.fillStyle='#9ca3af'; ctx.font='13px Orbitron,monospace'; ctx.textAlign='left';
    ctx.fillText('SPEED: '+spd.toFixed(1), RL+10, 26);
}

function startGame() { stopLoop(); initRace(); running=true; tick(); }
function tick() { if(!running||paused)return; updateRace(); if(running)drawRace(); raf=requestAnimationFrame(tick); }

<?php else: // puzzle ?>
// ─────────────────────────────────────────────────────────────
//  GEM MATCH PUZZLE
// ─────────────────────────────────────────────────────────────
const COLS=7, ROWS=7;
const GEMS=['#00f0ff','#ff3c78','#7c3aed','#ffd700','#00ff88','#ff6600'];
let grid, sel, combo, busy;

function mkGem(){ return {c:GEMS[Math.floor(Math.random()*GEMS.length)], mark:false}; }
const CW=()=>W/COLS, CH=()=>H/ROWS;

function initPuzzle() {
    resetHUD(5); combo=0; busy=false; sel=null;
    grid=Array.from({length:ROWS},()=>Array.from({length:COLS},mkGem));
}

function findMatches(){
    let found=false;
    for(let r=0;r<ROWS;r++){let run=1;for(let c=1;c<=COLS;c++){if(c<COLS&&grid[r][c].c===grid[r][c-1].c)run++;else{if(run>=3){for(let k=c-run;k<c;k++)grid[r][k].mark=true;found=true;}run=1;}}}
    for(let c=0;c<COLS;c++){let run=1;for(let r=1;r<=ROWS;r++){if(r<ROWS&&grid[r][c].c===grid[r-1][c].c)run++;else{if(run>=3){for(let k=r-run;k<r;k++)grid[k][c].mark=true;found=true;}run=1;}}}
    return found;
}

function clearMatches(){
    let n=0; grid.forEach(row=>row.forEach(g=>{if(g.mark)n++;}));
    if(!n){combo=0;busy=false;return;}
    combo++; addScore(n*10*combo); if(score>=level*600)setLevel(level+1);
    for(let c=0;c<COLS;c++){
        let col=grid.map(r=>r[c]).filter(g=>!g.mark);
        while(col.length<ROWS)col.unshift(mkGem());
        for(let r=0;r<ROWS;r++)grid[r][c]=col[r];
    }
    setTimeout(()=>{ if(findMatches())clearMatches(); else{combo=0;busy=false;} },300);
}

canvas.addEventListener('click',e=>{
    if(!running||paused||busy)return;
    const rect=canvas.getBoundingClientRect();
    const mx=(e.clientX-rect.left)*(W/rect.width);
    const my=(e.clientY-rect.top)*(H/rect.height);
    const c=Math.floor(mx/CW()), r=Math.floor(my/CH());
    if(r<0||r>=ROWS||c<0||c>=COLS)return;
    if(!sel){sel={r,c};}
    else{
        const dr=Math.abs(r-sel.r),dc=Math.abs(c-sel.c);
        if((dr===1&&dc===0)||(dr===0&&dc===1)){
            const tmp=grid[r][c].c; grid[r][c].c=grid[sel.r][sel.c].c; grid[sel.r][sel.c].c=tmp;
            if(!findMatches()){
                const tmp2=grid[r][c].c; grid[r][c].c=grid[sel.r][sel.c].c; grid[sel.r][sel.c].c=tmp2;
            } else { busy=true; clearMatches(); }
        }
        sel=null;
    }
});

function drawPuzzle(){
    ctx.fillStyle='#05080e'; ctx.fillRect(0,0,W,H);
    const gw=CW(),gh=CH();
    for(let r=0;r<ROWS;r++) for(let c=0;c<COLS;c++){
        const x=c*gw+3,y=r*gh+3,w=gw-6,h=gh-6;
        const isSel=sel&&sel.r===r&&sel.c===c;
        ctx.save();
        ctx.shadowColor=grid[r][c].c; ctx.shadowBlur=isSel?35:8;
        ctx.fillStyle=grid[r][c].c;
        ctx.beginPath(); ctx.roundRect(x,y,w,h,10); ctx.fill();
        if(isSel){ctx.strokeStyle='#fff';ctx.lineWidth=3;ctx.stroke();}
        ctx.shadowBlur=0; ctx.fillStyle='rgba(255,255,255,.16)';
        ctx.beginPath(); ctx.ellipse(x+w*.35,y+h*.3,w*.2,h*.13,-.4,0,Math.PI*2); ctx.fill();
        ctx.restore();
    }
    ctx.strokeStyle='rgba(0,240,255,.07)'; ctx.lineWidth=1;
    for(let r=0;r<=ROWS;r++){ctx.beginPath();ctx.moveTo(0,r*gh);ctx.lineTo(W,r*gh);ctx.stroke();}
    for(let c=0;c<=COLS;c++){ctx.beginPath();ctx.moveTo(c*gw,0);ctx.lineTo(c*gw,H);ctx.stroke();}
}

function startGame(){ stopLoop(); initPuzzle(); running=true; tick(); }
function tick(){ if(!running||paused)return; drawPuzzle(); raf=requestAnimationFrame(tick); }
<?php endif; ?>

</script>
<script src="js/main.js"></script>
</body>
</html>
