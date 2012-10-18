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

if (!isset($_POST['email']) || !isset($_POST['newPassword']) || !isset($_POST['newPasswordCheck']))
    die("FAILED");

// Create the User object
$user = new User($_SESSION['userId']);
if ($_POST['email'] != $user->GetEmail())
    if (!$user->SetEmail($_POST['email']))
        die("FAILED");
// Change the password only if there is something written in the password field
if ($_POST['newPassword'] != "")
    if ($_POST['newPassword'] === $_POST['newPasswordCheck'])
        if (!$user->SetPasswordSha1(CreateSha1Pass($user->GetUsername(), $_POST['newPassword'])))
            die("FAILED");
echo "SUCCESS";
?>