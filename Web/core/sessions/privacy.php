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
if (!isset($_POST['email']) || !isset($_POST['profileDetails']) || !isset($_POST['liveStream']))
    die("FAILED");
// Create the User object
$user = new User($_SESSION['userId']);
$allowedValues = array(
    0 => "1",
    1 => "2",
    2 => "3",
);
// Check that the received values are between the allowed limits
if (!in_array($_POST['profileDetails'], $allowedValues) || !in_array($_POST['liveStream'], $allowedValues))
    die("FAILED");
// Note that the only option that can have the value 0 (PRIVACY_LEVEL_NOBODY) is the email. So, we must add this possibility to the array.
$allowedValues[] = "0";
if (!in_array($_POST['email'], $allowedValues))
    die("FAILED");
// Here we are sure that the privacy levels are between valid values.
// Anyway, User::SetPrivacySettings() will return false if one param has an unallowed value.
if (!$user->SetPrivacySettings($_POST['email'], $_POST['profileDetails'], $_POST['liveStream']))
    die("FAILED");
echo "SUCCESS";
?>