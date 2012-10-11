<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../classes/Developer.Class.php");
require_once ($_SERVER['DOCUMENT_ROOT'] . "/../classes/Publisher.Class.php");

/**
 * Main Game class. Stores all the info about a game, and all operations that a game must be able to do.
 * @author Ankso
 */
Class Game
{
    /**
     * Class constructor. Loads all game data from the DB and initializes all private vars.
     * @param long $id The unique ID of the game that's going to be initialized.
     */
    function __construct($id)
    {
        global $DATABASES, $SERVER_INFO;
        
        if (!$id)
            die("Error while loading a game: No ID provided.");
        
        $this->_db = New Database($DATABASES['GAMES']);
        if ($result = $this->_db->ExecuteStmt(Statements::SELECT_GAME_DATA, $this->_db->BuildStmtArray("i", $id)))
        {
            // Load all the data.
            $this->_id = $id;
            if ($gameData = $result->fetch_assoc())
            {
                $this->_title = $gameData['title'];
                $this->_webpage = $gameData['webpage'];
                $this->_description = $gameData['description'];
                $this->_imagePath = $gameData['image_path'];
                $this->_developer = new Developer($gameData['developer_id'], $gameData['developer_name'], $gameData['developer_webpage'], $gameData['developer_description']);
                $this->_publisher = new Publisher($gameData['publisher_id'], $gameData['publisher_name'], $gameData['publisher_webpage'], $gameData['publisher_description']);
                // We'll need another query to load the game genres.
                if ($result = $this->_db->ExecuteStmt(Statements::SELECT_GAME_GENRES, $this->_db->BuildStmtArray("i", $id)))
                {
                    $this->_genres = array();
                    while ($genres = $result->fetch_assoc())
                        $this->_genres[] = $genres['name'];
                }
            }
            else
                die("Error while loading game " . $id . ". The game doesn't exists.");
        }
        else
            die("Error while loading game " . $id . ". There's a problem in the DB connection.");
    }
    
    /**
     * Class destructor.
     */
    function __destruct()
    {
    }
    
    /*
     * Note that there's no setters, because the data can be inserted only directly in the DB,
     * may be we should create something like an admin panel with these functions.
     */
    
     /**
     * Obtains the game's unique ID.
     * @return long The game's unique ID.
     */
    public function GetId()
    {
        return $this->_id;
    }
    
    /**
     * Obtains the game title/name.
     * @return string The game's title/name.
     */
    public function GetTitle()
    {
        return $this->_title;
    }
    
    /**
     * Obtains the game's official webpage.
     * @return string The game's official webpage link.
     */
    public function GetWebpage()
    {
        return $this->_webpage;
    }
    
    /**
     * Obtains the game's description.
     * @return string The game's description.
     */
    public function GetDescription()
    {
        return $this->_description;
    }
    
    /**
     * Obtains the game's genres.
     * @return array An array with all the game genres.
     */
    public function GetGenres()
    {
        return $this->_genres;
    }
    
    /**
     * Obtains the game's image path.
     * @return string An absolute or relative path to a game's cover.
     */
    public function GetImagePath()
    {
        return $this->_imagePath;
    }
    
    /**
     * Obtains the developer of the game.
     * @return array An array with all the data of the developer.
     */
    public function GetDeveloper()
    {
        return $this->_developer;
    }
    
    /**
     * Obtains the publisher of the game.
     * @return array An array with all the data of the publisher.
     */
    public function GetPublisher()
    {
        return $this->_publisher;
    }
    
    /**
     * Obtains the total number of users that plays this game. Note that this function creates a new DB connection on each call,
     * so the use of this function should be avoided when possible.
     * @return integer/boolean Returns the number of players of this game, or false if something goes wrong.
     */
    public function GetPlayersCount()
    {
        global $DATABASES;
        $usersDb = new Database($DATABASES['USERS']);
        if ($result = $usersDb->ExecuteStmt(Statements::SELECT_GAME_PLAYERS_COUNT, $usersDb->BuildStmtArray("i", $this->Getid())))
        {
            if ($row = $result->fetch_assoc())
                return $row['total_players'];
        }
        return false;
    }
    
    /**
     * Returns the number of times that a game has been recommended.
     * @return integer The number of times that the game has been recommended.
     */
    public function GetRecommendationsCount()
    {
        return 0; // Not yet implemented.
    }
    
    private $_id;          // The unique game's ID.
    private $_title;       // The game's title.
    private $_webpage;     // The game's official webpage.
    private $_description; // The game's description.
    private $_genres;      // An array with all the game genres.
    private $_imagePath;   // The path to the image of the game.
    private $_developer;   // The developer stored as a Developer class.
    private $_publisher;   // The publisher stored as a Publisher class.
    private $_db;          // The DB object.
}