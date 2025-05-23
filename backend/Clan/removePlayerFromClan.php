<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';

if (!$username) {
  echo json_encode(["success" => false, "error" => "Username mancante"]);
  exit;
}

$result = removePlayerFromClan($username);
echo json_encode($result); 