-- =====================================================
-- STEVEN GAMES — DATABASE SCHEMA (UPDATED)
-- Includes tbl_content for the data-driven milestone
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table: activity_log
-- --------------------------------------------------------
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(200) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: games
-- --------------------------------------------------------
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `icon` varchar(10) NOT NULL DEFAULT '🎮',
  `description` text DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 0.0,
  `plays` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `games` (`id`, `title`, `genre`, `icon`, `description`, `rating`, `plays`, `status`) VALUES
(1, 'Space Blaster', 'Action',  'rocket', 'Blast enemies across the galaxy in this fast-paced shooter.',        4.9, 12400, 'active'),
(2, 'Dragon Quest',  'RPG',     'dragon', 'Embark on an epic RPG journey to defeat the ancient dragon lord.',   4.8,  9800, 'active'),
(3, 'Turbo Race',    'Racing',  'car',    'High-speed racing with 20+ tracks and multiplayer support.',         4.7,  8300, 'active'),
(4, 'Mind Matrix',   'Puzzle',  'puzzle', 'Challenge your brain with mind-bending puzzles and logic tests.',    4.6,  7100, 'active'),
(5, 'Battle Arena',  'Fighting','sword',  'Enter the arena and fight your way to the championship title.',      4.9, 11600, 'active'),
(6, 'World Builder', 'Strategy','world',  'Build, expand, and dominate your own civilisation from scratch.',    4.5,  6400, 'active');

-- --------------------------------------------------------
-- Table: tbl_content  (MILESTONE REQUIREMENT)
-- --------------------------------------------------------
CREATE TABLE `tbl_content` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `title`       varchar(150) NOT NULL,
  `description` text         NOT NULL,
  `image_url`   varchar(300) NOT NULL DEFAULT 'https://placehold.co/400x220/0d1117/00f0ff?text=StevenGames',
  `category`    varchar(60)  NOT NULL DEFAULT 'News',
  `status`      enum('published','draft') NOT NULL DEFAULT 'published',
  `created_at`  datetime     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tbl_content` (`title`, `description`, `image_url`, `category`) VALUES
('Season 3 Tournament Kicks Off',
 'The biggest Steven Games tournament of the year is here. Over 5,000 players battle for the Grand Champion title and $10,000 in prizes. Register now before slots fill up!',
 'https://placehold.co/400x220/050810/00f0ff?text=Season+3+Tournament',
 'Tournament'),
('New Game: Cyber Strike Launched',
 'Our latest action title Cyber Strike is now live! Features 30 levels of adrenaline-fuelled combat, a ranked mode, and a brand-new soundtrack.',
 'https://placehold.co/400x220/050810/ff3c78?text=Cyber+Strike',
 'New Game'),
('Weekly Challenge: Survive 5 Minutes',
 'This week challenge is live on Space Blaster. Survive five minutes against endless waves for a chance to win 500 bonus points and an exclusive badge.',
 'https://placehold.co/400x220/050810/7c3aed?text=Weekly+Challenge',
 'Challenge'),
('Maintenance Complete — Servers Upgraded',
 'Our server infrastructure has been upgraded to handle 3x the previous player load. Expect smoother gameplay, lower latency, and zero downtime going forward.',
 'https://placehold.co/400x220/050810/00f0ff?text=Servers+Upgraded',
 'News');

-- --------------------------------------------------------
-- Table: scores
-- --------------------------------------------------------
CREATE TABLE `scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `played_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `game_id` (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `scores` (`user_id`, `game_id`, `score`) VALUES
(2,1,45200),(2,5,38900),(3,1,41200),(3,3,29800),(4,2,35600),(5,4,22400);

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','banned') NOT NULL DEFAULT 'active',
  `score` int(11) NOT NULL DEFAULT 0,
  `games_played` int(11) NOT NULL DEFAULT 0,
  `win_streak` int(11) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`,`username`,`email`,`password`,`role`,`status`,`score`,`games_played`,`win_streak`) VALUES
(1,'admin','admin@stevengames.com','$2y$10$2pqtNQa9mVX/Ehuffd8n7uotYctHZnMimfEempgCxrUFPamb3yVI6','admin','active',0,0,0),
(2,'StevenX_Pro','steven@stevengames.com','$2y$10$/4yVFBj5x9rBmLUPSyh8t.gk.f6asbAXC1ZLJjbAsEhk88n1MqBIu','user','active',128450,89,14),
(3,'NightHawk99','hawk@email.com','$2y$10$wxC.i6i4Nm9e5E1K82gx6e5.22cewAPoXfN4nqg1xlc.eHVnFrINu','user','active',115200,74,9),
(4,'PixelKing','pixel@email.com','$2y$10$H0e87Ishp45XsdfphUOu4O7.bqASUDTE4PlaoOtpnVXF63NCCAJES','user','active',98780,62,7),
(5,'GamerQueen','queen@email.com','$2y$10$PvI91cXk1gAEtTnyMjoGBulMs55iZEO8OU69GSqMwkBtyYze9LRCi','user','active',87560,51,5),
(6,'SwiftBlade','swift@email.com','$2y$10$nC7si5gqZF8KVrv3/RV06OORw0WT0PklyD6N46a0LiPFN1Y1uk4WG','user','banned',76340,44,3),
(7,'ntale steven','st.ntale@unik.ac.ug','$2y$10$R18RbzPojpZLLrv.HsmuRujPTUan5lbutM1b7JD29TcHXyP1Tnj.2','user','active',0,0,0);

ALTER TABLE `scores`
  ADD CONSTRAINT `fk_score_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_score_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;
