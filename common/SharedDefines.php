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
define("YOU_ALREADY_HAVE_FRIEND_REQUEST", -7);
define("USER_HAS_NO_MESSAGES", -8);
define("MESSAGES_HISTORY_MAX", 7);

/**
 * Profile system
 */
define("USER_DETAILS_BIO", 0);
define("USER_DETAILS_BIRTHDAY", 1);
define("USER_DETAILS_COUNTRY", 2);
define("USER_DETAILS_CITY", 3);
/**
 * Latest news system
 */
define("USER_HAS_NO_LATEST_NEWS", -1);
// News types
define("NEW_TYPE_NEW_MESSAGE", 1);
define("NEW_TYPE_NEW_FRIEND", 2);
define("NEW_TYPE_NEW_GAME", 3);
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
/**
 * Used in games system
 */
define("GAME_DEVELOPER_ID", 0);
define("GAME_DEVELOPER_NAME", 1);
define("GAME_DEVELOPER_DESCRIPTION", 2);
define("GAME_PUBLISHER_ID", 0);
define("GAME_PUBLISHER_NAME", 1);
define("GAME_PUBLISHER_DESCRIPTION", 2);
define("USER_HAS_NO_GAMES", -1);
define("USER_HAS_NO_RECOMMENDED_GAMES", -2);
/**
 * String used to encrypt the unique user's random session identifier.
 * TODO: It must be put in a external-well-protected file when this becomes live.
 */
define("MAGIC_STRING", "Vou mErcar Un focKing Rolls roYce")
?>