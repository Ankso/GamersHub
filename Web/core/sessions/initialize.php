<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");

session_start();

// If user is already loged in and tries access this page, redirect him to his or her main page
if (isset($_SESSION['user']))
    header("location:../../". $_SESSION['user']->GetUsername());
else
{
    // If the user access directly to this page, we can redirect him to the login page
    if (isset($_POST['username']) && isset($_POST['password']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($username === "" || $password === "")
            die("INCORRECT");

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
    	            $_SESSION['user'] = $user;
    	            echo "SUCCESS";
    	        }
    	        else
    	            echo "INCORRECT";
    	    }
    	    else
    	        echo "INCORRECT";
    	}
    	else
    	    echo "FAILED";
    }
    else
        header("location:../../login.php");
}
?>