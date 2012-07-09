-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.18 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2012-07-09 22:14:13
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
  `avatar_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '/images/default_avatar.png' COMMENT 'The avatar''s http path',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_USER_DATA` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The relation of the user with their avatar''s path in the filesystem';

-- Dumping data for table users.user_avatars: ~10 rows (approximately)
DELETE FROM `user_avatars`;
/*!40000 ALTER TABLE `user_avatars` DISABLE KEYS */;
INSERT INTO `user_avatars` (`user_id`, `avatar_path`) VALUES
	(1, 'http://www.gravatar.com/avatar/748b5b25d1b23de07adb3c5f4ebf5851?d=http://gamersnet.no-ip.org/images/default_avatar.png&s=200&r=pg'),
	(2, 'http://www.gravatar.com/avatar/b5720d434269ad10f6fcf1be7772c363?d=http://gamersnet.no-ip.org/images/default_avatar.png&s=200&r=pg'),
	(4, '/images/default_avatar.png'),
	(5, '/images/default_avatar.png'),
	(6, '/images/default_avatar.png'),
	(7, '/images/default_avatar.png'),
	(8, '/images/default_avatar.png'),
	(9, 'http://gamersnet.no-ip.org/images/users/MrAnkso/avatar/MrAnksos_avatar.jpg'),
	(10, '/images/default_avatar.png'),
	(11, 'http://www.gravatar.com/avatar/e7c5b7a8d29a08fe1484003287a020e9?d=http://gamersnet.no-ip.org/images/default_avatar.png&s=200&r=pg');
/*!40000 ALTER TABLE `user_avatars` ENABLE KEYS */;


