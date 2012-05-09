<?php
require_once("../Classes/User.Class.php");
require_once("../Common/Common.php");

session_start();
// If user is already loged in, redirect to his or her main page
if (isset($_SESSION['user']))
    header("location:". $_SESSION['user']->GetUsername());
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
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
	margin-left:40%;
	margin-right:40%;
	width:360px;
	border:2px solid #FFFFFF;
	border-radius:1em;
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

function LoginClick(event)
{
	event.preventDefault();
	linkLocation = this.href;
	$("body").fadeOut(1000, function() {window.location = linkLocation;});
}

function Redirect()
{
    window.location = linkLocation;
}

function SetLoginTopMargin()
{
    var htop = ($(window).height() - 250) / 2;
    $('div.login').css('margin-top', htop.toString() + 'px')
    setTimeout("SetLoginTopMargin()", 100);
}

function SendLogin(event)
{
	var userName = $('.inputUser').val();
	var password = $('.inputPass').val();
	if (userName == '' || password == '')
		$('#loginError').text("You must fill both username and password fields!");
	else
	{
    	$.post("core/sessions/initialize.php", {username: userName, password: password}, function(data) {
    		if (data.length > 0)
    		{
    			if (data == "SUCCESS")
    			    $('body').fadeOut(1000, function() {window.location = '/' + userName;});
    			else if (data == "INCORRECT")
    				$('#loginError').text("Incorrect username or password");
    			else if (data == "FAILED")
    				$('#loginError').text("Error connecting to the login server, please try again soon");
    		}
    		else
    			$('#loginError').text("Unknown error, please try again soon.");
    	});
	}
}
$(document).ready(FadeIn);
</script>
</head>
<body>
<?php PrintTopBar(NULL); ?>
<div class="login">
<form>
	<br><label class="lblInput">Username: </label><br/><input type="text" class="inputUser">
	<br><label class="lblInput">Password: </label><br/><input type="password" class="inputPass">
</form>
<div class="loginButton" onclick="SendLogin(event);">Connect</div>
<span id="loginError"></span>
<p class="newAccount">You don't have an account? Create it <a href="register.php">here</a>!</p>
</div>
</body>
</html>