<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
require_once '../conn.php';
require_once '../methods.php';

$input = json_decode(file_get_contents('php://input'), true);
$nome = $input['nome'] ?? '';
$image = $input['image'] ?? '';
$membri = $input['membri'] ?? [];
$creator = $input['creator'] ?? '';

if (!$nome || !$image || empty($membri) || !$creator) {
    echo json_encode(['success' => false, 'error' => 'Tutti i campi sono obbligatori']);
    exit;
}

$result = createClan($nome, $image, $membri, $creator);
echo json_encode($result); 