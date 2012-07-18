ALTER TABLE `user_detailed_data` ADD COLUMN `avatar_path` VARCHAR(255) NOT NULL DEFAULT '/images/default_avatar.png' COMMENT 'The path to the user\'s avatar' AFTER `city`;
UPDATE user_detailed_data, user_avatars SET user_detailed_data.avatar_path = user_avatars.avatar_path WHERE user_avatars.user_id = user_detailed_data.user_id;
DROP TABLE IF EXISTS user_avatars;