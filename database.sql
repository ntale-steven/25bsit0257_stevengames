-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 08:18 PM
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
  `icon` varchar(20) NOT NULL DEFAULT '?',
  `description` text DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 0.0,
  `plays` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `game_type` varchar(50) DEFAULT 'space'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `genre`, `icon`, `description`, `rating`, `plays`, `status`, `created_at`, `game_type`) VALUES
(1, 'Space Blaster', 'Action', '🚀', 'Blast enemies across the galaxy in this fast-paced shooter.', 4.9, 12400, 'active', '2026-03-18 08:28:30', 'space'),
(2, 'Dragon Quest', 'RPG', '🐉', 'Embark on an epic RPG journey to defeat the ancient dragon lord.', 4.8, 9800, 'active', '2026-03-18 08:28:30', 'space'),
(3, 'Turbo Race', 'Racing', '🏎️', 'High-speed racing game with 20+ tracks and multiplayer support.', 4.7, 8300, 'active', '2026-03-18 08:28:30', 'race'),
(4, 'Mind Matrix', 'Puzzle', '🧩', 'Challenge your brain with mind-bending puzzles and logic tests.', 4.6, 7100, 'active', '2026-03-18 08:28:30', 'puzzle'),
(5, 'Battle Arena', 'Fighting', '⚔️', 'Enter the arena and fight your way to the championship title.', 4.9, 11600, 'active', '2026-03-18 08:28:30', 'space'),
(6, 'World Builder', 'Strategy', '🌍', 'Build, expand, and dominate your own civilisation from scratch.', 4.5, 6400, 'active', '2026-03-18 08:28:30', 'puzzle');

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
-- Table structure for table `tbl_content`
--

CREATE TABLE `tbl_content` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(300) NOT NULL DEFAULT 'https://placehold.co/400x220/0d1117/00f0ff?text=StevenGames',
  `category` varchar(60) NOT NULL DEFAULT 'News',
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_content`
--

INSERT INTO `tbl_content` (`id`, `title`, `description`, `image_url`, `category`, `status`, `created_at`) VALUES
(1, 'Season 3 Tournament Kicks Off', 'The biggest Steven Games tournament of the year is here. Over 5,000 players battle for the Grand Champion title and $10,000 in prizes. Register now before slots fill up!', 'https://placehold.co/400x220/050810/00f0ff?text=Season+3+Tournament', 'Tournament', 'published', '2026-03-18 08:28:30'),
(2, 'New Game: Cyber Strike Launched', 'Our latest action title Cyber Strike is now live! Features 30 levels of adrenaline-fuelled combat, a ranked mode, and a brand-new soundtrack by DJ Kira.', 'https://placehold.co/400x220/050810/ff3c78?text=Cyber+Strike', 'New Game', 'published', '2026-03-18 09:00:00'),
(3, 'Weekly Challenge: Survive 5 Minutes', 'This week\'s community challenge is live on Space Blaster. Survive five minutes against endless waves for a chance to win 500 bonus points and an exclusive badge.', 'https://placehold.co/400x220/050810/7c3aed?text=Weekly+Challenge', 'Challenge', 'published', '2026-03-18 10:00:00'),
(4, 'Maintenance Complete — Servers Upgraded', 'Our server infrastructure has been upgraded to handle 3x the previous player load. Expect smoother gameplay, lower latency, and zero downtime going forward.', 'https://placehold.co/400x220/050810/00f0ff?text=Servers+Upgraded', 'News', 'published', '2026-03-18 11:00:00'),
(5, 'Season 3 Tournament Kicks Off', 'The biggest Steven Games tournament of the year is here. Over 5,000 players battle for the Grand Champion title and $10,000 in prizes. Register now before slots fill up!', 'https://placehold.co/400x220/050810/00f0ff?text=Season+3+Tournament', 'Tournament', 'published', '2026-03-22 11:49:15'),
(6, 'New Game: Cyber Strike Launched', 'Our latest action title Cyber Strike is now live! Features 30 levels of adrenaline-fuelled combat, a ranked mode, and a brand-new soundtrack.', 'https://placehold.co/400x220/050810/ff3c78?text=Cyber+Strike', 'New Game', 'published', '2026-03-22 11:49:15'),
(7, 'Weekly Challenge: Survive 5 Minutes', 'This week challenge is live on Space Blaster. Survive five minutes against endless waves for a chance to win 500 bonus points and an exclusive badge.', 'https://placehold.co/400x220/050810/7c3aed?text=Weekly+Challenge', 'Challenge', 'published', '2026-03-22 11:49:15'),
(8, 'Maintenance Complete — Servers Upgraded', 'Our server infrastructure has been upgraded to handle 3x the previous player load. Expect smoother gameplay, lower latency, and zero downtime going forward.', 'https://placehold.co/400x220/050810/00f0ff?text=Servers+Upgraded', 'News', 'published', '2026-03-22 11:49:15');

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
(1, 'admin', 'admin@stevengames.com', '$2y$10$2pqtNQa9mVX/Ehuffd8n7uotYctHZnMimfEempgCxrUFPamb3yVI6', 'admin', 'active', 0, 0, 0, '2026-03-23 15:17:40', '2026-03-18 08:28:30'),
(2, 'StevenX_Pro', 'steven@stevengames.com', '$2y$10$/4yVFBj5x9rBmLUPSyh8t.gk.f6asbAXC1ZLJjbAsEhk88n1MqBIu', 'user', 'active', 128450, 89, 14, '2026-03-18 08:28:30', '2026-02-16 08:28:30'),
(3, 'NightHawk99', 'hawk@email.com', '$2y$10$wxC.i6i4Nm9e5E1K82gx6e5.22cewAPoXfN4nqg1xlc.eHVnFrINu', 'user', 'active', 115200, 74, 9, '2026-03-18 08:28:30', '2026-02-21 08:28:30'),
(4, 'PixelKing', 'pixel@email.com', '$2y$10$H0e87Ishp45XsdfphUOu4O7.bqASUDTE4PlaoOtpnVXF63NCCAJES', 'user', 'active', 98780, 62, 7, '2026-03-18 08:28:30', '2026-02-26 08:28:30'),
(5, 'GamerQueen', 'queen@email.com', '$2y$10$PvI91cXk1gAEtTnyMjoGBulMs55iZEO8OU69GSqMwkBtyYze9LRCi', 'user', 'active', 87560, 51, 5, '2026-03-18 08:28:30', '2026-03-03 08:28:30'),
(6, 'SwiftBlade', 'swift@email.com', '$2y$10$nC7si5gqZF8KVrv3/RV06OORw0WT0PklyD6N46a0LiPFN1Y1uk4WG', 'user', 'banned', 76340, 44, 3, '2026-03-18 08:28:30', '2026-03-08 08:28:30'),
(7, 'ntale steven', 'st.ntale@unik.ac.ug', '$2y$10$R18RbzPojpZLLrv.HsmuRujPTUan5lbutM1b7JD29TcHXyP1Tnj.2', 'user', 'active', 0, 0, 0, '2026-03-18 11:43:56', '2026-03-18 11:43:33'),
(8, 'MR SWABUL', 'swabulofficial@gmail.com', '$2y$10$UrQzver1Z.Tzpt9f3TVPZOWANrBFY8m1LYi6.8EaBKC/eXE4JXqOC', 'admin', 'active', 0, 0, 0, '2026-03-22 12:29:32', '2026-03-22 11:16:14');

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
-- Indexes for table `tbl_content`
--
ALTER TABLE `tbl_content`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `tbl_content`
--
ALTER TABLE `tbl_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
