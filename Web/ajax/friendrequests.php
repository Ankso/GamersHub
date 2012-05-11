<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");

session_start();
if (!isset($_SESSION['user']))
    header("location:login.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Your friend requests</title>
<style type="text/css">

.mainFriendRequests {
    background-color:#000000;
    color:#FFFFFF;
    text-align:center;
    min-width:600px;
    min-height:400px;
    padding:20px 20px 20px 20px
}

.acceptFriend {
	color:#00FF00;
}

.acceptFriend:hover {
	cursor:pointer;
}

.declineFriend {
	color:#FF0000;
}

.declineFriend:hover {
	cursor:pointer;
}


</style>
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
</head>
<body>
<div class="mainFriendRequests">
<?php
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
?>
</div>
</body>
</html>