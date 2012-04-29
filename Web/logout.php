<?php
require_once("../Classes/User.Class.php");
session_start();
if (isset($_SESSION['user']))
    $_SESSION['user']->SetOnline(false);
session_destroy();
header("location:index.php");
?>