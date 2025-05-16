<?php
$conn = require_once 'conn.php';
function getNumberPlayers() {
    global $conn;
    $query = "SELECT COUNT(*) FROM players";
    $result = $conn->query($query);
    return $result->fetch_assoc()['COUNT(*)'];
}

function getNumberGames() {
    global $conn;
    $query = "SELECT COUNT(*) FROM partite";
    $result = $conn->query($query);
    return $result->fetch_assoc()['COUNT(*)'];
}

function getStatsPlayer(){
    global $conn;
    $query = "SELECT pla_username, ptr_uccisioni/ptr_morti kd FROM players, partecipazioni
    WHERE ptr_pla_id = pla_id
    ORDER BY pla_mmr DESC
    GROUP BY AVG(kd)";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}