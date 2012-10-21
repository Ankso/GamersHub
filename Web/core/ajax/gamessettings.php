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
if(!isset($_SESSION['userId']))
    die("Error: you must be logged in!");

$user = new User($_SESSION['userId']);
$games = $user->GetAllGames();
?>
<script>
$(document).ready(function() {
    $("div.myGamesListedGame").each(function(index) {
		// We should skip the first element, the "Add game" box.
        if (index > 0)
        {
            myGames.addedGames[index - 1] = $(this).attr("data-id");
            ++myGames.totalAddedGames;
        }
    });
    $("div.myGamesMenuOption").click(function(event) {
    	myGames.MenuOptionClick(event);
    });
    $("div#myGamesOptionDatabase").trigger("click");
	$("input#myGamesSearchInput").focusin(function(event) {
        if ($(event.target).val() == "Search in the database...")
            $(event.target).val("");
    });
    $("input#myGamesSearchInput").focusout(function(event) {
        if ($(event.target).val() == "")
            $(event.target).val("Search in the database...");
    });
    $("input#myGamesSearchInput").keyup(function(event) {
        if ($("input#myGamesSearchInput").val() != "")
            myGames.LookUpForGame($("input#myGamesSearchInput").val());
        else
            $("div#myGamesListContainer").html("");
    });
    $("div.myGamesGameViewBorderLeft").click(function(/*event*/) {
        $("div#myGamesGameView").fadeOut(400, function() {
			if (myGames.backToGamesList)
			{
				$("div#myGamesOptionMyGames").trigger("click");
				myGames.backToGamesList = false;
			}
            $("div#myGamesSearchInputContainer").show("slide", { direction: "left" }, 500);
        });
        $("input#myGamesSearchInput").val("Search in the database...");
    });
    $("div.myGamesListedGame").click(function (event) {
        myGames.backToGamesList = true;
        $("#myGamesSearchInputContainer").hide();
        myGames.LoadGame($(event.target.parentElement.parentElement).attr("data-id"));
        // This timeout is only for visual purposes, it doesn't affect functionality.
		setTimeout(function () {
			$("div#myGamesOptionDatabase").trigger("click");
		}, 50);
    });
    $("div#myGamesListedGameAddNew").unbind("click");
    $("div#myGamesListedGameAddNew").click(function() {
		myGames.backToGamesList = false;
		if (!$("div#myGamesGameView").is(":hidden"))
			$("div#myGamesGameView").hide();
		$("div#myGamesSearchInputContainer").show();
		$("input#myGamesSearchInput").val("Search in the database...");
		$("div#myGamesOptionDatabase").trigger("click");
    });
    $("a#myGamesSubmitNewGame").fancybox();
    // Calculate the height of the div, to allow the overflow property to work properly.
    $("div#myGamesTab").height($(window).height() - 51);
    $("div#myGamesTab").jScrollPane({
		showArrows: true,
    });
    // This will do any pre-programmed action, for example, you can have a link to a game in the main user space,
    // with this, that link can open the My Games control panel and load automatically the game data.
    if (space.onControlPanelOpenAction)
    {
        if (space.onControlPanelOpenAction.action && space.onControlPanelOpenAction.data)
        {
            switch (space.onControlPanelOpenAction.action)
            {
                case "loadGame":
                    myGames.LoadGame(space.onControlPanelOpenAction.data);
                    break;
                default:
                    break;
            }
        }
    }
});
</script>
<div class="myGamesMenu">
	<div class="myGamesMenuTop"><strong><i>GamersZone&trade;</i></strong></div>
	<div id="myGamesOptionDatabase" class="myGamesMenuOption">Database</div>
	<div id="myGamesOptionMyGames" class="myGamesMenuOption">My games</div>
	<div id="myGamesOptionRecommendations" class="myGamesMenuOption">Recommendations</div>
</div>
<div id="myGamesDatabase" class="myGamesTab">
	<div id="myGamesSearchInputContainer" class="myGamesSearchInputContainer">
    	<input id="myGamesSearchInput" class="myGamesSearchInput" type="text" value="Search in the database..."></input>
    	<span style="margin-left:15px;"><i>Your game is not here? <a id="myGamesSubmitNewGame" href="core/ajax/games/submitgame.php">Submit it!</a></i></span>
    	<div id="myGamesListContainer">
    	</div>
    </div>
	<div id="myGamesGameView" class="myGamesGameView" style="display:none">
		<div class="myGamesGameViewBorderLeft">
        	&lt;
    	</div>
    	<div id ="myGamesGameViewInfoContainer">
    	</div>
	</div>
</div>
<div id="myGamesMyGames" class="myGamesTab" style="padding-top:0px;">
	<div id="myGamesListedGameAddNew" class="myGamesListedGame">
		<div style="margin-top:70px;">
    		<img src="/images/more_info_large.png" style="margin-bottom:10px;"/><br/>
    		Add new<br/>
    		game
    	</div>
	</div>
	<?php
	if ($games !== USER_HAS_NO_GAMES && $games !== false)
	{
    	foreach ($games as $i => $value)
    	{
	?>
	<div class="myGamesListedGame" data-id="<?php echo $games[$i]['id']; ?>">
		<div><img src="<?php echo $games[$i]['imagePath']; ?>" style="width:170px; height:250px; border-radius:0.5em;"/></div>
		<div class="myGamesGameName"><?php echo $games[$i]['title']; ?></div>
	</div>
	<?php 
	    }
	}
	?>
</div>
<div id="myGamesRecommendations" class="myGamesTab">
	Recommended games - Not yet implemented.
</div>