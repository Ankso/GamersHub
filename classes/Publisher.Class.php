<?php

/**
 * Simple storage class for a game publisher data.
 * @author Ankso
 */
class Publisher
{
    /**
     * Class constructor. Only the first parameter is needed in order to initilize the object. The other three are optional, to save access to the DB.
     * @param long $id The publisher unique ID.
     * @param string $name Optional. The publisher company name.
     * @param string $webpage Optional. The publisher official website.
     * @param string $description Optional. A brief description of the publisher.
     */
    function __construct($id, $name, $webpage, $description)
    {
        global $DATABASES;
        
        if (!$id && $id != 0)
            die("Error initializing Publisher class: No ID provided.");
        
        $this->_id = $id;
        if ($name && $webpage && $description)
        {
            $this->_name = $name;
            $this->_webpage = $webpage;
            if (!$description)
                $this->_description = "No description yet.";
            else
                $this->_description = $description;
        }
        else
        {
            $db = new Database($DATABASES['GAMES']);
            if ($result = $db->ExecuteStmt(Statements::SELECT_GAME_PUBLISHER_DATA, $db->BuildStmtArray("i", $id)))
            {
                if ($row = $result->fetch_assoc())
                {
                    $this->_name = $row['name'];
                    $this->_webpage = $row['webpage'];
                    $this->_description = $row['description'];
                }
                else
                    die("Error initializing Publisher class: Invalid ID provided.");
            }
        }
    }
    
    /**
     * Class destructor.
     */
    function __destruct()
    {
        // Nothing by the way
    }
    
    /****************************\
    *       CLASS GETTERS        *
    \****************************/
    
    public function GetId()
    {
        return $this->_id;
    }
    
    public function GetName()
    {
        return $this->_name;
    }
    
    public function GetWebpage()
    {
        return $this->_webpage;
    }
    
    public function GetDescription()
    {
        return $this->_description;
    }
    
    private $_id;
    private $_name;
    private $_webpage;
    private $_description;
}
?>