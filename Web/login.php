<?php
require_once("../Classes/Database.Class.php");
require_once("../Classes/User.Class.php");
require_once("../Common/SharedDefines.php");
require_once("../Common/Common.php");

function PrintForm()
{
    echo "<form action=\"login.php\" method=\"post\">";
	echo "    <br><label class=\"lblInput\">Username: </label><br/><input type=\"text\" name=\"username\" class=\"input\">";
	echo "    <br><label class=\"lblInput\">Password: </label><br/><input type=\"password\" name=\"password\" class=\"input\">";
	echo "    <br><input type=\"submit\" value=\"Login\">";
	echo "</form>";
}

session_start();

// If user is already loged in, redirect to his or her main page
if (isset($_SESSION['user']))
    header("location:". $_SESSION['user']->GetUsername());
else
{
    if ($_POST)
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $query = "SELECT username, password_sha1 FROM user_data WHERE username = '". $username ."'";
    	$DB = New Database($DATABASES['USERS']);
    	if ($result = $DB->Execute($query));
    	{
    	    if ($row = mysql_fetch_assoc($result))            // If we have a coincidence...
    	    {
    	        if ($row['password_sha1'] === CreateSha1Pass($username, $password))    // The passwords match
    	        {
    	            // Create the user object
    	            $user = New User($username);
    	            $user->SetOnline(true);
    	            $_SESSION['user'] = $user;
    	            header("location:". $user->GetUsername());
    	        }
    	    }
    	}
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>GamersNet - Login</title>
<style type="text/css">
body {
	font:17px Calibri;
	background-color:#000000;
}

.login {
	text-align:center;
	position:relative;
	margin-top:17%;
	margin-left:41%;
	margin-right:41%;
	width:18%;
	border:2px solid #FFFFFF;
	border-radius:1em;
}

.lblInput {
	color: #FFFFFF;
}

.input {
	text-align:center;
	border-radius:1em;
}

.newAccount {
	color:#FFFFFF;
}
</style>
</head>
<body>
<div class="login">
<?php
PrintForm();
if ($_POST)
{
    echo "Incorrect username or password";
}
?>
<p class="newAccount">You don't have an account? Create it <a href="register.php">here</a>!</p>
</div>
</body>
</html>