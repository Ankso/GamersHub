<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

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
// If user is already loged in, redirect to his or her main page
if (isset($_SESSION['userId']))
    header("location:". GetUsernameFromId($_SESSION['userId']));
else
    session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>GamersNet - Login</title>
<link href="css/main.css" media="all" rel="stylesheet" type="text/css">
<style type="text/css">
.login {
	text-align:center;
	color:#FFFFFF;
	position:absolute;
	margin-left:50%;
	margin-right:50%;
	width:360px;
	border:2px solid #444444;
	border-radius:1em;
	box-shadow:3px 0 5px #000000;
}

.lblInput {
	color:#FFFFFF;
}

.inputUser, .inputPass {
	text-align:center;
	border-radius:1em;
}

.loginButton {
	border:2px #333333 solid;
	background-color:#000000;
	border-radius:1em;
	width:80px;
	margin-left:140px;
}

.loginButton:hover {
	background-color:#222222;
	cursor:pointer;
}

#loginError {
	color:#FF0000;
}
</style>
<script type="text/javascript" src="js/inc/jquery.latest.js"></script>
<script type="text/javascript">
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
    			if (data.status == "SUCCESS")
    			{
        			$("#loginError").text("Connecting to real time server...");
    			    $.ajax({
    			        dataType: "jsonp",
    			        data: "",
    			        url: "http://127.0.0.1:5124/login?userId=" + data.userId + "&sessionId=" + data.sessionId + "&callback=?",
    			        success: function(response) {
							if (response.status == "SUCCESS")
								$("#loginError").text("Connection successful.");
							else
							{
								$("#loginError").css("color", "#FF6633");
							    $("#loginError").text("Connection to real time server failed.");
							}
							setTimeout(function() {
								$("body").fadeOut(1000, function() {
									window.location = "/" + userName;
								});
							}, 500);
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
        		else 
            	{
                	$("#loginError").css("color", "#FF0000");
                	if (data.status == "INCORRECT")
        				$("#loginError").text("Incorrect username or password");
        			else if (data.status == "FAILED")
        				$("#loginError").text("Error connecting to the login server, please try again soon");
        			else
        				$("#loginError").text("Error connecting to the main server, please try again soon");
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

function PasswordOnKeyDown(event)
{
    if (event.keyCode == 13)
        SendLogin();
}

$(document).ready(FadeIn);
</script>
</head>
<body>
<?php PrintTopBar(NULL); ?>
<div class="login">
<form>
	<br><label class="lblInput">Username: </label><br/><input type="text" class="inputUser">
	<br><label class="lblInput">Password: </label><br/><input type="password" class="inputPass" onkeydown="PasswordOnKeyDown(event);">
</form>
<div class="loginButton" onclick="SendLogin();">Connect</div>
<span id="loginError"></span>
<p class="newAccount">You don't have an account? Create it <a href="register.php">here</a>!</p>
</div>
</body>
</html>