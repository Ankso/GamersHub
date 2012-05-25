<?php
require_once("../Classes/User.Class.php");
session_start();
// TODO: This must be moved to a file in the core/sessions folder, may be named destroy.php
if (isset($_SESSION['userId']))
{
    $user = new User($_SESSION['userId']);
    $user->SetLastLogin(date("Y-m-d H:i:s", time()));
    $user->SetLastIp($_SERVER['REMOTE_ADDR']);
    $user->SetOnline(false);
}
session_destroy();
header("location:index.php");
?>