<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
session_start();
if (!isset($_POST['username']) || !isset($_POST['action']) || !isset($_SESSION['user']))
    die("FAILED");

if ($_POST['action'] === 'a')
{
    if ($_SESSION['user']->AcceptFriend(GetIdFromUsername($_POST['username'])))
        die("SUCCESS");
}
elseif ($_POST['action'] === 'd')
{
    if ($_SESSION['user']->DeclineFriendRequest(GetIdFromUsername($_POST['username'])))
        die("SUCCESS");
}
echo "FAILED";
?>