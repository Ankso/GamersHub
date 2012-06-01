<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
// Check if the user is logged in
if (!isset($_SESSION['userId']))
    die("You must be logged in!");

// Create the user object
$user = new User($_SESSION['userId']);

if (isset($_POST['message']))
{
    $message = strip_tags($_POST['message']);
    // TODO: Here we must much more things, like photos or links to profiles etc.
    // Parse all links:
    $message = preg_replace("#(http://)([a-z0-9_\-\?\/.=&~]*)#i", '<a href="http://$2" target="_blank">http://$2</a>', $message);
    // Parse youtube links and embed the video instead of the simple link:
    $message = preg_replace("#(<a href=\"http://(www.)?youtube.com)?/(v/|watch\?v=)([a-z0-9\-_~]+)([^<]+)(</a>)#i", '<br /><iframe width="640" height="480" src="http://www.youtube.com/embed/$4?wmode=transparent" frameborder="0" allowfullscreen></iframe><br />', $message);
    if ($user->SendBoardMessage($message))
        echo "SUCCESS";
    else
        die("FAILED");
}
elseif (isset($_POST['spaceOwner']) && isset($_POST['from']) && isset($_POST['to']))
{
    if (!$user->IsFriendOf((int)$_POST['spaceOwner']) && $user->GetId() !== (int)$_POST['spaceOwner'])
        die("You are not a friend of this user!");
    
    if (isset($_POST['from']) && isset($_POST['to']) && !isset($_POST['message']))
    {
        $from = (int)$_POST['from'];
        $to = (int)$_POST['to'];
        if ($user->GetId() == (int)$_POST['spaceOwner'])
            $spaceOwner = $user;
        else
            $spaceOwner = new User((int)$_POST['spaceOwner']);
        $boardMessages = $spaceOwner->GetBoardMessages($from, $to);
        if ($boardMessages === false)
            die("Error connecting to the server. Please, try again in a few moments.");
        elseif ($boardMessages === USER_HAS_NO_BOARD_MESSAGES)
            exit("You haven't write anything yet. Start ASAP!");
        else
        {
            $userAvatarHost = $user->GetAvatarHostPath();
            foreach ($boardMessages as $i => $value)
            {
?>
<div class="boardComment">
	<div class="boardCommentBody"><?php echo $boardMessages[$i]['message']; ?></div>
	<div class="boardCommentBottom"><?php echo "By ", ($spaceOwner->GetId() == $user->GetId() ? "You" : $spaceOwner->GetUsername()), " ", $boardMessages[$i]['date']; ?></div>
	<!--
	<div class="replyCommentBoard">
		<div style="border-radius:0.4em; border:1px #FFFFFF solid; width:50px; height:20px; margin-top:7px; margin-left:100%; background-color:#222222; cursor:pointer;"><a class="fancyboxCommentReply" href="#fancyCommentReply" style="color:#FFFFFF; text-decoration:none">Reply</a></div>
	</div>
	-->
</div>
<?php
            }
        }
    }
}
else
    die("Invalid parameters. Manipulated POST request?");
?>