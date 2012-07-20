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