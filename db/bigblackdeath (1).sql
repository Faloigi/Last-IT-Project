-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 02, 2025 alle 13:35
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

--
-- Dump dei dati per la tabella `clans`
--

INSERT INTO `clans` (`cln_id`, `cln_nome`, `cln_punti`, `cln_pla_id`) VALUES
(1, 'Legione Oscura', 12500, 4),
(2, 'Lacrime Ghiacciate', 9800, 2),
(3, 'Rinascita Fenice', 11200, 7),
(4, 'Noob Uniti', 4500, 3),
(5, 'Guardia d\'Elite', 15600, 9);

-- --------------------------------------------------------

--
-- Struttura della tabella `classi`
--

CREATE TABLE `classi` (
  `cla_id` int(11) NOT NULL,
  `cla_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `classi`
--

INSERT INTO `classi` (`cla_id`, `cla_nome`) VALUES
(1, 'Tank'),
(2, 'Assassino'),
(3, 'Mago'),
(4, 'Supporto'),
(5, 'Tiratore');

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

--
-- Dump dei dati per la tabella `eroi`
--

INSERT INTO `eroi` (`ero_id`, `ero_nome`, `ero_difficolta`, `ero_cla_id`) VALUES
(1, 'Guardiapietra', 2, 1),
(2, 'Lamaombra', 4, 2),
(3, 'Tessigelo', 3, 3),
(4, 'Custodevitale', 3, 4),
(5, 'Occhiodifalco', 2, 5),
(6, 'Corazzadura', 1, 1),
(7, 'Ombratossica', 5, 2),
(8, 'Piromenta', 4, 3),
(9, 'Armonia', 2, 4),
(10, 'Occhiodiferro', 3, 5);

-- --------------------------------------------------------

--
-- Struttura della tabella `mappe`
--

CREATE TABLE `mappe` (
  `map_id` int(11) NOT NULL,
  `map_nome` varchar(50) NOT NULL,
  `map_immagine` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `mappe`
--

INSERT INTO `mappe` (`map_id`, `map_nome`, `map_immagine`) VALUES
(1, 'Roccaforte Pietranera', 'roccaforte_pietranera.jpg'),
(2, 'Canyon Gelomorso', 'canyon_gelomorso.jpg'),
(3, 'Landarde Ember', 'landarde_ember.jpg'),
(4, 'Bosco Rigoglioso', 'bosco_rigoglioso.jpg'),
(5, 'Guglia di Ossidiana', 'guglia_ossidiana.jpg');

-- --------------------------------------------------------

--
-- Struttura della tabella `modalita`
--

CREATE TABLE `modalita` (
  `mod_id` int(11) NOT NULL,
  `mod_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `modalita`
--

INSERT INTO `modalita` (`mod_id`, `mod_nome`) VALUES
(1, 'Scontro mortale'),
(2, 'Cattura la bandiera'),
(3, 'Re della collina'),
(4, 'Eliminazione a squad'),
(5, 'Sopravvivenza');

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

--
-- Dump dei dati per la tabella `partecipazioni`
--

INSERT INTO `partecipazioni` (`ptr_id`, `ptr_risultato`, `ptr_danni`, `ptr_cure`, `ptr_uccisioni`, `ptr_pla_id`, `ptr_par_id`, `ptr_ero_id`) VALUES
(1, 'Vinto', 12500, 0, 8, 1, 1, 2),
(2, 'Perso', 8500, 0, 5, 3, 1, 5),
(3, 'Vinto', 4200, 12500, 2, 5, 2, 4),
(4, 'Perso', 7800, 0, 4, 2, 2, 3),
(5, 'Vinto', 15000, 0, 10, 4, 3, 1),
(6, 'Perso', 9200, 0, 6, 6, 3, 5),
(7, 'Vinto', 6800, 9800, 3, 5, 4, 4),
(8, 'Vinto', 11200, 0, 7, 7, 4, 8),
(9, 'Perso', 10500, 0, 6, 9, 5, 10),
(10, 'Vinto', 14500, 0, 9, 4, 5, 6),
(11, 'Vinto', 13200, 0, 8, 1, 6, 2),
(12, 'Perso', 7600, 0, 4, 8, 6, 7),
(13, 'Vinto', 5400, 11200, 2, 5, 7, 9),
(14, 'Vinto', 11800, 0, 7, 2, 7, 3),
(15, 'Perso', 8800, 0, 5, 10, 8, 5),
(16, 'Vinto', 12600, 0, 8, 7, 8, 8),
(17, 'Vinto', 6200, 10500, 3, 5, 9, 4),
(18, 'Perso', 9500, 0, 5, 3, 9, 2),
(19, 'Vinto', 14200, 0, 9, 9, 10, 10),
(20, 'Perso', 8100, 0, 4, 6, 10, 1);

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

--
-- Dump dei dati per la tabella `partite`
--

INSERT INTO `partite` (`par_id`, `par_inizio`, `par_durata`, `par_mod_id`, `par_map_id`) VALUES
(1, '2025-04-28 18:30:00', '2025-04-28 18:45:00', 1, 1),
(2, '2025-04-28 19:15:00', '2025-04-28 19:35:00', 2, 3),
(3, '2025-04-29 20:00:00', '2025-04-29 20:25:00', 3, 2),
(4, '2025-04-30 17:45:00', '2025-04-30 18:10:00', 4, 5),
(5, '2025-05-01 21:30:00', '2025-05-01 21:50:00', 5, 4),
(6, '2025-05-01 22:15:00', '2025-05-01 22:30:00', 1, 3),
(7, '2025-05-02 15:00:00', '2025-05-02 15:20:00', 2, 1),
(8, '2025-05-02 16:45:00', '2025-05-02 17:15:00', 3, 4),
(9, '2025-05-02 19:30:00', '2025-05-02 19:55:00', 4, 2),
(10, '2025-05-02 20:30:00', '2025-05-02 20:45:00', 5, 5);

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

--
-- Dump dei dati per la tabella `players`
--

INSERT INTO `players` (`pla_id`, `pla_livello`, `pla_username`, `pla_mmr`, `pla_cln_id`) VALUES
(1, 25, 'ColpoOmbra', 2450, 1),
(2, 42, 'ReginaGelo', 3120, 2),
(3, 18, 'SterminaNoob', 1250, 4),
(4, 56, 'DioDeiTank', 3870, 5),
(5, 33, 'CuraBot', 2780, 2),
(6, 29, 'CecchinoPro', 2650, 1),
(7, 47, 'SignoreFiamma', 3540, 3),
(8, 12, 'Principiante123', 850, NULL),
(9, 63, 'GranMaestro', 4250, 5),
(10, 21, 'GiuseppeMedio', 1950, 3);

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

--
-- Dump dei dati per la tabella `ranks`
--

INSERT INTO `ranks` (`ran_id`, `ran_nome`, `ran_min`, `ran_max`) VALUES
(1, 'Bronzo', 0, 999),
(2, 'Argento', 1000, 1999),
(3, 'Oro', 2000, 2999),
(4, 'Platino', 3000, 3999),
(5, 'Diamante', 4000, 4999),
(6, 'Maestro', 5000, 5999),
(7, 'Gran Maestro', 6000, 9999);

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
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`ute_id`, `ute_username`, `ute_email`, `ute_password`, `ute_data_registrazione`, `ute_ruolo`, `ute_pla_id`) VALUES
(1, 'ColpoOmbra', 'ombra@esempio.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '2024-01-15 14:30:00', 'utente', 1),
(2, 'ReginaGelo', 'gelo@esempio.com', '99bd803cac6eb5348c1253728ea88fcf820edad24ab31aac2b456caf82575330', '2023-11-22 09:15:00', 'utente', 2),
(3, 'SterminaNoob', 'noob@esempio.com', '244071833db77587c870919e34054ac1af2e4a82f6b3e5352a799ceeec9d8368', '2024-03-05 18:45:00', 'utente', 3),
(4, 'DioDeiTank', 'tank@esempio.com', '9fe7db6aad8050f7fbd6cc4694c4433debc25ebab6fcf5151f0f0c5f8be29af2', '2023-09-30 11:20:00', 'admin', 4),
(5, 'CuraBot', 'cura@esempio.com', 'ff15a95205e8e4bd49f87bc8fb0ed3b5773291c3c1b4dde577b362e734a4a74e', '2024-02-14 16:10:00', 'utente', 5),
(6, 'CecchinoPro', 'cecchino@esempio.com', 'e350fac44e983c50335e9f260592ddae0fb958a86e9c45ff25886b2df6909f4c', '2024-01-28 13:25:00', 'utente', 6),
(7, 'SignoreFiamma', 'fiamma@esempio.com', '5ec74678da9c4a222189e25d0f3308f24bfe4f5ce150c12b9039278d05a89f70', '2023-12-10 20:05:00', 'utente', 7),
(8, 'Principiante123', 'principiante@esempio.com', 'e9ceb0138dee3739f18581de8d3336da7c3b5cb100017f1ecf8dcdd65b5b4dc5', '2024-04-18 10:00:00', 'utente', 8),
(9, 'GranMaestro', 'gm@esempio.com', '9fe66e11675b44f166725b2ccf127a556545a624b3de6dae00c85b6ff2be3050', '2023-08-05 15:45:00', 'admin', 9),
(10, 'GiuseppeMedio', 'giuseppe@esempio.com', '46610d0506e957be8a0a66be0daa47ac65dba9283c72b4093eae4e1a3a442f1a', '2024-02-29 12:30:00', 'utente', 10);

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
  MODIFY `cln_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `classi`
--
ALTER TABLE `classi`
  MODIFY `cla_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `eroi`
--
ALTER TABLE `eroi`
  MODIFY `ero_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `mappe`
--
ALTER TABLE `mappe`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `modalita`
--
ALTER TABLE `modalita`
  MODIFY `mod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `partecipazioni`
--
ALTER TABLE `partecipazioni`
  MODIFY `ptr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `partite`
--
ALTER TABLE `partite`
  MODIFY `par_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `players`
--
ALTER TABLE `players`
  MODIFY `pla_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `ranks`
--
ALTER TABLE `ranks`
  MODIFY `ran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `ute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
