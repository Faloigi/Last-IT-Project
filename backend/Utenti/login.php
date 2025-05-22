<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Content-Type');
$conn = require_once __DIR__ . '/../conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? null;
$password = $data['password'] ?? null;

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username e password obbligatori']);
    exit;
}

// Calcola hash sha256 della password
$hash = hash('sha256', $password);

$stmt = $conn->prepare('SELECT ute_id, ute_username, ute_ruolo FROM utenti WHERE ute_username = ? AND ute_password = ?');
$stmt->bind_param('ss', $username, $hash);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if ($user) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenziali non valide']);
} 