<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

$sessionsHandler = new CustomSessionsHandler();
session_set_save_handler(
    array($sessionsHandler, "open"),
    array($sessionsHandler, "close"),
    array($sessionsHandler, "read"),
    array($sessionsHandler, "write"),
    array($sessionsHandler, "destroy"),
    array($sessionsHandler, "gc")
    );
register_shutdown_function("session_write_close");
session_start();
if(!isset($_SESSION['userId']))
    die("Error: you must be logged in!");

$user = new User($_SESSION['userId']);
$friends = $user->GetAllFriends();
$friendRequests = $user->GetFriendRequests();
?>
<script type="text/javascript">
$(document).ready(function() {
    $("div.socialMenuOption").click(function(event) {
    	SocialMenuOptionClick(event);
    });
    $("div#socialOptionFriends").trigger("click");
    var dialogObject = {
        dialogItem: $("div#socialRemoveFriendDialog").dialog({
        	resizable: false,
        	width: 350,
        	modal: true,
        	autoOpen: false,
        	zIndex: 500,
        	title: "Confirm friend removal",
        	buttons: {
        		"Yes, I'm sure": function() {
            		RemoveFriend(dialogObject.friendId);
        			dialogObject.dialogItem.dialog("close");
        		},
        		"Cancel": function() {
        			dialogObject.dialogItem.dialog("close");
        		},
        	},
        }),
        friendId: "",
    };
    $("div#socialRemoveFriend").click(function(event) {
        dialogObject.friendId = $(event.target).attr("data-id");
        dialogObject.dialogItem.dialog("open");
    });
    $("span.socialAcceptFriendRequest").click(function(event) {
		HandleFriendRequest($(event.target).parent().attr("data-id"), "ACCEPT");
    });
    $("span.socialDeclineFriendRequest").click(function(event) {
        HandleFriendRequest($(event.target).parent().attr("data-id"), "DECLINE");
    });
});
</script>
<div class="socialMenu">
	<div class="socialMenuTop" style="background-color:rgba(255, 122, 0, 0.3);"><strong>People</strong></div>
	<div id="socialOptionFriends" class="socialMenuOption">My friends</div>
	<div id="socialOptionFriendRequests" class="socialMenuOption">Friend requests</div>
	<div id="socialOptionPrivateMessages" class="socialMenuOption">Private messages</div>
	<div id="socialOptionIgnoredList" class="socialMenuOption">Ignored list</div>
	<div class="socialMenuTop" style="background-color:rgba(22, 22, 210, 0.3);"><strong>Clans</strong></div>
	<div id="socialOptionClans" class="socialMenuOption">My Clans</div>
</div>
<div id="socialFriends" class="socialTab">
<?php
if ($friends != USER_HAS_NO_FRIENDS)
{
    foreach ($friends as $i => $value)
    {
    ?>
	<div class="socialTabItem">
		<div id="socialFriend<?php echo $friends[$i]['id']; ?>" class="socialFriendItem" style="border:2px <?php echo $friends[$i]['isOnline'] ? "#00FF00" : "#FF0000"; ?> solid; background:transparent url('<?php echo $friends[$i]['avatarPath']; ?>') no-repeat center center;"></div>
		<div class="socialFriendItemName">
			<a class="socialPlainLink" href="<?php echo "/", $friends[$i]['username']; ?>"><?php echo $friends[$i]['username']; ?></a>
			<div id="socialRemoveFriend" class="socialRemoveFriend" data-id="<?php echo $friends[$i]['id']; ?>" data-username="<?php echo $friends[$i]['username']; ?>">Remove</div>
		</div>
	</div>
    <?php
    }
}
else
{
?>
    <div class="socialTabItem">You have now friends. Add new friends using the friends panel, in the top right corner!</div>
<?php
}
?>
	<div id="socialFriendsError" class="socialError"></div>
	<div id="socialRemoveFriendDialog">Are you really sure?</div>
</div>
<div id="socialFriendRequests" class="socialTab">
<?php 
if ($friendRequests !== USER_HAS_NO_FRIEND_REQUESTS)
{
    foreach ($friendRequests as $i => $value)
    {
?>
	<div class="socialTabItem">
		<div class="socialFriendRequestContainer">
    		<div id="socialFriendRequest<?php echo $friendRequests[$i]['id']; ?>" class="socialFriendItem" style="border:2px rgb(255, 122, 0) solid; background:transparent url('<?php echo $friendRequests[$i]['avatarPath']; ?>') no-repeat center center;"></div>
    		<div class="socialFriendItemName">
    			<a class="socialPlainLink" href="<?php echo "/", $friendRequests[$i]['username']; ?>"><?php echo $friendRequests[$i]['username']; ?></a>
    			<div id="socialManageRequest<?php echo $friendRequests[$i]['id']; ?>" class="socialManageFriendRequest" data-id="<?php echo $friendRequests[$i]['id']; ?>">
    				<span class="socialAcceptFriendRequest">Accept</span> - <span class="socialDeclineFriendRequest">Decline</span>
    			</div>
    		</div>
		</div>
		<div class="socialSubTabItem"><?php echo "Message from ", $friendRequests[$i]['username'], ": ", $friendRequests[$i]['message']; ?></div>
	</div>
<?php
    }
}
else
{
?>
	<div class="socialTabItem">You have no friend requests.</div>
<?php
}
?>
	<div id="socialFriendRequestsError" class="socialError"></div>
</div>
<div id="socialPrivateMessages" class="socialTab">
	Private messages management - Not yet implemented here.
</div>
<div id="socialIgnoredList" class="socialTab">
	Ignored list - Not yet implemented at all.
</div>
<div id="socialClans" class="socialTab" style="border-left:10px rgb(22, 22, 210) solid;">
	The Clans System is not implemented yet at all.
</div>