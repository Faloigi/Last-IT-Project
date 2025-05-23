<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$nome = $input['nome'] ?? '';
$difficolta = $input['difficolta'] ?? 1;
$cla_id = $input['cla_id'] ?? null;
$image = $input['image'] ?? '';

echo json_encode(createEroe($nome, $difficolta, $cla_id, $image)); 