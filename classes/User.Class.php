<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");

Class User
{
    /**
     * Initializes the user class, loading all user data from database.
     * @param string/long $source A string representing the user's username or a long unsigned integer as the user's unique ID
     */
    function __construct($source)
    {
        global $DATABASES, $SERVER_INFO;
        if (is_int($source))
            $this->_id = $source;
        elseif (is_string($source))
            $this->_username = $source;
        else
            die("Error initializing User Class: invalid source.");
            
        $this->_db = new Database($DATABASES['USERS']);
        if (!$this->LoadFromDB())
            die("Error initializing User Class");
    }
    
    /**
     * Class destructor
     */
    function __destruct()
    {
    }
    
    /**
     * Load all user's data from DB into the Class variables.
     * @param $username
     * @return bool Returns true if the user is loaded successfully, or false if something fails.
     */
    private function LoadFromDB()
    {
        if (!isset($this->_id))
            $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_DATA_BY_USERNAME, $this->_db->BuildStmtArray("s", $this->_username));
        else
            $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_DATA_BY_ID, $this->_db->BuildStmtArray("i", $this->_id));
        if ($result && ($userData = $result->fetch_assoc()))
        {
            $this->_id = $userData['id'];
            $this->_username = $userData['username'];
            $this->_passwordSha1 = $userData['password_sha1'];
            $this->_email = $userData['email'];
            if (is_null($userData['ip_v6']))
                $this->_ip = $userData['ip_v4'];
            else
                $this->_ip = $userData['ip_v6'];
            $this->_lastLogin = $userData['last_login'];
            return true;
        }
        return false;
    }
    
    /**
     * Saves the users data sotred in the class variables to the DB.
     * @return bool Returns true if the user is saved to the DB successfully, else it returns false.
     */
    private function SaveToDB()
    {
        $data;
        if (filter_var($this->GetLastIp(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            $data = $this->_db->BuildStmtArray("issssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetEmail(), $this->GetLastIp(), NULL, $this->_lastLogin);
        else
            $data = $this->_db->BuildStmtArray("issssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetEmail(), NULL, $this->GetLastIp(), $this->_lastLogin);
        $this->_db->BeginTransaction();
        if ($this->_db->ExecuteStmt(Statements::REPLACE_USER_DATA, $data))
        {
            $this->_db->CommitTransaction();
            return true;
        }
        return false;
    }
    
    /***********************************************************\
    *  	                    PROFILE SYSTEM                      *
    \***********************************************************/
    
    /**
     * Gets the user's unique ID
     * @return long Returns a long unsigned integer representing the user's ID
     */
    public function GetId()
    {
        return $this->_id;
    }
    
    /**
     * Sets the user's ID to a new one. This function is private and must be used with a _lot_ of caution.
     * @param $newId The new user's unique ID
     * @return bool Returns true if success, or false in case of failure.
     */
    private function SetId($newId)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_ID, $this->_db->BuildStmtArray("ii", $newId, $this_>GetId())))
        {
            $this->_id = $newId;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the user's username.
     * @return string Returns a string representing the user's username (nick)
     */
    public function GetUsername() 
    {
        return $this->_username;
    }
    
    /**
     * Sets the user's username.
     * @param $newUsername
     * @return bool Returns true if success, or false in case of failure.
     */
    public function SetUsername($newUsername)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_USERNAME, $this->_db->BuildStmtArray("ss", $newUsername, $this->GetId())))
        {
            $this->_username = $newName;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the encripted user's password.
     */
    public function GetPasswordSha1()
    {
        return $this->_passwordSha1;
    }
    
    /**
     * Sets the encripted user's password to a new one.<p>Note that this function doesn't check if the password is encripted or anything, just sets the password to the passed string.</p>
     * @param $newPasswordSha1 The new encripted user's password as a string. The param must be checked _after_ the function is called.
     * @return bool Returns true if success, or false in case of failure.
     */
    public function SetPasswordSha1($newPasswordSha1)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_PASSWORD, $this->_db->BuildStmtArray("ss", $newPasswordSha1, $this->GetId())))
        {
            $this->_passwordSha1 = $newPasswordSha1;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the user's e-mail.
     * @return string Returns a string with the user's e-mail adress
     */
    public function GetEmail()
    {
        return $this->_email;
    }
    
    /**
     * Set's the user's e-mail.
     * @param $newEmail
     * @return bool Returns true if success, or false in case of failure.
     */
    public function SetEmail($newEmail)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_EMAIL, $this->_db->BuildStmtArray("ss", $newEmail, $this->GetId())))
        {
            $this->_email = $newEmail;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the last used IP by the user.
     * @return string Returns a string with the last user registered IP, in v6 format if available
     */
    public function GetLastIp()
    {
        return $this->_ip;
    }
    
    /**
     * Sets the last IP. The IP can be in v4 format or in v6 form.
     * @param string $newIp The new IP, in v4 or v6 format.
     */
    public function SetLastIp($newIp)
    {
        if ($this->_db->ExecuteStmt((filter_var($newIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? Statements::UPDATE_USER_DATA_IPV4 : Statements::UPDATE_USER_DATA_IP_V6),
        	$this->_db->BuildStmtArray("si", $newIp, $this->GetId())))
        {
            $this->_ip = $newIp;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the user's online status
     * @return bool Returns a boolean value (true if the user is online, else false)
     */
    public function IsOnline()
    {
        return $this->_isOnline;
    }
    
    /**
     * Changes the user's online status.
     * @param bool $isOnline
     * @return bool Returns true if success or false if failure.
     */
    public function SetOnline($isOnline)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_ONLINE, $this->_db->BuildStmtArray("ii", ($isOnline ? "1" : "0"), $this->GetId())))
        {
            $this->_isOnline = $isOnline;
            return true;
        }
        return false;
    }
    
    /**
     * Gets the date and time of the last user's login, in direct MySQL format (YYYY-MM-DD HH:MM:SS)
     * @return string Returns a string as a direct DATETIME MySQL format.
     */
    public function GetLastLogin()
    {
        return $this->_lastLogin;
    }
    
    /**
     * Sets the last login of this user.
     * @param string $lastLogin A string representing a MySQL DATETIME (YYYY-MM-DD HH:MM:SS)
     * @return boolean Returns true on success or false if failure.
     */
    public function SetLastLogin($lastLogin)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_LAST_LOGIN, $this->_db->BuildStmtArray("si", $lastLogin, $this->GetId())))
        {
            $this->_lastLogin = $lastLogin;
            return true;
        }
        return false;
    }
    
    /**
     * Sets the avatar path for this user.
     * @param string $avatarPath The avatar's relative path from the root server directory.
     * @return bool Returns true on success or false if failure.
     */
    public function SetAvatarPath($avatarPath)
    {
        $avatarPath = "http://". $_SERVER['HTTP_HOST'] . $avatarPath;
        
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_AVATARS_PATH, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            if ($result->num_rows === 0)
            {
                if (($result = $this->_db->ExecuteStmt(Statements::INSERT_USER_AVATARS_PATH, $this->_db->BuildStmtArray("is", $this->GetId(), $avatarPath))))
                    return true;
            }
            else
            {
                if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_AVATARS_PATH, $this->_db->BuildStmtArray("si", $avatarPath, $this->GetId())))
                    return true;
            }
        }
        return false;
    }
    
    /**
     * Gets the url for the avatar of this user.
     * @return mixed Returns a string with the full url to the avatar's location, a string representing a relative path if the avatar is the default one, or false if something fails.
     */
    public function GetAvatarHostPath()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_AVATARS_PATH, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                return $row['avatar_path'];
            }
            else
                return "/images/default_avatar.png";
        }
        return false;
    }
    
    /**
     * Gets the detailed data of a user from the DB
     * @return array Returns an array containing the detailed data, or false if something fails. See the "Profile system" constants defined in SharedDefines.php
     */
    public function GetDetailedUserData()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_DETAILED_DATA, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            if (($row = $result->fetch_assoc()))
            {
                return array(
                    USER_DETAILS_BIO      => $row['bio'],
                    USER_DETAILS_BIRTHDAY => $row['birthday'],
                    USER_DETAILS_COUNTRY  => $row['country'],
                    USER_DETAILS_CITY     => $row['city']
                );
            }
        }
        return false;
    }
    
    /**
     * Replaces a user's detailed data in the database for new info. The function strips tags of the bio to fight against potential XSS.
     * @param string $bio A valid string for the user's biography.
     * @param string(date) $birthday A string representing a valid user's date of birth.
     * @param string $country A string representing the user's country.
     * @param string $city A string representing the user's city.
     * @return boolean Returns true on success, or false in case of failure.
     */
    public function SetDetailedUserData($bio, $birthday, $country, $city)
    {
        if (!isset($bio) || !isset($birthday) || !isset($country) || !isset($city))
            return false;
        $bio = strip_tags($bio, "<font><br>");
        
        if ($this->_db->ExecuteStmt(Statements::REPLACE_USER_DETAILED_DATA, $this->_db->BuildStmtArray("issss", $this->GetId(), $bio, $birthday, $country, $city)))
            return true;
        return false;
    }
    
    /***********************************************************\
    *  	                    PRIVACY SYSTEM                      *
    \***********************************************************/
    
    /**
     * Gets the user privacy settings
     * @return mixed Returns an array with the different security levels for each of the security options, or false if something fails.
     */
    function GetPrivacySettings()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_PRIVACY, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            if(($row = $result->fetch_assoc()))
            {
                return array(
                    USER_PRIVACY_EMAIL      => $row['view_email'],
                    USER_PRIVACY_PROFILE    => $row['view_profile'],
                    USER_PRIVACY_LIVESTREAM => $row['view_livestream'],
                );
            }
        }
        return false;
    }
    
    /**
     * Sets the different privacy options to new values
     * @param mixed $email The security level for the user's email visualization, from 0 to 3. Can be in form of string or integer.
     * @param mixed $profile The security level for the user's detailed profile visualization, from 1 to 3. Can be in form of string or integer.
     * @param mixed $liveStream The security level for the user's livestream availability, from 1 to 3. Can be in form of string or integer.
     * @return boolean Returns true on success or false if something goes wrong.
     */
    function SetPrivacySettings($email, $profile, $liveStream)
    {
        if (!isset($email) || !isset($profile) || !isset($liveStream))
            return false;
        // Cast the params to integer to make sure that they have allowed values (between 0-3)
        $email = (int)$email;
        $profile = (int)$profile;
        $liveStream = (int)$liveStream;
        if ($email > PRIVACY_LEVEL_EVERYONE || $email < PRIVACY_LEVEL_NOBODY
            || $profile > PRIVACY_LEVEL_EVERYONE || $profile < PRIVACY_LEVEL_FRIENDS
            || $liveStream > PRIVACY_LEVEL_EVERYONE || $liveStream < PRIVACY_LEVEL_FRIENDS)
            return false;
        if (($result = $this->_db->ExecuteStmt(Statements::REPLACE_USER_PRIVACY, $this->_db->BuildStmtArray("iiii", $this->GetId(), $email, $profile, $liveStream))))
            return true;
        return false;
        
    }
    
    /***********************************************************\
    *  	                    FRIENDS SYSTEM                      *
    \***********************************************************/
    
    /**
     * Accepts a friend request for this user. [NOT COMPLETELY IMPLEMENTED]
     * @param integer $friendId The user's new friend ID.
     * @return bool Returns true if success or false if failure.
     */
    public function AcceptFriend($friendId)
    {
        // Insert the new friends in the DB (both are friends now, we must insert 2 records, at least for now)
        if ($this->_db->ExecuteStmt(Statements::INSERT_USER_FRIEND, $this->_db->BuildStmtPackage(2, "ii", $this->GetId(), $friendId, $friendId, $this->GetId())))
            // Remove the friend request
            if ($this->_db->ExecuteStmt(Statements::DELETE_USER_FRIEND_REQUEST, $this->_db->BuildStmtArray("ii", $this->GetId(), $friendId)))
                return true;
        return false;
    }
    
    /**
     * Declines a user friend request.
     * @param long $friendId The declined friend's ID
     * @return bool Returns true on success, or false if failure.
     */
    public function DeclineFriendRequest($friendId)
    {
        // Remove the friend request
        if ($this->_db->ExecuteStmt(Statements::DELETE_USER_FRIEND_REQUEST, $this->_db->BuildStmtArray("ii", $this->GetId(), $friendId)))
            return true;
        return false;
    }
    
    /**
     * Removes a friend from the user's friends list. [NOT COMPLETELY IMPLEMENTED]
     * @param integer $friendId The user's friend ID
     * @return bool Returns true if success or false if failure.
     */
    public function RemoveFriend($friendId)
    {
        // We must "set as removed" - beacuse we aren't going to remove anything at all - all the private messages and other kind of archives that the users may have
        if ($this->_db->ExecuteStmt(Statements::DELETE_USER_FRIEND, $this->_db->BuildStmtPackage(2, "ii", $this->GetId(), $friendId, $friendId, $this->GetId())))
            return true;
        return false;
    }
    
    /**
     * Returns a numeric array containing all the IDs of the user's friends.
     * @return array Returns an array with the user's friends ID, the constant USER_HAS_NO_FRIENDS if the user has no friends or false if something fails.
     */
    public function GetAllFriendsById()
    {
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIENDS_BY_ID, $this->_db->BuildStmtArray("i", $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = $result->fetch_assoc())
            $friends[] = $row['friend_id'];
        return $friends;
    }
    
    /**
     * Returns a numeric array containing all the usernames of the user's friends and they status (online, offline, and soon AFK)
     * @return array Returns a bidimensional array with the each friend's username and status, the constant USER_HAS_NO_FRIENDS if the user has no friends or false if something fails.
     */
    public function GetAllFriendsByUsername()
    {
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIENDS_BY_USERNAME, $this->_db->BuildStmtArray("s", $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = $result->fetch_assoc())
        {
            $friends[] = array(
                0 => $row['username'],
                1 => $row['is_online']
            );
        }
        return $friends;
    }
    
    /**
     * Determines if a user is friend of another user
     * @param long/string $identifier The other user's unique ID or username
     * @return bool Returns true if users are friends, else false.
     */
    public function IsFriendOf($identifier)
    {
        if (is_int($identifier))
            $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIENDS_IS_FRIEND_ID, $this->_db->BuildStmtArray("ii", $this->GetId(), $identifier));
        elseif (is_string($identifier))
            $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIENDS_IS_FRIEND, $this->_db->BuildStmtArray("si", $identifier, $this->GetId()));
        else
            return false;
        if ($result === false)
            return false;    // An error must be triggered here, or logged at least
        if ($result->num_rows > 0)
            return true;
        return false;
    }
    
    /**
     * [INCOMPLETE] Sends a new friend request. Note that the target friend must be updated in real-time when the function is complete.
     * @param long $friendId The request target ID
     * @param string $message The message that the user sends to his new friend
     * @return bool Returns true on success, USER_IS_ALREADY_FRIEND if the users are friends, RESQUEST_ALREADY_SENT if the friend request has been sent and is waiting for aproval, or false on failure
     */
    public function SendFriendRequest($friendId, $message)
    {
        if ($this->IsFriendOf($friendId))
            return USERS_ARE_FRIENDS;
        
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIEND_REQUEST_ID, $this->_db->BuildStmtArray("ii", $friendId, $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows > 0)
            return FRIEND_REQUEST_ALREADY_SENT;
        $result = $this->_db->ExecuteStmt(Statements::INSERT_USER_FRIEND_REQUEST,
            $this->_db->BuildStmtArray("iis", $friendId, $this->Getid(), (is_null($message) ? ($this->Getusername() . " wants to be your friend!") : $message)));
        if ($result)
            return true;
        return false;
    }
    
    /**
     * Gets all the friend requests for this user.
     * @return array Returns a bidimensional array with the usernames (not IDs) and messages of the friend requests or false if failure.
     */
    public function GetFriendRequests()
    {
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIEND_REQUEST, $this->_db->BuildStmtArray("i", $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows === 0)
            return USER_HAS_NO_FRIEND_REQUESTS;
        $friendRequests = array();
        while ($row = $result->fetch_assoc())
        {
            $friendRequests[] = array(
                "username" => $row['username'],
                "message"  => $row['message']
            );
        }
        return $friendRequests;
    }
    
    /**
     * Gets the total amount of friend requests for this user.
     * @return integer Returns the number of friend requests for this user, or false in case of failure.
     */
    public function GetFriendRequestsCount()
    {
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIEND_REQUESTS_COUNT, $this->_db->BuildStmtArray("i", $this->GetId()));
        if ($result === false)
            return false;
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    private $_id;                // The user's unique ID
    private $_username;          // The user's username (nickname)
    private $_passwordSha1;      // The encripted user's password
    private $_email;             // The user's e-mail
    private $_ip;                // The user's last used IP address
    private $_isOnline;          // True if the user is online, else false
    private $_lastLogin;         // Date and time of the last user's login.
    private $_db;                // The database object
}

?>