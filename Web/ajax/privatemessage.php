<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();

if (!isset($_SESSION['userId']))
    die('<div style="background-color:#000000; padding:10px;">You must be logged in!</div>');

if (!isset($_GET['friendName']))
    die('<div style="background-color:#000000; padding:10px;">A friend must be specified.</div>');

// Create the user object
$user = new User($_SESSION['userId']);

// Check that the user is friend of the given username
if (GetIdFromUsername($_GET['friendName']) === USER_DOESNT_EXISTS)
    die('<div style="background-color:#000000; padding:10px;">The user <b>'. $_GET['friendName'] . '</b> doesn\'t exitst.<br />Please make sure that the nick you have specified in the command is correct and try again.</div>');
elseif (!$user->IsFriendOf($_GET['friendName']))
    die('<div style="background-color:#000000; padding:10px;">The receiver must be in your friends list!</div>');
else
    $friend = GetIdFromUsername($_GET['friendName']);

// Now we know that the user is logged in and that the friend specified is really a valid username and is really a friend of the user.
$conversationHistory = $user->GetPrivateConversation($friend);
// We can now set the messages viwed here as readed
$user->SetMessagesAsReaded($friend);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Private Messages</title>
<style type="text/css">
.sendPrivateMessage {
	width:50px;
	padding-left:3px;
	padding-right:3px;
	text-align:center;
	border:2px #FFFFFF solid;
	border-radius:0.7em;
	margin-top:7px;
	float:right;
}

.errorSending {
	float:left;
	color:#FF0000;
	margin-top:9px;
}

.sendPrivateMessage:hover {
	cursor:pointer;
	background-color:#333333;
}

.message {
}

.messageHeader {
	width:550px;
	margin-left:50px;
    margin-top:10px;
	text-align:left;
}

.messageBody {
	width:540px;
	margin-left:60px;
	text-align:left;
}
</style>
</head>
<body>
<div style="max-height:600px; width:650px; color:#FFFFFF; background-color:#000000; border:2px solid #FFFFFF; border-radius:0.7em;text-align:center; padding:15px;">
	<div id="newMessage" style="text-align:left; width:550px; margin-left:50px; margin-bottom:10px; border-bottom:2px #FFFFFF solid; padding-bottom:40px;">
		New message to <b><?php echo $_GET['friendName']; ?></b>:<br />
		<textarea id="newMessageBody" style="width:545px; height:70px; background-color:#808080;"></textarea>
		<div id="sendPrivateMessage" class="sendPrivateMessage" onclick="SendPrivateMessage(<?php echo $friend; ?>);">Send</div>
	</div>
	<i>Conversation history</i>
	<div id="conversationHistory">
    <?php
    if ($conversationHistory === USER_HAS_NO_MESSAGES)
        echo '<div id="emptyMessageHistory"><b>No recent private messages</b></div>';
    elseif ($conversationHistory === false)
        echo '<div id="emptyMessageHistory"><b>An error occurred. Please, try again in a few moments.</b></div>';
    else
    {
        foreach ($conversationHistory as $i => $value)
        { 
            echo '        <div class="message">';
            echo '            <div class="messageHeader">Sended by <b>', ($conversationHistory[$i]['sender'] === $friend ? $_GET['friendName'] : "You"), "</b> (", $conversationHistory[$i]['date'], ")</div>";
            echo '            <div class="messageBody">', $conversationHistory[$i]['message'], '</div>';
            echo '        </div>';
            // We can show a maximun of 7 messages in the history. TODO: Move this to a constant.
            if ($i > 7)
                break;
        }
    }
    ?>
	</div>
</div>
</body>
</html>