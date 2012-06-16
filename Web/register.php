<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
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
// If the user is logged in, redirect to his or her folder
if (isset($_SESSION['userId']))
{
    header("location:". GetUsernameFromId($_SESSION['userId']));
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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

    $DB = New Database($DATABASES['USERS']);
    $result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_REGISTER, $DB->BuildStmtArray("ss", $username, $email));
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
        else                                    // else insert the data in the DB
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            $data;
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                $data = $DB->BuildStmtArray("sssssis", $username, CreateSha1Pass($username, $password), NULL, $email, NULL, $ip, 0, "1000-01-01 00:00:00");
            else
                $data = $DB->BuildStmtArray("sssssis", $username, CreateSha1Pass($username, $password), NULL, $email, $ip, NULL, 0, "1000-01-01 00:00:00");
            if ($DB->ExecuteStmt(Statements::INSERT_USER_DATA, $data))
            {
                // Now we can initialize the User object. Note that this is for obtain the user ID to create the rows in user_detailed_data and user_privacy tables.
                $user = new User($username);
                $allOk = true;
                // Begin the transaction and insert the data. This is to create all the rows in the related tables of the users Database.
                $DB->BeginTransaction();
                if ($DB->ExecuteStmt(Statements::INSERT_USER_DETAILED_DATA, $DB->BuildStmtArray("issss", $user->GetId(), NULL, NULL, NULL, NULL)))
                {
                    if ($DB->ExecuteStmt(Statements::INSERT_USER_PRIVACY, $DB->BuildStmtArray("iiii", $user->GetId(), 1, 1, 1)))
                    {
                        if ($DB->ExecuteStmt(Statements::INSERT_USER_AVATARS_PATH, $DB->BuildStmtArray("is", $user->GetId(), "/images/default_avatar.png")))
                        {
                            if ($DB->ExecuteStmt(Statements::INSERT_USER_CUSTOM_OPTIONS, $DB->BuildStmtArray("iiii", $user->GetId(), 0, 0, 1)))
                            {
                                $DB->CommitTransaction();
                                echo "\nUser created successfully! You can now <a href=\"login.php\">log in</a>";
                            }
                            else
                                $allOk = false;
                        }
                        else
                            $allOk = false;
                    }
                    else
                        $allOk = false;
                }
                else
                    $allOk = false;
            }
            if (!$allOk)
            {
                $DB->RollbackTransaction();
                $DB->ExecuteStmt(Statements::DELETE_USER_DATA, $DB->BuildStmtArray("i", $user->GetId()));
                echo "\nAn error occurred. Please, try again in a few moments.";
            }
        }
    }
    else
        die("\nError connecting to the database");
}
else
    PrintForm();
?>
</div>
</body>
</html>