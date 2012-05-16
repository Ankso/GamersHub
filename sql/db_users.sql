-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.18 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-05-17 00:45:32
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;

-- Dumping database structure for users
CREATE DATABASE IF NOT EXISTS `users` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `users`;


-- Dumping structure for table users.user_avatars
DROP TABLE IF EXISTS `user_avatars`;
CREATE TABLE IF NOT EXISTS `user_avatars` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The user''s unique ID',
  `avatar_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The avatar''s http path',
  KEY `FK_USER_DATA` (`user_id`),
  CONSTRAINT `FK_USER_DATA` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The relation of the user with their avatar''s path in the filesystem';

-- Dumping data for table users.user_avatars: ~1 rows (approximately)
DELETE FROM `user_avatars`;
/*!40000 ALTER TABLE `user_avatars` DISABLE KEYS */;
INSERT INTO `user_avatars` (`user_id`, `avatar_path`) VALUES
	(1, 'http://localhost//images/users/Ankso/avatar/Anksos_avatar.jpg');
/*!40000 ALTER TABLE `user_avatars` ENABLE KEYS */;


-- Dumping structure for table users.user_data
DROP TABLE IF EXISTS `user_data`;
CREATE TABLE IF NOT EXISTS `user_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User unique ID',
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Username (not real name)',
  `password_sha1` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'User password in SHA1',
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User e-mail',
  `ip_v4` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Last used IPv4',
  `ip_v6` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Last used IPv6',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 if the user is online, else 0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='General data about the users (name, mail, date of birth, etc...)';

-- Dumping data for table users.user_data: ~8 rows (approximately)
DELETE FROM `user_data`;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`id`, `username`, `password_sha1`, `email`, `ip_v4`, `ip_v6`, `is_online`) VALUES
	(1, 'Ankso', '69977bb50b14977d99b93f74d00499438ca466a5', 'misterankso@gmail.com', '127.0.0.1', NULL, 1),
	(2, 'Seldon', '8b04c7cea0aca3497f4315cecb6dd67536acdc5d', 'seldon_we@hotmail.com', '127.0.0.1', NULL, 0),
	(4, 'xItsy', '76794e37a3be9e8c40a2d4347a195dd0b87fb14c', 'javier.rf92@gmail.com', '192.168.1.137', NULL, 1),
	(5, 'Perico', '964bd26ba2aaa08c7d66e19934b651b2116a3d91', 'delospalotes@hotmail.com', '127.0.0.1', NULL, 0),
	(6, 'pericodelospalotes', 'd8048855620502ed73b20a1dbb993f536545ac4a', 'io@hotmail.com', '192.168.1.137', NULL, 0),
	(7, 'mrperico', '287b652dd3f61c7695d0234daa694b9dd07d81ff', 'el@hotmail.com', '192.168.1.137', NULL, 1),
	(8, 'pericoxd', '76790e667d055c8f3bb5a25a064af7bb6c23cab0', 'tres@hotmail.com', '192.168.1.137', NULL, 0),
	(9, 'MrAnkso', '339cf109b9cef8bbeb718d538fe8e471eb00c196', 'mrankso@hotmail.com', '127.0.0.1', NULL, 0);
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;


-- Dumping structure for table users.user_detailed_data
DROP TABLE IF EXISTS `user_detailed_data`;
CREATE TABLE IF NOT EXISTS `user_detailed_data` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The user''s unique ID',
  `bio` text COLLATE utf8_unicode_ci COMMENT 'A small biography written by the user',
  `birthday` date DEFAULT NULL COMMENT 'The user''s birthday date',
  `country` text COLLATE utf8_unicode_ci COMMENT 'The user''s country',
  `city` text COLLATE utf8_unicode_ci COMMENT 'The user''s city',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_USER_DETAILED_DATA` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Detailed data about the user';

-- Dumping data for table users.user_detailed_data: ~0 rows (approximately)
DELETE FROM `user_detailed_data`;
/*!40000 ALTER TABLE `user_detailed_data` DISABLE KEYS */;
INSERT INTO `user_detailed_data` (`user_id`, `bio`, `birthday`, `country`, `city`) VALUES
	(1, 'The test bio', '1991-11-24', 'Galicia', 'Montrove'),
	(2, NULL, NULL, NULL, NULL),
	(4, NULL, NULL, NULL, NULL),
	(5, NULL, NULL, NULL, NULL),
	(6, NULL, NULL, NULL, NULL),
	(7, NULL, NULL, NULL, NULL),
	(8, NULL, NULL, NULL, NULL),
	(9, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `user_detailed_data` ENABLE KEYS */;


-- Dumping structure for table users.user_friends
DROP TABLE IF EXISTS `user_friends`;
CREATE TABLE IF NOT EXISTS `user_friends` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'user''s ID',
  `friend_id` bigint(20) unsigned NOT NULL COMMENT 'friend''s ID',
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`),
  CONSTRAINT `friend_users_ibfk_1` FOREIGN KEY (`friend_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores the user''s friends ids';

-- Dumping data for table users.user_friends: ~10 rows (approximately)
DELETE FROM `user_friends`;
/*!40000 ALTER TABLE `user_friends` DISABLE KEYS */;
INSERT INTO `user_friends` (`user_id`, `friend_id`) VALUES
	(5, 2),
	(2, 5),
	(1, 5),
	(5, 1),
	(1, 8),
	(8, 1),
	(7, 6),
	(6, 7),
	(2, 1),
	(1, 2);
/*!40000 ALTER TABLE `user_friends` ENABLE KEYS */;


-- Dumping structure for table users.user_friend_requests
DROP TABLE IF EXISTS `user_friend_requests`;
CREATE TABLE IF NOT EXISTS `user_friend_requests` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The ID of the user that''s going to receive the friend request',
  `requester_id` bigint(20) unsigned NOT NULL COMMENT 'The ID of the user that sends the friend request',
  `message` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No message' COMMENT 'The message that the requester sends to the user',
  KEY `FK__user_data` (`user_id`),
  KEY `FK__friend_data_2` (`requester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table users.user_friend_requests: ~1 rows (approximately)
DELETE FROM `user_friend_requests`;
/*!40000 ALTER TABLE `user_friend_requests` DISABLE KEYS */;
INSERT INTO `user_friend_requests` (`user_id`, `requester_id`, `message`) VALUES
	(6, 1, 'Ankso wants to be your friend!');
/*!40000 ALTER TABLE `user_friend_requests` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
