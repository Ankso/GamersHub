<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

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
// TODO: This must be moved to a file in the core/sessions folder, may be named destroy.php
if (isset($_SESSION['userId']))
{
    $user = new User($_SESSION['userId']);
    $user->SetLastLogin(date("Y-m-d H:i:s", time()));
    $user->SetLastIp($_SERVER['REMOTE_ADDR']);
    $user->SetOnline(false);
    $user->DestroyRandomSessionId();
}
session_destroy();
header("location:index.php");
?>