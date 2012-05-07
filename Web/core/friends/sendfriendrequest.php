<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../Common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../Classes/User.Class.php");

session_start();
if(isset($_POST['username']))
{
    $friendId = GetIdFromUsername($_POST['username']);
    if ($friendId === false)
        echo 'FAILED';
    elseif ($friendId === USER_DOESNT_EXISTS)
        echo 'USER_DOESNT_EXISTS';
    else
    {
        $res = $_SESSION['user']->SendFriendRequest($friendId, NULL);
        if ($res === USERS_ARE_FRIENDS)
            echo 'USER_IS_ALREADY_FRIEND';
        elseif ($res === FRIEND_REQUEST_ALREADY_SENT)
            echo 'REQUEST_ALREADY_SENT';
        elseif ($res === false)
            echo 'FAILED';
        elseif ($res === true)
            echo 'SUCCESS';
        else
            echo 'FAILED';
    }
}
?>