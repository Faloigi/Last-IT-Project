<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../db.php';

$query = "SELECT cla_id, cla_nome FROM classi";
$result = $conn->query($query);
$classi = [];
while ($row = $result->fetch_assoc()) {
    $classi[] = $row;
}
echo json_encode($classi); 