-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.18 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-10-18 00:10:25
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for sessions
CREATE DATABASE IF NOT EXISTS `sessions` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `sessions`;


-- Dumping structure for table sessions.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(32) NOT NULL COMMENT 'The PHP session unique ID',
  `data` varchar(50) NOT NULL COMMENT 'The PHP session data',
  `last_update` int(10) unsigned NOT NULL COMMENT 'Last update of this session',
  PRIMARY KEY (`id`),
  KEY `data` (`data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores the ID and data of a PHP session';

-- Dumping data for table sessions.sessions: ~1 rows (approximately)
DELETE FROM `sessions`;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
