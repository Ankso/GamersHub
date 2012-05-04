<?php
/**
 * General
 */
define("USER_DOESNT_EXISTS", -1);
/**
 * Friends System
 */
define("USER_HAS_NO_FRIENDS", -2);
define("USER_HAS_NO_FRIEND_REQUESTS", -3);
define("USERS_ARENT_FRIENDS", -4);
define("USERS_ARE_FRIENDS", -5);
define("FRIEND_REQUEST_ALREADY_SENT", -6);

/**
 * Database defines
 */
// DB server connection info
$SERVER_INFO = array(
    'HOST'     => "localhost",  // MySQL Server address
    'USERNAME' => "root",       // MySQL Server user
    'PASSWORD' => "password",   // MySQL Server password
);

// Databases's names
$DATABASES = array(
    'USERS' => "users",
);
// This will be in an independent file:
class Statements
{
    // Basic load/save user data queries
    const SELECT_USER_DATA_BY_ID          = "SELECT id, username, password_sha1, email, ip_v4, ip_v6 FROM user_data WHERE id = ?";
    const SELECT_USER_DATA_BY_USERNAME    = "SELECT id, username, password_sha1, email, ip_v4, ip_v6 FROM user_data WHERE username = ?";
    const REPLACE_USER_DATA               = "REPLACE  INTO user_data VALUES (?, ?, ?, ?, ?, ?, ?)";
    // For user's data updates functions
    const UPDATE_USER_DATA_ID             = "UPDATE user_data SET id = ? WHERE id = ?";
    const UPDATE_USER_DATA_USERNAME       = "UPDATE user_data SET username = ? WHERE id = ?";
    const UPDATE_USER_DATA_PASSWORD       = "UPDATE user_data SET password_sha1 = ? WHERE id = ?";
    const UPDATE_USER_DATA_EMAIL          = "UPDATE user_data SET email = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV4           = "UPDATE user_data SET ip_v4 = ? WHERE id = ?";
    const UPDATE_USER_DATA_IPV6           = "UPDATE user_data SET ip_v6 = ? WHERE id = ?";
    const UPDATE_USER_DATA_ONLINE         = "UPDATE user_data SET is_online = ? WHERE id = ?";
    // Friends system
    const DELETE_USER_FRIEND_REQUEST      = "DELETE FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const INSERT_USER_FRIEND              = "INSERT INTO user_friends VALUES (?, ?)";
    const DELETE_USER_FRIEND              = "DELETE FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIENDS_BY_ID       = "SELECT friend_id FROM user_friends WHERE user_id = ?";
    const SELECT_USER_FRIENDS_BY_USERNAME = "SELECT a.username FROM user_data AS a, user_friends AS b WHERE b.friend_id = a.id AND b.user_id = ?";
    const SELECT_USER_FRIENDS_IS_FRIEND   = "SELECT user_id FROM user_friends WHERE user_id = ? AND friend_id = ?";
    const SELECT_USER_FRIEND_REQUEST_ID   = "SELECT user_id FROM user_friend_requests WHERE user_id = ? AND requester_id = ?";
    const INSERT_USER_FRIEND_REQUEST      = "INSERT INTO user_friend_requests (user_id, requester_id, message) VALUES (?, ?, ?)";
    const SELECT_USER_FRIEND_REQUEST      = "SELECT b.user_id, a.username, b.message FROM user_data AS a, user_friend_requests AS b WHERE b.user_id = ? AND a.id = b.requester_id";
    const SELECT_USER_DATA_SEARCH         = "SELECT username FROM user_data WHERE username LIKE ? ORDER BY username LIMIT 10";
    // Login system
    const SELECT_USER_DATA_LOGIN          = "SELECT username, password_sha1 FROM user_data WHERE username = ?";
    // Registration management
    const SELECT_USER_DATA_REGISTER       = "SELECT username, email FROM user_data WHERE username = ? OR email = ?";
    const INSERT_USER_DATA                = "INSERT INTO user_data (username, password_sha1, email, ip_v4, ip_v6, is_online) VALUES (?, ?, ?, ?, ?, ?)";
}
?>