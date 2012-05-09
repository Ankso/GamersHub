<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../Common/SharedDefines.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../Classes/Database.Class.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../Common/PreparedStatements.php");

Class User
{
    /**
     * Initializes the user class, loading all user data from database.
     * @param string/long $source A string representing the user's username or a long unsigned integer as the user's unique ID
     */
    function __construct($source)
    {
        if (is_int($source))
            $this->_id = $source;
        elseif (is_string($source))
            $this->_username = $source;
        else
            die("Error initializing User Class: invalid source.");
            
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        var_dump($DB->BuildStmtArray("s", $this->_username));
        if (!isset($this->_id))
            $result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_BY_USERNAME, $DB->BuildStmtArray("s", $this->_username));
        else
            $result = $DB->ExecuteStmt(Statements::SELECT_USER_DATA_BY_ID, $DB->BuildStmtArray("i", $this->_id));
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $data;
        if (filter_var($this->GetLastIp(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            $data = $DB->BuildStmtArray("isssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetEmail(), $this->GetLastIp(), NULL);
        else
            $data = $DB->BuildStmtArray("isssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetEmail(), NULL, $this->GetLastIp());
        if ($DB->ExecuteStmt(Statements::REPLACE_USER_DATA, $data))
            return true;
        return false;
    }
    
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::UPDATE_USER_DATA_ID, $DB->BuildStmtArray("ii", $newId, $this_>GetId())))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::UPDATE_USER_DATA_USERNAME, $DB->BuildStmtArray("ss", $newUsername, $this->GetId())))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::UPDATE_USER_DATA_PASSWORD, $DB->BuildStmtArray("ss", $newPasswordSha1, $this->GetId())))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::UPDATE_USER_DATA_EMAIL, $DB->BuildStmtArray("ss", $newEmail, $this->GetId())))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt((filter_var($newIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? Statements::UPDATE_USER_DATA_IPV4 : Statements::UPDATE_USER_DATA_IP_V6),
        	$DB->BuildStmtArray("si", $newIp, $this->GetId())))
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
     * Changes the user's online status
     * @param bool $isOnline
     * @return bool Returns true if success or false if failure.
     */
    public function SetOnline($isOnline)
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::UPDATE_USER_DATA_ONLINE, $DB->BuildStmtArray("ii", ($isOnline ? "1" : "0"), $this->GetId())))
        {
            $this->_isOnline = $isOnline;
            return true;
        }
        return false;
    }
    
    /**
     * Accepts a friend request for this user. [NOT COMPLETELY IMPLEMENTED]
     * @param integer $friendId The user's new friend ID
     * @return bool Returns true if success or false if failure.
     */
    public function AcceptFriend($friendId)
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        // Insert the new friends in the DB (both are friends now, we must insert 2 records, at least for now)
        if ($DB->ExecuteStmt(Statements::INSERT_USER_FRIEND, $DB->BuildStmtPackage(2, "ii", $this->GetId(), $friendId, $friendId, $this->GetId())))
            // Remove the friend request
            if ($DB->ExecuteStmt(Statements::DELETE_USER_FRIEND_REQUEST, $DB->BuildStmtArray("ii", $this->GetId(), $friendId)))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        // Remove the friend request
        if ($DB->ExecuteStmt(Statements::DELETE_USER_FRIEND_REQUEST, $DB->BuildStmtArray("ii", $this->GetId(), $friendId)))
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        if ($DB->ExecuteStmt(Statements::DELETE_USER_FRIEND, $DB->BuildStmtPackage(2, "ii", $this->GetId(), $friendId, $friendId, $this->GetId())))
            return true;
        return false;
    }
    
    /**
     * Returns a numeric array containing all the IDs of the user's friends.
     * @return array Returns an array with the user's friends ID, the constant USER_HAS_NO_FRIENDS if the user has no friends or false if something fails.
     */
    public function GetAllFriendsById()
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->ExecuteStmt(Statements::SELECT_USER_FRIENDS_BY_ID, $DB->BuildStmtArray("i", $this->GetId()));
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
     * Returns a numeric array containing all the usernames of the user's friends.
     * @return array Returns an array with the user's friends username, the constant USER_HAS_NO_FRIENDS if the user has no friends or false if something fails.
     */
    public function GetAllFriendsByUsername()
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->ExecuteStmt(Statements::SELECT_USER_FRIENDS_BY_USERNAME, $DB->BuildStmtArray("s", $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = $result->fetch_assoc())
            $friends[] = $row['username'];
        return $friends;
    }
    
    /**
     * Determines if a user is friend of another user
     * @param long $id The other user's unique ID
     * @return bool Returns true if users are friends, else false.
     */
    public function IsFriendOf($id)
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->ExecuteStmt(Statements::SELECT_USER_FRIENDS_IS_FRIEND, $DB->BuildStmtArray("ii", $this->GetId(), $id));
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
        global $DATABASES, $SERVER_INFO;
        if ($this->IsFriendOf($friendId))
            return USERS_ARE_FRIENDS;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->ExecuteStmt(Statements::SELECT_USER_FRIEND_REQUEST_ID, $DB->BuildStmtArray("ii", $friendId, $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows > 0)
            return FRIEND_REQUEST_ALREADY_SENT;
        $result = $DB->ExecuteStmt(Statements::INSERT_USER_FRIEND_REQUEST,
            $DB->BuildStmtArray("iis", $friendId, $this->Getid(), (is_null($message) ? ($this->Getusername() . " wants to be your friend!") : $message)));
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
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->ExecuteStmt(Statements::SELECT_USER_FRIEND_REQUEST, $DB->BuildStmtArray("i", $this->GetId()));
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
    
    private $_id;                // The user's unique ID
    private $_username;          // The user's username (nickname)
    private $_passwordSha1;      // The encripted user's password
    private $_email;             // The user's e-mail
    private $_ip;                // The user's last used IP address
    private $_isOnline;          // True if the user is online, else false
}

?>