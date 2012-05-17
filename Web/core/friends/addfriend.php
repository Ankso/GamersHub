<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
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