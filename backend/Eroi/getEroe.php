<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
require_once '../methods.php';

$nome = $_GET['nome'] ?? null;
$id = $_GET['id'] ?? null;

if ($nome) {
  echo json_encode(getEroe($nome));
  exit;
}
if ($id) {
  // Cerca per id (opzionale, qui puoi aggiungere una funzione se vuoi)
  global $conn;
  $query = "SELECT * FROM eroi WHERE ero_id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  echo json_encode($result->fetch_assoc());
  exit;
}
echo json_encode(["success" => false, "error" => "Parametro nome o id mancante"]); 