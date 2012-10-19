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

if (!isset($_POST['requesterId']) || !isset($_POST['action']) || !isset($_SESSION['userId']))
    die(json_encode(array("status" => "FAILED")));
    
// Create the user object
// TODO: Aditional checks are required.
$user = new User($_SESSION['userId']);
if ($_POST['action'] === 'ACCEPT')
{
    if ($user->AcceptFriend((int)$_POST['requesterId']))
        if (IsUserOnline((int)$_POST['requesterId']))
            exit(json_encode(array("status" => "SUCCESS", "isOnline" => "1")));
        else
            exit(json_encode(array("status" => "SUCCESS", "isOnline" => "0")));
}
elseif ($_POST['action'] === 'DECLINE')
{
    if ($user->DeclineFriendRequest((int)$_POST['requesterId']))
        exit(json_encode(array("status" => "SUCCESS")));
}
die(json_encode("status" => "FAILED"));
?>