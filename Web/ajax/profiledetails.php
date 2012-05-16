<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");

session_start();
if (!isset($_SESSION['user']))
{
    header("location:http://localhost/login.php");
    exit();
}
if (!isset($_POST['username']))
    die('<div id="profileDetails">Bad try</div>');

if ($_SESSION['user']->GetUsername() == $_POST['username'])
    $details = $_SESSION['user']->GetDetailedUserData();
elseif ($_SESSION['user']->IsFriendOf($_POST['username']))
{
    $friend = new User(GetIdFromUsername($_POST['username']));
    $details = $friend->GetDetailedUserData();
}
else
    die('<div id="profileDetails">Bad try</div>');
?>
<div id="profileDetails" style="min-height:250px; min-width:200px; border:2px #333333 solid; margin:0 auto;">
	<span>Bio: <?php echo $details[USER_DETAILS_BIO]; ?></span><br />
	<span>Birthday: <?php echo $details[USER_DETAILS_BIRTHDAY]; ?></span><br />
	<span>Country: <?php echo $details[USER_DETAILS_COUNTRY]; ?></span><br />
	<span>City: <?php echo $details[USER_DETAILS_CITY]; ?></span>
</div>