<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");

class CustomSessionsHandler
{
    public function open()
    {
        global $DATABASES;
        // Note that the name of the variable doesn't fits the standards, that's because it shouldn't be modified (it's more like a constant)
        $this->_sessionsDb = New Database($DATABASES['SESSIONS']);
        // We don't need to return false if the connection to the DB can't be stablished, because the Database contructor kill the script execution if something fails (with die())
        return true;
    }
    
    public function close()
    {
        // Nothing to do here really
        unset($this->_sessionsDb);
        return true;
    }
    
    public function read($id)
    {
        $id = $this->_sessionsDb->RealEscapeString($id);
        if ($result = $this->_sessionsDb->Execute("SELECT data FROM sessions WHERE id = '" . $id . "'"))
        {
            if ($result->num_rows !== 0)
            {
                $row = $result->fetch_assoc();
                return $row['data'];
            }
        }
        return "";
    }
    
    public function write($id, $data)
    {
        $id = $this->_sessionsDb->RealEscapeString($id);
        $data = $this->_sessionsDb->RealEscapeString($data);
        $lastUpdate = $this->_sessionsDb->RealEscapeString(time());
        if ($this->_sessionsDb->Execute("REPLACE INTO sessions VALUES ('" . $id . "', '" . $data . "', " . $lastUpdate . ")"))
            return true;
        return false;
    }
    
    public function destroy($id)
    {
        $id = $this->_sessionsDb->RealEscapeString($id);
        if ($this->_sessionsDb->Execute("DELETE FROM sessions WHERE id = '" . $id . "'"))
            return true;
        return false;
    }
    
    public function gc($max)
    {
        $outdated = $this->_sessionsDb->RealEscapeString(time() - $max);
        if ($this->_sessionsDb->Execute("DELETE FROM sessions WHERE last_update < " . $outdated))
            return true;
        return false;
    }
    
    private $_sessionsDb;
}
?>