<?php
$conn = include 'conn.php';
function getNumberPlayers() {
    global $conn;
    $query = "SELECT COUNT(*) FROM players";
    $result = $conn->query($query);
    return $result->fetch_assoc()['COUNT(*)'];
}

