-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 02, 2025 alle 13:21
-- Versione del server: 10.4.27-MariaDB
-- Versione PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bigblackdeath`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `clans`
--

CREATE TABLE `clans` (
  `cln_id` int(11) NOT NULL,
  `cln_nome` varchar(25) NOT NULL,
  `cln_punti` int(11) NOT NULL,
  `cln_pla_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `classi`
--

CREATE TABLE `classi` (
  `cla_id` int(11) NOT NULL,
  `cla_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `eroi`
--

CREATE TABLE `eroi` (
  `ero_id` int(11) NOT NULL,
  `ero_nome` varchar(20) NOT NULL,
  `ero_difficolta` int(11) NOT NULL,
  `ero_cla_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `mappe`
--

CREATE TABLE `mappe` (
  `map_id` int(11) NOT NULL,
  `map_nome` varchar(50) NOT NULL,
  `map_immagine` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `modalita`
--

CREATE TABLE `modalita` (
  `mod_id` int(11) NOT NULL,
  `mod_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipazioni`
--

CREATE TABLE `partecipazioni` (
  `ptr_id` int(11) NOT NULL,
  `ptr_risultato` enum('Vinto','Perso') NOT NULL,
  `ptr_danni` int(11) NOT NULL,
  `ptr_cure` int(11) NOT NULL,
  `ptr_uccisioni` int(11) NOT NULL,
  `ptr_pla_id` int(11) NOT NULL,
  `ptr_par_id` int(11) NOT NULL,
  `ptr_ero_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partite`
--

CREATE TABLE `partite` (
  `par_id` int(11) NOT NULL,
  `par_inizio` datetime DEFAULT NULL,
  `par_durata` datetime DEFAULT NULL,
  `par_mod_id` int(11) DEFAULT NULL,
  `par_map_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `players`
--

CREATE TABLE `players` (
  `pla_id` int(11) NOT NULL,
  `pla_livello` int(11) NOT NULL,
  `pla_username` varchar(20) DEFAULT NULL,
  `pla_mmr` int(11) NOT NULL,
  `pla_cln_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `ranks`
--

CREATE TABLE `ranks` (
  `ran_id` int(11) NOT NULL,
  `ran_nome` varchar(15) NOT NULL,
  `ran_min` int(11) NOT NULL,
  `ran_max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `ute_id` int(11) NOT NULL,
  `ute_username` varchar(20) DEFAULT NULL,
  `ute_email` varchar(150) DEFAULT NULL,
  `ute_password` varchar(64) NOT NULL,
  `ute_data_registrazione` datetime NOT NULL,
  `ute_ruolo` enum('admin','utente') DEFAULT NULL,
  `ute_pla_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `clans`
--
ALTER TABLE `clans`
  ADD PRIMARY KEY (`cln_id`),
  ADD UNIQUE KEY `cln_nome` (`cln_nome`),
  ADD KEY `fk_clan_player` (`cln_pla_id`);

--
-- Indici per le tabelle `classi`
--
ALTER TABLE `classi`
  ADD PRIMARY KEY (`cla_id`);

--
-- Indici per le tabelle `eroi`
--
ALTER TABLE `eroi`
  ADD PRIMARY KEY (`ero_id`),
  ADD KEY `ero_cla_id` (`ero_cla_id`);

--
-- Indici per le tabelle `mappe`
--
ALTER TABLE `mappe`
  ADD PRIMARY KEY (`map_id`);

--
-- Indici per le tabelle `modalita`
--
ALTER TABLE `modalita`
  ADD PRIMARY KEY (`mod_id`);

--
-- Indici per le tabelle `partecipazioni`
--
ALTER TABLE `partecipazioni`
  ADD PRIMARY KEY (`ptr_id`),
  ADD KEY `ptr_pla_id` (`ptr_pla_id`),
  ADD KEY `ptr_par_id` (`ptr_par_id`),
  ADD KEY `ptr_ero_id` (`ptr_ero_id`);

--
-- Indici per le tabelle `partite`
--
ALTER TABLE `partite`
  ADD PRIMARY KEY (`par_id`),
  ADD KEY `par_mod_id` (`par_mod_id`),
  ADD KEY `par_map_id` (`par_map_id`);

--
-- Indici per le tabelle `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`pla_id`),
  ADD UNIQUE KEY `pla_username` (`pla_username`),
  ADD KEY `pla_cln_id` (`pla_cln_id`);

--
-- Indici per le tabelle `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`ran_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`ute_id`),
  ADD UNIQUE KEY `ute_username` (`ute_username`),
  ADD UNIQUE KEY `ute_email` (`ute_email`),
  ADD UNIQUE KEY `ute_pla_id` (`ute_pla_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clans`
--
ALTER TABLE `clans`
  MODIFY `cln_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `classi`
--
ALTER TABLE `classi`
  MODIFY `cla_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `eroi`
--
ALTER TABLE `eroi`
  MODIFY `ero_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `mappe`
--
ALTER TABLE `mappe`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `modalita`
--
ALTER TABLE `modalita`
  MODIFY `mod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `partecipazioni`
--
ALTER TABLE `partecipazioni`
  MODIFY `ptr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `partite`
--
ALTER TABLE `partite`
  MODIFY `par_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `players`
--
ALTER TABLE `players`
  MODIFY `pla_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ranks`
--
ALTER TABLE `ranks`
  MODIFY `ran_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `ute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `clans`
--
ALTER TABLE `clans`
  ADD CONSTRAINT `fk_clan_player` FOREIGN KEY (`cln_pla_id`) REFERENCES `players` (`pla_id`);

--
-- Limiti per la tabella `eroi`
--
ALTER TABLE `eroi`
  ADD CONSTRAINT `eroi_ibfk_1` FOREIGN KEY (`ero_cla_id`) REFERENCES `classi` (`cla_id`);

--
-- Limiti per la tabella `partecipazioni`
--
ALTER TABLE `partecipazioni`
  ADD CONSTRAINT `partecipazioni_ibfk_1` FOREIGN KEY (`ptr_pla_id`) REFERENCES `players` (`pla_id`),
  ADD CONSTRAINT `partecipazioni_ibfk_2` FOREIGN KEY (`ptr_par_id`) REFERENCES `partite` (`par_id`),
  ADD CONSTRAINT `partecipazioni_ibfk_3` FOREIGN KEY (`ptr_ero_id`) REFERENCES `eroi` (`ero_id`);

--
-- Limiti per la tabella `partite`
--
ALTER TABLE `partite`
  ADD CONSTRAINT `partite_ibfk_1` FOREIGN KEY (`par_mod_id`) REFERENCES `modalita` (`mod_id`),
  ADD CONSTRAINT `partite_ibfk_2` FOREIGN KEY (`par_map_id`) REFERENCES `mappe` (`map_id`);

--
-- Limiti per la tabella `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`pla_cln_id`) REFERENCES `clans` (`cln_id`);

--
-- Limiti per la tabella `utenti`
--
ALTER TABLE `utenti`
  ADD CONSTRAINT `utenti_ibfk_1` FOREIGN KEY (`ute_pla_id`) REFERENCES `players` (`pla_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
