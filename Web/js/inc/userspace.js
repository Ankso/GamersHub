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
var userAvatar;

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
                $('#commentsHistory').prepend("An error occurred, please try again in a few moments.");
        }
        else
            $('#commentsHistory').prepend("An error occurred while connecting to the server, please try again in a few moments.");
    });
}

function DeleteBoardComment(event)
{
    var messageId = $(event.srcElement.parentElement.parentElement).attr("data-id");
    if (!messageId)
        return;
    $.post("ajax/boardmessages.php", { messageId: messageId, spaceOwner: ownerId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                --totalMessages;
                $(event.srcElement.parentElement.parentElement).fadeOut(500);
            }
            else
                $('#commentsHistory').prepend("An error occurred, please try again in a few moments.");
        }
        else
            $('#commentsHistory').prepend("An error occurred while connecting to the server, please try again in a few moments.");
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
            if (data == "You haven't write anything yet. Start ASAP!" && !prepend)
                return;
            
            if (prepend)
                $("#commentsHistory").prepend(data);
            else
                $("#commentsHistory").append(data);
            $('div.deleteBoardComment').click(function(event) {
                DeleteBoardComment(event);
            });
            $('div.displayCommentBoardReplies').click(function(event) {
                if ($(event.srcElement).prev().is(":hidden"))
                {
                    $(event.srcElement).prev().slideDown(200);
                    $(event.srcElement).text("Hide Replies");
                }
                else
                {
                    $(event.srcElement).prev().slideUp(200);
                    $(event.srcElement).text("Show Replies");
                }
            });
            $('div.newReplyCommentBoardInputSend').click(function(event) {
                SendMessageBoardReply(event);
            });
            $('img.deleteBoardReply').click(function(event) {
                DeleteBoardCommentReply(event);
            });
        }
        else
            $("#commentsHistory").text("An error occurred while connecting to the server. Please try again in a few moments.");
    });
}

function SendMessageBoardReply(event)
{
   var reply = $(event.srcElement).prev().val();
   var messageId = $(event.srcElement).attr("data-id");
   
   $.post("ajax/boardreplies.php", {reply: reply, messageId: messageId}, function(data) {
       if (data.length > 0)
       {
           if (data == "SUCCESS")
           {
               // Add the new reply to the list
               var date = new Date();
               var newReply = '<div class="boardCommentReply" data-id="' + messageId + '">' + "\n";
               newReply = newReply + '<div class="deleteBoardComment" style="margin-top:4px;"><img src="images/delete_16.png" style="width:10px; height:10px;" /></div>' + "\n";
               newReply = newReply + '<div class="boardCommentReplyBody">' + "\n";
               newReply = newReply + '<div class="boardCommentReplyAvatar"><img src="' + userAvatar + '" style="width:40px; height:40px; border-radius:0.3em;" alt="avatar" /></div>' + "\n";
               newReply = newReply + '<div class="boardCommentReplyContent">' + reply + '</div>' + "\n";
               newReply = newReply + '</div>' + "\n";
               newReply = newReply + '<div class="boardCommentReplyBottom">By You ' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '</div>' + "\n";
               newReply = newReply + '</div>' + "\n";
               $(event.srcElement.parentElement.parentElement).prev().prepend(newReply);
           }
           else
               $(event.srcElement).prev().val("An error occurred while sending your reply. Please try again.");
       }
       else
           $(event.srcElement).prev().val("Connection to the server lost. Please try again in a few moments.");
   });
}

function DeleteBoardCommentReply(event)
{
    var replyId = $(event.srcElement.parentElement).attr("data-id");
    if (!replyId)
        return;
        
    $.post("ajax/boardreplies.php", {replyId: replyId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
                $(event.srcElement.parentElement).fadeOut(500);
        }
    });
}
/**
 * Welcome to GamersHub's Command Parser ™. This will be the main function to parse commands written by the users.
 * Commands must start with the character "/", the same character used in World of Warcraft chat system.
 * Almost all things in the web are going to be able to be done with commands. From start a window chat to delete a message board or remove a friend from the friends list.
 * @param string command A text string representing the command that must be executed.
 * @returns integer By the way, returns 0 if the command is executed successfully, or -1 if something fails. Later, we must add codes for each specific error (like syntax error, invalid params, etc...)
 */
function ParseCommand(command)
{
    if (command.length < 2)
        return -1;
    
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
            $('a#tempFancyBoxLink').remove();
            return 0;
        case "friend":
            if (!cmdParams[1] || !cmdParams[2])
                return -1;
            
            switch(cmdParams[1])
            {
                case "add":
                    // Do nothing by the way, the friend request system must be structured properly.
                    return 0;
                case "remove":
                    // Remove a friend system must be structured also...
                    // Create temp link to spawn fancybox:
                    $('body').append('<a id="tempFancyboxLink" href="ajax/removefriendconfirmation.php?friendName=' + cmdParams[2] + '" style="display:none"></a>')
                    $('a#tempFancyboxLink').fancybox();
                    $('a#tempFancyboxLink').trigger("click");
                    $('.commentInputTextBox').val("Something interesting to say?");
                    $('a#tempFancyBoxLink').remove();
                    return 0;
                default:
                    return -1;
            }
        default:
            return -1;
    }
}