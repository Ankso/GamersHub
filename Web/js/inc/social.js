/**
 * Functions for the social menu.
 */

function SocialMenuOptionClick(event)
{
    $('div.socialTab').hide();
    $('div.socialMenuOption').attr("style", "");
    if ($(event.target).attr("id") != "socialOptionClans")
    {
        $(event.target).css("background-color", "rgb(255, 122, 0)");
        $(event.target).css("border-top", "1px rgb(255, 122, 0) solid");
        $(event.target).css("border-bottom", "1px rgb(255, 122, 0) solid");
    }
    else
    {
        $(event.target).css("background-color", "rgb(22, 22, 210)");
        $(event.target).css("border-top", "1px rgb(22, 22, 210) solid");
        $(event.target).css("border-bottom", "1px rgb(22, 22, 210) solid");
    }
    $(event.target).css("padding-top", "6px");
    $(event.target).css("padding-bottom", "6px");
    switch($(event.target).attr("id"))
    {
        case "socialOptionFriends":
            $("div#socialFriends").show();
            break;
        case "socialOptionFriendRequests":
            $("div#socialFriendRequests").show();
            break;
        case "socialOptionPrivateMessages":
            $("div#socialPrivateMessages").show();
            break;
        case "socialOptionIgnoredList":
            $("div#socialIgnoredList").show();
            break;
        case "socialOptionClans":
            $("div#socialClans").show();
            break;
        default:
            break;
    }
}

function RemoveFriend(friendId)
{
    if (!friendId)
        return;
    
    $.post("core/friends/removefriend.php", { friendId: friendId }, function(data) {
        if (data == "SUCCESS")
        {
            $("div#socialFriendsError").text("");
            $("div#socialFriend" + friendId).parent().fadeOut(500);
        }
        else
            $("div#socialFriendsError").text("An error has occurred, please try again in a few seconds.");
    });
}

function HandleFriendRequest(requesterId, action)
{
    if (!requesterId || !action)
        return;
    
    $.post("core/friends/addfriend.php", { requesterId: requesterId, action: action }, function(data) {
        if (data == "SUCCESS")
        {
            $("div#socialFriendRequestsError").text("");
            if (action == "ACCEPT")
                $("div#socialManageRequest" + requesterId).html('<span class="socialAcceptFriendRequest">Request accepted!</span>');
            else
                $("div#socialManageRequest" + requesterId).html('<span class="socialDeclineFriendRequest">Request declined :(</span>');
            setTimeout(function() {
                $("div#socialFriendRequest" + requesterId).parent().parent().fadeOut(1500);
            }, 5000);
            // TODO: Here we must send a packet to the RTS with the new friend, for latest news section, etc,
            // and add the new friend to the friends panel in the user main window. By the way, the page must be reloaded.
        }
        else
            $("div#socialFriendRequestsError").text("An error has occurred, please try again in a few seconds.")
            
    });
}

function SocialLookup(friendname)
{
    $('#sent').css("display", "none");
    if(friendname.length < 3)
        $('#suggestionsBox').hide();
    else
    {
        $.post("../core/friends/friendsfinder.php", {nickname: friendname}, function(data) {
            if (data.length > 0)
            {
                $("div#suggestionsBox").show();
                $("div#suggestionsList").html(data);
            }
        });
    }
}

function SocialCheckSuggestionsBox(returnedData)
{
    if (returnedData.lenght > 0)
    {
        $('#suggestionsBox').show();
        $('#suggestionsList').html(returnedData);
    }
}

function SocialFill(thisValue)
{
    $('#friendname').val(thisValue);
    setTimeout("$('#suggestionsBox').hide();", 200);
}

function SocialSendFriendRequest()
{
    var friendName = $('#friendname').val();
    if (friendName === "")
    {
        $('#sent').text("You must write a valid friend name");
        $('#sent').css('background-color', "#FF0000");
        $('#sent').css('display', '');
        return;
    }
    var message = 'Request sent!';
    var bgColor = '#00CC00';
    $.post("../core/friends/sendfriendrequest.php", {username: friendName}, function(data) {
        if (data.length > 0)
        {
            if (data === 'FAILED')
            {
                message = 'There was an error sending your request. Please, try again soon.';
                bgColor = '#FF0000';
            }
            else if (data === 'USER_DOESNT_EXISTS')
            {
                message = 'User not found!';
                bgColor = '#FF0000';
            }
            else if (data === 'USER_IS_ALREADY_FRIEND')
            {
                message = friendName + " already is your friend!";
            }
            else if (data === 'REQUEST_ALREADY_SENT')
            {
                message = "You have already sent a friend request to that person"
                bgColor = '#FF0000';
            }
        }
        else
        {
            message = 'There was a fatal error connecting to the server.';
            bgColor = '#FF0000';
        }
        $('#sent').text(message);
        $('#sent').css('background-color', bgColor);
        $('#sent').css('display', '');
    });
}

function SocialMarkMessageAsReaded(event)
{
    // The message is marked as readed in the DB by the php script called with fancybox.
    var unreadedCount = parseInt($("span#socialPrivateMessagesUnreaded").attr("data-count"));
    $(event.target).removeClass();
    $(event.target).addClass("socialPrivateMessage");
    $("div#socialReadedMessagesContainer").prepend(event.target.parentElement.outerHTML);
    $(event.target.parentElement).remove();
    // Add fancybox to the new DOM element
    $("a#sendPrivateMessage").fancybox();
    // Remove the mail simbol from the friend list
    $("div#newPrivateMessage" + $(event.target).attr("data-username")).remove();
    // We must update the number in the left menu
    --unreadedCount;
    if (unreadedCount <= 0)
    {
        $("span#socialPrivateMessagesUnreaded").text("");
        $("span#socialPrivateMessagesUnreaded").attr("data-count", unreadedCount);
        $("div#socialTabItemUnreaded").append('<div class="socialSubTabItem">You have no unreaded messages.</div>');
    }
    else
    {
        $("span#socialPrivateMessagesUnreaded").text("(" + unreadedCount + ")");
        $("span#socialPrivateMessagesUnreaded").attr("data-count", unreadedCount);
    }
}