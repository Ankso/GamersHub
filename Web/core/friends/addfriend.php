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

if (!isset($_POST['username']) || !isset($_POST['action']) || !isset($_SESSION['userId']))
    die("FAILED");
    
// Create the user object
$user = new User($_SESSION['userId']);
if ($_POST['action'] === 'a')
{
    if ($user->AcceptFriend(GetIdFromUsername($_POST['username'])))
        die("SUCCESS");
}
elseif ($_POST['action'] === 'd')
{
    if ($user->DeclineFriendRequest(GetIdFromUsername($_POST['username'])))
        die("SUCCESS");
}
echo "FAILED";
?>