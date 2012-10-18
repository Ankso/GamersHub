<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

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
<link href="css/login.css" media="all" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/inc/jquery.latest.js"></script>
<script type="text/javascript" src="js/inc/login.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    FadeIn();
});
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