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
?>
<script type="text/javascript">
$("div.socialMenuOption").click(function(event) {
	SocialMenuOptionClick(event);
});
$("div#socialOptionFriends").trigger("click");
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
foreach ($friends as $i => $value)
{
?>
	<div class="socialTabItem">
		<div id="socialFriend<?php echo $friends[$i]['id']; ?>" class="socialFriendItem" style="border:2px <?php echo $friends[$i]['isOnline'] ? "#00FF00" : "#FF0000"; ?> solid; background:transparent url('<?php echo $friends[$i]['avatarPath']; ?>') no-repeat center center;"></div>
		<div class="socialFriendItemName"><a class="socialPlainLink" href="<?php echo "/", $friends[$i]['username']; ?>"><?php echo $friends[$i]['username']; ?></a></div>
	</div>
<?php
}
?>
</div>
<div id="socialFriendRequests" class="socialTab">
	Requests
</div>
<div id="socialPrivateMessages" class="socialTab">
	Messages
</div>
<div id="socialIgnoredList" class="socialTab">
	Ignored list
</div>
<div id="socialClans" class="socialTab" style="border-left:10px rgb(22, 22, 210) solid;">
	The Clans System is not implemented yet!
</div>