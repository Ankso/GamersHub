<?php
require_once ("F:/GamersNet/GamersNet_Beta/Common/SharedDefines.php");
require_once ("F:/GamersNet/GamersNet_Beta/Classes/Database.Class.php");

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
            die("Error initializing User Class: " . mysql_error());
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
        $DB = New Database($DATABASES['USERS']);
        $query;
        if (!isset($this->_id))
            $query = "SELECT id, username, password_sha1, email, ip_v4, ip_v6 FROM user_data WHERE username = '". $this->_username ."'";
        else
            $query = "SELECT id, username, password_sha1, email, ip_v4, ip_v6 FROM user_data WHERE id = ". $this->_id;
        $result = $DB->Execute($query);
        if ($userData = mysql_fetch_assoc($result))
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
        $DB = New Database($DATABASES['USERS']);
        $query;
        if (filter_var($this->GetLastIp(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            $query = "REPLACE INTO user_data VALUES (". $this->GetId() .", '". $this->GetUsername() ."', '". $this->GetPasswordSha1() ."', '". $this->GetEmail() ."', '". $this->GetLastIp() ."', NULL)";
        else
            $query = "REPLACE INTO user_data VALUES (". $this->GetId() .", '". $this->GetUsername() ."', '". $this->GetPasswordSha1() ."', '". $this->GetEmail() ."', NULL, '". $this->GetLastIp() ."')";
        if ($DB->Execute($query))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET id = ". $newId ." WHERE id = ". $this_>GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET username = '". $newUsername ."' WHERE id = ". $this->GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET password_sha1 = '". $newPasswordSha1 ."' WHERE id = ". $this->GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET email = '". $newEmail ."' WHERE id = ". $this->GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET ". filter_var($newIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? "ip_v4" : "ip_v5" ." = ". $newIp ." WHERE id = ". $this->GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("UPDATE user_data SET is_online = ". ($isOnline ? "1" : "0") ." WHERE id = ". $this->GetId()))
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
        $DB = New Database($DATABASES['USERS']);
        // Remove the friend request
        if ($DB->Execute("DELETE FROM user_friend_requests WHERE user_id = ". $this->GetId() ." AND requester_id = ". $friendId))
            // Insert the new friends in the DB (both are friends now, we must insert 2 records, at least for now)
            if ($DB->Execute("INSERT INTO user_friends VALUES (". $this->GetId() .", ". $friendId ."), (". $friendId) .", ". $this->GetId() .")")
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
        $DB = New Database($DATABASES['USERS']);
        if ($DB->Execute("DELETE FROM user_friends WHERE user_id = ". $this->GetId()." AND friend_id = ". $friendId))
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
        $DB = New Database($DATABASES['USERS']);
        $result = $DB->Execute("SELECT friend_id FROM user_friends WHERE user_id = ". $this->GetId());
        if ($result === false)
            return false;
        if (mysql_num_rows($result) === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = mysql_fetch_assoc($result))
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
        $DB = New Database($DATABASES['USERS']);
        $result = $DB->Execute("SELECT a.username FROM user_data AS a, user_friends AS b WHERE b.friend_id = a.id AND b.user_id = ". $this->GetId());
        if ($result === false)
            return false;
        if (mysql_num_rows($result) === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = mysql_fetch_assoc($result))
            $friends[] = $row['username'];
        return $friends;
    }
    
    /**
     * Determines if a user is friend of another user
     * @param long $id The other user's unique ID
     * @return CONST Returns USERS_ARENT_FRIENDS, USERS_ARE_FRIENDS or false if something fails.
     */
    public function IsFriendOf($id)
    {
        global $DATABASES, $SERVER_INFO;
        $DB = new Database($DATABASES['USERS']);
        $result = $DB->Execute("SELECT * FROM user_friends WHERE user_id = ". $this->GetId() ." AND friend_id = ". $id);
        if ($result === false)
            return false;
        if (mysql_num_rows($result) > 0)
            return USERS_ARE_FRIENDS;
        return USERS_ARENT_FRIENDS;
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
        $result = $DB->Execute("SELECT user_id FROM user_friend_requests WHERE user_id = ". $friendId ." AND requester_id = ". $this->GetId());
        if ($result === false)
            return false;
        if (mysql_num_rows($result) > 0)
            return FRIEND_REQUEST_ALREADY_SENT;
        if ($DB->Execute("INSERT INTO user_friend_requests (user_id, requester_id, request_message) VALUES".
                		 "(". $friendId .", ". $this->GetId() .", '". ((is_null($message)) ? ($this->Getusername() . " wants to be your friend!") : $message) ."')"))
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
        $result = $DB->Execute("SELECT a.username, b.message FROM user_data AS a, user_friend_requests AS b WHERE b.user_id = ". $this->GetId() ." AND a.id = b.requester_id");
        if ($result === false)
            return false;
        if (mysql_num_rows($result) === 0)
            return USER_HAS_NO_FRIEND_REQUESTS;
        $friendRequests = array();
        while ($row = mysql_fetch_assoc($result))
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