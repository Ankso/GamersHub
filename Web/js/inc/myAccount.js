/**
 * Functions for the account configuration menu.
 */

function MenuOptionClick(tabId)
{
    var tabName = "";
    var i = 0;
    $('div.myAccountTab').hide();
    $('div.myAccountMenuOption').attr("style", "");
    $('#myAccountSubmitResult').text("");
    switch (tabId)
    {
        case 0:
            tabName = "#myAccountOptionBasic";
            $('#myAccountBasic').show();
            break;
        case 1:
            tabName = "#myAccountOptionPrivacy";
            $('#myAccountPrivacy').show();
            break;
        case 2:
            tabName = "#myAccountOptionCustomization";
            $('#myAccountCustomization').show();
            break;
        case 3:
            tabName = "#myAccountOptionSecurity";
            $('#myAccountSecurity').show();
            break;
    }
    $(tabName).css("background-color", "rgba(22, 22, 210, 1)");
    $(tabName).css("padding-top", "6px");
    $(tabName).css("padding-bottom", "6px");
    $(tabName).css("border-top", "1px #0000FF solid");
    $(tabName).css("border-bottom", "1px #0000FF solid");
}

function SubmitBasicChanges()
{
    var email = $('#email').val();
    var password = $('#password').val();
    var passwordCheck = $('#passwordCheck').val();
    if (password != "" && password !== passwordCheck)
    {
        $('#myAccountSubmitResult').css("color", "#FF0000");
        $('#myAccountSubmitResult').text("The passwords don't match");
        return;
    }
    $('#myAccountSubmitResult').css("color", "#FFFFFF");
    $('#myAccountSubmitResult').text("Sending...");
    $.post("core/sessions/modify.php", {email : email, newPassword : password, newPasswordCheck : passwordCheck}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $('#myAccountSubmitResult').css("color", "#00FF00");
                $('#myAccountSubmitResult').text("Account updated successfully!");
            }
            else
            {
                $('#myAccountSubmitResult').css("color", "#FF0000");
                $('#myAccountSubmitResult').text("An error occurred. Please make sure that the passwords match and try again.");
            }
        }
        else
        {
            $('#myAccountSubmitResult').css("color", "#FF0000");
            $('#myAccountSubmitResult').text("An error occurred. Please make sure that you are connected to the internet.");
        }
    });
}

function SubmitPrivacyChanges()
{
    var email = $('input:radio[name=email]:checked').val();
    var profileDetails = $('input:radio[name=profileDetails]:checked').val();
    var liveStream = $('input:radio[name=liveStream]:checked').val();
    $('span.myAccountSubmitResult').css("color", "#FFFFFF");
    $('span.myAccountSubmitResult').text("Sending...");
    $.post("core/sessions/privacy.php", {email : email, profileDetails : profileDetails, liveStream : liveStream}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $('span.myAccountSubmitResult').css("color", "#00FF00");
                $('span.myAccountSubmitResult').text("Privacy settings updated successfully!");
            }
            else
            {
                $('span.myAccountSubmitResult').css("color", "#FF0000");
                $('span.myAccountSubmitResult').text("An error occurred. Please try again in a few moments.");
            }
        }
        else
        {
            $('span.myAccountSubmitResult').css("color", "#FF0000");
            $('span.myAccountSubmitResult').text("An error occurred. Please make sure that you are connected to the internet.");
        }
    });
}