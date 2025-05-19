<?php
$conn = require_once __DIR__ . '/../conn.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
$sql = "SELECT ran_id, ran_nome, ran_image FROM ranks";
$res = $conn->query($sql);
$ranks = [];
while($row = $res->fetch_assoc()) {
    $ranks[] = $row;
}
echo json_encode($ranks); 