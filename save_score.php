<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['game_id'];
$score = $data['score'];
$user_id = $_SESSION['user_id'];

$db = getDB();

// Save score
$stmt = $db->prepare("INSERT INTO scores (user_id, game_id, score) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $user_id, $game_id, $score);
$stmt->execute();
$stmt->close();

// Update user total score
$stmt = $db->prepare("UPDATE users SET score = score + ? WHERE id = ?");
$stmt->bind_param("ii", $score, $user_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
?>