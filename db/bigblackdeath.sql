-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 11, 2025 alle 17:56
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

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
-- Struttura della tabella `avatar`
--

CREATE TABLE `avatar` (
  `ava_id` int(11) NOT NULL,
  `ava_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `gioca`
--

CREATE TABLE `gioca` (
  `gio_id` int(11) NOT NULL,
  `par_id` int(11) NOT NULL,
  `risultato` varchar(50) DEFAULT NULL,
  `durata` time DEFAULT NULL,
  `inizio` datetime DEFAULT NULL,
  `punteggio` int(11) DEFAULT NULL,
  `morti` int(11) DEFAULT NULL,
  `uccisioni` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `giocatore`
--

CREATE TABLE `giocatore` (
  `gio_id` int(11) NOT NULL,
  `gio_nickname` varchar(50) NOT NULL,
  `gio_email` varchar(100) NOT NULL,
  `gio_password` varchar(255) NOT NULL,
  `gio_nome` varchar(100) DEFAULT NULL,
  `gio_punti` int(11) DEFAULT 0,
  `ava_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partita`
--

CREATE TABLE `partita` (
  `par_id` int(11) NOT NULL,
  `par_difficolta` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`ava_id`);

--
-- Indici per le tabelle `gioca`
--
ALTER TABLE `gioca`
  ADD PRIMARY KEY (`gio_id`,`par_id`),
  ADD KEY `par_id` (`par_id`);

--
-- Indici per le tabelle `giocatore`
--
ALTER TABLE `giocatore`
  ADD PRIMARY KEY (`gio_id`),
  ADD UNIQUE KEY `gio_nickname` (`gio_nickname`),
  ADD UNIQUE KEY `gio_email` (`gio_email`),
  ADD KEY `ava_id` (`ava_id`);

--
-- Indici per le tabelle `partita`
--
ALTER TABLE `partita`
  ADD PRIMARY KEY (`par_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `avatar`
--
ALTER TABLE `avatar`
  MODIFY `ava_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `giocatore`
--
ALTER TABLE `giocatore`
  MODIFY `gio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `partita`
--
ALTER TABLE `partita`
  MODIFY `par_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `gioca`
--
ALTER TABLE `gioca`
  ADD CONSTRAINT `gioca_ibfk_1` FOREIGN KEY (`gio_id`) REFERENCES `giocatore` (`gio_id`),
  ADD CONSTRAINT `gioca_ibfk_2` FOREIGN KEY (`par_id`) REFERENCES `partita` (`par_id`);

--
-- Limiti per la tabella `giocatore`
--
ALTER TABLE `giocatore`
  ADD CONSTRAINT `giocatore_ibfk_1` FOREIGN KEY (`ava_id`) REFERENCES `avatar` (`ava_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
