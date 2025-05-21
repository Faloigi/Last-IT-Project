<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';
$classe = isset($_GET['classe']) ? $_GET['classe'] : null;
$mappa = isset($_GET['mappa']) ? $_GET['mappa'] : null;
$rank = isset($_GET['rank']) ? $_GET['rank'] : null;
echo json_encode(getStatsHero($classe, $mappa, $rank)); 