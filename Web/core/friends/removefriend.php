<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
if (!isset($_SESSION['user']))
    die("FAILED");
if (!isset($_POST['friendName']))
    die("FAILED");
    
if ($_SESSION['user']->RemoveFriend(GetIdFromUsername($_POST['friendName'])))
    echo 'SUCCESS';
else
    echo 'FAILED';
?>