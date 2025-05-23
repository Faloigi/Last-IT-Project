<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$clan = $input['clan'] ?? '';

if (!$username || !$clan) {
  echo json_encode(["success" => false, "error" => "Dati obbligatori mancanti"]);
  exit;
}

$result = addPlayerToClan($username, $clan);
echo json_encode($result); 