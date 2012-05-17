<?php
require_once("../Classes/User.Class.php");
session_start();
if (isset($_SESSION['userId']))
{
    $user = new User($_SESSION['userId']);
    $user->SetOnline(false);
}
session_destroy();
header("location:index.php");
?>