<?php

/**
 * Simple storage class for a game developer data.
 * @author Ankso
 */
class Developer
{
    /**
     * Class constructor. Only the first parameter is needed in order to initilize the object. The other three are optional, to save access to the DB.
     * @param long $id The developer unique ID.
     * @param string $name Optional. The developer company name.
     * @param string $webpage Optional. The developer official website.
     * @param string $description Optional. A brief description of the developer.
     */
    function __construct($id, $name, $webpage, $description)
    {
        global $DATABASES;
        
        if (!$id && $id != 0)
            die("Error initializing Developer class: No ID provided.");
        
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
            if ($result = $db->ExecuteStmt(Statements::SELECT_GAME_DEVELOPER_DATA, $db->BuildStmtArray("i", $id)))
            {
                if ($row = $result->fetch_assoc())
                {
                    $this->_name = $row['name'];
                    $this->_webpage = $row['webpage'];
                    $this->_description = $row['description'];
                }
                else
                    die("Error initializing Developer class: Invalid ID provided.");
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