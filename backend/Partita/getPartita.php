<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
require_once __DIR__ . '/../methods.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
echo json_encode(getPartita($id)); 