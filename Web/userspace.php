<?php 
require_once("../common/SharedDefines.php");
require_once("../common/Common.php");
require_once("../classes/Database.Class.php");
require_once("../classes/User.Class.php");

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $spaceOwner->GetUsername(); ?>'s profile - GamersNet</title>
<link href="css/mbExtruder.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/userspace.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/myaccount.css" media="all" rel="stylesheet" type="text/css" />
<link href="css/fancyboxjQuery.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/dark-hive/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="js/inc/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/inc/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.hoverIntent.min.js"></script>
<script type="text/javascript" src="js/inc/jquery.metadata.js"></script>
<script type="text/javascript" src="js/inc/jquery.mb.flipText.js"></script>
<script type="text/javascript" src="js/inc/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="js/inc/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="js/inc/mbExtruder.js"></script>
<script type="text/javascript" src="js/inc/myAccount.js"></script>
<script type="text/javascript" src="js/inc/privateMessages.js"></script>
<script type="text/javascript">
var previousBio;
var previousBirthday;
var previousCountry;
var previousCity;
var openedControlPanel;

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

function SwitchProfileDetails()
{
    if ($('#profileDetails').is(":hidden"))
    {
		$('#profileDetails').slideDown(400);
		$('div.editProfileButton').text("Hide");
    }
    else
    {
        $('#profileDetails').slideUp(400);
        $('div.editProfileButton').text("View profile");
    }
}

function EditProfileDetails()
{
    previousBio = $('#bioSpan').html();
    $('#bioDiv').html('Bio: <textarea id="bioInput" style="min-height:100px; width:95%;">' + previousBio + '</textarea>');
    previousBirthday = $('#birthdaySpan').text();
    $('#birthdayDiv').html('Birthday: <input type="text" id="birthdayInput"  value="' + previousBirthday + '" />');
    previousCountry = $('#countrySpan').text();
    $('#countryDiv').html('Country: <input type="text" id="countryInput"  value="' + previousCountry + '" />');
    previousCity = $('#citySpan').text();
    $('#cityDiv').html('City: <input type="text" id="cityInput"  value="' + previousCity + '" />');
    $('div.editProfileText').hide();
    $('#profileDetails').append('<div id="submitCancelEdit" style="height:20px; margin-top:7px;"><span style="float:left; color:#00FF00; cursor:pointer;" onclick="SubmitEditedProfileDetails();">Submit</span><span style="float:right; color:#FF0000; cursor:pointer;" onclick="CancelEditProfileDetails();">Cancel</span></div>');
}

function CancelEditProfileDetails()
{
    $('#bioDiv').html('Bio: <span id="bioSpan">' + previousBio + '</span>');
    $('#birthdayDiv').html('Birthday: <span id="birthdaySpan">' + previousBirthday + '</span>');
    $('#countryDiv').html('Country: <span id="countrySpan">' + previousCountry + '</span>');
    $('#cityDiv').html('City: <span id="citySpan">' + previousCity + '</span>');
    $('#submitCancelEdit').remove();
    $('#submitProfileError').remove();
    $('div.editProfileText').show();
}

function SubmitEditedProfileDetails()
{
    var bio = $('#bioInput').val();
    var birthday = $('#birthdayInput').val();
    var country = $('#countryInput').val();
    var city = $('#cityInput').val();
    $.post("ajax/editdetailedprofile.php", {bio : bio, birthday : birthday, country : country, city : city}, function(data) {
		if (data.length > 0)
		{
			if (data == "SUCCESS")
			{
				previousBio = bio;
				previousBirthday = birthday;
				previousCountry = country;
				previousCity = city;
				CancelEditProfileDetails();
			}
			else
			{
				$('#submitProfileError').remove();
				$('#profileDetails').append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">An error has occurred. Please try again.</div>');
			}
		}
		else
		{
		    $('#submitProfileError').remove();
			$('#profileDetails').append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">Unable to connect to the server. Please make sure that you are connected to the internet and try again.</div>');
		}
    });
}

