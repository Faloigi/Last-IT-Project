<?php
$conn = require_once __DIR__ . '/../conn.php';
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

$modalita = isset($_GET['modalita']) && $_GET['modalita'] !== '' ? $_GET['modalita'] : null;
$mappa = isset($_GET['mappa']) && $_GET['mappa'] !== '' ? $_GET['mappa'] : null;
$rank = isset($_GET['rank']) && $_GET['rank'] !== '' ? $_GET['rank'] : null;

$sql = "
SELECT
  p.pla_id,
  p.pla_username,
  p.pla_mmr,
  r.ran_nome AS rank,
  COUNT(pa.ptr_id) AS partite_giocate,
  SUM(pa.ptr_uccisioni) AS uccisioni,
  SUM(pa.ptr_morti) AS morti,
  SUM(pa.ptr_danni) AS danni,
  SUM(pa.ptr_risultato = 'Vinto') AS vittorie
FROM players p
JOIN ranks r ON p.pla_ran_id = r.ran_id
JOIN partecipazioni pa ON pa.ptr_pla_id = p.pla_id
JOIN partite par ON pa.ptr_par_id = par.par_id
JOIN modalita m ON par.par_mod_id = m.mod_id
JOIN mappe ma ON par.par_map_id = ma.map_id
WHERE 1
";
$params = [];
if ($modalita) {
  $sql .= " AND m.mod_nome = ? ";
  $params[] = $modalita;
}
if ($mappa) {
  $sql .= " AND ma.map_nome = ? ";
  $params[] = $mappa;
}
if ($rank) {
  $sql .= " AND r.ran_nome = ? ";
  $params[] = $rank;
}
$sql .= " GROUP BY p.pla_id ORDER BY p.pla_mmr DESC ";

$stmt = $conn->prepare($sql);
if ($params) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$players = [];
while($row = $res->fetch_assoc()) {
    $players[] = $row;
}
echo json_encode($players); 