-- Dumping structure for table users.user_board
DROP TABLE IF EXISTS `user_board`;
CREATE TABLE IF NOT EXISTS `user_board` (
  `message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The message''s unique ID',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The ID of the user that wrotes the message',
  `message_number` int(10) unsigned NOT NULL COMMENT 'The message number for a specific user. It''s basically the number of messages that a specific user has sent.',
  `message` varchar(1500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The message or comment',
  `date` datetime NOT NULL COMMENT 'Date and hour when the message was sent',
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_USER_BOARD_ID` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores the messages written by the user''s in their board.';

-- Dumping data for table users.user_board: ~11 rows (approximately)
DELETE FROM `user_board`;
/*!40000 ALTER TABLE `user_board` DISABLE KEYS */;
INSERT INTO `user_board` (`message_id`, `user_id`, `message_number`, `message`, `date`) VALUES
	(15, 9, 1, 'Another testing message from this user!!', '2012-05-29 21:51:17'),
	(20, 1, 5, 'Let\'s go test the link parser and the youtube video parser <div align="center"><iframe width="640" height="480" src="http://www.youtube.com/embed/wqvgttQVt4s?wmode=transparent" frameborder="0" allowfullscreen></iframe></div><br />', '2012-06-01 20:35:57'),
	(21, 1, 6, 'Esto es un link completamente normal, automáticamente parseado: <a href="http://php.net/manual/es/function.preg-replace.php" target="_blank">http://php.net/manual/es/function.preg-replace.php</a>', '2012-06-01 20:36:57'),
	(22, 9, 2, 'jQuery, una de las muchas librerías que uso para desarrollar todo esto: <a href="http://jquery.com/" target="_blank">http://jquery.com/</a>', '2012-06-01 21:08:45'),
	(23, 2, 1, 'U.u ya puedo escribir aquí cosas que pro.', '2012-06-01 22:06:28'),
	(37, 1, 7, 'Implementado el manejo de sesiones via MySQL <a href="http://github.com/Ankso/GamersNet/commit/99595ed0bb2274a845421d0469630ca4915e3465" target="_blank">http://github.com/Ankso/GamersNet/commit/99595ed0bb2274a845421d0469630ca4915e3465</a>', '2012-06-07 23:00:37'),
	(39, 1, 8, 'Re-escribiendo toda la aplicación cliente para que sea un diseño orientado a objetos real. Va a llevar lo suyo.', '2012-06-15 22:03:35'),
	(41, 1, 9, 'Probando los mensajes con la nueva API orientada a objetos.', '2012-06-16 18:11:45'),
	(42, 1, 10, 'La conexión de iago VA EN LA LLAMA!', '2012-06-18 20:10:03'),
	(44, 1, 11, 'Repositorio del RTS: <a href="https://github.com/Ankso/GamersHub-Real-Time-Server" target="_blank">https://github.com/Ankso/GamersHub-Real-Time-Server</a>', '2012-06-20 18:22:59'),
	(45, 2, 2, 'Cosas', '2012-07-09 17:49:17');
/*!40000 ALTER TABLE `user_board` ENABLE KEYS */;


-- Dumping structure for table users.user_board_replies
DROP TABLE IF EXISTS `user_board_replies`;
CREATE TABLE IF NOT EXISTS `user_board_replies` (
  `reply_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'An unique ID for the reply',
  `sender_id` bigint(20) unsigned NOT NULL COMMENT 'The unique ID of the user that writes the reply',
  `message_id` bigint(20) unsigned NOT NULL COMMENT 'The main comment ID',
  `message` varchar(1500) COLLATE utf8_unicode_ci NOT NULL COMMENT 'The message in the reply',
  `date` datetime NOT NULL COMMENT 'Date and time when the reply was sent',
  PRIMARY KEY (`reply_id`),
  KEY `FK_USER_BOARD` (`message_id`),
  KEY `FK_USER_DATA_IDS` (`sender_id`),
  CONSTRAINT `FK_USER_BOARD` FOREIGN KEY (`message_id`) REFERENCES `user_board` (`message_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_USER_DATA_IDS` FOREIGN KEY (`sender_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores all the replies made to main comments on the user''s boards.';

-- Dumping data for table users.user_board_replies: ~7 rows (approximately)
DELETE FROM `user_board_replies`;
/*!40000 ALTER TABLE `user_board_replies` DISABLE KEYS */;
INSERT INTO `user_board_replies` (`reply_id`, `sender_id`, `message_id`, `message`, `date`) VALUES
	(5, 9, 21, 'Mola', '2012-06-05 19:49:30'),
	(16, 9, 20, 'Canción epiquérrima <div style="text-align:center;"><iframe width="480" height="360" src="http://www.youtube.com/embed/iqoUDGJ4g2A?wmode=transparent" frameborder="0" allowfullscreen></iframe></div><br />', '2012-06-05 22:12:27'),
	(23, 1, 21, 'Lo se', '2012-06-06 20:35:38'),
	(24, 2, 37, 'Pues que yupi', '2012-06-07 23:02:10'),
	(25, 1, 37, 'Pos si', '2012-06-07 23:02:36'),
	(39, 1, 41, 'yupi', '2012-06-18 20:01:21'),
	(40, 1, 23, 'juas juas', '2012-06-18 20:51:45');
/*!40000 ALTER TABLE `user_board_replies` ENABLE KEYS */;


-- Dumping structure for table users.user_custom_options
DROP TABLE IF EXISTS `user_custom_options`;
CREATE TABLE IF NOT EXISTS `user_custom_options` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The user''s unique ID',
  `option_livestream` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0 if the user''s livestream is disabled, else 1',
  `option_livestream_livecomments` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0 if the user''s LiveCommnets section in the livestream is disabled, else 1',
  `option_latest_news` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0 if the Latests News Section is disabled, else 1',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_USER_CUSTOM_OPTIONS_ID` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Saves customization options for the user''s space. The options are a set of boolean values (Enabled/Disabled) by the way.';

-- Dumping data for table users.user_custom_options: ~10 rows (approximately)
DELETE FROM `user_custom_options`;
/*!40000 ALTER TABLE `user_custom_options` DISABLE KEYS */;
INSERT INTO `user_custom_options` (`user_id`, `option_livestream`, `option_livestream_livecomments`, `option_latest_news`) VALUES
	(1, 0, 0, 1),
	(2, 1, 1, 1),
	(4, 1, 1, 1),
	(5, 1, 1, 1),
	(6, 1, 1, 1),
	(7, 1, 1, 1),
	(8, 1, 1, 1),
	(9, 1, 1, 1),
	(10, 1, 1, 1),
	(11, 1, 1, 1);
/*!40000 ALTER TABLE `user_custom_options` ENABLE KEYS */;


-- Dumping structure for table users.user_data
DROP TABLE IF EXISTS `user_data`;
CREATE TABLE IF NOT EXISTS `user_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User unique ID',
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Username (not real name)',
  `password_sha1` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'User password in SHA1',
  `random_session_id` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Random unique session identifier generated on each user login. It''s used to identify the user when connecting to a real time server app',
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User e-mail',
  `ip_v4` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Last used IPv4',
  `ip_v6` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Last used IPv6',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 if the user is online, else 0',
  `last_login` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT 'Time and date of the last user login.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `random_session_id` (`random_session_id`),
  KEY `id` (`id`,`username`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='General data about the users (name, mail, date of birth, etc...)';

-- Dumping data for table users.user_data: ~10 rows (approximately)
DELETE FROM `user_data`;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` (`id`, `username`, `password_sha1`, `random_session_id`, `email`, `ip_v4`, `ip_v6`, `is_online`, `last_login`) VALUES
	(1, 'Ankso', '69977bb50b14977d99b93f74d00499438ca466a5', '4c1b5f350e7337a48c215f2b02d78201181c0d1a929cad2847106e18975d30764d792d0dbf0385e1beb9ec091490baf4d5fc3092d0f5849b32dd4e11568b4b60', 'misterankso@gmail.com', '127.0.0.1', NULL, 1, '2012-06-20 18:26:55'),
	(2, 'Seldon', '8b04c7cea0aca3497f4315cecb6dd67536acdc5d', NULL, 'seldon_we@hotmail.com', '127.0.0.1', NULL, 0, '2012-06-20 19:59:19'),
	(4, 'xItsy', '76794e37a3be9e8c40a2d4347a195dd0b87fb14c', NULL, 'javier.rf92@gmail.com', '192.168.1.137', NULL, 0, '1000-01-01 00:00:00'),
	(5, 'Perico', '964bd26ba2aaa08c7d66e19934b651b2116a3d91', NULL, 'delospalotes@hotmail.com', '127.0.0.1', NULL, 0, '1000-01-01 00:00:00'),
	(6, 'pericodelospalotes', 'd8048855620502ed73b20a1dbb993f536545ac4a', NULL, 'io@hotmail.com', '192.168.1.137', NULL, 0, '2012-06-15 18:57:49'),
	(7, 'mrperico', '287b652dd3f61c7695d0234daa694b9dd07d81ff', NULL, 'el@hotmail.com', '192.168.1.137', NULL, 0, '2012-06-15 18:56:30'),
	(8, 'pericoxd', '76790e667d055c8f3bb5a25a064af7bb6c23cab0', NULL, 'tres@hotmail.com', '192.168.1.137', NULL, 0, '1000-01-01 00:00:00'),
	(9, 'MrAnkso', '339cf109b9cef8bbeb718d538fe8e471eb00c196', NULL, 'mrankso@hotmail.com', '192.168.1.137', NULL, 0, '2012-06-14 20:08:33'),
	(10, 'hache', '77d9d0d6d90d6f0388bc626f6f3db845c85263df', NULL, 'hachegamer@gmail.com', '192.168.1.137', NULL, 0, '1000-01-01 00:00:00'),
	(11, 'Muphasa', '59ab773b040ffb8d7710386fb8cc1f3bc0e81984', NULL, 'iagedopr@hotmail.com', '192.168.1.137', NULL, 0, '2012-06-01 23:41:03');
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
  CONSTRAINT `FK_USER_DETAILED_DATA` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Detailed data about the user';

-- Dumping data for table users.user_detailed_data: ~10 rows (approximately)
DELETE FROM `user_detailed_data`;
/*!40000 ALTER TABLE `user_detailed_data` DISABLE KEYS */;
INSERT INTO `user_detailed_data` (`user_id`, `bio`, `birthday`, `country`, `city`) VALUES
	(1, '<br><font color="#FF0000">if</font> ($this->IsSuccess())<br>\n{<br>\n    we->CastToRich();<br>\n    me->Buy(<font color="#5522FF">TYPE_CAR</font>, <font color="#5522FF">BRAND_ROLLSROYCE</font>);<br>\n}', '1991-11-24', 'Galicia', 'Montrove Capital City'),
	(2, 'Pwned.', '1800-12-12', 'Antarctica', 'Antarctica Capital City'),
	(4, NULL, NULL, NULL, NULL),
	(5, NULL, NULL, NULL, NULL),
	(6, NULL, NULL, NULL, NULL),
	(7, NULL, NULL, NULL, NULL),
	(8, NULL, NULL, NULL, NULL),
	(9, NULL, NULL, NULL, NULL),
	(10, NULL, NULL, NULL, NULL),
	(11, 'Gamer', '0000-00-00', 'Montrove', 'Montro city');
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

-- Dumping data for table users.user_friends: ~16 rows (approximately)
DELETE FROM `user_friends`;
/*!40000 ALTER TABLE `user_friends` DISABLE KEYS */;
INSERT INTO `user_friends` (`user_id`, `friend_id`) VALUES
	(5, 2),
	(2, 5),
	(1, 5),
	(5, 1),
	(7, 6),
	(6, 7),
	(2, 1),
	(1, 2),
	(6, 1),
	(1, 6),
	(9, 1),
	(1, 9),
	(11, 1),
	(1, 11),
	(11, 2),
	(2, 11);
/*!40000 ALTER TABLE `user_friends` ENABLE KEYS */;


-- Dumping structure for table users.user_friend_requests
DROP TABLE IF EXISTS `user_friend_requests`;
CREATE TABLE IF NOT EXISTS `user_friend_requests` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The ID of the user that''s going to receive the friend request',
  `requester_id` bigint(20) unsigned NOT NULL COMMENT 'The ID of the user that sends the friend request',
  `message` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'No message' COMMENT 'The message that the requester sends to the user',
  KEY `FK_USER_FRIEND_REQUESTS_ID_USER` (`user_id`),
  KEY `FK_USER_FRIEND_REQUESTS_ID_REQUESTER` (`requester_id`),
  CONSTRAINT `FK_USER_FRIEND_REQUESTS_ID_REQUESTER` FOREIGN KEY (`requester_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_USER_FRIEND_REQUESTS_ID_USER` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores the friend requests between users.';

-- Dumping data for table users.user_friend_requests: ~3 rows (approximately)
DELETE FROM `user_friend_requests`;
/*!40000 ALTER TABLE `user_friend_requests` DISABLE KEYS */;
INSERT INTO `user_friend_requests` (`user_id`, `requester_id`, `message`) VALUES
	(8, 1, 'Ankso wants to be your friend!'),
	(10, 1, 'Ankso wants to be your friend!'),
	(4, 1, 'Ankso wants to be your friend!');
/*!40000 ALTER TABLE `user_friend_requests` ENABLE KEYS */;


-- Dumping structure for table users.user_privacy
DROP TABLE IF EXISTS `user_privacy`;
CREATE TABLE IF NOT EXISTS `user_privacy` (
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'The user''s unique ID',
  `view_email` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Determines who can view the user''s email. Allowed values are 0 (nobody), 1 (friends), 2(friends and clan memebers) and 3 (everyone)',
  `view_profile` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Determines who can view the user''s detailed profile. Allowed values are 1 (friends), 2(friends and clan memebers) and 3 (everyone)',
  `view_livestream` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Determines who can view the user''s livestream. Allowed values are 1 (friends), 2(friends and clan memebers) and 3 (everyone)',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_USERS_DATA_ID` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The user''s privacy options.';

-- Dumping data for table users.user_privacy: ~10 rows (approximately)
DELETE FROM `user_privacy`;
/*!40000 ALTER TABLE `user_privacy` DISABLE KEYS */;
INSERT INTO `user_privacy` (`user_id`, `view_email`, `view_profile`, `view_livestream`) VALUES
	(1, 1, 2, 3),
	(2, 1, 1, 1),
	(4, 1, 1, 1),
	(5, 1, 1, 1),
	(6, 1, 1, 1),
	(7, 1, 1, 1),
	(8, 1, 1, 1),
	(9, 1, 1, 1),
	(10, 1, 1, 1),
	(11, 1, 1, 3);
/*!40000 ALTER TABLE `user_privacy` ENABLE KEYS */;


-- Dumping structure for table users.user_private_messages
DROP TABLE IF EXISTS `user_private_messages`;
CREATE TABLE IF NOT EXISTS `user_private_messages` (
  `sender_id` bigint(20) unsigned NOT NULL COMMENT 'The sender''s unique ID',
  `receiver_id` bigint(20) unsigned NOT NULL COMMENT 'The receiver''s unique ID',
  `message` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'The message itself',
  `date` datetime NOT NULL COMMENT 'The date and time when the message was sent.',
  `readed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 if the message hasn''t been readed yet, else 1',
  KEY `FK_USER_DATA_ID` (`sender_id`),
  KEY `FK_USER_DATA_ID2` (`receiver_id`),
  CONSTRAINT `FK_USER_DATA_ID` FOREIGN KEY (`sender_id`) REFERENCES `user_data` (`id`),
  CONSTRAINT `FK_USER_DATA_ID2` FOREIGN KEY (`receiver_id`) REFERENCES `user_data` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table that contains the user''s private messages';

-- Dumping data for table users.user_private_messages: ~8 rows (approximately)
DELETE FROM `user_private_messages`;
/*!40000 ALTER TABLE `user_private_messages` DISABLE KEYS */;
INSERT INTO `user_private_messages` (`sender_id`, `receiver_id`, `message`, `date`, `readed`) VALUES
	(1, 2, 'This is a testing message fuck yeah ¡!', '2012-05-24 22:10:35', 1),
	(1, 2, 'And this is another one', '2012-05-24 22:11:09', 1),
	(9, 1, 'Hello m8! This is a test message.', '2012-05-28 17:30:27', 1),
	(1, 9, 'Yupi, que coñas que esté hablando conmigo mismo.\n\nxD', '2012-05-28 17:55:03', 1),
	(9, 1, 'Pues vaya', '2012-05-28 17:55:38', 1),
	(1, 11, 'Iaguitooooooooooooooooooooooooooooooooo!!!!!!!!!', '2012-06-01 22:01:11', 1),
	(11, 1, 'no veo el cursor de escrbir en este texto tan gris D:\n', '2012-06-02 18:13:19', 1),
	(1, 11, 'Eres un julai\n\npedo', '2012-06-02 20:21:06', 0),
	(1, 2, 'julai', '2012-07-09 18:11:54', 1),
	(2, 1, 'chumacho', '2012-07-09 18:12:17', 1);
/*!40000 ALTER TABLE `user_private_messages` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
