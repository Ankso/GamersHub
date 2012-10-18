<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

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
	padding:10px;
	margin-left:40%;
	margin-right:40%;
	margin-top:200px;
	text-align:center;
	border:2px #FFFFFF solid;
	border-radius:1em;
}
input {
	border-radius:0.3em;
}
</style>
</head>
<body>
<?php PrintTopBar(NULL); ?>
<div class="register">
<?php
function PrintForm()
{
    echo "<div style=\"text-align:center;\"><form action=\"register.php\" method=\"post\">";
    echo "    <br/>Username: <input type=\"text\" name=\"username\" style=\"margin-left:53px;\"/>";
    echo "    <br/>E-mail: <input type=\"text\" name=\"email\" style=\"margin-left:81px;\"/>";
    echo "    <br/>Password: <input type=\"password\" name=\"password\" style=\"margin-left:57px;\"/>";
    echo "    <br/>Retype password: <input type=\"password\" name=\"password_check\" style=\"margin-left:5px;\"/>";
    echo "    <br/>Private key*: <input type=\"text\" name=\"key\" style=\"margin-left:39px;\"/>";
    echo "    <br/><input type=\"submit\" name=\"user_register\" style=\"margin-top:7px;\"/>";
    echo "</form></div>";
    echo "<br/>*The private key is a combination of 32 alphanumeric characters used to avoid unauthorized access to the webpage. You can obtain one contacting the site <a href=\"mailto:misterankso@gmail.com\">admin</a>.";
}

if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_check']) && isset($_POST['key']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordCheck = $_POST['password_check'];
    $privateKey = $_POST['key'];

    // Check if both passwords are similar
    if ($password != $passwordCheck)
        die("The passwords don't match! <a href=\"/register.php\">Try again</a>.");

    $DB = New Database($DATABASES['USERS']);
    // Check that the private key is a valid one
    if ($result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_PRIVATE_KEY, $DB->BuildStmtArray("s", $privateKey)))
    {
        // If a coincidence is found, delete that private key from the DB
        if ($result->num_rows > 0)
            $DB->ExecuteStmt(Statements::DELETE_USER_DATA_PRIVATE_KEY, $DB->BuildStmtArray("s", $privateKey));
        else
            die("Invalid private key. If you want one, you must contact the site <a href=\"mailto:misterankso@gmail.com\">admin</a>. <a href=\"/register.php\">Try again</a>.");
    }
    else
    {
        // The private key is invalid
        die("Invalid private key. If you want one, you must contact the site <a href=\"mailto:misterankso@gmail.com\">admin</a>. <a href=\"/register.php\">Try again</a>.");
    }
    // Check if the username or the email were already used
    if ($result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_REGISTER, $DB->BuildStmtArray("ss", $username, $email)))
    {
        if ($row = $result->fetch_assoc())      // If a coincidence was found in the DB, print the errors
        {
            if ($row["username"] == $username)
                echo "That username is already in use. <a href=\"/register.php\">Try again</a>.";
            else
                echo "That email is already in use. <a href=\"/register.php\">Try again</a>.";
        }
        else                                    // else insert the data in the DB
        {
            $ip = $_SERVER['REMOTE_ADDR'];
            // Simple control var to ensure that no errors are triggered while the user is being created.
            $allOk = true;
            $data = NULL;
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
                $data = $DB->BuildStmtArray("sssssssis", $username, CreateSha1Pass($username, $password), NULL, NULL, $email, NULL, $ip, 0, "1000-01-01 00:00:00");
            else
                $data = $DB->BuildStmtArray("sssssssis", $username, CreateSha1Pass($username, $password), NULL, NULL, $email, $ip, NULL, 0, "1000-01-01 00:00:00");
            
            // Here we start the DB operations
            if ($DB->ExecuteStmt(Statements::INSERT_USER_DATA, $data))
            {
                // Now we can initialize the User object. Note that this is for obtain the user ID to create the rows in user_detailed_data and user_privacy tables.
                $user = new User($username);
                // Begin the transaction and insert the data. This is to create all the rows in the related tables of the users Database.
                $DB->BeginTransaction();
                if ($DB->ExecuteStmt(Statements::INSERT_USER_DETAILED_DATA, $DB->BuildStmtArray("issssss", $user->GetId(), NULL, NULL, NULL, NULL, "/images/default_avatar.png")))
                {
                    if ($DB->ExecuteStmt(Statements::INSERT_USER_PRIVACY, $DB->BuildStmtArray("iiii", $user->GetId(), 1, 1, 1)))
                    {
                        if ($DB->ExecuteStmt(Statements::INSERT_USER_CUSTOM_OPTIONS, $DB->BuildStmtArray("iiii", $user->GetId(), 0, 0, 1)))
                        {
                            $DB->CommitTransaction();
                            echo "User created successfully! You can now <a href=\"login.php\">log in</a>";
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
            // If an error(s) has happen, we must rollback the DB transactions
            if (!$allOk)
            {
                $DB->RollbackTransaction();
                if (isset($user))
                    $DB->ExecuteStmt(Statements::DELETE_USER_DATA, $DB->BuildStmtArray("i", $user->GetId()));
                echo "An error occurred. Please, try again in a few moments. <a href=\"/register.php\">Try again</a>.";
            }
        }
    }
    else
        die("Error connecting to the database. <a href=\"/register.php\">Try again</a>.");
}
else
    PrintForm();
?>
</div>
</body>
</html>