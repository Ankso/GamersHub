<?php
/*
 * TODO: Redo "Add a friend" and "Remove friend" Structure.
 */
$loadTime = microtime(true);
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
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
// Check if the user is logged in
if (!isset($_SESSION['userId']))
{
    header("location:login.php");
    exit();
}
// Build the user object
$user = new User($_SESSION['userId']);
// Check if the user is accessing a specified profile
if (!isset($_GET['username']))
{
    header("location:index.php");
    exit();
}
// Check if the username is a valid one
if (GetIdFromUsername($_GET['username']) === false)
{
    // here we must redirect the user to a page of type "The user that you are looking for doesn't exists!"
    header("location:index.php");
    exit();
}
// Determine if the user is the owner of the space
if ($user->GetUsername() === $_GET['username'])
{
    $spaceOwner = $user;
    $isOwner = true;
    $usersAreNotFriends = false;
}
// We must check if the users are friends...
elseif ($user->IsFriendOf($_GET['username']))
{
    $spaceOwner = new User($_GET['username']);
    $isOwner = false;
    $usersAreNotFriends = false;
}
// ...if they aren't, we should show a special space here, just saying that the user isn't allowed to see the specified profile
else
{
    $spaceOwner = new User($_GET['username']);
    $isOwner = false;
    $usersAreNotFriends = true;
}
$customOptions = $spaceOwner->GetCustomOptions();
$privacySettings = $spaceOwner->GetPrivacySettings();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $spaceOwner->GetUsername(); ?>'s profile - GamersHub</title>
<link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/myaccount.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/social.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/fancyboxjQuery.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/dark-hive/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="js/inc/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/inc/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.cookie.js"></script>
<script type="text/javascript" src="js/inc/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="js/inc/socket.io.js"></script>
<script type="text/javascript" src="js/inc/myAccount.js"></script>
<script type="text/javascript" src="js/inc/social.js"></script>
<script type="text/javascript" src="js/inc/privateMessages.js"></script>
<script type="text/javascript" src="js/inc/userspace.js"></script>
<script type="text/javascript">
// TODO: Move all the inline function calls from the HTML to here
$(document).ready(function() {
    space.totalMessages = <?php echo $spaceOwner->GetBoardMessagesCount(); ?>;
    user.id = <?php echo $user->GetId(); ?>;
    spaceOwner.id = <?php echo $spaceOwner->GetId(); ?>;
    user.avatarPath = "<?php echo $user->GetAvatarHostPath(); ?>";
    user.randomSessionId = "<?php echo $user->GetRandomSessionId(); ?>";
    friendsManager.totalFriends = <?php echo $user->GetFriendsCount(); ?>;
	// Top bar functions:
    $("div#myAccountButton").click(TriggerOpenControlPanel);
    $("div#mySocialButton").click(TriggerOpenControlPanel);
    $("div#myGamesButton").click(TriggerOpenControlPanel);
    $("a#topbarLogOffButton").click(function(event) {
        FadeOut(event, "logout.php");
    });
    // FancyBox(es)
    $("a#friendRequests").fancybox();
    <?php
    if ($isOwner)
    {
    ?>
    $("a#changeAvatar").fancybox();
    $(".editAvatar").hide();
    $(".imgAvatar").mouseenter(function(event) {
        $(".editAvatar").stop(true, true).fadeIn("fast");
    });
    $(".imgAvatar").mouseleave(function(event) {
        $(".editAvatar").stop(true, true).fadeOut("fast");
    });
    <?php
    }
    ?>
    var myFriendsPanelWidth = space.PercentageWidthToPx(18);
    if (myFriendsPanelWidth >= 250)
        $('#myFriendsPanel').css("width", myFriendsPanelWidth.toString() + "px");
    else
        $('#myFriendsPanel').css("width", "250px");
    $("div#myFriendsPanelFlapClosed").click(function(/*event*/) {
        space.ShowMyFriendsPanel();
    });
    $("div#closeMyFriendsPanel").click(function(/*event*/) {
        space.CloseMyFriendsPanel();
    });
    if ($.cookie("FriendsPanel") == "closed")
    {
        $('#myFriendsPanel').hide();
        $('#myFriendsPanelFlapOpened').hide();
    }
    else
        $('#myFriendsPanelFlapClosed').hide();
    $('#profileDetails').hide();
    $('.controlPanel').hide();
    $('.friendPanelOptions').hide();
    $('img#moreOptionsImg').click(function(event) {
        SwitchFriendOptionsMenu(event);
    });
    $('img#moreOptionsImg').hide();
    $('.friendHeader').mouseenter(function(event) {
        try {
            $(event.target.children[1].children[0]).stop().fadeIn(100);
        }
        catch(e) {
            $('img#moreOptionsImg').hide();
        }
    });
    $('.friendHeader').mouseleave(function(event) {
        try {
            $(event.target.children[1].children[0]).stop.fadeOut(100);
        }
        catch(e) {
            $('img#moreOptionsImg').hide();
        }
    });
    $('#addNewFriend').load('core/ajax/friendsfinder.html');
    space.LoadBoardComments(1, 5, true);
    $("div#sendBoardMessage").click(function(/*event*/) {
        space.SendBoardComment($('.commentInputTextBox').val());
    });
    $('.commentInputTextBox').focusin(function(event) {
        if ($(event.target).val() == "Something interesting to say?")
            $(event.target).val("");
    });
    $('.commentInputTextBox').focusout(function(event) {
        if ($(event.target).val() == "")
            $(event.target).val("Something interesting to say?");
    });
    $('.commentInputTextBox').keydown(function(event) {
        if (event.keyCode == 13)
            space.SendBoardComment($('.commentInputTextBox').val());
    });
    $('span#moreCommentsHistoryButton').click(function(event) {
        if (space.lastLoadedComment < space.totalMessages)
            space.LoadBoardComments(space.lastLoadedComment + 1, space.lastLoadedComment + 6, false)
        if (space.lastLoadedComment >= space.totalMessages)
            $(event.target).fadeOut(250);
    });
    $("div.editProfileButton").click(function(/*event*/) {
        space.SwitchProfileDetails();
    });
    $("div.editProfileText").click(function(/*event*/) {
        space.EditProfileDetails();
    });
    $("input.chatBoxInput").keydown(function(event) {
        if (event.keyCode == 13)
            chatManager.SendChatMessage(event);
    });
    // For idle time and more:
    setInterval(function() {
        space.IncrementIdleTimer();
    }, IDLE_TIMER_STEP);
    $(this).mousemove(function(/*event*/) {
        space.idleTime = 0;
    });
    $(this).keypress(function(/*event*/) {
        space.idleTime = 0;
    });
    $("input#afkPassword").keydown(function(event) {
        if (event.keyCode == 13)
            space.DisableAfkMode();
    });
    FadeIn();
    socket.ConnectToRealTimeServer();
});
</script>
</head>
<body style="display:none">
<?php PrintTopBar($user); ?>
<div id="myFriendsPanelFlapClosed" class="myFriendsPanelFlapClosed">
	<b>Friends</b><div class="imgMyFriendsPanelFlap"><img src="images/more_info_large.png" style="height:25px; width:25px;" /></div>
