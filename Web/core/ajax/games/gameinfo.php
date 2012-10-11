<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/Game.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

$sessionsHandler = new CustomSessionsHandler();
session_set_save_handler(
    array($sessionsHandler, "open"),
    array($sessionsHandler, "close"),
    array($sessionsHandler, "read"),
    array($sessionsHandler, "write"),
    array($sessionsHandler, "destroy"),
    array($sessionsHandler, "gc")
    );
register_shutdown_function("session_write_close");
session_start();
if (!isset($_SESSION['userId']))
    die(json_encode(array("status" => "NOT_LOGGED_IN")));

if (!isset($_POST['id']))
    die(json_encode(array("status" => "FAILED")));
    
$game = new Game($_POST['id']);
echo json_encode(
    array(
        "status"      => "SUCCESS",
        "title"       => $game->GetTitle(),
        "genres"      => $game->GetGenres(),
        "webpage"     => $game->GetWebpage(),
        "description" => $game->GetDescription(),
        "imagePath"   => $game->GetImagePath(),
        "developer"   => array(
            "id"          => $game->GetDeveloper()->Getid(),
            "name"        => $game->GetDeveloper()->GetName(),
            "webpage"     => $game->GetDeveloper()->GetWebpage(),
            "description" => $game->GetDeveloper()->GetDescription(),
        ),
        "publisher"   => array(
            "id"          => $game->GetPublisher()->Getid(),
            "name"        => $game->GetPublisher()->GetName(),
            "webpage"     => $game->GetPublisher()->GetWebpage(),
            "description" => $game->GetPublisher()->GetDescription(),
        ),
        "totalPlayers" => $game->GetplayersCount(),
    )
);
?>