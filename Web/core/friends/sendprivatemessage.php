<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
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
if (!isset($_SESSION['userId']))
    die("FAILED");
if (!isset($_POST['receiverId']) || !isset($_POST['message']) || $_POST['message'] === "")
    die("FAILED");
// Create the User object
$user = new User($_SESSION['userId']);
$friendId = (int)$_POST['receiverId'];
if (!$user->IsFriendOf($friendId))
    die("FAILED");
if ($user->SendPrivateMessage($friendId, $_POST['message']))
    echo "SUCCESS";
else
    die("FAILED");
?>