</div>
<div id="myFriendsPanelFlapOpened" class="myFriendsPanelFlapOpened">
</div>
<div id ="myFriendsPanel" class="myFriendsPanel">
	<div id="friendWrapper" class="friendWrapper">
		<div id="friendHeader" class="friendHeader" style="border-top-right-radius:0.5em; background-color:#333333; border-bottom:1px #FFFFFF solid;">
    		<div class="friendName"><span style="font:20px Calibri; margin-left:5px;"><b>Add New Friend</b></span></div>
    		<div class="plusImg"><img id="moreOptionsImg" src="images/more_info_large.png" style="height:25px; width:25px;" /></div>
		</div>
		<div id="addNewFriend" class="friendPanelOptions"></div>
	</div>
    <div id="closeMyFriendsPanel" class="closeMyFriendsPanel">
    	<b>Hide</b><!-- We must put an image here, like a minus sign or a minimize icon, may be a left arrow, something like that -->
    </div>
</div>
<div class="mainContent">
	<div class="mainBoard">
        <?php
        if ($usersAreNotFriends)
        {
        ?>
        <div class="usersAreNotFriends">
        	<b><?php echo $spaceOwner->GetUserName();?></b> is not your friend!
        	<div style="font:16px Calibri;">You can send him a friend request using the left panel.</div>
        </div>
        <?php
        }
        else
        {
    		if ($customOptions[CUSTOM_OPTION_LIVESTREAM])
    		{
            ?>
    		<div class="mainLivestream">
    			<div class="videoWindow">
    				<br/><br/><br/><br/><br/><br/><br/>-- Here is your live streaming video (640x360) --
    			</div>
                <?php
    			if ($customOptions[CUSTOM_OPTION_LIVESTREAM_COMMENTS])
    			{
                ?>
    			<div class="videoComments">
    				<br/><br/>-- Live comments about the livestream here --<br/><br/><br/>
    			</div>
                <?php
    			}
                ?>
    		</div>
    		<?php 
    		}
    		?>
    		<div id="commentsBoard" class="commentsBoard">
    		<?php
    	    if ($isOwner)
    		{
    		?>
    			<div id="commentsBoardInput" class="commentsBoardInput">
    				<input class="commentInputTextBox" type="text" value="Something interesting to say?" />
    				<div id="sendBoardMessage" class="sendBoardMessage"><img src="images/send_comment.png" /></div>
    			</div>
            <?php 
    		}
    		?>
    			<div id="commentsHistory" class="commentsHistory">
    			</div>
    			<div id="moreCommentsHistory"><span id="moreCommentsHistoryButton" class="moreCommentsHistoryButton">More</span></div>
    		</div>
    		<div class="clansBoard">
    			<br/></br>-- Live comments written by your clan(s) here, independent from your comments --<br/><br/><br/>
    		</div>
    	<?php 
        }
    	?>
	</div>
	<div class="profileBoard">
		<div class="profileInfo">
			<div class="imgAvatar">
				<div style="background:transparent url('<?php echo $spaceOwner->GetAvatarHostPath(); ?>') no-repeat center center; background-size:100%; height:200px; width:200px; border-radius:0.5em;">
					<?php
					if ($isOwner)
					{
					?>
					<div class="editAvatar"><a id="changeAvatar" href="core/ajax/changeavatar.php"><img src="images/edit.png" alt="Edit" style="height:22px;width:22px;margin-top:3px;"/></a></div>
					<?php
					}
					?>
				</div>
			</div>
			<div id="profileDetails">
				<?php
				    if ($usersAreNotFriends && $privacySettings[USER_PRIVACY_PROFILE] != PRIVACY_LEVEL_EVERYONE)
				        echo "This profile is private";
				    else
				    {
				        $details = $spaceOwner->GetDetailedUserData();
				?>
            	<div id="bioDiv" class="profileText"><b>Bio: </b><span id="bioSpan"><?php echo $details[USER_DETAILS_BIO]; ?></span></div><br />
            	<div id="birthdayDiv" class="profileText"><b>Birthday: </b><span id="birthdaySpan"><?php echo $details[USER_DETAILS_BIRTHDAY]; ?></span></div><br />
            	<div id="countryDiv" class="profileText"><b>Country: </b><span id="countrySpan"><?php echo $details[USER_DETAILS_COUNTRY]; ?></span></div><br />
            	<div id="cityDiv" class="profileText"><b>City: </b><span id="citySpan"><?php echo $details[USER_DETAILS_CITY]; ?></span></div>
            	<?php
            	        if ($isOwner)
            	        {
            	?>
            	<div class="editProfileText"><img src="images/edit.png" style="height:25px; width:25px; float:right;" /><br /></div>
            	<?php
                    	}
                    	else
                    	{ 
            	?>
            	<br />
            	<?php
            	        }
				    }
            	?>
			</div>
			<div class="editProfileButton">View profile</div>
		</div>
		<?php
		if ($customOptions[CUSTOM_OPTION_LATEST_NEWS])
		{
		?>
		<div class="latestNews">
			<br/><br/><br/><br/><br/><br/><br/>-- The latest news in real-time about your friends, clans, games... --
		</div>
		<?php
		}
		?>
		<div class="customAdvert">
			<br/>-- An advertisement may be? --<br/><br/>
		</div>
	</div>