function OpenControlPanel(panelName)
{
    var direction = "right";
    if (openedControlPanel != "#none")
    {
        if (panelName == "#myAccount")
            direction = "left";
        else if (panelName == "#mySocial" && openedControlPanel == "#myGames")
            direction = "left";
        $(openedControlPanel).hide("drop", { direction: (direction == "right" ? "left" : "right") }, 500);
        if (openedControlPanel == panelName)
            $(panelName).slideUp(500);
        else
        	$(panelName).show("drop", { direction: direction }, 500);
        $(openedControlPanel + 'Button').css("background-color", "transparent");
        $(openedControlPanel + 'Button').attr("onclick", "OpenControlPanel('" + openedControlPanel + "');");
    }
    else
    	$(panelName).slideDown(500);
    $(panelName + 'Button').css("background-color", "#333333");
    $(panelName + 'Button').attr("onclick", "CloseControlPanel();");
    $(panelName).text("Loading...");
    switch (panelName)
    {
        case "#myAccount":
            $(panelName).load("ajax/accountsettings.php");
        case "#mySocial":
            $(panelName).load("ajax/socialsettings.php");
        case "#myGames":
            $(panelName).load("ajax/gamessettings.php");
    }
    openedControlPanel = panelName;
}

function CloseControlPanel()
{
    $(openedControlPanel).slideUp(400, function() {
        // Here we must implement a cache system or something...
		$(openedControlPanel).html("");
    });
    $(openedControlPanel + 'Button').css("background-color", "transparent");
    $(openedControlPanel + 'Button').attr("onclick", "OpenControlPanel('" + openedControlPanel + "');");
    openedControlPanel = "#none";
}

function SwitchFriendOptionsMenu(event)
{
    var node = event.srcElement.parentElement.parentElement.parentElement.children[1];
    if ($(node).is(':hidden'))
    {
        $('.friendPanelOptions').slideUp();
        $('.friendHeader').mouseleave(function(event) {
    		$(event.srcElement.children[1].children[0]).hide();
    	});
    	$('img#moreOptionsImg').hide();
    	$(event.srcElement).show();
        $(node).slideDown();
        $(event.srcElement.parentElement.parentElement).off('mouseleave');
    }
    else
    {
        try
        {
            $('.friendHeader').mouseleave(function(event) {
                try {
        			$(event.srcElement.children[1].children[0]).hide();
                }
                catch(e) {
                    $('img#moreOptionsImg').hide();
                }
        	});
        }
        catch(e)
        {
            alert("Error");
        }
    	$(node).slideUp();
    }
}

