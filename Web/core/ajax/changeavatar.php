<?php 
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
{
    header("http://localhost/login.php");
    die();
}
// Create the user object
$user = new User($_SESSION['userId']);

if (isset($_POST['gravatar']))
{
    if ($_POST['gravatar'] === "gravatar")
    {
        if ($user->SetAvatarHost("http://www.gravatar.com/avatar/" . md5(strtolower(trim($user->GetEmail()))) . "?d=http://gamersnet.no-ip.org/images/default_avatar.png&s=200&r=pg"))
            header("location:../" . $user->GetUsername());
        else
            die("A fatal error occurred while changing your settings. Please try again in a few moments.");
    }
    die('A fatal error occurred while changing your settings ($_POST manipulation?). Please try again in a few moments.');
}
elseif (isset($_FILES['avatar']))
{
    if (($_FILES['avatar']['type'] == "image/gif" || $_FILES['avatar']['type'] == "image/jpeg" || $_FILES['avatar']['type'] == "image/pjpeg" || $_FILES['avatar']['type'] == "image/png") && $_FILES['avatar']['size'] <= 2024000)
    {
        if ($_FILES['avatar']['error'] > 0)
            die("Error when uploading the image. Please try again.");
        else
        {
            $relativeHost = "/images/users/". $user->GetUsername() ."/avatar/". $user->GetUsername() ."s_avatar.". substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], ".") + 1);
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/images/users/". $user->GetUsername() ."/avatar"))
                if (!mkdir($_SERVER['DOCUMENT_ROOT'] . "/images/users/". $user->GetUsername() ."/avatar", 0777, true))
                    die("A fatal error occurred while uploading the avatar. Please try again soon.");

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $relativeHost))
            {
                if (!$user->SetAvatarHost("http://gamersnet.no-ip.org" . $relativeHost))
                    die("A fatal error occurred while connecting to the database server. Please try again soon.");
                else
                    header("location:../". $user->GetUsername());
            }
            else
                die("An error occurred while uploading your avatar. Please try again.");
        }
    }
    else
        die("Your avatar must be a .jpeg, .png or .gif file and it's size can't be larger than 200Kb");
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Upload new avatar</title>
</head>
<body>
<div style="height:200px; width:450px; color:#FFFFFF; background-color:#000000; border:2px solid #FFFFFF; border-radius:0.7em;text-align:center;">
<form action="ajax/changeavatar.php" method="post" enctype="multipart/form-data" style="margin-top:30px; border:1px #FFFFFF solid; border-radius:0.7em; margin-right:15px; margin-left:15px;padding:10px 10px 10px 10px;">
	<input type="checkbox" name="gravatar" value="gravatar" <?php if ($user->IsUsingGravatar()) echo 'checked="checked"'; ?> />Use my <a href="http://gravatar.com" target="_blank" style="color:orange;">Gravatar</a> associated with this email
	<br />or...<br />
	<label for="file">Upload an image:</label><input type="file" name="avatar" id="avatar" style="background-color:#999999;border:1px #FFFFFF solid;border-radius:0.5em;" /><br /><br />
	<input type="submit" name="upload" value="Submit" />
</form>
</div>
</body>
</html>
<?php
}
?>