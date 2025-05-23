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
    $query = "SELECT pla_id, pla_username, pla_livello, pla_mmr, pla_ran_id, pla_cln_id FROM players WHERE pla_username = ?";
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
    $eroiGiocati = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
        'eroiGiocati' => $eroiGiocati,
        'matches' => $matches,
        'kd' => $stats['kd'],
        'winrate' => $stats['winrate'],
        'danni' => $stats['danni'],
        'partite_giocate' => $stats['partite_giocate'],
        'clan_id' => $player['pla_cln_id']
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

function getClanName($id){
    global $conn;
    $query = "SELECT cln_nome as nome FROM clans WHERE cln_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getFilteredStatsPlayer($modalita = null, $mappa = null, $rank = null) {
    global $conn;
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
    $types = '';
    if ($modalita) {
      $sql .= " AND m.mod_nome = ? ";
      $params[] = $modalita;
      $types .= 's';
    }
    if ($mappa) {
      $sql .= " AND ma.map_nome = ? ";
      $params[] = $mappa;
      $types .= 's';
    }
    if ($rank) {
      $sql .= " AND r.ran_nome = ? ";
      $params[] = $rank;
      $types .= 's';
    }
    $sql .= " GROUP BY p.pla_id ORDER BY p.pla_mmr DESC ";
    $stmt = $conn->prepare($sql);
    if ($params) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $players = [];
    while($row = $res->fetch_assoc()) {
        $players[] = $row;
    }
    return $players;
}

function getMappe() {
    global $conn;
    $sql = "SELECT map_id, map_nome, map_image FROM mappe";
    $res = $conn->query($sql);
    $mappe = [];
    while($row = $res->fetch_assoc()) {
        $mappe[] = $row;
    }
    return $mappe;
}

function getModalita() {
    global $conn;
    $sql = "SELECT mod_id, mod_nome FROM modalita";
    $res = $conn->query($sql);
    $modalita = [];
    while($row = $res->fetch_assoc()) {
        $modalita[] = $row;
    }
    return $modalita;
}

function getRanks() {
    global $conn;
    $sql = "SELECT ran_id, ran_nome, ran_image FROM ranks";
    $res = $conn->query($sql);
    $ranks = [];
    while($row = $res->fetch_assoc()) {
        $ranks[] = $row;
    }
    return $ranks;
}

