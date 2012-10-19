<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");

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
    die('FAILED');

if(isset($_POST['username']))
{
    $friendId = GetIdFromUsername($_POST['username']);
    if ($friendId === false)
        echo 'FAILED';
    elseif ($friendId === USER_DOESNT_EXISTS)
        echo 'USER_DOESNT_EXISTS';
    else
    {
        // Create the user object here, to avoid unnecessary queries executed in the DB
        $user = new User($_SESSION['userId']);
        $res = $user->SendFriendRequest($friendId, NULL);
        if ($res === USERS_ARE_FRIENDS)
            echo 'USER_IS_ALREADY_FRIEND';
        elseif ($res === FRIEND_REQUEST_ALREADY_SENT)
            echo 'REQUEST_ALREADY_SENT';
        elseif ($res === YOU_ALREADY_HAVE_FRIEND_REQUEST)
            echo 'YOU_ALREADY_HAVE_FRIEND_REQUEST';
        elseif ($res === false)
            echo 'FAILED';
        elseif ($res === true)
            echo 'SUCCESS';
        else
            echo 'FAILED';
    }
}
?>