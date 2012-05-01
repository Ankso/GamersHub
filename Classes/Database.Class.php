<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "gamersNet_Beta/Common/SharedDefines.php");

/**
 * Main Database Class.<p>Handles all Database tasks like conection, query execution, etc.</p>
 */
class Database
{
    /**
     * Class constructor, establishes a MySQL connection to the specified DB.
     * @param $dbToConnect Database where the operations are going to be executed.
     */
    function __construct($dbToConnect)
    {
        if (!isset($dbToConnect))
            die("Fatal error: Missing argument when initializing the Database class.");
        
        global $SERVER_INFO;
        $this->_connection = mysql_connect($SERVER_INFO['HOST'], $SERVER_INFO['USERNAME'], $SERVER_INFO['PASSWORD']) or die("Fatal error initializing Database class: ". mysql_error());
        mysql_select_db($dbToConnect, $this->_connection) or die("Fatal error initializing Database class: ". mysql_error());
    }
    
    /**
     * Class destructor, disconnects from the connected database.
     */
    function __destructor()
    {
        mysql_close($this->_connection);
    }
    
    /**
     * Executes a query on the database.
     * @param $query
     * @return $resource Returns resource on success for SELECT or other operations that return a resource value, or true for INSERT type operation.<p>Returns false on failure.</p>
     */
	public function Execute($query)
	{
	    return mysql_query($query, $this->_connection);
	}
	
	private $_connection;
}
?>