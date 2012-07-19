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