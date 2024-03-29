<?php
/*
 * TODO: Redo "Add a friend" and "Remove friend" Structure.
 */
$loadTime = microtime(true);
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Developer.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Publisher.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Game.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

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
if (GetIdFromUsername($_GET['username']) === USER_DOESNT_EXISTS)
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
if (!$spaceOwner->GetLiveStreamId())
    $spaceOwner->GenerateLiveStreamId();
$customOptions = $spaceOwner->GetCustomOptions();
$privacySettings = $spaceOwner->GetPrivacySettings();
$recommendedGame = $user->GetRecommendedGame();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $isOwner ? "Your " : ($spaceOwner->GetUsername() . "'s"); ?> profile - GamersHub</title>
<link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/myaccount.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/social.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/mygames.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/fancyboxjQuery.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/dark-hive/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/jquery.jscrollpane.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="js/inc/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/inc/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.cookie.js"></script>
<script type="text/javascript" src="js/inc/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="js/inc/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="js/inc/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="js/inc/socket.io.js"></script>
<script type="text/javascript" src="js/inc/jwplayer.js"></script>
<script type="text/javascript" src="js/inc/swfobject.js"></script>
<script type="text/javascript" src="js/inc/myAccount.js"></script>
<script type="text/javascript" src="js/inc/social.js"></script>
<script type="text/javascript" src="js/inc/myGames.js"></script>
<script type="text/javascript" src="js/inc/privateMessages.js"></script>
<script type="text/javascript" src="js/inc/userspace.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    space.totalMessages = <?php echo $spaceOwner->GetBoardMessagesCount(); ?>;
    user.id = <?php echo $user->GetId(); ?>;
    user.username = "<?php echo $user->GetUsername(); ?>";
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
    // Temp link to a list with the TODO:
    $("a.listStatus").fancybox();
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
    $("img.recommendedGameImg").click(function() {
        if (myGames)
            myGames.LoadGame($("div#recommendedGame").attr("data-id"));
        else
        {
    		space.onControlPanelOpenAction.action = "loadGame";
    		space.onControlPanelOpenAction.data = $("div#recommendedGame").attr("data-id");
        }
		$("div#myGamesButton").trigger("click");
    });
    $("div#closeChatBox").click(function() {
		chatManager.CloseChatConversation();
    });
    // Update timestamps
    space.UpdateTimestamps();
    // For idle timer and more:
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
    SetMainWrapperHeight();
    FadeIn();
    $("div#latestNews").jScrollPane({
		showArrows: true,
    });
    /*
    $("div.mainWrapper").jScrollPane({
		showArrows: true,
    });
    */
    space.LoadPlugin();
    gamesManager.GetGamesList();
    socket.ConnectToRealTimeServer();
    if ($("div#videoWindow").length)
    {
        jwplayer("videoContainer").setup({
            flashplayer: "/flash/player.swf",
            streamer: "rtmp://gamershub.no-ip.org/oflaDemo",
            // This ID should be hashed to avoid stream steal...
            file: "<?php echo /*hash("SHA256", */$spaceOwner->GetLiveStreamId()/*)*/; ?>",
    		width: ($("div#videoWindow").width() >= 720) ? 720 : 640,
    		height: 480,
        });
        var leftPadding = 0;
        if ($("div#videoWindow").width() > 720)
            leftPadding = ($("div#videoWindow").width() - 720) / 2;
        else
            leftPadding = ($("div#videoWindow").width() - 640) / 2;
        $("div#videoContainer_wrapper").css("margin-left", leftPadding + "px");
    }
    /* TODO: Delete this after the testing phase. */
    setTimeout(function() {
        $("div#pluginStatus").fadeOut(1000);
        $("div#pageGenerationTime").fadeOut(1000);
    }, 10000);
    /* END TODO */
});
</script>
</head>
<body style="display:none">
<object id="gamershubPlugin" type="application/x-gamershubtools" width="0px" height="0px">
</object>
<?php PrintTopBar($user); ?>
<div id="myAccount" class="controlPanel"></div>
<div id="mySocial" class="controlPanel"></div>
<div id="myGames" class="controlPanel"></div>
<div class="afkWindow" style="display:none;">
	<div class="afkWindowContainer">
		You have been too much time AFK. Please enter your password to restore your session:<br/>
		<input id="afkPassword" type="password" style="border-radius:0.3em; text-align:center; margin-top:20px;" /><br/>
	</div>
