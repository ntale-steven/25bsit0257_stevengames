<?php
// ===== STEVEN GAMES - AUTH HELPERS =====
require_once __DIR__ . '/db.php';

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        // Use relative path — works on XAMPP subfolders (localhost/steven_games/)
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        header('Location: ' . $base . '/login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        header('Location: ' . $base . '/index.php');
        exit;
    }
}

function loginUser($username, $password) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, password, role, status FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) return ['error' => 'Invalid username or password.'];
    if ($user['status'] === 'banned') return ['error' => 'Your account has been banned.'];
    if (!password_verify($password, $user['password'])) return ['error' => 'Invalid username or password.'];

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    $db->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");

    return ['success' => true, 'role' => $user['role']];
}

function registerUser($username, $email, $password) {
    $db = getDB();

    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['error' => 'Username or email already exists.'];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password, role, status, created_at) VALUES (?, ?, ?, 'user', 'active', NOW())");
    $stmt->bind_param("sss", $username, $email, $hash);
    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['error' => 'Registration failed. Please try again.'];
}
?>
