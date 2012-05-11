<?php 
require_once("../Common/SharedDefines.php");
require_once("../Common/Common.php");
require_once("../Classes/Database.Class.php");
require_once("../Classes/User.Class.php");

session_start();
if (!isset($_SESSION['user']))
    header("location:login.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $_GET['username'] ?>'s profile - GamersNet</title>
<link href="css/mbExtruder.css" media="all" rel="stylesheet" type="text/css">
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css">
<link href="css/main.css" media="all" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/inc/jquery.latest.js"></script>
<script type="text/javascript" src="js/inc/jquery.hoverIntent.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.metadata.js"></script>
<script type="text/javascript" src="js/inc/jquery.mb.flipText.js"></script>
<script type="text/javascript" src="js/inc/mbExtruder.js"></script>
<script type="text/javascript">
	$(function(){
		$("#friendsTab").buildMbExtruder({
		positionFixed: true,
		sensibility:700,
		autoOpenTime: 1,
      	position:"right",
        width:230,
        flapDim:"200",
        extruderOpacity:1,
        autoCloseTime:0,
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
        autoOpenTime:1,
        autoCloseTime:500,
        slideTimer:200,
        closeOnExternalClick:false,
        onExtClose:function(){},
        onExtOpen:function(){},
        onExtContentLoad: function(){}
    	});
	});
	$(function(){
		$("#mySpaceTab").buildMbExtruder({
		positionFixed: true,
		sensibility:700,
		autoOpenTime: 1,
      	position:"left",
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
</script>
<script type="text/javascript">
function ProcessFriendRequest(event, friendName, action)
{
    var message = "Server error, please try again soon.";
    var theClass = "declineFriend";
    $.post("../core/friends/addfriend.php", {username: friendName, action: action}, function(data) {
		if (data.length > 0)
		{
			if (data == "SUCCESS")
			{
			    if (action = 'a')
			    {
					message = "Accepted!";
					theClass = "acceptFriend";
				}
				else
					message = "Declined";
			}
		}
	    event.target.parentNode.outerHTML = '<div><a class="' + theClass + '">' + message + '</a></div>';
	});
}
</script>
<script type="text/javascript">
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
$(document).ready(FadeIn);
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
			<br/><br/><br/><br/><br/>-- YOUR AVATAR --
		</div>
		<div class="latestNews">
			<br/><br/><br/><br/><br/><br/><br/>-- The latest news in real-time about your friends, clans, games... --
		</div>
		<div class="customAdvert">
			<br/>-- An advertisement may be? --<br/><br/>
		</div>
	</div>
<?php
// Check if the user is the space's owner
/*
if ($_GET['username'] == $_SESSION['user']->GetUsername())
{
    echo "This is your personal space...<br/>";
    $friendRequests = $_SESSION['user']->GetFriendRequests();
    if ($friendRequests === false)
        echo "<br/>There was a problem loading the friend requests sended to you.<br/>";
    elseif ($friendRequests === USER_HAS_NO_FRIEND_REQUESTS)
        echo "<br/>You have no friend requests<br/>";
    else
    {
        foreach ($friendRequests as $i => $value)
            echo "<div>New friend request from ", $friendRequests[$i]['username'], "! <a onclick=\"ProcessFriendRequest(event, '", $friendRequests[$i]['username'], "', 'a')\" class=\"acceptFriend\">Accept</a> - <a onclick=\"ProcessFriendRequest(event, '", $friendRequests[$i]['username'], "', 'd')\" class=\"declineFriend\">Decline</a></div>";
        echo "<br/>";
    }
}
else
    echo "This is your friend's main page...<br>";
*/
?>
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
        echo '    <div id="friend" class="voice {panel: \'core/friends/friendmenutab.php\'}"><span class="label"><a class="label">', $friendsList[$i], '</a></span></div>', "\n";
}
?>
</div>
<div id="clansTab" class="a {title:'My Clans'}">
<?php 
for ($i = 1; $i < 5; ++$i)
    echo '    <div id="clan" class="voice {panel: \'core/clans/clansmenutab.php\'}"><span class="label"><a class="label">Clan', $i, '</a></span></div>', "\n";
?>
</div>
<div id="mySpaceTab" class="a {title: 'My games'}"></div>
</body>
</html>