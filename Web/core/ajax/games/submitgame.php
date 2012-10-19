<?php
require($_SERVER['DOCUMENT_ROOT'] . "/../common/PreparedStatements.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/SharedDefines.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../common/Common.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/SessionHandler.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/Database.Class.php");
require($_SERVER['DOCUMENT_ROOT'] . "/../classes/User.Class.php");

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
/*
if (!isset($_SESSION['userId']))
{
    header("http://gamershub.no-ip.org/login.php");
    die();
}
// Create the user object
$user = new User($_SESSION['userId']);
*/
global $DATABASES;
$gamesDb = new Database($DATABASES['GAMES']);
$developers = array();
if ($result = $gamesDb->Execute("SELECT id, name FROM game_developers"))
{
    while ($row = $result->fetch_assoc())
    {
        $developers[] = array(
            "id"   => $row['id'],
            "name" => $row['name'],
        );
    }
}
else
{
    $developers[0] = array(
        "id"   => "0",
        "name" => "Error",
    );
}
$publishers = array();
if ($result = $gamesDb->Execute("SELECT id, name FROM game_publishers"))
{
    while ($row = $result->fetch_assoc())
    {
        $publishers[] = array(
            "id"   => $row['id'],
            "name" => $row['name'],
        );
    }
}
else
{
    $publishers[0] = array(
        "id"   => "0",
        "name" => "Error",
    );
}
$genres = array();
if ($result = $gamesDb->Execute("SELECT id, name FROM game_genres"))
{
    while ($row = $result->fetch_assoc())
    {
        $genres[] = array(
            "id"   => $row['id'],
            "name" => $row['name'],
        );
    }
}
else
{
    $genres[0] = array(
        "id"   => "0",
        "name" => "Error",
    );
}
if (isset($_POST['title']) && isset($_POST['webpage']) && isset($_POST['description']) && isset($_POST['developer']) && isset($_POST['publisher']) && isset($_POST['exename']) && isset($_FILES['cover']) && isset($_POST['genres']))
{
    if (GameExists($_POST['title']))
        die("That game is already in the database!");
    
    if (($_FILES['cover']['type'] == "image/gif" || $_FILES['cover']['type'] == "image/jpeg" || $_FILES['cover']['type'] == "image/pjpeg" || $_FILES['cover']['type'] == "image/png") && $_FILES['cover']['size'] <= 2024000)
    {
        if ($_FILES['cover']['error'] > 0)
            die("Error when uploading the image. Please try again.");
        else
        {
            $relativeHost = "/images/games/". str_replace(" ", "_", $_POST['title']) ."/". str_replace(" ", "_", $_POST['title']) ."_cover.". substr($_FILES['cover']['name'], strrpos($_FILES['cover']['name'], ".") + 1);
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . "/images/games/". str_replace(" ", "_", $_POST['title'])))
                if (!mkdir($_SERVER['DOCUMENT_ROOT'] . "/images/games/". str_replace(" ", "_", $_POST['title']), 0777, true))
                    die("A fatal error occurred while uploading the game data. Please try again soon.");

            if (move_uploaded_file($_FILES['cover']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $relativeHost))
            {
                if (!$gamesDb->ExecuteStmt(Statements::INSERT_GAME_DATA, $gamesDb->BuildStmtArray("sssiiss", $_POST['title'], $_POST['webpage'], $_POST['description'], $_POST['developer'], $_POST['publisher'], $relativeHost, $_POST['exename'])))
                    die("An error occurred while uploading the game data. Please try again.");
                $gameId = GameExists($_POST['title']);
                if (!$gameId)
                    die("This shit is broken man." . var_dump($gameId));
                // A loop with querys? that's not good but anyway this is only a temporal solution...
                foreach ($_POST['genres'] as $i => $value)
                {
                    if (!$gamesDb->ExecuteStmt(Statements::INSERT_GAME_DATA_GENRE, $gamesDb->BuildStmtArray("ii", $gameId, $_POST['genres'][$i])))
                        die("An error occurred while uploading the game data. Please try again.");
                }
                //header("location:../../". $user->GetUsername());
            }
            else
                die("An error occurred while uploading the game data. Please try again.");
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Submit new game</title>
</head>
<body>
<div style="height:600px; width:650px; overflow:auto; color:#FFFFFF; background-color:#000000; border-radius:0.7em; text-align:center; padding:20px;">
<form action="/core/ajax/games/submitgame.php" method="post" enctype="multipart/form-data" style="border-radius:0.7em; padding:25px; text-align:left; margin-left:100px;">
	Game title: <input type="text" name="title" style="margin-left:85px;"/><br/><br/>
	Game official webpage: <input type="text" name="webpage" style="margin-left:8px;"/><br/><br/>
	Game description: <input type="textarea" name="description" style="margin-left:40px;"/><br/><br/>
	Game genres: 
	<?php 
	foreach ($genres as $i => $value)
	{
	?>
		<br/><input type="checkbox" name="genres[]" value="<?php echo $genres[$i]['id']; ?>" style="margin-left:153px;"><?php echo $genres[$i]['name']; ?>
	<?php
	}
	?>
	<br/><br/>Game developer: 
	<select name="developer" style="margin-left:46px;">
	<?php
	foreach ($developers as $i => $value)
	{
	?>
		<option value="<?php echo $developers[$i]['id']; ?>"><?php echo $developers[$i]['name']; ?></option>
	<?php
	}
	?>
	</select><br/><br/>
	Game publisher:
	<select name="publisher" style="margin-left:52px;">
	<?php
	foreach ($publishers as $i => $value)
	{
	?>
		<option value="<?php echo $publishers[$i]['id']; ?>"><?php echo $publishers[$i]['name']; ?></option>
	<?php
	}
	?>
	</select><br/><br/>
	Exe name: <input type="text" name="exename" style="margin-left:86px;"/> (separated by ";")<br/><br/>
	<label for="file">Upload game cover: </label><input type="file" name="cover" id="cover" style="background-color:#999999;border:1px #FFFFFF solid;border-radius:0.5em; margin-left:24px;" /><br /><br />
	<div style="margin-left:160px;"><input type="submit" name="upload" value="Submit"/></div>
</form>
</div>
</body>
</html>