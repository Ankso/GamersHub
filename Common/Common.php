<?php
require_once("../Common/SharedDefines.php");
require_once("../Classes/Database.Class.php");

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
 * Gets a username from a user's ID without creating a full user object. Note that the function doesn't checks if the ID exists, so the ID _must_ be valid.
 * @param long $id
 */
function GetUsernameFromId($id)
{
    global $DATABASES, $SERVER_INFO;
    $DB = New Database($SERVER_INFO['USERS']);
    $result = $DB->Execute("SELECT username FROM user_data WHERE id = ". $id);
    if ($result)
    {
        $row = mysql_fetch_assoc($result);
        return $row['username'];
    }
    return false;
}
?>