<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$conn = include 'conn.php';
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['message'])) {
  echo json_encode(['status' => 'success', 'message' => 'Message received: ' . $data['message']]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'No message received']);
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getStatsEroe':
            echo json_encode(getStatsEroe());
            break;
    }
}