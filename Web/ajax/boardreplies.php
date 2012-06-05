<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
// Check if the user is logged in
if (!isset($_SESSION['userId']))
    die("You must be logged in!");

// Create the user object
$user = new User($_SESSION['userId']);

// If only the reply ID is sent, then it's to delete it
if (isset($_POST['replyId']))
{
    // Remove the reply
    // TODO: Filter input!
    if ($user->DeleteBoardMessageReply((int)$_POST['replyId']))
        echo "SUCCESS";
    else
        die("FAILED");
}
elseif (isset($_POST['messageId']) && isset($_POST['reply']))
{
    // TODO: Check messageId, it must be a valid message ID (the writer of the message with the given ID must be the sender of the reply or a friend of him)
    $reply = strip_tags($_POST['reply']);
    // TODO: Here we must parse much more things, like photos or links to profiles etc.
    // Parse all links. TODO: HTTPS protocol is not yet supported.
    $reply = preg_replace("#(http://)([a-z0-9_\-\?\/.=&~]*)#i", '<a href="http://$2" target="_blank">http://$2</a>', $reply);
    // Parse youtube links and embed the video instead of the simple link. The code is similar to the one in boardmessages.php, but the video window is smaller.
    $reply = preg_replace("#(<a href=\"http://(www.)?youtube.com)?/(v/|watch\?v=)([a-z0-9\-_~]+)([^<]+)(</a>)#i", '<div style="text-align:center;"><iframe width="480" height="360" src="http://www.youtube.com/embed/$4?wmode=transparent" frameborder="0" allowfullscreen></iframe></div><br />', $reply);
    if ($user->SendBoardMessageReply((int)$_POST['messageId'], $reply))
        echo "SUCCESS";
    else
        die("FAILED");
}
else
    die("Invalid parameters. Manipulated POST request?");
?>