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

/**
 * [DEPRECATED]<p>Creates a new user's "space".</p><p>Technically, the user's Space is the folder in the root directory were the user has his personal page.</p>
 * @param string $username The user's username
 */
function CreateUserSpace ($username)
{
    // Create new directory for the new user
    $AllOk = mkdir($username, 744);
    // Check if the directory has been sucessfully created to continue
    if ($AllOk)
    {
        // Create the user index.php file
        $file = fopen($username ."/index.php", "w");
        if ($file !== false)
        {
            fwrite($file, "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\">\n");
            fwrite($file, "<html>\n");
            fwrite($file, "<head>\n");
            fwrite($file, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Cp1252\">\n");
            fwrite($file, "<title>GamersNet - Login</title>\n");
            fwrite($file, "</head>\n");
            fwrite($file, "<body>\n");
            fwrite($file, "<div align=\"Center\">\n");
            fwrite($file, "<div align=\"center\">Welcome ". $username ." to your page!");
            fwrite($file, "<p><a href=\"../logout.php\">Logout</a></p></div>\n");
            fwrite($file, "</body>\n");
            fwrite($file, "</html>\n");
            fclose($file);
        }
        else
            $AllOk = false;
    }
    return $AllOk;
}
?>