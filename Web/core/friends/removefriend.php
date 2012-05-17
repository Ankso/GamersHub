<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
if (!isset($_SESSION['userId']))
    die("FAILED");
if (!isset($_POST['friendName']))
    die("FAILED");

// Create the user object
$user = new User($_SESSION['userId']);
if ($user->RemoveFriend(GetIdFromUsername($_POST['friendName'])))
    echo 'SUCCESS';
else
    echo 'FAILED';
?>