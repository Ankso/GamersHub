<?php
/**
 * This class acts as an enum with all the prepared statements that the application needs.
 * That's why it isn't placed in /classes folder.
 * @author Ankso
 */
class Statements
{
    // Basic load/save user data queries
    const SELECT_USER_DATA_BY_ID              = "SELECT id, username, password_sha1, random_session_id, email, ip_v4, ip_v6, last_login FROM user_data WHERE id = ?";
    const SELECT_USER_DATA_BY_USERNAME        = "SELECT id, username, password_sha1, random_session_id, email, ip_v4, ip_v6, last_login FROM user_data WHERE username = ?";
    const REPLACE_USER_DATA                   = "REPLACE INTO user_data VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    const SELECT_USER_DATA_ID                 = "SELECT id FROM user_data WHERE username = ?";
    const SELECT_USER_DATA_USERNAME           = "SELECT username FROM user_data WHERE id = ?";
    // For user's data updates and profile changers functions
    const UPDATE_USER_DATA_ID                 = "UPDATE user_data SET id = ? WHERE id = ?";
    const UPDATE_USER_DATA_USERNAME           = "UPDATE user_data SET username = ? WHERE id = ?";
    const UPDATE_USER_DATA_PASSWORD           = "UPDATE user_data SET password_sha1 = ? WHERE id = ?";
    const UPDATE_USER_DATA_EMAIL              = "UPDATE user_data SET email = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV4               = "UPDATE user_data SET ip_v4 = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV6               = "UPDATE user_data SET ip_v6 = ? WHERE id = ?";
    const UPDATE_USER_DATA_ONLINE             = "UPDATE user_data SET is_online = ? WHERE id = ?";
    const UPDATE_USER_DATA_LAST_LOGIN         = "UPDATE user_data SET last_login = ? WHERE id = ?";
    const UPDATE_USER_DETAILED_DATA_AVATAR    = "UPDATE user_detailed_data SET avatar_path = ? WHERE user_id = ?";
    const SELECT_USER_DETAILED_DATA_AVATAR    = "SELECT avatar_path FROM user_detailed_data WHERE user_id = ?";
    const REPLACE_USER_DETAILED_DATA          = "REPLACE INTO user_detailed_data VALUES (?, ?, ?, ?, ?, ?)";
    const SELECT_USER_DETAILED_DATA           = "SELECT bio, birthday, country, city, avatar_path FROM user_detailed_data WHERE user_id = ?";
    // Privacy System
    const SELECT_USER_PRIVACY                 = "SELECT view_email, view_profile, view_livestream FROM user_privacy WHERE user_id = ?";
    const UPDATE_USER_PRIVACY                 = "UPDATE user_privacy SET view_email = ?, view_profile = ?, view_livestream = ? WHERE user_id = ?";
    // User board
    const INSERT_USER_BOARD                   = "INSERT INTO user_board (user_id, message_number, message, date) VALUES (?, ?, ?, ?)";
    const SELECT_USER_BOARD                   = "SELECT message_id, message_number, message, date FROM user_board WHERE user_id = ? AND message_number BETWEEN ? AND ? ORDER BY message_id DESC";
    const SELECT_USER_BOARD_COUNT             = "SELECT message_number FROM user_board WHERE user_id = ? ORDER BY message_number DESC LIMIT 1";
    const DELETE_USER_BOARD                   = "DELETE FROM user_board WHERE message_id = ? AND user_id = ?";
    const SELECT_USER_BOARD_MESSAGE_NUMBER    = "SELECT message_number FROM user_board WHERE message_id = ?";
    const UPDATE_USER_BOARD_MESSAGE_NUMBERS   = "UPDATE user_board SET message_number = message_number - 1 WHERE user_id = ? AND message_number > ?";
    const INSERT_USER_BOARD_REPLY             = "INSERT INTO user_board_replies (sender_id, message_id, message, date) VALUES (?, ?, ?, ?)";
    // TODO: Remove the "DISTINCT" from this query. Research why the fuck it's needed here.
    const SELECT_USER_BOARD_REPLIES           = "SELECT DISTINCT a.reply_id, a.sender_id, a.message, a.date, c.username, d.avatar_path FROM user_board_replies AS a, user_board AS b, user_data AS c, user_detailed_data AS d WHERE b.user_id = ? AND a.message_id = ? AND a.sender_id = c.id AND a.sender_id = d.user_id ORDER BY a.reply_id DESC";
    const DELETE_USER_BOARD_REPLY             = "DELETE FROM user_board_replies WHERE reply_id = ?";
    // Friends system
    const DELETE_USER_FRIEND_REQUEST          = "DELETE FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const INSERT_USER_FRIEND                  = "INSERT INTO user_friends VALUES (?, ?)";
    const DELETE_USER_FRIEND                  = "DELETE FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIENDS                 = "SELECT a.id, a.username, a.is_online, c.avatar_path FROM user_data AS a, user_friends AS b, user_detailed_data AS c WHERE b.user_id = ? AND b.friend_id = a.id AND b.friend_id = c.user_id ORDER BY a.username";
    const SELECT_USER_FRIENDS_BY_ID           = "SELECT friend_id FROM user_friends WHERE user_id = ?";
    const SELECT_USER_FRIENDS_BY_USERNAME     = "SELECT a.username, a.is_online FROM user_data AS a, user_friends AS b WHERE b.friend_id = a.id AND b.user_id = ? ORDER BY a.username";
    const SELECT_USER_FRIENDS_IS_FRIEND       = "SELECT user_id FROM user_friends AS a, user_data AS b WHERE b.username = ? AND a.user_id = ? AND b.id = a.friend_id";
    const SELECT_USER_FRIENDS_IS_FRIEND_ID    = "SELECT user_id FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIEND_REQUEST_ID       = "SELECT user_id FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const SELECT_USER_FRIEND_REQUESTS_COUNT   = "SELECT count(*) AS total FROM user_friend_requests WHERE user_id = ?";
    const INSERT_USER_FRIEND_REQUEST          = "INSERT INTO user_friend_requests (user_id, requester_id, message) VALUES (?, ?, ?)";
    const SELECT_USER_FRIEND_REQUEST          = "SELECT b.requester_id, a.username, b.message, c.avatar_path FROM user_data AS a, user_friend_requests AS b, user_detailed_data AS c WHERE b.user_id = ? AND a.id = b.requester_id AND c.user_id = b.requester_id";
    const SELECT_USER_DATA_SEARCH             = "SELECT username FROM user_data WHERE username LIKE ? ORDER BY username LIMIT 10";
    const INSERT_USER_PRIVATE_MESSAGE         = "INSERT INTO user_private_messages (sender_id, receiver_id, message, date, readed) VALUES (?, ?, ?, ?, ?)";
    const SELECT_USER_PRIVATE_MESSAGE         = "SELECT message, date, readed FROM user_private_messages WHERE sender_id = ? AND receiver_id = ? ORDER BY date DESC";
    const SELECT_USER_PRIVATE_MESSAGES        = "SELECT a.sender_id, a.message, a.date, a.readed, b.username FROM user_private_messages AS a, user_data AS b WHERE a.receiver_id = ? AND a.sender_id = b.id ORDER BY a.date DESC";
    const SELECT_USER_PRIVATE_MESSAGES_COUNT  = "SELECT count(*) FROM user_private_messages WHERE receiver_id = ? AND readed = 0";
    const SELECT_USER_PRIVATE_CONVERSATION    = "SELECT sender_id, message, date, readed FROM user_private_messages WHERE sender_id IN (?, ?) AND receiver_id IN (?, ?) ORDER BY date DESC";
    const UPDATE_USER_PRIVATE_MESSAGES_READED = "UPDATE user_private_messages SET readed = ? WHERE receiver_id = ? AND sender_id = ?";
    // Login system
    const SELECT_USER_DATA_LOGIN              = "SELECT username, password_sha1 FROM user_data WHERE username = ?";
    const UPDATE_USER_DATA_RND_IDENTIFIER     = "UPDATE user_data SET random_session_id = ? WHERE id = ?";
    // Registration management
    const SELECT_USER_DATA_REGISTER           = "SELECT username, email FROM user_data WHERE username = ? OR email = ?";
    const INSERT_USER_DATA                    = "INSERT INTO user_data (username, password_sha1, random_session_id, email, ip_v4, ip_v6, is_online, last_login) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    const DELETE_USER_DATA                    = "DELETE FROM user_data WHERE user_id = ?";
    const INSERT_USER_DETAILED_DATA           = "INSERT INTO user_detailed_data (user_id, bio, birthday, country, city) VALUES (?, ?, ?, ?, ?)";
    //const DELETE_USER_DETAILED_DATA           = "DELETE FROM user_detailed_data WHERE user_id = ?";
    const INSERT_USER_PRIVACY                 = "INSERT INTO user_privacy (user_id, view_email, view_profile, view_livestream) VALUES (?, ?, ?, ?)";
    //const DELETE_USER_PRIVACY                 = "DELETE FROM user_privacy WHERE user_id = ?";
    // Customization options
    const INSERT_USER_CUSTOM_OPTIONS          = "INSERT INTO user_custom_options (user_id, option_livestream, option_livestream_livecomments, option_latest_news) VALUES (?, ?, ?, ?)";
    const UPDATE_USER_CUSTOM_OPTIONS          = "UPDATE user_custom_options SET option_livestream = ?, option_livestream_livecomments = ?, option_latest_news = ? WHERE user_id = ?";
    const SELECT_USER_CUSTOM_OPTIONS          = "SELECT option_livestream, option_livestream_livecomments, option_latest_news FROM user_custom_options WHERE user_id = ?";
}
?>