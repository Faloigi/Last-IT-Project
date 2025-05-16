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