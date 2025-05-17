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
    $query = "SELECT pla_username as username, 
                ROUND(ptr_uccisioni/ptr_morti, 1) as kd,
                ROUND(COUNT(CASE WHEN ptr_risultato = 'Vinto' THEN 1 END) / COUNT(*), 1) * 100 as winrate,
                ROUND(AVG(ptr_danni), 1) as danni,
                COUNT(*) as partite_giocate
    FROM players, partecipazioni
    WHERE ptr_pla_id = pla_id
    GROUP BY pla_username
    ORDER BY pla_mmr DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPlayerData($username){
    global $conn;
    $query = "SELECT 
            ROUND(ptr_uccisioni/ptr_morti, 1) as kd,
            ROUND(COUNT(CASE WHEN ptr_risultato = 'Vinto' THEN 1 END) / COUNT(*), 1) * 100 as winrate,
            ROUND(AVG(ptr_danni), 1) as danni,
            COUNT(*) as partite_giocate,
            pla_id as id
            FROM players, partecipazioni 
            WHERE pla_username = ? AND ptr_pla_id = pla_id
            GROUP BY pla_username";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getStatsClan(){
    global $conn;
    $query = "SELECT 
                cln_nome as nome,
                cln_punti as punti,
                ROUND(COUNT(CASE WHEN ptr_risultato = 'Vinto' THEN 1 END) / COUNT(DISTINCT partecipazioni.ptr_id) * 100, 1) as vittorie,
                ROUND(COUNT(CASE WHEN ptr_risultato = 'Perso' THEN 1 END) / COUNT(DISTINCT partecipazioni.ptr_id) * 100, 1) as sconfitte,
                COUNT(DISTINCT players.pla_id) as partecipanti
              FROM clans
              JOIN players ON players.pla_cln_id = clans.cln_id
              LEFT JOIN partecipazioni ON partecipazioni.ptr_pla_id = players.pla_id
              GROUP BY clans.cln_id
              ORDER BY cln_punti DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
