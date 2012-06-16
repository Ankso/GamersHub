<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
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

// If user is already loged in and tries access this page, redirect him to his or her main page
if (isset($_SESSION['userId']))
    header("location:../../". GetUsernameFromId($_SESSION['userId']));
else
{
    $loginResult = array('status' => "");
    // If the user access directly to this page, we can redirect him to the login page
    if (isset($_POST['username']) && isset($_POST['password']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($username === "" || $password === "")
        {
            $loginResult['status'] = "INCORRECT";
            die(json_encode($loginResult));
        }

    	$DB = new Database($DATABASES['USERS']);
    	if (($result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_LOGIN, $DB->BuildStmtArray("s", $username))))
    	{
    	    if ($row = $result->fetch_assoc())            // If we have a coincidence...
    	    {
    	        if ($row['password_sha1'] === CreateSha1Pass($username, $password))    // The passwords match
    	        {
    	            // Create the user object
    	            $user = new User($username);
    	            $user->SetOnline(true);
    	            $user->GenerateRandomSessionId();
    	            $loginResult['sessionId'] = $user->GetRandomSessionId();
    	            $_SESSION['userId'] = $user->GetId();
    	            $loginResult['userId'] = $user->GetId();
    	            $loginResult['status'] = "SUCCESS";
    	            echo json_encode($loginResult);
    	        }
    	        else
    	        {
    	            $loginResult['status'] = "INCORRECT";
    	            die(json_encode($loginResult));
    	        }
    	    }
    	    else
    	    {
    	        $loginResult['status'] = "INCORRECT";
    	        die(json_encode($loginResult));
    	    }
    	}
    	else
    	{
    	    $loginResult['status'] = "FAILED";
    	    die(json_encode($loginResult));
    	}
    }
    else
        header("location:../../login.php");
}
?>