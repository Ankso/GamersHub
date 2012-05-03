<?php
require_once("F:/GamersNet/GamersNet_Beta/Common/SharedDefines.php");

/**
 * Main Database Class. Handles all Database tasks like conection, query execution, etc, using the mysqli library.
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
        $this->_mysqli = new mysqli($SERVER_INFO['HOST'], $SERVER_INFO['USERNAME'], $SERVER_INFO['PASSWORD'], $dbToConnect);
        if ($this->_mysqli->connect_errno)
            die("Fatal error ". $this->_mysqli->connect_errno .": ". $this->_mysqli->connect_error);
        /*
        if (!isset($dbToConnect))
            die("Fatal error: Missing argument when initializing the Database class.");
        global $SERVER_INFO;
        $this->_connection = mysql_connect($SERVER_INFO['HOST'], $SERVER_INFO['USERNAME'], $SERVER_INFO['PASSWORD']) or die("Fatal error initializing Database class: ". mysql_error());
        mysql_select_db($dbToConnect, $this->_connection) or die("Fatal error initializing Database class: ". mysql_error());
        */
    }
    
    /**
     * Class destructor, disconnects from the connected database.
     */
    function __destructor()
    {
        $this->_mysqli->close();
    }
    
    /**
     * Executes a query on the database.
     * @param string $query The query to be executed.
     * @return $resource Returns mysqli_result on success for SELECT or other operations that return a result value, or true for INSERT type operation.<p>Returns false on failure.</p>
     */
	public function Execute($query)
	{
	    return $this->_mysqli->query($query);
	}
	
	/**
	 * Prepares and executes a prepared statement. It can execute the stmt once or multiple times.
	 * @param string $query The prepared statement to be executed.
	 * @param array $params The params that are going to be inserted in the prepared statement. $params is a tridimensional array with the following structure:<br/>$params = array(<br/>0 => array(<br/>0 => array(</br>0 => 'i',<br/>1 => $myInt,<br/>)<br/>)<br/>);</p>
	 * @return mixed Returns a mysqli_result on success for SELECT or other operations that return a result value, or true for INSERT type operation.<p>Returns an array of mysqli_results if multiple sentences return a result</p><p>Returns false on failure.</p>
	 */
	public function ExecuteStmt($query, $params)
	{
	    // We'll need both parameters. To execute a query without variables, use Database::query() instead.
	    if (!isset($query) || !isset($params))
	        return false;
	    if(!is_array($params))
	        return false;
	    // Prepare the stmt
	    if (!($stmt = $this->_mysqli->prepare($query)))
	        return false;
	    /**
	     * Determine if the stmt must be executed only once or more times
	     * The $params structure is:
	     * $params = array(
	     *     0 => array(
	     *         0 => array(
	     *             0 => 'i',
	     *             1 => $myInt1
	     *             ),
	     *         1 => array(
	     *             0 => 'i',
	     *             1 => $myInt2
	     *             )
	     *         )
	     *     );
	     * so...
	     */
	    // If we have multiple sentences to be executed, 
	    $results = array();
	    $eCount = count($params);
	    $vCount = count($params[0]);
	    for ($j = 0; $j < $eCount; ++$j)
	    {
	        for ($i = 0; $i < $vCount; ++$i)
	        {
	            $dynTypeIdentifier = "a". $i;
	            $$dynTypeIdentifier = $params[$j][$i][0];
	            $dynVar = "b". $i;
	            $$dynVar = $params[$j][$i][1];
	            if (!$stmt->bind_param($$dynTypeIdentifier, $$dynVar))
	                return false;
	        }
	        if (!$stmt->execute())
	            return false;
	        $results[] = $stmt->get_result();
	    }
	    if (count($results) == 1)
	        return $results[0];
	    return $results;
	}
	
	/**
	 * Creates a valid array for one statement to use it when calling ExecuteStmt. Multiple params can be sended, but it must be a par number of them.
	 * @param string A valid type identifier for stmt::bind_param (i, d, s, b)
	 * @param mixed The variable related with the previous type identifier.
	 * @return bool Returns true if success, false in case of failure.
	 */
	public function BuildStmtSimpleArray()
	{
	    if (func_num_args() < 2 || func_num_args() % 2 != 0)
	        return false;
	    
	    $args = func_get_args();
	    $StmtArray = array(
	        0 => array()
	        );
	    for ($i = 0; $i < func_num_args(); $i += 2)
	    {
	        $StmtArray[0][] = array(
	            0 => $args[$i],
	            1 => $args[$i + 1]
	            );
	    }
	}
	
	private $_mysqli;
}
?>