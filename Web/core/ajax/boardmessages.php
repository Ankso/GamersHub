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
// Check if the user is logged in
if (!isset($_SESSION['userId']))
    die("You must be logged in!");

// Create the user object
$user = new User($_SESSION['userId']);

if (isset($_POST['message']))
{
    // Create new message board
    $message = strip_tags($_POST['message']);
    // TODO: Here we must parse much more things, like photos or links to profiles etc.
    // Parse all links:
    $message = preg_replace("#(http(s)?://)([a-z0-9_\-\?\/.=&~]*)#i", '<a href="http$2://$3" target="_blank">http$2://$3</a>', $message);
    // Parse youtube links and embed the video instead of the simple link:
    $message = preg_replace("#(<a href=\"http://(www.)?youtube.com)?/(v/|watch\?v=)([a-z0-9\-_~]+)([^<]+)(</a>)#i", '<div style="text-align:center;"><iframe width="640" height="480" src="http://www.youtube.com/embed/$4?wmode=transparent" frameborder="0" allowfullscreen></iframe></div><br />', $message);
    if ($user->SendBoardMessage($message))
        echo "SUCCESS";
    else
        die("FAILED");
}
elseif(isset($_POST['messageId']) && isset($_POST['spaceOwner']))
{
    // Delete a message board
    // Messages can only be removed from self space
    if ($user->GetId() !== (int)$_POST['spaceOwner'])
        die("FAILED");
    // Anyway, the message won't be deleted of the user is not the writer.
    if ($user->DeleteBoardMessage((int)$_POST['messageId']))
        echo "SUCCESS";
    else
        die("FAILED");
}
elseif (isset($_POST['spaceOwner']) && isset($_POST['from']) && isset($_POST['to']))
{
    // Load the messages between the specified interval
    if (!$user->IsFriendOf((int)$_POST['spaceOwner']) && $user->GetId() !== (int)$_POST['spaceOwner'])
        die("You are not a friend of this user!");
    
    if (isset($_POST['from']) && isset($_POST['to']) && !isset($_POST['message']))
    {
        $from = (int)$_POST['from'];
        $to = (int)$_POST['to'];
        $isOwner = false;
        if ($user->GetId() == (int)$_POST['spaceOwner'])
        {
            $spaceOwner = $user;
            $isOwner = true;
        }
        else
            $spaceOwner = new User((int)$_POST['spaceOwner']);
        $boardMessages = $spaceOwner->GetBoardMessages($from, $to);
        if ($boardMessages === false)
            die("Error connecting to the server. Please, try again in a few moments.");
        elseif ($boardMessages === USER_HAS_NO_BOARD_MESSAGES)
            exit("You haven't write anything yet. Start ASAP!");
        else
        {
            // Create an array with the IDs of the messages loaded to load the replies for those messages.
            $messageIds = array();
            foreach ($boardMessages as $i => $value)
                $messageIds[] = $boardMessages[$i]['messageId'];
            $boardReplies = $spaceOwner->GetBoardMessageReplies($messageIds);
            if ($boardReplies === false)
                die("Error connecting to the server. Please, try again in a few moments.");
            $userAvatarHost = $user->GetAvatarHostPath();
            foreach ($boardMessages as $i => $value)
            {
?>
<div class="boardComment" data-id="<?php echo $boardMessages[$i]['messageId']; ?>">
	<?php if ($isOwner) { ?><div class="deleteBoardComment" style="display:none"><img src="images/delete_16.png" /></div> <?php } ?>
	<div class="boardCommentBody"><?php echo $boardMessages[$i]['message']; ?></div>
	<div class="boardCommentBottom"><?php echo "By ", ($spaceOwner->GetId() == $user->GetId() ? "you" : $spaceOwner->GetUsername()), " ";?><span class="timestamp" data-timestamp="<?php echo strtotime($boardMessages[$i]['date']); ?>">Unknown time ago</span></div>
	<div class="repliesCommentBoard" style="display:none">
		<div>
	        <?php
	            foreach ($boardReplies[$i] as $j => $val)
	            {
	                if ($boardReplies[$i][$j]['replyId'] !== USER_COMMENT_HAS_NO_REPLIES)
	                {
	        ?>
    		<div class="boardCommentReply" data-id="<?php echo $boardReplies[$i][$j]['replyId']; ?>">
    			<?php if ($isOwner || $boardReplies[$i][$j]['username'] == $user->GetUsername()) { ?><img src="images/delete_16.png" class="deleteBoardReply" /> <?php } ?>
    			<div class="boardCommentReplyBody" <?php if ($boardReplies[$i][$j]['senderId'] == $spaceOwner->GetId()) echo 'style="background-color:rgba(204, 153, 51, 0.1);"'?>>
    				<div class="boardCommentReplyAvatar"><img src="<?php echo $boardReplies[$i][$j]['avatarPath']; ?>" style="width:40px; height:40px; border-radius:0.3em;" alt="avatar" /></div>
    				<div class="boardCommentReplyContent"><?php echo $boardReplies[$i][$j]['message']; ?></div>
    			</div>
    			<div class="boardCommentReplyBottom" <?php if ($boardReplies[$i][$j]['senderId'] == $spaceOwner->GetId()) echo 'style="background-color:rgba(204, 153, 51, 0.1);"'?>>By <?php echo ($boardReplies[$i][$j]['username'] == $user->Getusername() ? "you" : $boardReplies[$i][$j]['username']), " ";?><span class="timestamp" data-timestamp="<?php echo strtotime($boardReplies[$i][$j]['date']); ?>">Unknown time ago</span></div>
    		</div>
		    <?php
	                }
	            }
		    ?>
		</div>
    	<div class="newReplyCommentBoard">
    		<div class="newReplyCommentBoardInput">
    			<input class="newReplyCommentBoardInputTextbox" type="text" value="" />
    			<div class="newReplyCommentBoardInputSend" onclick="space.SendMessageBoardReply(event);" data-id="<?php echo $messageIds[$i]; ?>">Comment</div>
    		</div>
    		<div class="newReplyCommentBoardAvatar"><img src="<?php echo $userAvatarHost; ?>" style="width:40px; height:40px; border:1px #00FF00 solid; border-radius:0.3em;" /></div>
    	</div>
	</div>
	<div class="displayCommentBoardReplies">Show Replies</div>
</div>
<?php
            }
        }
    }
}
else
    die("Invalid parameters. Manipulated POST request?");
?>