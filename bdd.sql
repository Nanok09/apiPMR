-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2021 at 11:23 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projetweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `idLieu` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `edited` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `commentaires`
--

INSERT INTO `commentaires` (`id`, `idLieu`, `idUtilisateur`, `message`, `timestamp`, `edited`) VALUES
(1, 1, 4, 'blablabla', '2021-04-11 10:15:10', 0),
(3, 1, 5, 'bla', '2021-04-11 10:35:28', 0),
(4, 1, 4, 'blablabla2', '2021-04-11 10:27:26', 0);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `membre_1` int(11) NOT NULL,
  `membre_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `creneauxDispo`
--

CREATE TABLE `creneauxDispo` (
  `id` int(11) NOT NULL,
  `idLieu` int(11) NOT NULL,
  `date` date NOT NULL,
  `heureDebut` time NOT NULL,
  `heureFin` time NOT NULL,
  `capacite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `creneauxDispo`
--

INSERT INTO `creneauxDispo` (`id`, `idLieu`, `date`, `heureDebut`, `heureFin`, `capacite`) VALUES
(1, 1, '2021-04-15', '01:30:00', '04:00:00', 5),
(2, 1, '2021-04-15', '10:00:00', '15:00:00', 3),
(3, 2, '2021-04-20', '08:00:00', '18:00:00', 5),
(4, 1, '2040-04-16', '14:00:00', '16:00:00', 10),
(11, 1, '2021-04-17', '18:00:00', '18:30:00', 10),
(12, 1, '2021-04-17', '17:00:00', '19:30:00', 10),
(13, 1, '2021-04-15', '01:00:00', '02:30:00', 5),
(14, 1, '2021-04-17', '18:30:00', '19:00:00', 10),
(15, 1, '2021-04-16', '00:00:00', '23:59:00', 10),
(16, 1, '2021-04-14', '16:00:00', '17:00:00', 10),
(17, 1, '2021-04-13', '16:00:00', '17:00:00', 10),
(18, 1, '2021-04-14', '13:00:00', '14:30:00', 10),
(19, 1, '2021-04-13', '16:30:00', '17:30:00', 10),
(20, 1, '2021-04-14', '06:00:00', '07:00:00', 10);

-- --------------------------------------------------------

--
-- Table structure for table `creneauxValides`
--

