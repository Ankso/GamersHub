<?php
// This class acts as an enum
class Statements
{
    // Basic load/save user data queries
    const SELECT_USER_DATA_BY_ID            = "SELECT id, username, password_sha1, email, ip_v4, ip_v6, last_login FROM user_data WHERE id = ?";
    const SELECT_USER_DATA_BY_USERNAME      = "SELECT id, username, password_sha1, email, ip_v4, ip_v6, last_login FROM user_data WHERE username = ?";
    const REPLACE_USER_DATA                 = "REPLACE INTO user_data VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    const SELECT_USER_DATA_ID               = "SELECT id FROM user_data WHERE username = ?";
    const SELECT_USER_DATA_USERNAME         = "SELECT username FROM user_data WHERE id = ?";
    // For user's data updates and profile changers functions
    const UPDATE_USER_DATA_ID               = "UPDATE user_data SET id = ? WHERE id = ?";
    const UPDATE_USER_DATA_USERNAME         = "UPDATE user_data SET username = ? WHERE id = ?";
    const UPDATE_USER_DATA_PASSWORD         = "UPDATE user_data SET password_sha1 = ? WHERE id = ?";
    const UPDATE_USER_DATA_EMAIL            = "UPDATE user_data SET email = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV4             = "UPDATE user_data SET ip_v4 = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV6             = "UPDATE user_data SET ip_v6 = ? WHERE id = ?";
    const UPDATE_USER_DATA_ONLINE           = "UPDATE user_data SET is_online = ? WHERE id = ?";
    const UPDATE_USER_DATA_LAST_LOGIN       = "UPDATE user_data SET last_login = ? WHERE id = ?";
    const INSERT_USER_AVATARS_PATH          = "INSERT INTO user_avatars VALUES (?, ?)";
    const UPDATE_USER_AVATARS_PATH          = "UPDATE user_avatars SET avatar_path = ? WHERE user_id = ?";
    const SELECT_USER_AVATARS_PATH          = "SELECT avatar_path FROM user_avatars WHERE user_id = ?";
    const REPLACE_USER_DETAILED_DATA        = "REPLACE INTO user_detailed_data VALUES (?, ?, ?, ?, ?)";
    const SELECT_USER_DETAILED_DATA         = "SELECT bio, birthday, country, city FROM user_detailed_data WHERE user_id = ?";
    // Friends system
    const DELETE_USER_FRIEND_REQUEST        = "DELETE FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const INSERT_USER_FRIEND                = "INSERT INTO user_friends VALUES (?, ?)";
    const DELETE_USER_FRIEND                = "DELETE FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIENDS_BY_ID         = "SELECT friend_id FROM user_friends WHERE user_id = ?";
    const SELECT_USER_FRIENDS_BY_USERNAME   = "SELECT a.username, a.is_online FROM user_data AS a, user_friends AS b WHERE b.friend_id = a.id AND b.user_id = ? ORDER BY a.username";
    const SELECT_USER_FRIENDS_IS_FRIEND     = "SELECT user_id FROM user_friends AS a, user_data AS b WHERE b.username = ? AND a.user_id = ? AND b.id = a.friend_id";
    const SELECT_USER_FRIENDS_IS_FRIEND_ID  = "SELECT user_id FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIEND_REQUEST_ID     = "SELECT user_id FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const SELECT_USER_FRIEND_REQUESTS_COUNT = "SELECT count(*) AS total FROM user_friend_requests WHERE user_id = ?";
    const INSERT_USER_FRIEND_REQUEST        = "INSERT INTO user_friend_requests (user_id, requester_id, message) VALUES (?, ?, ?)";
    const SELECT_USER_FRIEND_REQUEST        = "SELECT b.user_id, a.username, b.message FROM user_data AS a, user_friend_requests AS b WHERE b.user_id = ? AND a.id = b.requester_id";
    const SELECT_USER_DATA_SEARCH           = "SELECT username FROM user_data WHERE username LIKE ? ORDER BY username LIMIT 10";
    // Login system
    const SELECT_USER_DATA_LOGIN            = "SELECT username, password_sha1 FROM user_data WHERE username = ?";
    // Registration management
    const SELECT_USER_DATA_REGISTER         = "SELECT username, email FROM user_data WHERE username = ? OR email = ?";
    const INSERT_USER_DATA                  = "INSERT INTO user_data (username, password_sha1, email, ip_v4, ip_v6, is_online, last_login) VALUES (?, ?, ?, ?, ?, ?, ?)";
    const DELETE_USER_DATA                  = "DELETE FROM user_data WHERE user_id = ?";
    const INSERT_USER_DETAILED_DATA         = "INSERT INTO user_detailed_data (user_id, bio, birthday, country, city) VALUES (?, ?, ?, ?, ?)";
    //const DELETE_USER_DETAILED_DATA         = "DELETE FROM user_detailed_data WHERE user_id = ?";
    const INSERT_USER_PRIVACY               = "INSERT INTO user_privacy (user_id, view_email, view_profile, view_livestream) VALUES (?, ?, ?, ?)";
    //const DELETE_USER_PRIVACY               = "DELETE FROM user_privacy WHERE user_id = ?";
}
?>