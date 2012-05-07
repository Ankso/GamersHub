<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../Common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../Classes/Database.Class.php");

/**
 * Encripts the password using the username as modifier.
 * @param string $username The user's username
 * @param string $password The user's password decripted
 */
function CreateSha1Pass ($username, $password)
{
    return sha1($username . ":" . $password);
}

/**
 * Gets a username from a user's ID without creating a full user object.
 * @param long $id
 * @return string Returns a string with the username, USER_DOESNT_EXISTS if no result or false if something fails.
 */
function GetUsernameFromId($id)
{
    global $DATABASES, $SERVER_INFO;
    $DB = new Database($DATABASES['USERS']);
    $result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_USERNAME, $DB->BuildStmtArray("i", $id));
    if ($result)
    {
        if($row = $result->fetch_assoc())
            return $row['username'];
        return USER_DOESNT_EXISTS;
    }
    return false;
}

/**
 * Gets a ID from a user's username without creating a full user object.
 * @param string $username
 * @return long Returns a long integer representing the user's ID, USER_DOESNT_EXISTS if no result or false if something fails.
 */
function GetIdFromUsername($username)
{
    global $DATABASES, $SERVER_INFO;
    $DB = new Database($DATABASES['USERS']);
    $result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_ID, $DB->BuildStmtArray("s", $username));
    if ($result)
    {
        if ($row = $result->fetch_assoc())
            return $row['id'];
        return USER_DOESNT_EXISTS;
    }
    return false;
}

function PrintTopBar()
{
    echo '<div id="topbar"></div>';
}

?>