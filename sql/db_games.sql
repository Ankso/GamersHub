-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.18 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-10-18 00:10:13
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for games
CREATE DATABASE IF NOT EXISTS `games` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `games`;


-- Dumping structure for table games.game_data
DROP TABLE IF EXISTS `game_data`;
CREATE TABLE IF NOT EXISTS `game_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'A unique ID for each game',
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No title' COMMENT 'Game''s title',
  `webpage` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No webpage' COMMENT 'Game''s official webpage.',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'A brief description about the game',
  `developer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The game''s developer ID (table game_developers)',
  `publisher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The game''s publisher ID (table game_publishers)',
  `image_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'The game''s image path.',
  `exe_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unknown' COMMENT 'The game''s executable name (or names, separated by ";") , to compare to the user''s running processes and know if the user is playing a the specific game.',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `FK_DEVELOPER` (`developer_id`),
  KEY `FK_PUBLISHER` (`publisher_id`),
  CONSTRAINT `FK_DEVELOPER` FOREIGN KEY (`developer_id`) REFERENCES `game_developers` (`id`),
  CONSTRAINT `FK_PUBLISHER` FOREIGN KEY (`publisher_id`) REFERENCES `game_publishers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='General data about the specific games';

-- Dumping data for table games.game_data: ~7 rows (approximately)
DELETE FROM `game_data`;
/*!40000 ALTER TABLE `game_data` DISABLE KEYS */;
INSERT INTO `game_data` (`id`, `title`, `webpage`, `description`, `developer_id`, `publisher_id`, `image_path`, `exe_name`) VALUES
	(1, 'Battlefield 3', 'http://battlefield.com', 'Battlefield 3 is a FPS (First Person Shooter) powered by the Frosbite 2 engine.', 1, 1, '/images/games/battlefield_3/battlefield_3_cover.jpg', 'bf3.exe'),
	(2, 'Call of Duty 4: Modern Warfare', 'http://callofduty.com', 'Call of Duty: Modern Warfare, also known as Call of Duty 4, is a First Person Shooter based on the actual warfare.', 6, 2, '/images/games/call_of_duty_modern_warfare/call_of_duty_modern_warfare_cover.jpg', 'iw3sp.exe;iw3mp.exe'),
	(3, 'World of Warcraft', 'http://battle.net', 'World of Warcraft is a MMORPG (Massive Multiplayer Online Roll Play Game) developed by Blizzard. It\'s well known for be the most famous game in his genre.', 3, 2, '/images/games/world_of_warcraft/world_of_warcraft_cover.jpg', 'Wow.exe;Wow-64.exe'),
	(4, 'EvE Online', 'http://eveonline.com', 'EvE Online is a Sci-fi MMORPG were freedom is the best game feature.', 4, 3, '/images/games/eve_online/eve_online_cover.jpg', 'eve.exe;ExeFile.exe'),
	(5, 'Mass Effect', 'http://masseffect.com', 'Mass Effect is a Sci-fi RPG developed by Bioware, well known for his relations system between characters.', 5, 1, '/images/games/mass_effect/mass_effect_cover.jpg', 'MassEffect.exe'),
	(6, 'Mass Effect 2', 'http://masseffect.com', 'The continuation of the critically aclaimed Mass Effect.', 5, 1, '/images/games/mass_effect_2/mass_effect_2_cover.jpg', 'MassEffect2.exe'),
	(7, 'Mass Effect 3', 'http://masseffect.com', 'The continuation of the critically aclaimed Mass Effect 2, and the last of the saga.', 5, 1, '/images/games/mass_effect_3/mass_effect_3_cover.jpg', 'MassEffect3.exe');
/*!40000 ALTER TABLE `game_data` ENABLE KEYS */;


