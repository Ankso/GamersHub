<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
if (!isset($_SESSION['userId']))
    die("FAILED");
if (!isset($_POST['liveStream']) || !isset($_POST['liveStreamComments']) || !isset($_POST['latestNews']))
    die("FAILED");
// Create the User object
$user = new User($_SESSION['userId']);
$allowedValues = array(
    0 => "0",
    1 => "1",
);
// Check that the received values are between the allowed limits
if (!in_array($_POST['liveStream'], $allowedValues) || !in_array($_POST['liveStreamComments'], $allowedValues) || !in_array($_POST['latestNews'], $allowedValues))
    die("FAILED");
// Here we are sure that the customization options are between valid values.
// Anyway, User::SetCustomOptions() will return false if one param has an unallowed value.
if (!$user->SetCustomOptions($_POST['liveStream'], $_POST['liveStreamComments'], $_POST['latestNews']))
    die("FAILED");
echo "SUCCESS";
?>