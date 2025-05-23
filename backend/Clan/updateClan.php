<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$nome_originale = $input['nome_originale'] ?? '';
$nome = $input['nome'] ?? '';

if (!$nome_originale || !$nome) {
  echo json_encode(["success" => false, "error" => "Dati obbligatori mancanti"]);
  exit;
}

global $conn;
$stmt = $conn->prepare("UPDATE clans SET cln_nome = ? WHERE cln_nome = ?");
$stmt->bind_param("ss", $nome, $nome_originale);
if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
} 