<?php
$conn = require_once __DIR__ . '/../conn.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
$sql = "SELECT map_id, map_nome, map_image FROM mappe";
$res = $conn->query($sql);
$mappe = [];
while($row = $res->fetch_assoc()) {
    $mappe[] = $row;
}
echo json_encode($mappe); 