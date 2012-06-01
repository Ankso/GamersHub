/**
 * User's space javascript functions
 */

var previousBio;
var previousBirthday;
var previousCountry;
var previousCity;
var openedControlPanel;
var totalMessages;
var lastLoadedComment;
var ownerId;

function PercentageWidthToPx(percentWidth)
{
    var totalWidth = $(window).width();
    return ($(window).width() / 100) * percentWidth;
}

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
        $(openedControlPanel).hide("slide", { direction: (direction == "right" ? "left" : "right") }, 500);
        if (openedControlPanel == panelName)
            $(panelName).slideUp(500);
        else
            $(panelName).show("slide", { direction: direction }, 500);
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

function ShowMyFriendsPanel()
{
    $('#myFriendsPanelFlapClosed').hide("slide", 150, function() {
        setTimeout("$('#myFriendsPanelFlapOpened').show();", 150);
        $('#myFriendsPanel').show("slide", 300);
    });
    $.cookie("FriendsPanel", "opened");
}

function CloseMyFriendsPanel()
{
    $('#myFriendsPanel').hide("slide", 300);
    $('#myFriendsPanelFlapOpened').hide("slide", 300, function() {
        $('#myFriendsPanelFlapClosed').show("slide", 150);
    })
    $.cookie("FriendsPanel", "closed");
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

function SendBoardComment(message)
{
    if (message.length == 0 || message.length > 255)
    {
        // Just do nothing
        return;
    }
    
    if (message.charAt(0) == "/")
    {
        // Board comments starting with "/" are going to be parsed as commands
        ParseCommand(message);
        return;
    }

    $.post("ajax/boardmessages.php", { message: message }, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                if (totalMessages == 0)
                    $('#commentsHistory').text("");
                ++totalMessages;
                LoadBoardComments(1, 1, true)
                $('.commentInputTextBox').val("Something interesting to say?");
            }
            else
                $('#commentsHistory').text("An error occurred, please try again in a few moments.");
        }
        else
            $('#commentsHistory').text("An error occurred while connecting to the server, please try again in a few moments.");
    });
}

function LoadBoardComments(from, to, prepend)
{
    if (prepend === null)
        prepend = false;
    lastLoadedComment = to;
    from = from - 1;
    to = to - 1;
    realFrom = totalMessages - to;
    realTo = totalMessages - from;
    $.post("ajax/boardmessages.php", { from: realFrom, to: realTo, spaceOwner: ownerId }, function(data) {
        if (data.length > 0)
        {
            if (prepend)
                $("#commentsHistory").prepend(data);
            else
                $("#commentsHistory").append(data);
            /*$('div.replyCommentBoard').hide();
            $('div.boardComment').mouseenter(function(event) {
                $(event.srcElement).next().stop(true, true).show();
            });
            $('div.boardComment').mouseleave(function(event) {
                $(event.srcElement).next().stop(true, true).hide();
            });
            $('div.replyCommentBoard').mouseenter(function(event) {
                $(event.srcElement).stop(true, true).show();
            });
            $('div.replyCommentBoard').mouseleave(function(event) {
                $(event.srcElement).stop(true, true).hide();
            });*/
        }
        else
            $("#commentsHistory").text("An error occurred while connecting to the server. Please try again in a few moments.");
    });
}

function ParseCommand(command)
{
    if (command.length < 2)
        return;
    
    var cmdParams = new Array();
    cmdParams = command.substring(1).split(" ");
    switch (cmdParams[0])
    {
        case "message":
        case "msg":
        case "m":
            // Send private message to target user
            // Check if the user has set the needed params
            if (!cmdParams[1])
                return -1;

            // Create temp link to spawn fancybox:
            $('body').append('<a id="tempFancyboxLink" href="ajax/privatemessage.php?friendName=' + cmdParams[1] + '" style="display:none"></a>');
            $('a#tempFancyboxLink').fancybox();
            $('a#tempFancyboxLink').trigger("click");
            $('.commentInputTextBox').val("Something interesting to say?");
            return 0;
       default:
           return;
    }
}

/*
function SendCommentReply(messageId)
{
    var reply = $(event.srcElement.parentElement.children[1].children[0]).val();
    if (reply == "")
        return;
    
    $.post("ajax/boardreplies.php", { messageId: messageId }, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                
            }
        }
    });
}

function LoadBoardCommentReplies(messageNumber)
{
    
}
*/