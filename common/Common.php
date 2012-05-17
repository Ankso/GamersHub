<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");

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

/**
 * Prints the topbar for a specified user, or the default topbar if the user is not logged in
 * @param User $user The user class initilized, or NULL if the user is not logged in.
 */
function PrintTopBar($user)
{
    $isLoggedIn = (!is_null($user) && isset($user));
    // Create some vars with data for use in the topbar
    $friendRequestsCount;
    if ($isLoggedIn)
    {
        $friendRequestsCount = $user->GetFriendRequestsCount();
        if ($friendRequestsCount === false)
            $friendRequestsCount = "Unknown";
    }
    echo '<div id="topbar">', "\n";
	echo '    <div style="margin-right:20%; margin-left:20%; width:60%; height:51px; position:absolute;">', "\n";
	echo '        <div class="topbarLeft">', "\n";
	echo '            <div style="float:left; border-right:2px #333333 solid;"><a href="login.php"><img style="margin-right:10px;" src="images/blog_button.png" alt="Blog"/></a></div>', "\n";
	if ($isLoggedIn)
	{
    	echo '            <div id="topbarButton" style="float:right;">My games</div>', "\n";
    	echo '            <div id="topbarButton" style="float:right;">Social</div>', "\n";
    	echo '            <div id="topbarButton" style="float:right;">My account</div>', "\n";
	}
	echo '        </div>', "\n";
	echo '        <div class="topbarRight">', "\n";
	if ($isLoggedIn)
	{
	    if ($friendRequestsCount === 0)
	        echo '            <div id="topbarButton" class="newFriendRequests">&nbsp;No friend requests&nbsp;</div>', "\n";
	    elseif (is_integer($friendRequestsCount) && $friendRequestsCount > 0)
	        echo '            <div id="topbarButton" class="newFriendRequests">&nbsp;<a id="friendRequests" href="ajax/friendrequests.php">New friend requests!</a>&nbsp;</div>', "\n";
	    echo '            <div style="float:right; border-left:2px #333333 solid;height:51px; width:40px;"><a href="logout.php" onclick="FadeOut(event, \'logout.php\');"><img src="images/logout.png" height="30px" width="30px" alt="Logout" style="margin-top:10px; float:right;"/></a></div>', "\n";
	}
	echo '        </div>', "\n";
	echo '    </div>', "\n";
	echo '</div>', "\n";
}

?>