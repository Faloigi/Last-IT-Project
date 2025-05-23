<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../methods.php';

$classe = $_GET['classe'] ?? null;
$mappa = $_GET['mappa'] ?? null;
$rank = $_GET['rank'] ?? null;

$result = getStatsEroe($classe, $mappa, $rank);
echo json_encode(is_array($result) ? $result : []); 