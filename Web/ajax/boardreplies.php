<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
// Check if the user is logged in
if (!isset($_SESSION['userId']))
    die("You must be logged in!");

// Create the user object
$user = new User($_SESSION['userId']);

if (isset($_POST['messageId']))
{
    if (isset($_POST['reply']))
    {
        $reply = strip_tags($_POST['reply']);
        // TODO: Here we must proccess things like links to videos or photos, etc.
        if ($user->SendBoardMessage($message))
            echo "SUCCESS";
        else
            die("FAILED");
    }
    else
    {
        $messageId = (int)$_POST['reply'];
        $boardReplies = $user->GetBoardMessageReplies($messageId);
        if ($boardReplies === false)
            die("Error connecting to the server. Please, try again in a few moments.");
        elseif ($boardReplies === USER_COMMENT_HAS_NO_REPLIES)
            exit("This comment hasn't any replies yet.");
        else
        {
            foreach ($boardReplies as $i => $value)
            {
?>
<div class="boardCommentReply">
	<div class="boardCommentReplyBody">
		<div class="boardCommentReplyAvatar"></div>
		<div class="boardCommentReplyContent"></div>
	</div>
	<div class="boardCommentReplyBottom"></div>
</div>
<?php
            }
        }
    }
}
else
    die("Invalid parameters. Manipulated POST request?");
?>