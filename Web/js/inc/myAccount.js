/**
 * Functions for the account configuration menu.
 */

function MyAccountMenuOptionClick(event)
{
    $("div.myAccountTab").hide();
    $("div.myAccountMenuOption").attr("style", "");
    $("span.myAccountSubmitResult").text("");
    $(event.target).css("background-color", "rgba(255, 122, 0, 1)");
    $(event.target).css("padding-top", "6px");
    $(event.target).css("padding-bottom", "6px");
    $(event.target).css("border-top", "1px rgb(255, 122, 0) solid");
    $(event.target).css("border-bottom", "1px rgb(255, 122, 0) solid");
    switch ($(event.target).attr("id"))
    {
        case "myAccountOptionBasic":
            $("#myAccountBasic").show();
            break;
        case "myAccountOptionPrivacy":
            $("#myAccountPrivacy").show();
            break;
        case "myAccountOptionCustomization":
            $("#myAccountCustomization").show();
            break;
        case "myAccountOptionSecurity":
            $("#myAccountSecurity").show();
            break;
        default:
            break;
    }
}

function SubmitBasicChanges()
{
    var email = $("#email").val();
    var password = $("#password").val();
    var passwordCheck = $("#passwordCheck").val();
    if (password != "" && password !== passwordCheck)
    {
        $("#myAccountSubmitResult").css("color", "#FF0000");
        $("#myAccountSubmitResult").text("The passwords don't match");
        return;
    }
    $("#myAccountSubmitResult").css("color", "#FFFFFF");
    $("#myAccountSubmitResult").text("Sending...");
    $.post("core/sessions/modify.php", {email : email, newPassword : password, newPasswordCheck : passwordCheck}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $("#myAccountSubmitResult").css("color", "#00FF00");
                $("#myAccountSubmitResult").text("Account updated successfully!");
            }
            else
            {
                $("#myAccountSubmitResult").css("color", "#FF0000");
                $("#myAccountSubmitResult").text("An error occurred. Please make sure that the passwords match and try again.");
            }
        }
        else
        {
            $("#myAccountSubmitResult").css("color", "#FF0000");
            $("#myAccountSubmitResult").text("An error occurred. Please make sure that you are connected to the internet.");
        }
    });
}

function SubmitPrivacyChanges()
{
    var email = $("input:radio[name=email]:checked").val();
    var profileDetails = $("input:radio[name=profileDetails]:checked").val();
    var liveStream = $("input:radio[name=liveStream]:checked").val();
    $("span.myAccountSubmitResult").css("color", "#FFFFFF");
    $("span.myAccountSubmitResult").text("Sending...");
    $.post("core/sessions/privacy.php", {email : email, profileDetails : profileDetails, liveStream : liveStream}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $("span.myAccountSubmitResult").css("color", "#00FF00");
                $("span.myAccountSubmitResult").text("Privacy settings updated successfully!");
            }
            else
            {
                $("span.myAccountSubmitResult").css("color", "#FF0000");
                $("span.myAccountSubmitResult").text("An error occurred. Please try again in a few moments.");
            }
        }
        else
        {
            $("span.myAccountSubmitResult").css("color", "#FF0000");
            $("span.myAccountSubmitResult").text("An error occurred. Please make sure that you are connected to the internet.");
        }
    });
}

function SubmitCustomizationChanges()
{
    var liveStream = ($("input:checkbox[name=liveStream]").is(":checked") ? 1 : 0);
    var liveStreamComments = ($("input:checkbox[name=liveStreamComments]").is(":checked") ? 1 : 0);
    var latestNews = ($("input:checkbox[name=latestNews]").is(":checked") ? 1 : 0);
    $("span.myAccountSubmitResult").css("color", "#FFFFFF");
    $("span.myAccountSubmitResult").text("Sending...");
    $.post("core/sessions/customization.php", {liveStream : liveStream, liveStreamComments : liveStreamComments, latestNews : latestNews}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $("span.myAccountSubmitResult").css("color", "#00FF00");
                $("span.myAccountSubmitResult").text("Customization settings updated successfully!");
            }
            else
            {
                $("span.myAccountSubmitResult").css("color", "#FF0000");
                $("span.myAccountSubmitResult").text("An error occurred. Please try again in a few moments.");
            }
        }
        else
        {
            $("span.myAccountSubmitResult").css("color", "#FF0000");
            $("span.myAccountSubmitResult").text("An error occurred. Please make sure that you are connected to the internet.");
        }
    });
}