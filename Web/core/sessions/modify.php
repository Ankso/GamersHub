<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");

session_start();

if (!isset($_SESSION['userId']))
    die("FAILED");

if (!isset($_POST['email']) || !isset($_POST['newPassword']) || !isset($_POST['newPasswordCheck']))
    die("FAILED");

// Create the User object
$user = new User($_SESSION['userId']);
if ($_POST['email'] != $user->GetEmail())
    if (!$user->SetEmail($_POST['email']))
        die("FAILED");
// Change the password only if there is something written in the password field
if ($_POST['newPassword'] != "")
    if ($_POST['newPassword'] === $_POST['newPasswordCheck'])
        if (!$user->SetPasswordSha1(CreateSha1Pass($user->GetUsername(), $_POST['newPassword'])))
            die("FAILED");
echo "SUCCESS";
?>