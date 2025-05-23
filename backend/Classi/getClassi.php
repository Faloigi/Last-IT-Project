<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
$conn = require_once '../conn.php';

if (!$conn) {
    echo json_encode(["success" => false, "error" => "Connessione al database fallita"]);
    exit;
}

$query = "SELECT cla_id, cla_nome FROM classi";
$result = $conn->query($query);
if (!$result) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}
$classi = [];
while ($row = $result->fetch_assoc()) {
    $classi[] = $row;
}
echo json_encode($classi); 