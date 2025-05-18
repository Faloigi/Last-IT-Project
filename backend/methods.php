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
    // Dati base player
    $query = "SELECT pla_id, pla_username, pla_livello, pla_mmr, pla_ran_id FROM players WHERE pla_username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $player = $stmt->get_result()->fetch_assoc();
    if (!$player) return null;

    // Rank
    $query = "SELECT ran_nome, ran_image FROM ranks WHERE ran_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $player['pla_ran_id']);
    $stmt->execute();
    $rank = $stmt->get_result()->fetch_assoc();

    // Eroi giocati
    $query = "SELECT e.ero_id, e.ero_nome as nome, c.cla_nome as classe, e.ero_difficolta as difficolta, e.ero_image as img, COUNT(*) as partite_giocate
              FROM partecipazioni p
              JOIN eroi e ON p.ptr_ero_id = e.ero_id
              LEFT JOIN classi c ON e.ero_cla_id = c.cla_id
              WHERE p.ptr_pla_id = ?
              GROUP BY e.ero_id, e.ero_nome, c.cla_nome, e.ero_difficolta, e.ero_image
              ORDER BY partite_giocate DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $player['pla_id']);
    $stmt->execute();
    $heroesPlayed = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Partite giocate (con lista giocatori e immagine eroe)
    $query = "SELECT par.par_id, par.par_inizio as data, m.mod_nome as modalita, mp.map_nome as mappa, e.ero_nome as eroe, e.ero_image as eroe_img, p.ptr_risultato as risultato, p.ptr_danni as danni, p.ptr_cure as cure, p.ptr_uccisioni as uccisioni, p.ptr_morti as morti
              FROM partecipazioni p
              JOIN partite par ON p.ptr_par_id = par.par_id
              LEFT JOIN modalita m ON par.par_mod_id = m.mod_id
              LEFT JOIN mappe mp ON par.par_map_id = mp.map_id
              LEFT JOIN eroi e ON p.ptr_ero_id = e.ero_id
              WHERE p.ptr_pla_id = ?
              ORDER BY par.par_inizio DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $player['pla_id']);
    $stmt->execute();
    $matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Per ogni partita, aggiungi la lista dei giocatori partecipanti (solo nomi)
    foreach ($matches as &$match) {
        $par_id = $match['par_id'];
        $q = "SELECT pl.pla_username FROM partecipazioni p JOIN players pl ON p.ptr_pla_id = pl.pla_id WHERE p.ptr_par_id = ?";
        $s = $conn->prepare($q);
        $s->bind_param("i", $par_id);
        $s->execute();
        $players = $s->get_result()->fetch_all(MYSQLI_ASSOC);
        $match['partecipanti'] = array_column($players, 'pla_username');
    }

    // Statistiche generali
    $query = "SELECT 
            ROUND(SUM(p.ptr_uccisioni)/NULLIF(SUM(p.ptr_morti),0), 1) as kd,
            ROUND(SUM(CASE WHEN p.ptr_risultato = 'Vinto' THEN 1 ELSE 0 END) / COUNT(*), 2) * 100 as winrate,
            ROUND(AVG(p.ptr_danni), 1) as danni,
            COUNT(*) as partite_giocate
            FROM partecipazioni p
            WHERE p.ptr_pla_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $player['pla_id']);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();

    return [
        'id' => $player['pla_id'],
        'username' => $player['pla_username'],
        'livello' => $player['pla_livello'],
        'mmr' => $player['pla_mmr'],
        'rank' => $rank ? $rank['ran_nome'] : null,
        'rank_img' => $rank ? $rank['ran_image'] : null,
        'heroesPlayed' => $heroesPlayed,
        'matches' => $matches,
        'kd' => $stats['kd'],
        'winrate' => $stats['winrate'],
        'danni' => $stats['danni'],
        'partite_giocate' => $stats['partite_giocate']
    ];
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
