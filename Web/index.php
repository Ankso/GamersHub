<?php
require_once("../Classes/User.Class.php");
session_start();
// If the user is logged in, redirect to his or her folder
if (isset($_SESSION['user']))
    header("location:". $_SESSION['user']->GetUsername());
else
    header("location:login.php");
?>