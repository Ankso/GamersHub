<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");

/**
 * Main User class. Can be initilized with a valid username or a valid user ID.<br />It stores data about an user, and has all the methods to access that data.<br />It also has methods representing user actions, like sending a private message to another user.<br />
 * TODO: Create classes for some objects like Board messages, Board message replies, Private messages, Custom options, Privacy options, etc, instead of returning bi-dimensional arrays or even tri-dimensional arrays with the values.
 * @author Ankso
 */
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
            $this->_randomSessionId = $userData['random_session_id'];
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
            $data = $this->_db->BuildStmtArray("isssssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetRandomSessionId(), $this->GetEmail(), $this->GetLastIp(), NULL, $this->_lastLogin);
        else
            $data = $this->_db->BuildStmtArray("isssssss", $this->GetId(), $this->GetUsername(), $this->GetPasswordSha1(), $this->GetRandomSessionId(), $this->GetEmail(), NULL, $this->GetLastIp(), $this->_lastLogin);
        $this->_db->BeginTransaction();
        if ($this->_db->ExecuteStmt(Statements::REPLACE_USER_DATA, $data))
        {
            $this->_db->CommitTransaction();
            return true;
        }
        return false;
    }
    
    /**
     * Obtains the user's unique random session identifier for this login.
     * @return mixed Returns a 128 characters length string with the WHIRLPOOL hash of the session identifier, or NULL if the session identifier has not been initilized yet.
     */
    public function GetRandomSessionId()
    {
        return $this->_randomSessionId;
    }
    
    /**
     * Generates (or re-generates) a new random unique session identifier for this user, and stores it in the database. This ID is sended to the client to identify itself when connecting to the real time server app.
     * @return boolean Returns true on success, false in case of failure.
     */
    public function GenerateRandomSessionId()
    {
        $unbreakable = $this->GetUsername() . "-:-" . MAGIC_STRING . "-" . microtime(true) . $this->GetPasswordSha1();
        $unbreakable = hash("whirlpool", $unbreakable);
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_RND_IDENTIFIER, $this->_db->BuildStmtArray("si", $unbreakable, $this->GetId())))
        {
            $this->_randomSessionId = $unbreakable;
            return true;
        }
        return false;
    }
    
    /**
     * Destroys (Removes from database) the user's unique random session identifier. This will force the user to relog if he wants to continue using the real time information systems.
     * @return boolean Returns true on success, or false on failure.
     */
    public function DestroyRandomSessionId()
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_RND_IDENTIFIER, $this->_db->BuildStmtArray("si", NULL, $this->GetId())))
        {
            $this->_randomSessionId = NULL;
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
     * @param long $newId The new user's unique ID
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
     * @param string $newUsername The new user's username.
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
     * @return string The encrypted user's password.
     */
    public function GetPasswordSha1()
    {
        return $this->_passwordSha1;
    }
    
    /**
     * Sets the encripted user's password to a new one.<p>Note that this function doesn't check if the password is encripted or anything, just sets the password to the passed string.</p>
     * @param string $newPasswordSha1 The new encripted user's password as a string. The param must be checked _after_ the function is called.
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
     * @param string $newEmail The new user's e-mail.
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
     * @param bool $isOnline Obiusly, true if the user is online.
     * @return bool Returns true if success or false if failure.
     */
    public function SetOnline($isOnline)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DATA_ONLINE, $this->_db->BuildStmtArray("ii", ($isOnline ? "1" : "0"), $this->GetId())))
        {
            $this->_isOnline = (bool)$isOnline;
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
    public function SetAvatarHost($avatarHost)
    {
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_DETAILED_DATA_AVATAR, $this->_db->BuildStmtArray("si", $avatarHost, $this->GetId())))
            return true;
        return false;
    }
    
    /**
     * Gets the url for the avatar of this user.
     * @return mixed Returns a string with the full url to the avatar's location, a string representing a relative path if the avatar is the default one, or false if something fails.
     */
    public function GetAvatarHostPath()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_DETAILED_DATA_AVATAR, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            $row = $result->fetch_assoc();
            return $row['avatar_path'];
        }
        return false;
    }
    
    /**
     * Determines if the user is using gravatar to get his or her avatar
     * @return boolean Returns true if the user is using gravatar, else it returns false
     */
    public function IsUsingGravatar()
    {
        if (($avatarLink = $this->GetAvatarHostPath()))
        {
            if (strpos($avatarLink, "http://www.gravatar.com/") !== false)
                return true;
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
        // Check the values
        if ($email > PRIVACY_LEVEL_EVERYONE || $email < PRIVACY_LEVEL_NOBODY
            || $profile > PRIVACY_LEVEL_EVERYONE || $profile < PRIVACY_LEVEL_FRIENDS
            || $liveStream > PRIVACY_LEVEL_EVERYONE || $liveStream < PRIVACY_LEVEL_FRIENDS)
            return false;
        if (($result = $this->_db->ExecuteStmt(Statements::UPDATE_USER_PRIVACY, $this->_db->BuildStmtArray("iiii", $email, $profile, $liveStream, $this->GetId()))))
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
     * Returns a bidimensional array containing all the IDs, usernames of the user's friends, they status (online, offline, and soon AFK) and the avatar path.
     * @return array Returns a bidimensional array with each friend's ID, username and status, the constant USER_HAS_NO_FRIENDS if the user has no friends or false if something fails.
     */
    public function GetAllFriends()
    {
        $result = $this->_db->ExecuteStmt(Statements::SELECT_USER_FRIENDS, $this->_db->BuildStmtArray("i", $this->GetId()));
        if ($result === false)
            return false;
        if ($result->num_rows === 0)
            return USER_HAS_NO_FRIENDS;
        $friends = array();
        while ($row = $result->fetch_assoc())
        {
            $friends[] = array(
                'id'         => $row['id'],
                'username'   => $row['username'],
                'isOnline'   => $row['is_online'],
                'avatarPath' => $row['avatar_path'],
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
     * Sends a new friend request. The target user must be updated in real-time via the Real Time Server (RTS)
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
                "id"         => $row['requester_id'],
                "username"   => $row['username'],
                "message"    => $row['message'],
                "avatarPath" => $row['avatar_path'],
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
    
    /**
     * Inserts a private message, sended by this user, in the database. Note that the function doesn't check if the users are friends, nor if the $receiver is valid (if it is an ID)
     * @param long/string $receiver A long integer representing a valid user ID or a string representing a valid username.
     * @param string $message The message that must be sent.
     * @return boolean Returns true on success, oo false if failure.
     */
    public function SendPrivateMessage($receiver, $message)
    {
        $date = date("Y-m-d H:i:s", time());
        // If the parameter $receiver is a string, it must be a username, so, pass it to a valid user ID
        if (is_string($receiver))
        {
            $receiver = GetIdFromUsername($receiver);
            if ($receiver === USER_DOESNT_EXISTS || $receiver === false)
                return false;
        }
        if ($this->_db->ExecuteStmt(Statements::INSERT_USER_PRIVATE_MESSAGE, $this->_db->BuildStmtArray("iissi", $this->GetId(), $receiver, $message, $date, 0)))
            return true;
        return false;
    }
    
    /**
     * Gets the unreaded private messages count for this user.
     * @return mixed Returns the number of unreaded messages, or false if something goes wrong.
     */
    public function GetUnreadPrivateMessagesCount()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_PRIVATE_MESSAGES_COUNT, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            $row = $result->fetch_array();
            return $row[0];
        }
        return false;
    }
    
    /**
     * Gets all the messages sended by the specified friend to this user. Note that the function doesn't check if the users are friends, nor if the $receiver is valid (if it is an ID)
     * @param long/string $sender [Optional] A long integer representing a valid user ID or a string representing a valid username.<br />If this param is not provided, the function returns all the unreaded private messages for this user.
     * @return mixed Returns a bidimensional array with each message, the date it was sended, and the sernder ID and username or false if something fails.
     */
    public function GetPrivateMessages($sender = NULL, $unreadedOnly = false)
    {
        if (!is_null($sender))
        {
            // If the parameter $sender is a string, it must be a username, so, cast it to a valid user ID
            if (is_string($sender))
            {
                $sender = GetIdFromUsername($sender);
                if ($sender === USER_DOESNT_EXISTS || $sender === false)
                    return false;
            }
            if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_PRIVATE_MESSAGE, $this->_db->BuildStmtArray("ii", $sender, $this->GetId()))))
            {
                if ($result->num_rows === 0)
                    return USER_HAS_NO_MESSAGES;
                $messages = array();
                while (($row = $result->fetch_assoc()))
                {
                    $messages[] = array(
                        'message' => $row['message'],
                        'date'    => $row['date'],
                        'readed'  => $row['readed'],
                    );
                }
                return $messages;
            }
        }
        else
        {
            if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_PRIVATE_MESSAGES, $this->_db->BuildStmtArray("i", $this->GetId()))))
            {
                if ($result->num_rows === 0)
                    return USER_HAS_NO_MESSAGES;
                $messages = array();
                while (($row = $result->fetch_assoc()))
                {
                    if ($unreadedOnly && $row['readed'] == 1)
                        continue;
                    
                    $messages[] = array(
                        'sender'         => $row['sender_id'],
                        'senderUsername' => $row['username'],
                        'message'        => $row['message'],
                        'date'           => $row['date'],
                        'readed'         => $row['readed'],
                    );
                }
                if (count($messages) === 0)
                    return USER_HAS_NO_MESSAGES;
                return $messages;
            }
        }
        return false;
    }
    
    /**
     * Gets all the messages sended by the specified friend to this user and vice versa (a "private conversation"). Note that the function doesn't check if the users are friends, nor if the $receiver is valid (if it is an ID)
     * @param long/string $sender A long integer representing a valid user ID or a string representing a valid username.
     * @return mixed Returns a bidimensional array with each message of the conversation, the date it was sended, and the sender or false if something fails.
     */
    public function GetPrivateConversation($friend)
    {
        // If the parameter $sender is a string, it must be a username, so, cast it to a valid user ID
        if (is_string($friend))
        {
            $friend = GetIdFromUsername($friend);
            if ($friend === USER_DOESNT_EXISTS || $friend === false)
                return false;
        }
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_PRIVATE_CONVERSATION, $this->_db->BuildStmtArray("iiii", $friend, $this->GetId(), $this->GetId(), $friend))))
        {
            if ($result->num_rows === 0)
                return USER_HAS_NO_MESSAGES;
            $messages = array();
            while (($row = $result->fetch_assoc()))
            {
                $messages[] = array(
                    'sender'  => $row['sender_id'],
                    'message' => $row['message'],
                    'date'    => $row['date'],
                    'readed'  => $row['readed'],
                );
            }
            return $messages;
        }
        return false;
    }
    
    public function GetAllPrivateConversations()
    {
        
    }
    
    /**
     * Sets _all_ messages from a specific user as readed for this user.
     * @param long/string $friend A long integer representing a unique user ID, or a string representing a username.
     * @return boolean Returns true on success, or false in case of failure.
     */
    public function SetMessagesAsReaded($friend)
    {
        // If the parameter $sender is a string, it must be a username, so, cast it to a valid user ID
        if (is_string($friend))
        {
            $friend = GetIdFromUsername($friend);
            if ($friend === USER_DOESNT_EXISTS || $friend === false)
                return false;
        }
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_PRIVATE_MESSAGES_READED, $this->_db->BuildStmtArray("iii", 1, $this->GetId(), $friend)))
            return true;
        return false;
    }
    
    /***********************************************************\
    *  	                 USER BOARD FUNCTIONS                   *
    \***********************************************************/
    
    /**
     * Gets the total board messages sended by this user based in the message_number field in the database (not in the COUNT(*) statement)
     * @return mixed Returns the number of messages for a specific user, USER_HAS_NO_BOARD_MESSAGES (0) if the user hasn't written any comment in his board yet, or false if something fails.
     */
    public function GetBoardMessagesCount()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_BOARD_COUNT, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            if ($result->num_rows === 0)
                return USER_HAS_NO_BOARD_MESSAGES;
            
            $row = $result->fetch_array();
            return $row[0];
        }
        return false;
    }
    
    /**
     * Inserts a new comment/message in the database. Note that the message must be proccessed/stripped/etc _before_ be sended here. The message is inserted in the DB as is.
     * @param string $message The message itself.
     * @return boolean Returns true on success, or false in case of failure.
     */
    public function SendBoardMessage($message)
    {
        if ($this->_db->ExecuteStmt(Statements::INSERT_USER_BOARD, $this->_db->BuildStmtArray("iiss", $this->GetId(), $this->GetBoardMessagesCount() + 1, $message, date("Y-m-d H:i:s", time()))))
            return true;
        return false;
    }
    
    /**
     * Gets all the messages between the specific interval for this user.
     * @param integer $from One of the limits.
     * @param integer $to The other limit.
     * @return mixed Returns a bidimensional array containing all the messages related data (except the user's id, that is implicit), USER_HAS_NO_BOARD_MESSAGES if the user hasn't sent any messages yet, or false if something fails.
     */
    public function GetBoardMessages($from, $to)
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_BOARD, $this->_db->BuildStmtArray("iii", $this->GetId(), $from, $to))))
        {
            if ($result->num_rows === 0)
                return USER_HAS_NO_BOARD_MESSAGES;
            
            $messages = array();
            while (($row = $result->fetch_assoc()))
            {
                $messages[] = array(
                    'messageId'     => $row['message_id'],
                    'messageNumber' => $row['message_number'],
                    'message'       => $row['message'],
                    'date'          => $row['date'],
                );
            }
            return $messages;
        }
        return false;
    }
    
    /**
     * Deletes a specified board message from the database. Note that the message will be deleted only if this user is the message's writer.
     * @param long $messageId The ID of the message to be deleted (Note: not the message number, but the message unique ID)
     * @return boolean Returns true on success, or false in case of failure.
     */
    public function DeleteBoardMessage($messageId)
    {
        // We'll need the message number later
        if ($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_BOARD_MESSAGE_NUMBER, $this->_db->BuildStmtArray("i", $messageId)))
        {
            $row = $result->fetch_assoc();
            $messageNumber = $row['message_number'];
        }
        else
            return false;
        
        // We must update the message's numbers in order to maintain the structure used to obtain the messages.
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_BOARD_MESSAGE_NUMBERS, $this->_db->BuildStmtArray("ii", $this->GetId(), $messageNumber)))
        {
            // Note that we don't need to delete the related rows in tables like user_board_replies
            // because the FK has the ON DELETE CASCADE and ON UPDATE CASCADE properties. This rows are going to be deleted automatically.
            // Also note that the operation that checks that the user is the writer of the message is in the SQL statement itself.
            if ($this->_db->ExecuteStmt(Statements::DELETE_USER_BOARD, $this->_db->BuildStmtArray("ii", $messageId, $this->GetId())))
                return true;
        }
        return false;
    }
    
    public function SendBoardMessageReply($messageId, $content)
    {
        if ($this->_db->ExecuteStmt(Statements::INSERT_USER_BOARD_REPLY, $this->_db->BuildStmtArray("iiss", $this->GetId(), $messageId, $content, date("Y-m-d H:i:s", time()))))
            return true;
        return false;
    }
    
    /**
     * Obtains all the replies for a specific user's message
     * @param long $messageId An integer representing an unique message ID, or an array of message IDs to retrieve more than the replies of one message.
     * @return mixed Returns a bidimensional array with all the replies and more data like the writer username and avatar path for later use if one ID is suplied, a tridimensional array with the replies for each supplied ID if the input param is an array of IDs; USER_COMMENT_HAS_NO_REPLIES if the comment has not replies, and finally false if something fails.
     */
    public function GetBoardMessageReplies($messageId)
    {
        if (is_array($messageId))
        {
            // First, we must create an array with the params for the function Database::BuildStmtPackage()
            // Add the number of times the query is going to be executed, and the string with the var types for bind_params() function.
            $builtParams = array(
                0 => count($messageId),
                1 => "ii",
            );
            // Now we can add the message IDs to the array
            foreach ($messageId as $i => $value)
            {
                $builtParams[] = $this->GetId();
                $builtParams[] = $messageId[$i];
            }
            // Here the array with the params must be properly intialized
            if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_BOARD_REPLIES, call_user_func_array(array($this->_db, "BuildStmtPackage"), $builtParams))))
            {
                // This conversion is very important. Remember that Database::ExecuteStmt returns an array
                // of results _only_ if more than one query is executed. We can have that situation here, if
                // only one message ID is passed as param, but inside an array (mainly from boardmessages.php)
                if (!is_array($result))
                {
                    $result = array(
                        0 => $result,
                    );
                }
                $replies = array();
                // We must process each of the returned values
                foreach ($result as $i => $value)
                {
                    if ($result[$i]->num_rows === 0)
                    {
                        // We'll have one result per message ID
                        $replies[$i][] = array(
                            'messageId' => $messageId[$i],
                            'replyId'   => USER_COMMENT_HAS_NO_REPLIES,
                        );
                    }
                    else
                    {
                        while (($row = $result[$i]->fetch_assoc()))
                        {
                            $replies[$i][] = array(
                                'messageId'  => $messageId[$i],
                                'replyId'    => $row['reply_id'],
                                'senderId'   => $row['sender_id'],
                                'message'    => $row['message'],
                                'date'       => $row['date'],
                                'username'   => $row['username'],
                                'avatarPath' => $row['avatar_path'],
                            );
                        }
                    }
                }
                return $replies;
            }
        }
        else
        {
            if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_BOARD_REPLIES, $this->_db->BuildStmtArray("ii", $this->GetId(), $messageId))))
            {
                if ($result->num_rows === 0)
                    return USER_COMMENT_HAS_NO_REPLIES;
                
                $replies = array();
                while (($row = $result->fetch_assoc()))
                {
                    $replies[] = array(
                        'reply_id'   => $row['reply_id'],
                        'sender_id'  => $row['sender_id'],
                        'message'    => $row['message'],
                        'date'       => $row['date'],
                        'username'   => $row['username'],
                        'avatarPath' => $row['avatar_path'],
                    );
                }
                return $replies;
            }
        }
        return false;
    }
    
    public function DeleteBoardMessageReply($replyId)
    {
        // TODO: Checks checks checks!
        if ($this->_db->ExecuteStmt(Statements::DELETE_USER_BOARD_REPLY, $this->_db->BuildStmtArray("i", $replyId)))
            return true;
        return false;
    }
    /**
     * Gets the customization options for this user.
     * @return boolean Returns an array with the values for all the custom options, or false if something fails.
     */
    public function GetCustomOptions()
    {
        if (($result = $this->_db->ExecuteStmt(Statements::SELECT_USER_CUSTOM_OPTIONS, $this->_db->BuildStmtArray("i", $this->GetId()))))
        {
            $row = $result->fetch_assoc();
            return array(
                CUSTOM_OPTION_LIVESTREAM          => (bool)$row['option_livestream'],
                CUSTOM_OPTION_LIVESTREAM_COMMENTS => (bool)$row['option_livestream_livecomments'],
                CUSTOM_OPTION_LATEST_NEWS         => (bool)$row['option_latest_news'],
            );
        }
        return false;
    }
    
    /**
     * Sets the values of the custom options for this user.
     * @param boolean $livestream Livestream enabled (true) or disabled (false).
     * @param boolean $livestreamComments Livestream LiveComments enabled (true) or disabled (false).
     * @param boolean $latestNews Latest News Section enabled (true) or disabled (false).
     * @return boolean Returns true on success, false on failure.
     */
    public function SetCustomOptions($liveStream, $liveStreamComments, $latestNews)
    {
        $liveStream = (int)$liveStream;
        $liveStreamComments = (int)$liveStreamComments;
        $latestNews = (int)$latestNews;
        // Check that the values of the params are valid ones
        if ($liveStream > 1 || $liveStream < 0 || $liveStreamComments > 1 || $liveStreamComments < 0 || $latestNews > 1 || $latestNews < 0)
            return false;
        // If livestream is disabled, the LiveStream LiveComments section must be disabled also
        if ($liveStream === 0)
            $liveStreamComments = 0;
        if ($this->_db->ExecuteStmt(Statements::UPDATE_USER_CUSTOM_OPTIONS, $this->_db->BuildStmtArray("iiii", $liveStream, $liveStreamComments, $latestNews, $this->GetId())))
            return true;
        return false;
    }
    
    private $_id;                // The user's unique ID
    private $_username;          // The user's username (nickname)
    private $_passwordSha1;      // The encripted user's password
    private $_randomSessionId;   // The user's unique session identifier. It changes on every new login.
    private $_email;             // The user's e-mail
    private $_ip;                // The user's last used IP address
    private $_isOnline;          // True if the user is online, else false
    private $_lastLogin;         // Date and time of the last user's login.
    private $_db;                // The database object
}

?>