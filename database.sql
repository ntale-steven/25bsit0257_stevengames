-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 09:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steven_games`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `icon` varchar(10) NOT NULL DEFAULT '?',
  `description` text DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 0.0,
  `plays` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `genre`, `icon`, `description`, `rating`, `plays`, `status`, `created_at`) VALUES
(1, 'Space Blaster', 'Action', '🚀', 'Blast enemies across the galaxy in this fast-paced shooter.', 4.9, 12400, 'active', '2026-03-18 08:28:30'),
(2, 'Dragon Quest', 'RPG', '🐉', 'Embark on an epic RPG journey to defeat the ancient dragon lord.', 4.8, 9800, 'active', '2026-03-18 08:28:30'),
(3, 'Turbo Race', 'Racing', '🏎️', 'High-speed racing game with 20+ tracks and multiplayer support.', 4.7, 8300, 'active', '2026-03-18 08:28:30'),
(4, 'Mind Matrix', 'Puzzle', '🧩', 'Challenge your brain with mind-bending puzzles and logic tests.', 4.6, 7100, 'active', '2026-03-18 08:28:30'),
(5, 'Battle Arena', 'Fighting', '⚔️', 'Enter the arena and fight your way to the championship title.', 4.9, 11600, 'active', '2026-03-18 08:28:30'),
(6, 'World Builder', 'Strategy', '🌍', 'Build, expand, and dominate your own civilisation from scratch.', 4.5, 6400, 'active', '2026-03-18 08:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `played_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `user_id`, `game_id`, `score`, `played_at`) VALUES
(1, 2, 1, 45200, '2026-03-17 08:28:30'),
(2, 2, 5, 38900, '2026-03-16 08:28:30'),
(3, 3, 1, 41200, '2026-03-17 08:28:30'),
(4, 3, 3, 29800, '2026-03-15 08:28:30'),
(5, 4, 2, 35600, '2026-03-16 08:28:30'),
(6, 5, 4, 22400, '2026-03-14 08:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','banned') NOT NULL DEFAULT 'active',
  `score` int(11) NOT NULL DEFAULT 0,
  `games_played` int(11) NOT NULL DEFAULT 0,
  `win_streak` int(11) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `score`, `games_played`, `win_streak`, `last_login`, `created_at`) VALUES
(1, 'admin', 'admin@stevengames.com', '$2y$10$2pqtNQa9mVX/Ehuffd8n7uotYctHZnMimfEempgCxrUFPamb3yVI6', 'admin', 'active', 0, 0, 0, '2026-03-18 11:41:46', '2026-03-18 08:28:30'),
(2, 'StevenX_Pro', 'steven@stevengames.com', '$2y$10$/4yVFBj5x9rBmLUPSyh8t.gk.f6asbAXC1ZLJjbAsEhk88n1MqBIu', 'user', 'active', 128450, 89, 14, '2026-03-18 08:28:30', '2026-02-16 08:28:30'),
(3, 'NightHawk99', 'hawk@email.com', '$2y$10$wxC.i6i4Nm9e5E1K82gx6e5.22cewAPoXfN4nqg1xlc.eHVnFrINu', 'user', 'active', 115200, 74, 9, '2026-03-18 08:28:30', '2026-02-21 08:28:30'),
(4, 'PixelKing', 'pixel@email.com', '$2y$10$H0e87Ishp45XsdfphUOu4O7.bqASUDTE4PlaoOtpnVXF63NCCAJES', 'user', 'active', 98780, 62, 7, '2026-03-18 08:28:30', '2026-02-26 08:28:30'),
(5, 'GamerQueen', 'queen@email.com', '$2y$10$PvI91cXk1gAEtTnyMjoGBulMs55iZEO8OU69GSqMwkBtyYze9LRCi', 'user', 'active', 87560, 51, 5, '2026-03-18 08:28:30', '2026-03-03 08:28:30'),
(6, 'SwiftBlade', 'swift@email.com', '$2y$10$nC7si5gqZF8KVrv3/RV06OORw0WT0PklyD6N46a0LiPFN1Y1uk4WG', 'user', 'banned', 76340, 44, 3, '2026-03-18 08:28:30', '2026-03-08 08:28:30'),
(7, 'ntale steven', 'st.ntale@unik.ac.ug', '$2y$10$R18RbzPojpZLLrv.HsmuRujPTUan5lbutM1b7JD29TcHXyP1Tnj.2', 'user', 'active', 0, 0, 0, '2026-03-18 11:43:56', '2026-03-18 11:43:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `fk_score_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_score_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