$(document).ready(function() {
	$("a#friendRequests").fancybox();
	$("a#removeFriend").fancybox();
	$("a#sendPrivateMessage").fancybox();
	<?php if ($isOwner) echo '$("a#changeAvatar").fancybox();'; ?>
	$('#profileDetails').hide();
	$('div.controlPanel').hide();
	$('.friendPanelOptions').hide();
	$('img#moreOptionsImg').click(SwitchFriendOptionsMenu);
	$('img#moreOptionsImg').hide();
	$('.friendHeader').mouseenter(function(event) {
		try {
			$(event.srcElement.children[1].children[0]).show();
		}
		catch(e) {
		    $('img#moreOptionsImg').hide();
		}
	});
	$('.friendHeader').mouseleave(function(event) {
		try {
			$(event.srcElement.children[1].children[0]).hide();
		}
		catch(e) {
			$('img#moreOptionsImg').hide();
		}
	});
	$('#addNewFriend').load('ajax/friendsfinder.html');
	openedControlPanel = "#none";
	FadeIn();
});
</script>
</head>
<body>
<?php PrintTopBar($user); ?>
<div id ="myFriendsPanel" class="myFriendsPanel">
	<div id="friendWrapper" class="friendWrapper">
		<div id="friendHeader" class="friendHeader" style="border-top-right-radius:0.5em; border-top-left-radius:0.5em; background-color:#333333; border-bottom:1px #FFFFFF solid;">
    		<div class="friendName"><a class="friendSpaceLink">Add New Friend</a></div>
    		<div class="plusImg"><img id="moreOptionsImg" src="images/more_info_large.png" style="height:30px; width:30px;" /></div>
		</div>
		<div id="addNewFriend" class="friendPanelOptions" style="margin-bottom:5px;">
		</div>
	</div>
	<?php
	$friendsList = $user->GetAllFriendsByUsername();
    if ($friendsList === USER_HAS_NO_FRIENDS)
        echo '    <div id="friendWrapper" class="friendWrapper" style="text-align:center;">You have no friends</div>', "\n";
    elseif ($friendsList === false)
        echo '    <div id="friendWrapper" class="friendWrapper" style="text-align:center;">An error occurred. Please try again in a few moments.</div>', "\n";
    else
    {
        foreach ($friendsList as $i => $value)
        {
    ?>
    <div id="friendWrapper" class="friendWrapper">
		<div id="friendHeader" class="friendHeader">
    		<div class="friendName"><img src="images/<?php echo ($friendsList[$i][1] ? "friend_online" : "friend_offline"); ?>.png" /><a class="friendSpaceLink" href="/<?php echo $friendsList[$i][0]; ?>"><?php echo $friendsList[$i][0]; ?></a></div>
    		<div class="plusImg"><img id="moreOptionsImg" src="images/more_info_large.png" style="height:30px; width:30px;" /></div>
		</div>
		<div class="friendPanelOptions">
			<div class="friendOption">Invite to chat</div>
			<div class="friendOption">Invite to LiveStream</div>
			<div class="friendOption"><a id="sendPrivateMessage" href="ajax/privatemessage.php?friendName=<?php echo $friendsList[$i][0]; ?>" style="text-decoration:none; color:#FFFFFF;">Send private message</a></div>
			<div class="friendOptionRemove"><a id="removeFriend" href="ajax/removefriendconfirmation.php?friendName=<?php echo $friendsList[$i][0]; ?>" style="text-decoration:none; color:#FFFFFF;">Remove friend</a></div>
		</div>
	</div>
	<?php
        }
    } 
    ?>
</div>
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
		<div class="latestNews">
			<br/><br/><br/><br/><br/><br/><br/>-- The latest news in real-time about your friends, clans, games... --
		</div>
		<div class="customAdvert">
			<br/>-- An advertisement may be? --<br/><br/>
		</div>
	</div>
</div>
<!--
<div id="friendsTab" class="a {title:'My friends'}">
    <div id="newFriend" class="voice {panel: 'ajax/friendsfinder.html'}"><span class="label">Add New Friend +</span></div>
    <?php 
    /*$friendsList = $user->GetAllFriendsByUsername();
    if ($friendsList === USER_HAS_NO_FRIENDS)
        echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">You have no friends</a></span></div>', "\n";
    elseif ($friendsList === false)
        echo '    <div id="noFriends" class="voice {}"><span class="label"><a class="label">Error retrieving your friends.</a></span></div>', "\n";
    else
    {
        foreach ($friendsList as $i => $value)
            echo '    <div id="friend" class="voice {panel: \'core/friends/friendmenutab.php?friendName='. $friendsList[$i][0] .'\'}"><span class="label"><img src="images/'. ($friendsList[$i][1] ? "friend_online" : "friend_offline") .'.png" style="margin-top:3px;"/><a class="label" href="../', $friendsList[$i][0], '">', $friendsList[$i][0], '</a></span></div>', "\n";
    }*/
    ?>
</div>
<div id="clansTab" class="a {title:'My Clans'}">
<?php
// Clans system is not yet implemented
//for ($i = 1; $i < 5; ++$i)
//    echo '    <div id="clan" class="voice {panel: \'core/clans/clansmenutab.php\'}"><span class="label"><a class="label">Clan', $i, '</a></span></div>', "\n";
?>
</div>
-->
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
</body>
</html>