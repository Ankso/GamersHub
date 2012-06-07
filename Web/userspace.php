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
}
// We must check if the users are friends...
elseif ($user->IsFriendOf($_GET['username']))
{
    $spaceOwner = new User($_GET['username']);
    $isOwner = false;
}
// ...if they aren't, we should show a special space here, just saying that the user isn't allowed to see the specified profile
else
{
    // TODO: show a special space here.
    header("location:index.php");
    exit();
}
// Get private messages(if any) for later use.
$privateMessages = $user->GetPrivateMessages();
$userAvatarPath = $user->GetAvatarHostPath();
$customOptions = $spaceOwner->GetCustomOptions();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $spaceOwner->GetUsername(); ?>'s profile - GamersNet</title>
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/myaccount.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/fancyboxjQuery.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/dark-hive/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="js/inc/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/inc/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.cookie.js"></script>
<script type="text/javascript" src="js/inc/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="js/inc/myAccount.js"></script>
<script type="text/javascript" src="js/inc/privateMessages.js"></script>
<script type="text/javascript" src="js/inc/userspace.js"></script>
<script type="text/javascript">
// TODO: Move all the inline function calls from the HTML to here
$(document).ready(function() {
    totalMessages = <?php echo $spaceOwner->GetBoardMessagesCount(); ?>;
    ownerId = <?php echo $spaceOwner->GetId(); ?>;
    userAvatar = '<?php echo $userAvatarPath; ?>';
    $("a#friendRequests").fancybox();
    $("a#removeFriend").fancybox();
    $("a#sendPrivateMessage").fancybox();
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
    var myFriendsPanelWidth = PercentageWidthToPx(18);
    if (myFriendsPanelWidth >= 250)
    	$('#myFriendsPanel').css("width", myFriendsPanelWidth.toString() + "px");
    else
        $('#myFriendsPanel').css("width", "250px");
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
    $('img#moreOptionsImg').click(SwitchFriendOptionsMenu);
    $('img#moreOptionsImg').hide();
    $('.friendHeader').mouseenter(function(event) {
        try {
            $(event.srcElement.children[1].children[0]).stop().fadeIn(100);
        }
        catch(e) {
            $('img#moreOptionsImg').hide();
        }
    });
    $('.friendHeader').mouseleave(function(event) {
        try {
            $(event.srcElement.children[1].children[0]).stop.fadeOut(100);
        }
        catch(e) {
            $('img#moreOptionsImg').hide();
        }
    });
    $('div#newPrivateMessage').click(function(event) {
        $(event.srcElement).remove();
    });
    $('#addNewFriend').load('ajax/friendsfinder.html');
    LoadBoardComments(1, 5, true);
    $('.commentInputTextBox').focusin(function(event) {
        if ($(event.srcElement).val() == "Something interesting to say?")
        	$(event.srcElement).val("");
    });
    $('.commentInputTextBox').focusout(function(event) {
        if ($(event.srcElement).val() == "")
			$(event.srcElement).val("Something interesting to say?");
    });
    $('.commentInputTextBox').keydown(function(event) {
        if (event.keyCode == 13)
            SendBoardComment($('.commentInputTextBox').val());
    });
    $('span#moreCommentsHistoryButton').click(function(event) {
        if (lastLoadedComment < totalMessages)
        	LoadBoardComments(lastLoadedComment + 1, lastLoadedComment + 6, false)
        if (lastLoadedComment >= totalMessages)
            $(event.srcElement).fadeOut(250);
    });
    openedControlPanel = "#none";
    FadeIn();
});
</script>
</head>
<body>
<?php PrintTopBar($user); ?>
<div id="myFriendsPanelFlapClosed" class="myFriendsPanelFlapClosed" onclick="ShowMyFriendsPanel();">
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
		<div id="addNewFriend" class="friendPanelOptions" style="margin-bottom:5px;"></div>
	</div>
	<?php
	$friendsList = $user->GetAllFriends();
    if ($friendsList === USER_HAS_NO_FRIENDS)
        echo '    <div id="friendWrapper" class="friendWrapper" style="text-align:center;">You have no friends</div>', "\n";
    elseif ($friendsList === false)
        echo '    <div id="friendWrapper" class="friendWrapper" style="text-align:center;">An error occurred. Please try again in a few moments.</div>', "\n";
    else
    {
        $totalFriends = count($friendsList);
        $privateMessagesSenders = false;
        if ($privateMessages !== USER_HAS_NO_MESSAGES && $privateMessages !== false)
        {
            $privateMessagesSenders = array();
            foreach ($privateMessages as $i => $value)
                $privateMessagesSenders[] = $privateMessages[$i]['sender'];
        }
        foreach ($friendsList as $i => $value)
        {
    ?>
    <div id="friendWrapper" class="friendWrapper">
		<div id="friendHeader" class="friendHeader" <?php if ($i === $totalFriends - 1) echo 'style="border-bottom-left-radius:0.5em;"';?>>
    		<div class="friendName"><img src="images/<?php echo ($friendsList[$i][2] ? "friend_online" : "friend_offline"); ?>.png" /><a class="friendSpaceLink" href="/<?php echo $friendsList[$i][1]; ?>"><?php echo $friendsList[$i][1]; ?></a></div>
    		<div class="plusImg"><img id="moreOptionsImg" src="images/more_info_large.png" style="height:25px; width:25px;" /></div>
    		<?php
    		if ($privateMessagesSenders !== false)
    		{
    		    if (in_array($friendsList[$i][0], $privateMessagesSenders))
    		    {
    		?>
			<div id="newPrivateMessage" class="newPrivateMessage"><a id="sendPrivateMessage" href="ajax/privatemessage.php?friendName=<?php echo $friendsList[$i][1]; ?>"><img src="images/new_message.png" /></a></div>
    		<?php 
    		    }
    		}
    		?>
		</div>
		<div class="friendPanelOptions">
			<div class="friendOption">Invite to chat</div>
			<div class="friendOption">Invite to LiveStream</div>
			<div class="friendOption"><a id="sendPrivateMessage" href="ajax/privatemessage.php?friendName=<?php echo $friendsList[$i][1]; ?>" style="text-decoration:none; color:#FFFFFF;">Send private message</a></div>
			<div class="friendOptionRemove"><a id="removeFriend" href="ajax/removefriendconfirmation.php?friendName=<?php echo $friendsList[$i][1]; ?>" style="text-decoration:none; color:#FFFFFF;">Remove friend</a></div>
		</div>
	</div>
	<?php
        }
    } 
    ?>
    <div id="closeMyFriendsPanel" class="closeMyFriendsPanel" onclick="CloseMyFriendsPanel();">
    	<b>Hide</b><!-- We must put an image here, like a minus sign or a minimize icon, may be a left arrow, something like that -->
    </div>
