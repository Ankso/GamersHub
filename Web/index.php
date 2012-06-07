<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../Classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

$sessionsHandler = new CustomSessionsHandler();
session_set_save_handler(
    array($sessionsHandler, "open"),
    array($sessionsHandler, "close"),
    array($sessionsHandler, "read"),
    array($sessionsHandler, "write"),
    array($sessionsHandler, "destroy"),
    array($sessionsHandler, "gc")
    );
register_shutdown_function("session_write_close");
session_start();
// If the user is logged in, redirect to his or her folder
if (isset($_SESSION['userId']))
    header("location:". GetUsernameFromId($_SESSION['userId']));
else
    header("location:login.php");
?>