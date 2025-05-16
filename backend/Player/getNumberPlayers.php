<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';
echo json_encode(getNumberPlayers());