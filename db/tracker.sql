-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 16, 2025 alle 11:33
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
-- Database: `tracker`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `avatar`
--

CREATE TABLE `avatar` (
  `ava_id` int(11) NOT NULL,
  `ava_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `gestione_partite`
--

CREATE TABLE `gestione_partite` (
  `gpa_id` int(11) NOT NULL,
  `gpa_gio_id` int(11) DEFAULT NULL,
  `gpa_par_id` int(11) DEFAULT NULL,
  `gpa_per_id` int(11) DEFAULT NULL,
  `gpa_risultato` varchar(20) DEFAULT NULL,
  `gpa_durata` time DEFAULT NULL,
  `gpa_inzio` datetime DEFAULT NULL,
  `gpa_punteggio` int(11) DEFAULT NULL,
  `gpa_morti` int(11) DEFAULT NULL,
  `gpa_uccisioni` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `giocatori`
--

CREATE TABLE `giocatori` (
  `gio_id` int(11) NOT NULL,
  `gio_nickame` varchar(30) NOT NULL,
  `gio_email` varchar(50) NOT NULL,
  `gio_password` varchar(255) NOT NULL,
  `gio_nome` varchar(50) DEFAULT NULL,
  `gio_punti` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `modalita`
--

CREATE TABLE `modalita` (
  `mod_id` int(11) NOT NULL,
  `mod_nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partite`
--

CREATE TABLE `partite` (
  `par_id` int(11) NOT NULL,
  `par_difficolta` varchar(20) NOT NULL,
  `par_mod_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `personaggi`
--

CREATE TABLE `personaggi` (
  `per_id` int(11) NOT NULL,
  `per_ava_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`ava_id`);

--
-- Indici per le tabelle `gestione_partite`
--
ALTER TABLE `gestione_partite`
  ADD PRIMARY KEY (`gpa_id`),
  ADD KEY `gpa_gio_id` (`gpa_gio_id`),
  ADD KEY `gpa_par_id` (`gpa_par_id`),
  ADD KEY `gpa_per_id` (`gpa_per_id`);

--
-- Indici per le tabelle `giocatori`
--
ALTER TABLE `giocatori`
  ADD PRIMARY KEY (`gio_id`),
  ADD UNIQUE KEY `gio_nickame` (`gio_nickame`),
  ADD UNIQUE KEY `gio_email` (`gio_email`);

--
-- Indici per le tabelle `modalita`
--
ALTER TABLE `modalita`
  ADD PRIMARY KEY (`mod_id`);

--
-- Indici per le tabelle `partite`
--
ALTER TABLE `partite`
  ADD PRIMARY KEY (`par_id`),
  ADD KEY `par_mod_id` (`par_mod_id`);

--
-- Indici per le tabelle `personaggi`
--
ALTER TABLE `personaggi`
  ADD PRIMARY KEY (`per_id`),
  ADD KEY `per_ava_id` (`per_ava_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `avatar`
--
ALTER TABLE `avatar`
  MODIFY `ava_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `gestione_partite`
--
ALTER TABLE `gestione_partite`
  MODIFY `gpa_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `giocatori`
--
ALTER TABLE `giocatori`
  MODIFY `gio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `modalita`
--
ALTER TABLE `modalita`
  MODIFY `mod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `partite`
--
ALTER TABLE `partite`
  MODIFY `par_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `personaggi`
--
ALTER TABLE `personaggi`
  MODIFY `per_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `gestione_partite`
--
ALTER TABLE `gestione_partite`
  ADD CONSTRAINT `gestione_partite_ibfk_1` FOREIGN KEY (`gpa_gio_id`) REFERENCES `giocatori` (`gio_id`),
  ADD CONSTRAINT `gestione_partite_ibfk_2` FOREIGN KEY (`gpa_par_id`) REFERENCES `partite` (`par_id`),
  ADD CONSTRAINT `gestione_partite_ibfk_3` FOREIGN KEY (`gpa_per_id`) REFERENCES `personaggi` (`per_id`);

--
-- Limiti per la tabella `partite`
--
ALTER TABLE `partite`
  ADD CONSTRAINT `partite_ibfk_1` FOREIGN KEY (`par_mod_id`) REFERENCES `modalita` (`mod_id`);

--
-- Limiti per la tabella `personaggi`
--
ALTER TABLE `personaggi`
  ADD CONSTRAINT `personaggi_ibfk_1` FOREIGN KEY (`per_ava_id`) REFERENCES `avatar` (`ava_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
