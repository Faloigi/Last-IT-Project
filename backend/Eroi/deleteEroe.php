<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;
if (!$id) {
  echo json_encode(["success" => false, "error" => "ID mancante"]);
  exit;
}
echo json_encode(deleteEroe($id)); 