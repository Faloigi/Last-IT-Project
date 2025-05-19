<?php
$conn = require_once __DIR__ . '/../conn.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');
$sql = "SELECT mod_id, mod_nome FROM modalita";
$res = $conn->query($sql);
$modalita = [];
while($row = $res->fetch_assoc()) {
    $modalita[] = $row;
}
echo json_encode($modalita); 