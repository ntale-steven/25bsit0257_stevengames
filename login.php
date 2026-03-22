<?php
session_start();

// Already logged in — send to redirect target or default
if (isset($_SESSION['user_id'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
    if ($redirect && strpos($redirect, '..') === false) {
        header('Location: ' . $redirect);
    } else {
        header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php'));
    }
    exit;
}

require_once 'includes/auth.php';
$error = '';
// Keep redirect param across POST
$redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect_to = trim($_POST['redirect_to'] ?? '');

    if (!$username || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $result = loginUser($username, $password);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            // After login, go to redirect target or default
            if ($redirect_to && strpos($redirect_to, '..') === false) {
                header('Location: ' . $redirect_to);
            } else {
                header('Location: ' . ($result['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php'));
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Steven Games</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="bg-grid"></div>
<div class="particles" id="particles"></div>

<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">
      <a href="index.php">⚡ STEVEN<span>GAMES</span></a>
    </div>
    <h2>Welcome Back</h2>
    <p class="sub">Sign in to play</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <!-- Preserve the redirect target through the POST -->
      <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirect_to) ?>">

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn-full">SIGN IN</button>
    </form>

    <div class="auth-alt">
      Don't have an account? <a href="register.php">Sign Up</a>
    </div>
  </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
