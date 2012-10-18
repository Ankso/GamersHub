<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

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
if (!isset($_SESSION['userId']))
    die("FAILED");
if (!isset($_POST['bio']) || !isset($_POST['birthday']) || !isset($_POST['country']) || !isset($_POST['city']))
    die("FAILED");
// Create the User object
$user = new User($_SESSION['userId']);
if ($_POST['bio'] === "")
    $_POST['bio'] = NULL;
else
    $_POST['bio'] = strip_tags($_POST['bio'], "<font><br>");
if ($_POST['birthday'] === "")
    $_POST['birthday'] = NULL;
else
    $_POST['birthday'] = strip_tags($_POST['birthday']);
if ($_POST['country'] === "")
    $_POST['country'] = NULL;
else
    $_POST['country'] = strip_tags($_POST['country']);
if ($_POST['city'] === "")
    $_POST['city'] = NULL;
else
    $_POST['city'] = strip_tags($_POST['city']);
if ($user->SetDetailedUserData($_POST['bio'], $_POST['birthday'], $_POST['country'], $_POST['city']))
    echo "SUCCESS";
else
    die("FAILED");
?>