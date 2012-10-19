<?php
/**
 * Encripts the password using the username as modifier.
 * @param string $username The user's username
 * @param string $password The user's password decripted
 * @return string Returns a user's password encripted using the username as modifier with the format username:password
 */
function CreateSha1Pass ($username, $password)
{
    return sha1($username . ":" . $password);
}

/**
 * Gets a username from a user's ID without creating a full user object.
 * @param long $id The user's unique ID.
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
 * @param string $username The user's username.
 * @return long Returns a long integer representing the user's ID, USER_DOESNT_EXISTS if no result or false if something fails.
 */
function GetIdFromUsername($username)
{
    global $DATABASES;
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
 * [NOT IMPLEMENTED] Gets the avatar host path of a specific user without creating a full user class
 * @param long $userId The user's unique ID
 * @return mixed Returns a string representing the host path of the user's avatar, or false if something fails..
 */
function GetAvatarHostPathFromId($userId)
{
    // Not yet implemented
    return false;
}

/**
 * Prints the topbar for a specified user, or the default topbar if the user is not logged in
 * @param User $user The User class initilized, or NULL if the user is not logged in.
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
        // Note that the order of the buttons is inverted
        echo '            <div id="myGamesButton" class="topbarButton" style="float:right;">My games</div>', "\n";
        if ($friendRequestsCount === 0 && $user->GetUnreadPrivateMessagesCount() === 0)
            echo '            <div id="mySocialButton" class="topbarButton" style="float:right;">Social</div>', "\n";
        else
            echo '            <div id="mySocialButton" class="topbarButton" style="float:right;">Social<span id="socialNewsAdvert" style="font: color:#FF0000"><b>!</b></span></div>', "\n";
        echo '            <div id="myAccountButton" class="topbarButton" style="float:right;">My account</div>', "\n";
    }
    echo '        </div>', "\n";
    echo '        <div class="topbarRight">', "\n";
    if ($isLoggedIn)
    {
        echo '            <div style="float:right; border-left:2px #333333 solid;height:51px; width:40px;"><a id="topbarLogOffButton" href="logout.php"><img src="images/logout.png" height="30px" width="30px" alt="Logout" style="margin-top:10px; float:right;"/></a></div>', "\n";
    }
    echo '        </div>', "\n";
    echo '    </div>', "\n";
    echo '</div>', "\n";
}

/**
 * Gets the total number of online users based in the number of active PHP sessions.
 * @return mixed Returns an integer representing the number of online users, or false if something fails.
 */
function GetOnlineUsersCount()
{
    global $DATABASES;
    
    $sessionsDb = New Database($DATABASES['SESSIONS']);
    // We don't need a prepared statement here because there are no variables in the query
    if ($result = $sessionsDb->Execute("SELECT COUNT(*) FROM sessions"))
    {
        if ($row = $result->fetch_array(MYSQLI_NUM))
            return $row[0];
    }
    return false;
}

/**
 * Determines if an user is connected to the website right now.
 * @param long $userId The unique user identifier.
 * @return boolean Returns true if the user is online, else false. Note that the function also returns false if the ID is invalid or other problems.
 */
function IsUserOnline($userId)
{
    global $DATABASES;
    
    $usersDb = New Database($DATABASES['USERS']);
    if ($result = $usersDb->ExecuteStmt(Statements::SELECT_USER_IS_ONLINE, $usersDb->BuildStmtArray("i", $userId)))
    {
        if ($row = $result->fetch_assoc())
            if ($row['is_online'])
                return true;
    }
    return false;
}
?>