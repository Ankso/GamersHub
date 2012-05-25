<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

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