<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
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

if (!isset($_POST['userId']))
    die(json_encode(array("status" => "FAILED")));

if ($_SESSION['userId'] != $_POST['userId'])
    die(json_encode(array("status" => "FAILED_BAD_TRY_M8")));

$user = new User($_SESSION['userId']);
$games = $user->GetAllGames();

if ($games == USER_HAS_NO_GAMES)
    exit(json_encode(array("status" => "USER_HAS_NO_GAMES")));

$gamesList = array(
    "status" => "SUCCESS",
    "list"   => array(),
);

foreach ($games as $i => $value)
{
    $gamesList['list'][] = array(
        "id"        => $games[$i]['id'],
        "title"     => $games[$i]['title'],
        "imagePath" => $games[$i]['imagePath'],
        "exeName"   => $games[$i]['exeName'],
    );
}
echo(json_encode($gamesList));
?>