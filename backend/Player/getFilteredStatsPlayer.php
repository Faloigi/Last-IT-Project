<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';
echo json_encode(getFilteredStatsPlayer(
    isset($_GET['modalita']) ? $_GET['modalita'] : null,
    isset($_GET['mappa']) ? $_GET['mappa'] : null,
    isset($_GET['rank']) ? $_GET['rank'] : null
)); 