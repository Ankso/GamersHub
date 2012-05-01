<?php
require_once("F:/GamersNet/GamersNet_Beta/Common/SharedDefines.php");
require_once("F:/GamersNet/GamersNet_Beta/Classes/Database.Class.php");

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
    $result = $DB->Execute("SELECT username FROM user_data WHERE id = ". $id);
    if ($result)
    {
        if($row = mysql_fetch_assoc($result))
            return $row['username'];
        return -1;
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
    $result = $DB->Execute("SELECT id FROM user_data WHERE username = '". $username ."'");
    if ($result)
    {
        if ($row = mysql_fetch_assoc($result))
            return $row['id'];
        return -1;
    }
    return false;
}
?>