function getStatsEroe($classe = null, $mappa = null, $rank = null) {
    global $conn;
    $where = [];
    $params = [];
    $types = '';
    if ($classe) {
        $where[] = 'c.cla_nome = ?';
        $params[] = $classe;
        $types .= 's';
    }
    if ($mappa) {
        $where[] = 'mp.map_nome = ?';
        $params[] = $mappa;
        $types .= 's';
    }
    if ($rank) {
        $where[] = 'r.ran_nome = ?';
        $params[] = $rank;
        $types .= 's';
    }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Query principale: aggrega solo sulle partite che rispettano i filtri
    $query = "SELECT 
        e.ero_nome as eroe,
        c.cla_nome as classe,
        e.ero_difficolta as difficolta,
        e.ero_image as img,
        ROUND(SUM(p.ptr_uccisioni)/NULLIF(SUM(p.ptr_morti),0), 2) as kd,
        ROUND(SUM(CASE WHEN p.ptr_risultato = 'Vinto' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as vittorie,
        COUNT(*) as partite
    FROM partecipazioni p
    JOIN eroi e ON p.ptr_ero_id = e.ero_id
    LEFT JOIN classi c ON e.ero_cla_id = c.cla_id
    LEFT JOIN partite par ON p.ptr_par_id = par.par_id
    LEFT JOIN mappe mp ON par.par_map_id = mp.map_id
    LEFT JOIN players pl ON p.ptr_pla_id = pl.pla_id
    LEFT JOIN ranks r ON pl.pla_ran_id = r.ran_id
    $whereSql
    GROUP BY e.ero_id, e.ero_nome, c.cla_nome, e.ero_difficolta, e.ero_image
    ORDER BY vittorie DESC, partite DESC";
    $stmt = $conn->prepare($query);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $eroi = $result->fetch_all(MYSQLI_ASSOC);

    // Calcola il totale delle partite filtrate (per % scelta)
    $queryTot = "SELECT COUNT(*) as tot_partite
        FROM partecipazioni p
        LEFT JOIN eroi e ON p.ptr_ero_id = e.ero_id
        LEFT JOIN classi c ON e.ero_cla_id = c.cla_id
        LEFT JOIN partite par ON p.ptr_par_id = par.par_id
        LEFT JOIN mappe mp ON par.par_map_id = mp.map_id
        LEFT JOIN players pl ON p.ptr_pla_id = pl.pla_id
        LEFT JOIN ranks r ON pl.pla_ran_id = r.ran_id
        $whereSql";
    $stmtTot = $conn->prepare($queryTot);
    if ($params) $stmtTot->bind_param($types, ...$params);
    $stmtTot->execute();
    $resTot = $stmtTot->get_result();
    $totPartite = $resTot->fetch_assoc()['tot_partite'] ?: 1;

    // Aggiungi % scelta
    foreach ($eroi as &$h) {
        $h['scelta'] = round(($h['partite'] / $totPartite) * 100, 2);
    }
    return $eroi;
}

function getClassi() {
    global $conn;
    $sql = "SELECT cla_id, cla_nome FROM classi";
    $res = $conn->query($sql);
    $classi = [];
    while($row = $res->fetch_assoc()) {
        $classi[] = $row;
    }
    return $classi;
}

function getPartita($id) {
    global $conn;
    if (!$id) return null;
    // Info partita
    $query = "SELECT par_id, par_inizio as data, m.mod_nome as modalita, mp.map_nome as mappa FROM partite par LEFT JOIN modalita m ON par.par_mod_id = m.mod_id LEFT JOIN mappe mp ON par.par_map_id = mp.map_id WHERE par.par_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $partita = $stmt->get_result()->fetch_assoc();
    if (!$partita) return null;
    // Partecipanti
    $query = "SELECT pl.pla_username as username, e.ero_nome as eroe, e.ero_image as eroe_img, p.ptr_uccisioni as uccisioni, p.ptr_morti as morti, p.ptr_danni as danni, p.ptr_cure as cure, p.ptr_risultato as risultato FROM partecipazioni p JOIN players pl ON p.ptr_pla_id = pl.pla_id LEFT JOIN eroi e ON p.ptr_ero_id = e.ero_id WHERE p.ptr_par_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $partecipanti = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $partita['partecipanti'] = $partecipanti;
    return $partita;
}

function getEroe($nome) {
    global $conn;
    if (!$nome) return null;
    // Info base eroe
    $query = "SELECT e.ero_id, e.ero_nome as nome, c.cla_nome as classe, e.ero_difficolta as difficolta, e.ero_image as img FROM eroi e LEFT JOIN classi c ON e.ero_cla_id = c.cla_id WHERE e.ero_nome = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $eroe = $stmt->get_result()->fetch_assoc();
    if (!$eroe) return null;
    // Statistiche generali
    $query = "SELECT COUNT(*) as partite, ROUND(SUM(p.ptr_uccisioni)/NULLIF(SUM(p.ptr_morti),0),1) as kd, ROUND(SUM(CASE WHEN p.ptr_risultato = 'Vinto' THEN 1 ELSE 0 END)/COUNT(*),2)*100 as vittorie, ROUND(AVG(p.ptr_danni),1) as danni FROM partecipazioni p JOIN eroi e ON p.ptr_ero_id = e.ero_id WHERE e.ero_nome = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $eroe = array_merge($eroe, $stats);
    // Migliori partite (top 10 danni)
    $query = "SELECT par.par_id, pl.pla_username as username, par.par_inizio as data, m.mod_nome as modalita, mp.map_nome as mappa, p.ptr_uccisioni as uccisioni, p.ptr_morti as morti, p.ptr_danni as danni, p.ptr_cure as cure, p.ptr_risultato as risultato FROM partecipazioni p JOIN players pl ON p.ptr_pla_id = pl.pla_id JOIN partite par ON p.ptr_par_id = par.par_id LEFT JOIN modalita m ON par.par_mod_id = m.mod_id LEFT JOIN mappe mp ON par.par_map_id = mp.map_id JOIN eroi e ON p.ptr_ero_id = e.ero_id WHERE e.ero_nome = ? ORDER BY p.ptr_danni DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $eroe['matches'] = $matches;
    return $eroe;
}

function getClanData($nome) {
    global $conn;
    if (!$nome) return null;
    // Info base clan
    $query = "SELECT c.cln_id, c.cln_nome as nome, c.cln_punti as punti, c.cln_image as img, r.ran_nome as rank, r.ran_image as rank_img FROM clans c LEFT JOIN ranks r ON c.cln_ran_id = r.ran_id WHERE c.cln_nome = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $clan = $stmt->get_result()->fetch_assoc();
    if (!$clan) return null;
    // Lista membri
    $query = "SELECT pla_id, pla_username, pla_livello, pla_mmr FROM players WHERE pla_cln_id = ? ORDER BY pla_mmr DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clan['cln_id']);
    $stmt->execute();
    $membri = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $clan['membri'] = $membri;
    // Statistiche generali sulle partite del clan
    $query = "SELECT COUNT(*) as partite, ROUND(SUM(p.ptr_uccisioni)/NULLIF(SUM(p.ptr_morti),0),1) as kd, ROUND(SUM(CASE WHEN p.ptr_risultato = 'Vinto' THEN 1 ELSE 0 END)/COUNT(*),2)*100 as vittorie, ROUND(AVG(p.ptr_danni),1) as danni FROM partecipazioni p JOIN players pl ON p.ptr_pla_id = pl.pla_id WHERE pl.pla_cln_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clan['cln_id']);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $clan = array_merge($clan, $stats);
    // Migliori partite dei membri (top 10 danni)
    $query = "SELECT par.par_id, pl.pla_username as username, par.par_inizio as data, m.mod_nome as modalita, mp.map_nome as mappa, e.ero_nome as eroe, p.ptr_uccisioni as uccisioni, p.ptr_morti as morti, p.ptr_danni as danni, p.ptr_cure as cure, p.ptr_risultato as risultato FROM partecipazioni p JOIN players pl ON p.ptr_pla_id = pl.pla_id JOIN partite par ON p.ptr_par_id = par.par_id LEFT JOIN modalita m ON par.par_mod_id = m.mod_id LEFT JOIN mappe mp ON par.par_map_id = mp.map_id LEFT JOIN eroi e ON p.ptr_ero_id = e.ero_id WHERE pl.pla_cln_id = ? ORDER BY p.ptr_danni DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clan['cln_id']);
    $stmt->execute();
    $matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $clan['matches'] = $matches;
    return $clan;
}

function createEroe($nome, $difficolta, $cla_id, $image) {
    global $conn;
    $query = "INSERT INTO eroi (ero_nome, ero_difficolta, ero_cla_id, ero_image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siis", $nome, $difficolta, $cla_id, $image);
    if ($stmt->execute()) {
        return ["success" => true, "id" => $conn->insert_id];
    } else {
        return ["success" => false, "error" => $stmt->error];
    }
}

function updateEroe($id, $nome, $difficolta, $cla_id, $image) {
    global $conn;
    $query = "UPDATE eroi SET ero_nome = ?, ero_difficolta = ?, ero_cla_id = ?, ero_image = ? WHERE ero_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siisi", $nome, $difficolta, $cla_id, $image, $id);
    if ($stmt->execute()) {
        return ["success" => true];
    } else {
        return ["success" => false, "error" => $stmt->error];
    }
}

function deleteEroe($id) {
    global $conn;
    $query = "DELETE FROM eroi WHERE ero_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return ["success" => true];
    } else {
        return ["success" => false, "error" => $stmt->error];
    }
}

function updateEroeByName($nome_originale, $nome_nuovo, $difficolta, $cla_id, $image) {
    global $conn;
    $query = "UPDATE eroi SET ero_nome = ?, ero_difficolta = ?, ero_cla_id = ?, ero_image = ? WHERE ero_nome = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisss", $nome_nuovo, $difficolta, $cla_id, $image, $nome_originale);
    if ($stmt->execute()) {
        return ["success" => true];
    } else {
        return ["success" => false, "error" => $stmt->error];
    }
}
