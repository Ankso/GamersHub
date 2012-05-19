<?php
require_once("../Classes/User.Class.php");
require_once("../common/Common.php");
session_start();
// If the user is logged in, redirect to his or her folder
if (isset($_SESSION['userId']))
    header("location:". GetUsernameFromId($_SESSION['userId']));
else
    header("location:login.php");
?>