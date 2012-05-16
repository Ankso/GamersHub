<?php 
require_once("../common/SharedDefines.php");
require_once("../common/Common.php");
require_once("../classes/Database.Class.php");
require_once("../classes/User.Class.php");

session_start();
// Check if the user is logged in
if (!isset($_SESSION['user']))
{
    header("location:login.php");
    exit();
}
// Check if the user is accessing a specified profile
if (!isset($_GET['username']))
{
    header("location:index.php");
    exit();
}
// Check if the username is a valid one
if (GetIdFromUsername($_GET['username']) === false)
{
    header("location:index.php");
    exit();
}
// Determine if the user is the owner of the space
if ($_SESSION['user']->GetUsername() === $_GET['username'])
{
    $spaceOwner = $_SESSION['user'];
    $isOwner = true;
}
else
{
    $spaceOwner = new User($_GET['username']);
    $isOwner = false;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $_GET['username'] ?>'s profile - GamersNet</title>
<link href="css/mbExtruder.css" media="all" rel="stylesheet" type="text/css">
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css">
<link href="css/main.css" media="all" rel="stylesheet" type="text/css">
<link href="css/fancyboxjQuery.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="js/inc/jquery.latest.js"></script>
<script type="text/javascript" src="js/inc/jquery.hoverIntent.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.metadata.js"></script>
<script type="text/javascript" src="js/inc/jquery.mb.flipText.js"></script>
<script type="text/javascript" src="js/inc/mbExtruder.js"></script>
<script type="text/javascript" src="js/inc/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="js/inc/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript">
	// Tabs scripts \\
	$(function(){
		$("#friendsTab").buildMbExtruder({
    		positionFixed: true,
    		sensibility:700,
    		autoOpenTime: 10,
          	position:"right",
            width:230,
            flapDim:"200",
            extruderOpacity:1,
            autoCloseTime:500,
            slideTimer:200,
            closeOnExternalClick:false,
            onExtClose:function(){},
            onExtOpen:function(){},
            onExtContentLoad: function(){}
    	});
	});
	$(function(){
		$("#clansTab").buildMbExtruder({
    		positionFixed:true,
    		sensibility:700,
          	position:"right",
            width:230,
            flapDim:"200",
            extruderOpacity:1,
            autoOpenTime:10,
            autoCloseTime:500,
            slideTimer:200,
            closeOnExternalClick:false,
            onExtClose:function(){},
            onExtOpen:function(){},
            onExtContentLoad: function(){}
    	});
	});
	$(function(){
		$("#myGamesTab").buildMbExtruder({
    		positionFixed: true,
    		sensibility:700,
    		autoOpenTime: 10,
          	position:"left",
            width:230,
            flapDim:"200",
            extruderOpacity:1,
            autoCloseTime:500,
            slideTimer:200,
            textOrientation:"tb",
            closeOnExternalClick:false,
            onExtClose:function(){},
            onExtOpen:function(){},
            onExtContentLoad: function(){}
    	});
	});

function FadeOut(event, redirectUrl)
{
	event.preventDefault();
	$('body').fadeOut(1000, function() { window.location = redirectUrl; });
}

function FadeIn()
{
    $("body").css("display", "none");
    $("body").fadeIn(2000);
}

function ShowProfileDetails(userName)
{
    $('div.editProfileButton').remove();
    $.post("ajax/profiledetails.php", {username: userName}, function(data) {
		if (data.length > 0)
		{
			$('div.profileInfo').append(data);
			$('div.profileInfo').append('<div class="editProfileButton" onclick="HideProfileDetails();">Hide</div>');
		}
		else
			$('div.profileInfo').append('<div style="text-align:center; color:#FF0000;">Error while connecting to the server</div>');
	});
}

function HideProfileDetails()
{
    $('div.editProfileButton').remove();
    $('#profileDetails').remove();
    $('div.profileInfo').append('<div class="editProfileButton" onclick="ShowProfileDetails(\'<?php echo $spaceOwner->GetUsername(); ?>\');">Edit profile</div>');
}

$(document).ready(function() {
	FadeIn();
	$("a#friendRequests").fancybox();
	$("a.removeFriend").fancybox();
	<?php if ($isOwner) echo '$("a#changeAvatar").fancybox();'; ?>
});
</script>
</head>
<body>
<?php PrintTopBar($_SESSION['user']); ?>
<div class="mainContent">
	<div class="mainBoard">
		<div class="mainLivestream">
			<div class="videoWindow">
				<br/><br/><br/><br/><br/><br/><br/>-- Here is your live streaming video (640x360) --
			</div>
			<div class="videoComments">
				<br/><br/>-- Live comments about the livestream here --<br/><br/><br/>
			</div>
		</div>
		<div class="commentsBoard">
			<br/></br>-- Live comments written by you here, independent from the streaming --<br/><br/><br/></br><br/></br><br/></br><br/></br><br/>
		</div>
		<div class="clansBoard">
			<br/></br>-- Live comments written by your clan(s) here, independent from your comments --<br/><br/><br/>
		</div>
	</div>
	<div class="profileBoard">
		<div class="profileInfo">
			<div class="imgAvatar">
				<div style="background:transparent url('<?php echo $spaceOwner->GetAvatarHostPath(); ?>') no-repeat center center; background-size:100%; height:200px; width:200px; border-radius:0.5em;">
					<?php if ($isOwner) echo '<div class="editAvatar"><a id="changeAvatar" href="ajax/changeavatar.php"><img src="images/edit.png" alt="Edit" style="height:22px;width:22px;margin-top:3px;"/></a></div>'; ?>
				</div>
			</div>
			<div class="editProfileButton" onclick="ShowProfileDetails('<?php echo $spaceOwner->GetUsername(); ?>');"><?php echo ($isOwner ? 'Edit profile' : 'View profile'); ?></div>
		</div>
		<div class="latestNews">
			<br/><br/><br/><br/><br/><br/><br/>-- The latest news in real-time about your friends, clans, games... --
		</div>
		<div class="customAdvert">
			<br/>-- An advertisement may be? --<br/><br/>
		</div>
	</div>
</div>
<div id="friendsTab" class="a {title:'My friends'}">
<div id="newFriend" class="voice {panel: 'ajax/friendsfinder.html'}"><span class="label">Add New Friend +</span></div>
<?php
$friendsList = $_SESSION['user']->GetAllFriendsByUsername();
if ($friendsList === USER_HAS_NO_FRIENDS)
    echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">You have no friends</a></span></div>', "\n";
elseif ($friendsList === false)
    echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">Error retrieving your friends.</a></span></div>', "\n";
else
{
    foreach ($friendsList as $i => $value)
        echo '    <div id="friend" class="voice {panel: \'core/friends/friendmenutab.php?friendName='. $friendsList[$i][0] .'\'}"><span class="label"><img src="images/'. ($friendsList[$i][1] ? "friend_online" : "friend_offline") .'.png" style="margin-top:3px;"/><a class="label" href="../', $friendsList[$i][0], '">', $friendsList[$i][0], '</a></span></div>', "\n";
}
?>
</div>
<div id="clansTab" class="a {title:'My Clans'}">
<?php 
for ($i = 1; $i < 5; ++$i)
    echo '    <div id="clan" class="voice {panel: \'core/clans/clansmenutab.php\'}"><span class="label"><a class="label">Clan', $i, '</a></span></div>', "\n";
?>
</div>
<div id="myGamesTab" class="a {title: 'My games'}"></div>
<!--
<div id="chatWindows" class="chatWindows">
	<div style="float:right; height:40px;">
		<div class="chatTab">ChatTab#1</div>
		<div class="chatTab">ChatTab#2</div>
	</div>
</div>
-->
</body>
</html>