</div>
<!--
<div id="myGamesTab" class="a {title: 'My games'}"></div>
-->
<div id="myAccount" class="controlPanel"></div>
<div id="mySocial" class="controlPanel"></div>
<div id="myGames" class="controlPanel"></div>
<div class="chatBoxWrapper">
    <div class="chatBox">
    	<div class="chatBoxTextWrapper">
    	</div>
    	<input class="chatBoxInput" type="text" />
	</div>
</div>
<div id="chatWindows" class="chatWindows">
	<div class="chatTabsWrapper">
    </div>
</div>
<div id="realTimeNotification" class="realTimeNotification" style="display:none"></div>
<div id="nodeServerStatus" style="position:fixed; text-align:center; bottom:0; left:0; font:12px Calibri; margin-bottom:20px;">Unknown</div>
<div style="position:fixed; text-align:center; bottom:0; left:0; font:12px Calibri;">Page generated in <?php echo microtime(true) - $loadTime; ?> seconds.</div>
<div class="afkWindow" style="display:none;">
	<div class="afkWindowContainer">
		You have been too much time AFK. Please enter your password to restore your session:<br />
		<input id="afkPassword" type="password" style="border-radius:0.3em; text-align:center; margin-top:20px;" />
	</div>
</div>
</body>
</html>