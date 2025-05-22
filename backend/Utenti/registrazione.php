<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Content-Type');
$conn = require_once __DIR__ . '/../conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$username || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Tutti i campi sono obbligatori']);
    exit;
}

// Controlla se username o email già esistono
$stmt = $conn->prepare('SELECT ute_id FROM utenti WHERE ute_username = ? OR ute_email = ?');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username o email già registrati']);
    exit;
}

// Hash sha256 della password
$hash = hash('sha256', $password);
$data_reg = date('Y-m-d H:i:s');
$ruolo = 'utente';

$stmt = $conn->prepare('INSERT INTO utenti (ute_username, ute_email, ute_password, ute_data_registrazione, ute_ruolo) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('sssss', $username, $email, $hash, $data_reg, $ruolo);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Registrazione avvenuta con successo']);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nella registrazione']);
} 