</div>
<div class="mainWrapper">
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
    <div id="advertMessagePopUp" class="advertMessagePopUp" style="display:none;">
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
        			<div id="videoWindow">
        				<div id="videoContainer">
        				</div>
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
        		<!--
        		<div class="clansBoard">
        			<br/></br>-- Live comments written by your clan(s) here, independent from your comments --<br/><br/><br/>
        		</div>
        		-->
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
                	<div class="editProfileText"><img src="images/edit.png" style="height:25px; width:25px; float:right; " /><br /></div>
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
    		<div id="latestNewsContainer" class="latestNewsContainer">
    			<div class="latestNewsHeader"><i><b>Latest News</b></i></div>
    			<div id="latestNews" class="latestNews">
    			<?php
    			if ($latestNews = $user->GetLatestNews())
    			{
    			    if ($latestNews == USER_HAS_NO_LATEST_NEWS)
    			    {
    			    ?>
    			    <div id="noLatestNews" class="latestNew">No news from your network.</div>
    			    <?php
    			    }
    			    else
    			    {
    			        foreach ($latestNews as $i => $value)
    			        {
    			            switch ($latestNews[$i]['newType'])
    			            {
    			                case NEW_TYPE_NEW_MESSAGE:
    			                    ?>
    			                    <div class="latestNew">
                                		<a href="/<?php echo $latestNews[$i]['extraInfo']['friendName']; ?>" style="color:#FFFFFF;"><b><?php echo $latestNews[$i]['extraInfo']['friendName']; ?></b></a> has posted a new message!
                                		<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="<?php echo $latestNews[$i]['extraInfo']['timestamp']; ?>"><i>Unknown time ago</i></span></div>
                                	</div>
    			                    <?php
    			                    break;
    			                case NEW_TYPE_NEW_FRIEND:
    			                    ?>
    			                    <div class="latestNew">
                                		<a href="<?php echo $latestNews[$i]['extraInfo']['friendName']; ?>" style="color:#FFFFFF;"><b><?php echo $latestNews[$i]['extraInfo']['friendName']; ?></b></a> is now friend of 
                                		<a href="<?php echo $latestNews[$i]['extraInfo']['newFriendName']; ?>" style="color:#FFFFFF;"><b><?php echo $latestNews[$i]['extraInfo']['newFriendName']; ?></b></a>
                                		<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="<?php echo $latestNews[$i]['extraInfo']['timestamp']; ?>"><i>Unknown time ago</i></span></div>
                                	</div>
                                    <?php
    			                    break;
    			                case NEW_TYPE_NEW_GAME:
    			                    ?>
    			                    <div class="latestNew">
                                		<a href="<?php echo $latestNews[$i]['extraInfo']['friendName']; ?>" style="color:#FFFFFF;"><b><?php echo $latestNews[$i]['extraInfo']['friendName']; ?></b></a> has a new game: <b><?php echo $latestNews[$i]['extraInfo']['newGameTitle']; ?></b>
                                		<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="<?php echo $latestNews[$i]['extraInfo']['timestamp']; ?>"><i>Unknown time ago</i></span></div>
                                	</div>
    			                    <?php
    			                    break;
    			                default:
    			                    break;
    			            }
    			        }
    			    }
    			}
    			?>
    			</div>
    		</div>
    		<?php
    		}
    		if ($recommendedGame && !is_int($recommendedGame))
            {
    		?>
    		<div class="customAdvert">
    			<div><i>GamersHub recommends:</i></div>
    			<div id="recommendedGame" data-id="<?php echo $recommendedGame->GetId(); ?>">
    				<img class="recommendedGameImg" src="<?php echo $recommendedGame->GetImagePath(); ?>"/><br />
    				<span style="margin-top:5px; font:22px Calibri;"><b><?php echo $recommendedGame->GetTitle(); ?></b></span>
    			</div>
    		</div>
    		<?php
    		}
    		?>
    	</div>
    </div>
    <div class="chatBoxWrapper">
        <div class="chatBox">
        	<div class="closeChatBox" id="closeChatBox">Close</div>
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
    <div id="gameNotification" class="gameNotification" style="display:none"></div>
    <!-- This is information usefull only during the testing phase -->
    <div id="pluginStatus" style="position:fixed; bottom:0; left:0; font:12px Calibri; margin-bottom:60px;">Plugin status: Unknown</div>
    <div id="nodeServerStatus" style="position:fixed; bottom:0; left:0; font:12px Calibri; margin-bottom:40px;">RTS connection status: Unknown</div>
    <div id="pageGenerationTime" style="position:fixed; text-align:center; bottom:0; left:0; margin-bottom:20px; font:12px Calibri;">Page generated in <?php echo microtime(true) - $loadTime; ?> seconds.</div>
    <div style="position:fixed; bottom:0; left:0; font:12px Calibri; cursor:pointer; background-color:rgba(33, 33, 33, 0.8); border-top-right-radius:0.4em; padding:4px; z-index:101;"><a class="listStatus" href="todolist.html" style="color:#FFFFFF;">The list.</a></div>
</div>
</body>
</html>