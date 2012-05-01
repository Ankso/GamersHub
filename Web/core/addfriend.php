<?php
require_once("F:/GamersNet/GamersNet_Beta/Common/Common.php");
require_once("F:/GamersNet/GamersNet_Beta/Classes/User.Class.php");

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
        $temp = $_SESSION['user']->SendFriendRequest($friendId, null);
        if ($temp === USER_IS_ALREADY_FRIEND)
            echo 'USER_IS_ALREADY_FRIEND';
        elseif ($temp === REQUEST_ALREADY_SENT)
            echo 'REQUEST_ALREADY_SENT';
        elseif ($temp === false)
            echo 'FAILED';
        elseif ($temp === true)
            echo 'SUCCESS';
        else
            echo 'FAILED';
    }
}
?>