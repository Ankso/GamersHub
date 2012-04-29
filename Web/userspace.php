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
<link href="js/css/mbExtruder.css" media="all" rel="stylesheet" type="text/css">
<link href="design/css/userspace.css" media="all" rel="stylesheet" type="text/css">
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
        autoOpenTime:50,
        autoCloseTime:500,
        slideTimer:200,
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
        autoCloseTime:0,
        slideTimer:200,
        onExtClose:function(){},
        onExtOpen:function(){},
        onExtContentLoad: function(){}
    	});
	});
</script>
</head>
<body>
<div align="center">
<table class="mainContent">
	<tr>
		<td><?php
            // Check if the user is the space's owner
            if ($_GET['username'] == $_SESSION['user']->GetUsername())
                echo "Loading your personal space...<br><a href=\"logout.php\">Logout</a>";
            else
                echo "Loading your friend's main page...<br><a href=\"logout.php\">Logout</a>";
            
            ?>
	</tr>
</table>
</div>
<div id="friendsTab" class="a {title:'My friends'}">
<div id="newFriend" class="voice {}"><span class="label">Add New Friend +</span></div>
<?php
$friendsList = $_SESSION['user']->GetAllFriendsByUsername();
if ($friendsList === USER_HAS_NO_FRIENDS)
    echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">You have no friends</a></span></div>' . "\n";
elseif ($friendsList === false)
    echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">Error retrieving your friends.</a></span></div>' . "\n";
else
{
    foreach ($friendsList as $i => $value)
    {
        echo '    <div id="friend" class="voice {panel: \'friendmenutab.php\'}"><span class="label"><a class="label">'. $friendsList[$i] .'</a></span></div>' . "\n";
    }
}
?>
</div>
<div id="clansTab" class="a {title:'My Clans'}">
<?php 
for ($i = 1; $i < 5; ++$i)
    echo '    <div id="clan" class="voice {panel: \'clansmenutab.php\'}"><span class="label"><a class="label">Clan'. $i .'</a></span></div>' . "\n";
?>
</div>
<div id="mySpaceTab" class="a {title: 'My Space'}"></div>
</body>
</html>