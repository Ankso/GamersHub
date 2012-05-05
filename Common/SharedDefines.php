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
?>