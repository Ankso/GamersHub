<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

session_start();
if (!isset($_SESSION['userId']))
{
    header("http://localhost/login.php");
    die();
}
// Create the user object
$user = new User($_SESSION['userId']);

if (isset($_FILES['avatar']))
{
    $message = "Your avatar has been changed";
    if (($_FILES['avatar']['type'] == "image/gif" || $_FILES['avatar']['type'] == "image/jpeg" || $_FILES['avatar']['type'] == "image/pjpeg" || $_FILES['avatar']['type'] == "image/png") && $_FILES['avatar']['size'] <= 2024000)
    {
        if ($_FILES['avatar']['error'] > 0)
            $message = "Error when uploading the image. Please try again.";
        else
        {
            $relativePath = "/images/users/". $user->GetUsername() ."/avatar/". $user->GetUsername() ."s_avatar.". substr($_FILES['avatar']['name'], strrpos($_FILES['avatar']['name'], ".") + 1);
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/images/users/". $user->GetUsername() ."/avatar"))
                if (!mkdir($_SERVER['DOCUMENT_ROOT'] . "/images/users/". $user->GetUsername() ."/avatar", 0777, true))
                    die("A fatal error occurred while uploading the avatar. Please try again soon.");

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $relativePath))
            {
                if (!$user->SetAvatarPath($relativePath))
                    die("A fatal error occurred while connecting to the database server. Please try again soon.");
                else
                    header("location:../". $user->GetUsername());
            }
            else
                $message = "An error occurred while uploading your avatar. Please try again.";
        }
    }
    else
        $message = "Your avatar must be a .jpeg, .png or .gif file and it's size can't be larger than 200Kb";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Upload new avatar</title>
</head>
<body>
<div style="height:200px; width:400px; color:#FFFFFF; background-color:#000000; border:2px solid #FFFFFF; border-radius:0.7em;text-align:center;">
<form action="ajax/changeavatar.php" method="post" enctype="multipart/form-data" style="margin-top:50px; border:1px #FFFFFF solid; border-radius:0.7em; margin-right:15px; margin-left:15px;padding:10px 10px 10px 10px;">
	<label for="file">Image:</label><input type="file" name="avatar" id="avatar" style="background-color:#999999;border:1px #FFFFFF solid;border-radius:0.5em;" />
	<br /><br /><input type="submit" name="upload" value="Upload" />
	<?php if (isset($message)) echo "<br />", $message; ?>
</form>
</div>
</body>
</html>