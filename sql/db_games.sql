-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.18 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-05-17 00:45:43
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for games
CREATE DATABASE IF NOT EXISTS `games` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `games`;


-- Dumping structure for table games.games_genres_relation
DROP TABLE IF EXISTS `games_genres_relation`;
CREATE TABLE IF NOT EXISTS `games_genres_relation` (
  `game_id` bigint(20) unsigned NOT NULL COMMENT 'The game''s unique ID',
  `genre_id` int(10) unsigned NOT NULL COMMENT 'The genre''s unique ID',
  KEY `game_id` (`game_id`),
  KEY `genre_id` (`genre_id`),
  CONSTRAINT `FK_GAMES_DATA` FOREIGN KEY (`game_id`) REFERENCES `game_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_GENRE_DATA` FOREIGN KEY (`genre_id`) REFERENCES `game_genres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table games.games_genres_relation: ~1 rows (approximately)
DELETE FROM `games_genres_relation`;
/*!40000 ALTER TABLE `games_genres_relation` DISABLE KEYS */;
INSERT INTO `games_genres_relation` (`game_id`, `genre_id`) VALUES
	(1, 0);
/*!40000 ALTER TABLE `games_genres_relation` ENABLE KEYS */;


-- Dumping structure for table games.game_data
DROP TABLE IF EXISTS `game_data`;
CREATE TABLE IF NOT EXISTS `game_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'A unique ID for each game',
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No title' COMMENT 'Game''s title',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'A brief description about the game',
  `developer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The game''s developer ID (table game_developers)',
  `publisher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The game''s publisher ID (table game_publishers)',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `FK_DEVELOPER` (`developer_id`),
  KEY `FK_PUBLISHER` (`publisher_id`),
  CONSTRAINT `FK_DEVELOPER` FOREIGN KEY (`developer_id`) REFERENCES `game_developers` (`id`),
  CONSTRAINT `FK_PUBLISHER` FOREIGN KEY (`publisher_id`) REFERENCES `game_publishers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='General data about the specific games';

-- Dumping data for table games.game_data: ~1 rows (approximately)
DELETE FROM `game_data`;
/*!40000 ALTER TABLE `game_data` DISABLE KEYS */;
INSERT INTO `game_data` (`id`, `title`, `description`, `developer_id`, `publisher_id`) VALUES
	(1, 'Test game', 'This is a test game, it must be removed after testing.', 0, 0);
/*!40000 ALTER TABLE `game_data` ENABLE KEYS */;


-- Dumping structure for table games.game_developers
DROP TABLE IF EXISTS `game_developers`;
CREATE TABLE IF NOT EXISTS `game_developers` (
  `id` int(10) unsigned NOT NULL COMMENT 'The developer company''s unique ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The developer company''s name',
  `description` text COLLATE utf8_unicode_ci COMMENT 'A description about the company itself',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of gaming developing companies (more data must be added)';

-- Dumping data for table games.game_developers: ~1 rows (approximately)
DELETE FROM `game_developers`;
/*!40000 ALTER TABLE `game_developers` DISABLE KEYS */;
INSERT INTO `game_developers` (`id`, `name`, `description`) VALUES
	(0, 'Unknown', 'No description');
/*!40000 ALTER TABLE `game_developers` ENABLE KEYS */;


-- Dumping structure for table games.game_genres
DROP TABLE IF EXISTS `game_genres`;
CREATE TABLE IF NOT EXISTS `game_genres` (
  `id` int(10) unsigned NOT NULL COMMENT 'The genre unique ID',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The genre itself...',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The game''s genres';

-- Dumping data for table games.game_genres: ~1 rows (approximately)
DELETE FROM `game_genres`;
/*!40000 ALTER TABLE `game_genres` DISABLE KEYS */;
INSERT INTO `game_genres` (`id`, `name`) VALUES
	(0, 'Unknown');
/*!40000 ALTER TABLE `game_genres` ENABLE KEYS */;


-- Dumping structure for table games.game_publishers
DROP TABLE IF EXISTS `game_publishers`;
CREATE TABLE IF NOT EXISTS `game_publishers` (
  `id` int(10) unsigned NOT NULL COMMENT 'The compnay unique ID',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unknown' COMMENT 'The company''s name',
  `description` text COLLATE utf8_unicode_ci COMMENT 'A brief description of the company',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of games publisher companies';

-- Dumping data for table games.game_publishers: ~1 rows (approximately)
DELETE FROM `game_publishers`;
/*!40000 ALTER TABLE `game_publishers` DISABLE KEYS */;
INSERT INTO `game_publishers` (`id`, `name`, `description`) VALUES
	(0, 'Unknown', 'No description');
/*!40000 ALTER TABLE `game_publishers` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
