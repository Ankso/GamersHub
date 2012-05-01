<?php
/**
 * General
 */
define("USER_DOESNT_EXISTS", -1);
/**
 * Friends System
 */
define("USER_HAS_NO_FRIENDS", -2);
define("USER_IS_ALREADY_FRIEND", -3);
define("REQUEST_ALREADY_SENT", -4);
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