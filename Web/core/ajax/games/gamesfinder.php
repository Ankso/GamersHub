<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");

if (isset($_POST['game']))
{
    $db = new Database($DATABASES['GAMES']);
    if ($result = $db->ExecuteStmt(Statements::SELECT_GAMES_BY_NAME, $db->BuildStmtArray("s", $_POST['game'] . "%")))
    {
        if ($result->num_rows === 0)
        {
            echo json_encode(
                array("status" => "NO_GAMES_FOUND")
            );
            exit();
        }
        
        $games = array(
            "status" => "SUCCESS",
            "list"   => array(),
        );
        
        while ($row = $result->fetch_assoc())
        {
            $games['list'][] = array(
                "id"    => $row['id'],
                "title" => $row['title'],
            );
        }
        echo json_encode($games);
    }
}
?>