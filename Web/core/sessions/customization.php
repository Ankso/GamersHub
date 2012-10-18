<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
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
if (!isset($_POST['liveStream']) || !isset($_POST['liveStreamComments']) || !isset($_POST['latestNews']))
    die("FAILED");
// Create the User object
$user = new User($_SESSION['userId']);
$allowedValues = array(
    0 => "0",
    1 => "1",
);
// Check that the received values are between the allowed limits
if (!in_array($_POST['liveStream'], $allowedValues) || !in_array($_POST['liveStreamComments'], $allowedValues) || !in_array($_POST['latestNews'], $allowedValues))
    die("FAILED");
// Here we are sure that the customization options are between valid values.
// Anyway, User::SetCustomOptions() will return false if one param has an unallowed value.
if (!$user->SetCustomOptions($_POST['liveStream'], $_POST['liveStreamComments'], $_POST['latestNews']))
    die("FAILED");
echo "SUCCESS";
?>