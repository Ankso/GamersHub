<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");

session_start();
if (!isset($_SESSION['userId']))
{
    header("location:http://localhost/login.php");
    exit();
}
if (!isset($_POST['username']))
    die('<div id="profileDetails">Bad try</div>');

// Create the user object
$user = new User($_SESSION['userId']);
if ($user->GetUsername() == $_POST['username'])
    $details = $user->GetDetailedUserData();
elseif ($user->IsFriendOf($_POST['username']))
{
    $friend = new User($_POST['username']);
    $details = $friend->GetDetailedUserData();
}
else
    die('<div id="profileDetails">Bad try</div>');
?>
<div id="profileDetails" style="min-height:250px; width:200px; margin:0 auto; margin-top:10px; text-align:left;">
	<span>Bio: <?php echo $details[USER_DETAILS_BIO]; ?></span><br />
	<span>Birthday: <?php echo $details[USER_DETAILS_BIRTHDAY]; ?></span><br />
	<span>Country: <?php echo $details[USER_DETAILS_COUNTRY]; ?></span><br />
	<span>City: <?php echo $details[USER_DETAILS_CITY]; ?></span>
</div>