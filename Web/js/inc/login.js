/**
 * Functions used in the login process and for improve overall design.
 */

function FadeIn()
{
    SetLoginTopMargin();
    $("body").css("display", "none");
    $("body").fadeIn(2000);
}

function SetLoginTopMargin()
{
    var htop = ($(window).height() - 250) / 2;
    $("div.login").css("margin-top", htop.toString() + "px");
    setTimeout("SetLoginLeftMargin()", 100);
}

function SetLoginLeftMargin()
{
    var wleft = ($(window).width() - 360) / 2;
    $("div.login").css("margin-left", wleft.toString() + "px");
    setTimeout("SetLoginTopMargin()", 100);
}

function SendLogin()
{
    var userName = $(".inputUser").val();
    var password = $(".inputPass").val();
    if (userName == "" || password == "")
        $("#loginError").text("You must fill both username and password fields!");
    else
    {
        $("#loginError").css("color", "#00FF00");
        $("#loginError").text("Connecting to server...");
        $.post("core/sessions/initialize.php", {username: userName, password: password}, function(data) {
            if (data)
            {
                switch (data.status)
                {
                    case "SUCCESS":
                        $("#loginError").text("Connecting to real time server...");
                        ConnectToRealTimeServer(data, userName);
                        break;
                    case "ALREADY_LOGGED_IN":
                        $("#loginError").css("color", "#FF6633");
                        $("#loginError").text("You're logged in another location, closing your other session...");
                        ConnectToRealTimeServer(data, userName);
                        break;
                    case "INCORRECT":
                        $("#loginError").css("color", "#FF0000");
                        $("#loginError").text("Incorrect username or password");
                        break;
                    case "FAILED":
                        $("#loginError").css("color", "#FF0000");
                        $("#loginError").text("Error connecting to the login server, please try again soon");
                        break;
                    default:
                        $("#loginError").css("color", "#FF0000");
                        $("#loginError").text("Error connecting to the main server, please try again soon");
                        break;
                }
            }
            else
            {
                $("#loginError").css("color", "#FF0000");
                $("#loginError").text("Unknown error, please try again soon.");
            }
        }, "json");
    }
}

function ConnectToRealTimeServer(data, userName)
{
    $.ajax({
        dataType: "jsonp",
        data: "",
        url: "http://gamershub.no-ip.org:5124/login?userId=" + data.userId + "&sessionId=" + data.sessionId + "&callback=?",
        success: function(response) {
            switch (response.status)
            {
                case "SUCCESS":
                    $("#loginError").css("color", "#00FF00");
                    $("#loginError").text("Connection successful.");
                    setTimeout(function() {
                        $("body").fadeOut(1000, function() {
                            window.location = "/" + userName;
                        });
                    }, 500);
                    break;
                case "FAILED":
                    $("#loginError").css("color", "#FF6633");
                    $("#loginError").text("Connection to real time server failed.");
                    break;
                case "ALREADY_LOGGED_IN":
                    // This response is received when the RTS logs the user off because he or she
                    // was already logged in from another location. When this packet is received,
                    // the user has been already logged off, and we can try to log in again.
                    SendLogin();
                    break;
                default:
                    break;
            }
        },
        error: function() {
            $("#loginError").css("color", "#FF6633");
            $("#loginError").text("Connection to real time server failed.");
            setTimeout(function() {
                $("body").fadeOut(1000, function() {
                    window.location = "/" + userName;
                });
            }, 500);
        },
        timeout: 5000,
    });
}

function PasswordOnKeyDown(event)
{
    if (event.keyCode == 13)
        SendLogin();
}