CREATE TABLE `creneauxValides` (
  `id` int(11) NOT NULL,
  `debut` time NOT NULL,
  `fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `creneauxValides`
--

INSERT INTO `creneauxValides` (`id`, `debut`, `fin`) VALUES
(1, '00:00:00', '00:30:00'),
(2, '00:30:00', '01:00:00'),
(3, '01:00:00', '01:30:00'),
(4, '01:30:00', '02:00:00'),
(5, '02:00:00', '02:30:00'),
(6, '02:30:00', '03:00:00'),
(7, '03:00:00', '03:30:00'),
(8, '03:30:00', '04:00:00'),
(9, '04:00:00', '04:30:00'),
(10, '04:30:00', '05:00:00'),
(11, '05:00:00', '05:30:00'),
(12, '05:30:00', '06:00:00'),
(13, '06:00:00', '06:30:00'),
(14, '06:30:00', '07:00:00'),
(15, '07:00:00', '07:30:00'),
(16, '07:30:00', '08:00:00'),
(17, '08:00:00', '08:30:00'),
(18, '08:30:00', '09:00:00'),
(19, '09:00:00', '09:30:00'),
(20, '09:30:00', '10:00:00'),
(21, '10:00:00', '10:30:00'),
(22, '10:30:00', '11:00:00'),
(23, '11:00:00', '11:30:00'),
(24, '11:30:00', '12:00:00'),
(25, '12:00:00', '12:30:00'),
(26, '12:30:00', '13:00:00'),
(27, '13:00:00', '13:30:00'),
(28, '13:30:00', '14:00:00'),
(29, '14:00:00', '14:30:00'),
(30, '14:30:00', '15:00:00'),
(31, '15:00:00', '15:30:00'),
(32, '15:30:00', '16:00:00'),
(33, '16:00:00', '16:30:00'),
(34, '16:30:00', '17:00:00'),
(35, '17:00:00', '17:30:00'),
(36, '17:30:00', '18:00:00'),
(37, '18:00:00', '18:30:00'),
(38, '18:30:00', '19:00:00'),
(39, '19:00:00', '19:30:00'),
(40, '19:30:00', '20:00:00'),
(41, '20:00:00', '20:30:00'),
(42, '20:30:00', '21:00:00'),
(43, '21:00:00', '21:30:00'),
(44, '21:30:00', '22:00:00'),
(45, '22:00:00', '22:30:00'),
(46, '22:30:00', '23:00:00'),
(47, '23:00:00', '23:30:00'),
(48, '23:30:00', '23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `lieux`
--

CREATE TABLE `lieux` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `adresse` text NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `sport` varchar(30) NOT NULL,
  `prive` tinyint(1) NOT NULL,
  `createur` int(11) NOT NULL,
  `prix` float NOT NULL,
  `capacite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lieux`
--

INSERT INTO `lieux` (`id`, `nom`, `description`, `adresse`, `latitude`, `longitude`, `sport`, `prive`, `createur`, `prix`, `capacite`) VALUES
(1, 'terrain1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation', 'Addresse 1', 50.6166, 3.164, 'basket', 1, 4, 1, 10),
(2, 'terrain2', 'description', 'Addresse 2', 51.6166, 3.064, 'tennis', 1, 4, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `messagesChat`
--

CREATE TABLE `messagesChat` (
  `id` int(11) NOT NULL,
  `auteur` int(11) NOT NULL,
  `destinataire` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_conv` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messagesChat`
--

INSERT INTO `messagesChat` (`id`, `auteur`, `destinataire`, `message`, `timestamp`, `id_conv`) VALUES
(1, 4, 5, 'mess1', '2021-04-13 10:20:02', 0),
(2, 5, 4, 'mess2', '2021-04-13 10:20:30', 0),
(3, 6, 5, 'mess3', '2021-04-13 10:21:21', 0),
(4, 4, 5, 'mess4', '2021-04-13 10:45:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `idLieu` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL COMMENT 'Id de l''utilisateur qui a donné la note',
  `note` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`idLieu`, `idUtilisateur`, `note`, `timestamp`) VALUES
(2, 4, 3, '2021-04-11 10:03:15');

-- --------------------------------------------------------

--
-- Table structure for table `photosLieux`
--

CREATE TABLE `photosLieux` (
  `id` int(11) NOT NULL,
  `idLieu` int(11) NOT NULL,
  `nomFichier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `photosLieux`
--

INSERT INTO `photosLieux` (`id`, `idLieu`, `nomFichier`) VALUES
(1, 1, 'terrain1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `date` date NOT NULL,
  `heureDebut` time NOT NULL,
  `heureFin` time NOT NULL,
  `nbPersonnes` int(11) NOT NULL,
  `idLieu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `idUtilisateur`, `date`, `heureDebut`, `heureFin`, `nbPersonnes`, `idLieu`) VALUES
(1, 5, '2021-04-15', '02:00:00', '03:30:00', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE `sports` (
  `id` varchar(30) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sports`
--

INSERT INTO `sports` (`id`, `nom`, `logo`) VALUES
('basket', 'Basket', 'basketball-ball.png'),
('escalade', 'Escalade', 'climbing.png'),
('football', 'Football', 'football.png'),
('muscu', 'Musculation', 'dumbbell.png'),
('rugby', 'Rugby', 'rugby-ball.png'),
('tennis', 'Tennis', 'tennis-racket.png');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `timeInscription` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `password`, `nom`, `prenom`, `email`, `timeInscription`, `admin`) VALUES
(4, 'matt', 'test', 'noma', 'prenom', 'test1@dd', '2021-04-14 21:08:11', 0),
(5, 'pseudo', 'motdepasse', 'nom', 'prenom', 'test@gmail.com', '2021-04-11 08:28:03', 0),
(6, 'test2', 'pass', '', '', '', '2021-04-13 10:19:39', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idLieu` (`idLieu`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `membre_1` (`membre_1`),
  ADD KEY `membre_2` (`membre_2`);

--
-- Indexes for table `creneauxDispo`
--
ALTER TABLE `creneauxDispo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idLieu` (`idLieu`);

--
-- Indexes for table `creneauxValides`
--
ALTER TABLE `creneauxValides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lieux`
--
ALTER TABLE `lieux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `createur` (`createur`),
  ADD KEY `sport` (`sport`);

--
-- Indexes for table `messagesChat`
--
ALTER TABLE `messagesChat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auteur` (`auteur`),
  ADD KEY `destinataire` (`destinataire`),
  ADD KEY `id_conv` (`id_conv`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD UNIQUE KEY `unique_note` (`idLieu`,`idUtilisateur`),
  ADD KEY `idLieu` (`idLieu`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Indexes for table `photosLieux`
--
ALTER TABLE `photosLieux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomFichier` (`nomFichier`),
  ADD KEY `idLieu` (`idLieu`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idLieu` (`idLieu`);

--
-- Indexes for table `sports`
--
ALTER TABLE `sports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `creneauxDispo`
--
ALTER TABLE `creneauxDispo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `creneauxValides`
--
ALTER TABLE `creneauxValides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `lieux`
--
ALTER TABLE `lieux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messagesChat`
--
ALTER TABLE `messagesChat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `photosLieux`
--
ALTER TABLE `photosLieux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`idLieu`) REFERENCES `lieux` (`id`),
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`membre_1`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`membre_2`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `creneauxDispo`
--
ALTER TABLE `creneauxDispo`
  ADD CONSTRAINT `creneauxDispo_ibfk_1` FOREIGN KEY (`idLieu`) REFERENCES `lieux` (`id`);

--
-- Constraints for table `lieux`
--
ALTER TABLE `lieux`
  ADD CONSTRAINT `lieux_ibfk_1` FOREIGN KEY (`createur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `lieux_ibfk_2` FOREIGN KEY (`sport`) REFERENCES `sports` (`id`);

--
-- Constraints for table `messagesChat`
--
ALTER TABLE `messagesChat`
  ADD CONSTRAINT `messagesChat_ibfk_1` FOREIGN KEY (`auteur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `messagesChat_ibfk_2` FOREIGN KEY (`destinataire`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `messagesChat_ibfk_3` FOREIGN KEY (`id_conv`) REFERENCES `conversations` (`id`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`idLieu`) REFERENCES `lieux` (`id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `photosLieux`
--
ALTER TABLE `photosLieux`
  ADD CONSTRAINT `photosLieux_ibfk_1` FOREIGN KEY (`idLieu`) REFERENCES `lieux` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`idLieu`) REFERENCES `lieux` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
