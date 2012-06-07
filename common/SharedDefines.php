<?php
/**
 * Database defines
 */
// DB server connection info
$SERVER_INFO = array(
    'HOST'     => "localhost",  // MySQL Server host address
    'USERNAME' => "root",       // MySQL Server user
    'PASSWORD' => "password",   // MySQL Server password
);
// Databases's names
$DATABASES = array(
    'USERS'    => "users",
    'GAMES'    => "games",
    'SESSIONS' => "sessions",
);

/**
 * General
 */
define("USER_DOESNT_EXISTS", -1);
define("GAME_DOESNT_EXISTS", -1);

/**
 * Friends System
 */
define("USER_HAS_NO_FRIENDS", -2);
define("USER_HAS_NO_FRIEND_REQUESTS", -3);
define("USERS_ARENT_FRIENDS", -4);
define("USERS_ARE_FRIENDS", -5);
define("FRIEND_REQUEST_ALREADY_SENT", -6);
define("USER_HAS_NO_MESSAGES", -7);
define("MESSAGES_HISTORY_MAX", 7);

/**
 * Profile system
 */
define("USER_DETAILS_BIO", 0);
define("USER_DETAILS_BIRTHDAY", 1);
define("USER_DETAILS_COUNTRY", 2);
define("USER_DETAILS_CITY", 3);

/**
 * Privacy system
 */
// Privacy options
define("USER_PRIVACY_EMAIL", 0);
define("USER_PRIVACY_PROFILE", 1);
define("USER_PRIVACY_LIVESTREAM", 2);
// Privacy levels
define("PRIVACY_LEVEL_NOBODY", 0);
define("PRIVACY_LEVEL_FRIENDS", 1);
define("PRIVACY_LEVEL_CLAN_MEMBERS", 2);
define("PRIVACY_LEVEL_EVERYONE", 3);

/**
 * User space customization option
 */
define("CUSTOM_OPTION_LIVESTREAM", 0);
define("CUSTOM_OPTION_LIVESTREAM_COMMENTS", 1);
define("CUSTOM_OPTION_LATEST_NEWS", 2);

/**
 * User's board defines
 */
define("USER_HAS_NO_BOARD_MESSAGES", 0);
define("USER_COMMENT_HAS_NO_REPLIES", -1);

?>