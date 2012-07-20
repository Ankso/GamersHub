<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
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

if (!isset($_POST['requesterId']) || !isset($_POST['action']) || !isset($_SESSION['userId']))
    die("FAILED");
    
// Create the user object
// TODO: Aditional checks are required.
$user = new User($_SESSION['userId']);
if ($_POST['action'] === 'ACCEPT')
{
    if ($user->AcceptFriend((int)$_POST['requesterId']))
        die("SUCCESS");
}
elseif ($_POST['action'] === 'DECLINE')
{
    if ($user->DeclineFriendRequest((int)$_POST['requesterId']))
        die("SUCCESS");
}
echo "FAILED";
?>