</div>
<div class="mainContent">
	<div class="mainBoard">
        <?php
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
				<div id="sendBoardMessage" class="sendBoardMessage" onclick="SendBoardComment($('.commentInputTextBox').val(), <?php echo $spaceOwner->GetId(); ?>);"><img src="images/send_comment.png" /></div>
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
	</div>
	<div class="profileBoard">
		<div class="profileInfo">
			<div class="imgAvatar">
				<div style="background:transparent url('<?php echo $spaceOwner->GetAvatarHostPath(); ?>') no-repeat center center; background-size:100%; height:200px; width:200px; border-radius:0.5em;">
					<?php if ($isOwner) { ?>
					<div class="editAvatar"><a id="changeAvatar" href="ajax/changeavatar.php"><img src="images/edit.png" alt="Edit" style="height:22px;width:22px;margin-top:3px;"/></a></div>
					<?php } ?>
				</div>
			</div>
			<div id="profileDetails">
				<?php
				    $details = $spaceOwner->GetDetailedUserData();
				?>
            	<div id="bioDiv" class="profileText"><b>Bio: </b><span id="bioSpan"><?php echo $details[USER_DETAILS_BIO]; ?></span></div><br />
            	<div id="birthdayDiv" class="profileText"><b>Birthday: </b><span id="birthdaySpan"><?php echo $details[USER_DETAILS_BIRTHDAY]; ?></span></div><br />
            	<div id="countryDiv" class="profileText"><b>Country: </b><span id="countrySpan"><?php echo $details[USER_DETAILS_COUNTRY]; ?></span></div><br />
            	<div id="cityDiv" class="profileText"><b>City: </b><span id="citySpan"><?php echo $details[USER_DETAILS_CITY]; ?></span></div>
            	<?php if ($isOwner) { ?>
            	<div class="editProfileText" onclick="EditProfileDetails();"><img src="images/edit.png" style="height:25px; width:25px; float:right;" /><br /></div>
            	<?php } else { ?>
            	<br />
            	<?php } ?>
			</div>
			<div class="editProfileButton" onclick="SwitchProfileDetails();">View profile</div>
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
<!--
<div id="chatWindows" class="chatWindows">
	<div style="float:right; height:40px;">
		<div class="chatTab">ChatTab#1</div>
		<div class="chatTab">ChatTab#2</div>
	</div>
</div>
-->
<div style="position:fixed; text-align:center; bottom:0; right:0; font:12px Calibri;">Page loaded in <?php echo microtime(true) - $loadTime; ?> seconds.</div>
</body>
</html>