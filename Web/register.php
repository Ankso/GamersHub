<?php
require_once("../Common/SharedDefines.php");
require_once("../Common/Common.php");
require_once("../Classes/User.Class.php");
require_once("../Classes/Database.Class.php");

session_start();
// If the user is logged in, redirect to his or her folder
if (isset($_SESSION['user']))
    header("location:". $_SESSION['user']->GetUsername());
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>GamersNet - Register</title>
<link href="css/main.css" media="all" rel="stylesheet" type="text/css">
<style type="text/css">
.register {
	position:absolute;
	width:350px;
	margin-left:40%;
	margin-right:40%;
	margin-top:200px;
	text-align:center;
	border:2px #FFFFFF solid;
	border-radius:1em;
}
</style>
</head>
<body>
<?php PrintTopBar(NULL); ?>
<div class="register">
<?php
function PrintForm()
{
    echo "<form action=\"register.php\" method=\"post\">";
    echo "    <br/>Username: <input type=\"text\" name=\"username\"/>";
    echo "    <br/>E-mail: <input type=\"text\" name=\"email\"/>";
    echo "    <br/>Password: <input type=\"password\" name=\"password\"/>";
    echo "    <br/>Retype password: <input type=\"password\" name=\"password_check\"/>";
    echo "    <br/><input type=\"submit\" name=\"user_register\"/>";
    echo "</form>";
}

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_check']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordCheck = $_POST['password_check'];

    // Check if both passwords are similar
    if ($password != $passwordCheck)
    {
        PrintForm();
        echo "\nThe passwords don't match!";
        die();
    }

    $query = "SELECT username, email FROM user_data WHERE username = '". $username ."' OR email = '". $email ."'";
    $DB = New Database($DATABASES['USERS']);
    $result = $DB->Execute($query);
    if ($result)
    {
        $row;
        if ($row = $result->fetch_assoc())      // If a coincidence was found in the DB, print the errors
        {
            PrintForm();
            if ($row["username"] == $username)
                echo "\nThat username is already in use.";
            else
                echo "\nThat email is already in use.";
        }
        else                                        // else insert the data in the DB
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                $query = "INSERT INTO user_data (username, password_sha1, email, ip_v4, ip_v6) VALUES ('". $username ."','". CreateSha1Pass($username, $password) ."', '". $email ."',NULL,'". $ip ."')";
            else
                $query = "INSERT INTO user_data (username, password_sha1, email, ip_v4, ip_v6) VALUES ('". $username ."','". CreateSha1Pass($username, $password) ."', '". $email ."','". $ip ."',NULL)";
            if ($DB->Execute($query))
                echo "\nUser created successfully! You can now <a href=\"login.php\">log in</a>";
            else
                die(mysql_error());
        }
    }
    else
        die(mysql_error());
}
else
    PrintForm();
?>
</div>
</body>
</html>