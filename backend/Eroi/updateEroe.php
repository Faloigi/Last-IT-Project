<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$nome_originale = $input['nome_originale'] ?? '';
$nome_nuovo = $input['nome'] ?? '';
$difficolta = $input['difficolta'] ?? 1;
$cla_id = $input['cla_id'] ?? null;
$image = $input['image'] ?? '';

if (!$nome_originale || !$nome_nuovo) {
  echo json_encode(["success" => false, "error" => "Nome mancante"]);
  exit;
}
echo json_encode(updateEroeByName($nome_originale, $nome_nuovo, $difficolta, $cla_id, $image)); 