-- Dumping structure for table games.game_developers
DROP TABLE IF EXISTS `game_developers`;
CREATE TABLE IF NOT EXISTS `game_developers` (
  `id` int(10) unsigned NOT NULL COMMENT 'The developer company''s unique ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The developer company''s name',
  `webpage` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The developer company''s official webpage',
  `description` text COLLATE utf8_unicode_ci COMMENT 'A description about the company itself',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of gaming developing companies (more data must be added)';

-- Dumping data for table games.game_developers: ~7 rows (approximately)
DELETE FROM `game_developers`;
/*!40000 ALTER TABLE `game_developers` DISABLE KEYS */;
INSERT INTO `game_developers` (`id`, `name`, `webpage`, `description`) VALUES
	(0, 'Unknown', 'Unknown', 'No description'),
	(1, 'DICE', 'Unknown', 'No description.'),
	(2, 'Treyarch', 'Unknown', 'No description.'),
	(3, 'Activision-Blizzard', 'Unknown', 'No description.'),
	(4, 'CCP Games', 'Unknown', 'HQs in Iceland.'),
	(5, 'Bioware', 'Unknown', 'No description.'),
	(6, 'Infinity Ward', 'Unknown', 'No description.');
/*!40000 ALTER TABLE `game_developers` ENABLE KEYS */;


-- Dumping structure for table games.game_genres
DROP TABLE IF EXISTS `game_genres`;
CREATE TABLE IF NOT EXISTS `game_genres` (
  `id` int(10) unsigned NOT NULL COMMENT 'The genre unique ID',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The genre itself...',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The game''s genres';

-- Dumping data for table games.game_genres: ~7 rows (approximately)
DELETE FROM `game_genres`;
/*!40000 ALTER TABLE `game_genres` DISABLE KEYS */;
INSERT INTO `game_genres` (`id`, `name`) VALUES
	(0, 'FPS (First Person Shooter)'),
	(1, 'MMO (Massive Multiplayer Online)'),
	(2, 'RPG (Roll Play Game)'),
	(3, 'Shooter'),
	(4, 'Action'),
	(5, 'Adventures'),
	(6, 'Sci-fi');
/*!40000 ALTER TABLE `game_genres` ENABLE KEYS */;


-- Dumping structure for table games.game_genres_relation
DROP TABLE IF EXISTS `game_genres_relation`;
CREATE TABLE IF NOT EXISTS `game_genres_relation` (
  `game_id` bigint(20) unsigned NOT NULL COMMENT 'The game''s unique ID',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'The genre''s unique ID',
  KEY `game_id` (`game_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `FK_GAMES_DATA` FOREIGN KEY (`game_id`) REFERENCES `game_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GENRE_DATA` FOREIGN KEY (`genre_id`) REFERENCES `game_genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table games.game_genres_relation: ~17 rows (approximately)
DELETE FROM `game_genres_relation`;
/*!40000 ALTER TABLE `game_genres_relation` DISABLE KEYS */;
INSERT INTO `game_genres_relation` (`game_id`, `genre_id`) VALUES
	(1, 0),
	(1, 4),
	(1, 3),
	(2, 0),
	(2, 4),
	(2, 3),
	(4, 1),
	(4, 2),
	(4, 6),
	(5, 2),
	(6, 2),
	(5, 6),
	(6, 6),
	(7, 2),
	(7, 6),
	(3, 1),
	(3, 2);
/*!40000 ALTER TABLE `game_genres_relation` ENABLE KEYS */;


-- Dumping structure for table games.game_publishers
DROP TABLE IF EXISTS `game_publishers`;
CREATE TABLE IF NOT EXISTS `game_publishers` (
  `id` int(10) unsigned NOT NULL COMMENT 'The compnay unique ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The company''s name',
  `webpage` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The company''s official webpage',
  `description` text COLLATE utf8_unicode_ci COMMENT 'A brief description of the company',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of games publisher companies';

-- Dumping data for table games.game_publishers: ~4 rows (approximately)
DELETE FROM `game_publishers`;
/*!40000 ALTER TABLE `game_publishers` DISABLE KEYS */;
INSERT INTO `game_publishers` (`id`, `name`, `webpage`, `description`) VALUES
	(0, 'Unknown', 'Unknown', 'No description'),
	(1, 'EA Games', 'Unknown', 'Electronic Arts'),
	(2, 'Activision-Blizzard', 'Unknown', 'No description.'),
	(3, 'CCP Games', 'Unknown', 'No description.');
/*!40000 ALTER TABLE `game_publishers` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
