/**
 * Basic functions for the My Games control panel.
 */

function MyGames()
{
    this.backToGamesList = false;
    this.totalAddedGames = 0;
    this.addedGames = new Array();
    this.isGameLoading = false;
}

MyGames.prototype.MenuOptionClick = function (event)
{
    $("div.myGamesTab").hide();
    $("div.myGamesMenuOption").attr("style", "");
    $(event.target).css("background-color", "rgba(255, 51, 30, 1)");
    $(event.target).css("padding-top", "6px");
    $(event.target).css("padding-bottom", "6px");
    $(event.target).css("border-top", "1px rgb(255, 51, 30) solid");
    $(event.target).css("border-bottom", "1px rgb(255, 51, 30) solid");
    switch ($(event.target).attr("id"))
    {
        case "myGamesOptionDatabase":
            $("#myGamesDatabase").show();
            $("div#myGamesDatabase").jScrollPane({
                showArrows: true,
            });
            break;
        case "myGamesOptionMyGames":
            $("#myGamesMyGames").show();
            this.backToGamesList = false;
            $("div#myGamesMyGames").jScrollPane({
                showArrows: true,
            });
            break;
        case "myGamesOptionRecommendations":
            $("#myGamesRecommendations").show();
            $("div#myGamesRecommendations").jScrollPane({
                showArrows: true,
            });
            break;
        default:
            break;
    }
}

MyGames.prototype.LookUpForGame = function (pattern)
{
    var self = this
    
    $.post("core/ajax/games/gamesfinder.php", { game: pattern }, function(data) {
        if (data)
        {
            if (data.status == "NO_GAMES_FOUND")
                $("#myGamesListContainer").html('<div id="myGamesListItem" class="myGamesListItem">No games found.</div>');
            else
            {
                var htmlCode = "";
                for (var i in data.list)
                    htmlCode = htmlCode + '<div class="myGamesListItem" data-id="' + data.list[i].id + '">' + data.list[i].title + '</div>\n';
                $("#myGamesListContainer").html(htmlCode);
                $(".myGamesListItem").unbind("click");
                $(".myGamesListItem").click(function (event) {
                    $("input#myGamesSearchInput").val($(event.target).text());
                    self.LoadGame($(event.target).attr("data-id"));
                });
            }
        }
    }, "json");
}

MyGames.prototype.LoadGame = function (gameId)
{
    var self = this;
    
    if (!gameId)
        return false;
    
    // Don't start loading a new game until the last petition ends.
    if (self.isGameLoading)
        return;
    
    self.isGameLoading = true;
    
    var htmlCode;
    $.post("core/ajax/games/gameinfo.php", { id: gameId }, function(data) {
        if (data)
        {
            if (data.status != "FAILED")
            {
                var isAdded = false;
                for (var i in self.addedGames)
                {
                    if (self.addedGames[i] == gameId)
                    {
                        isAdded = true;
                        break;
                    }
                }
                htmlCode = '<img src="' + data.imagePath + '" class="myGamesGameViewCoverImg"/>\n'
                    + '<div class="myGamesGameViewInfo">\n'
                    + '    <div>\n'
                    + '        <ul>\n'
                    + '            <li>Title: <label id="title">' + data.title + '</label>\n'
                    + '            <li>Genres: ';
                for (var i in data.genres)
                {
                    if (i > 0)
                        htmlCode = htmlCode + ", ";
                    htmlCode = htmlCode + data.genres[i];
                }
                htmlCode = htmlCode + '\n            <li>Developer: ' + data.developer.name + '\n'
                    + '            <li>Publisher: ' + data.publisher.name + '\n'
                    + '            <li>Description: ' + data.description + '\n'
                    + '        </ul>\n'
                    + '     </div>\n'
                    + '    <div>\n'
                    + '        ' + data.totalPlayers + ' users have this game and 0 recommend it.</br>\n'
                    + '    </div>\n'
                    + '    <div style="margin-top:15px;">\n';
                if (isAdded)
                    htmlCode += '        <label class="myGamesRemoveGameButton">Remove game</label>\n';
                else
                    htmlCode += '        <label class="myGamesAddGameButton">Add game</label>\n';
                htmlCode = htmlCode + '    </div>\n'
                    + '</div>\n';
                $("#myGamesGameViewInfoContainer").html(htmlCode);
                $("label.myGamesRemoveGameButton").click(function(event) {
                    myGames.RemoveGame(gameId);
                    $(event.target).unbind("click");
                });
                $("label.myGamesAddGameButton").click(function(event) {
                    myGames.AddGame(gameId, data.title);
                    $(event.target).unbind("click");
                });
                if (!self.backToGamesList)
                {
                    $("#myGamesSearchInputContainer").hide("slide", { direction: "left" }, 500, function () {
                        $("#myGamesGameView").fadeIn(400);
                    });
                }
                else
                    $("#myGamesGameView").fadeIn(400);
                $("#myGamesListContainer").html("");
            }
        }
        self.isGameLoading = false;
    }, "json");
    return true;
}

MyGames.prototype.AddGame = function(gameId, gameTitle)
{
    var self = this;
    
    $.post("core/ajax/games/addgame.php", { gameId: gameId }, function(data) {
        if (data && data == "SUCCESS")
        {
            $("label.myGamesAddGameButton").parent().html('<label class="myGamesRemoveGameButton">Remove game</label>\n');
            $("label.myGamesRemoveGameButton").click(function(event) {
                myGames.RemoveGame(gameId);
                $(event.target).unbind("click");
            });
            $("div#myGamesMyGames").append('<div class="myGamesListedGame" data-id="' + gameId + '">\n'
                    + '    <div><img src="' + $("img.myGamesGameViewCoverImg").attr("src") + '" style="width:170px; height:250px; border-radius:0.5em;"/></div>\n'
                    + '    <div class="myGamesGameName">' + $("label#title").text() + '</div>\n'
                    + '</div>\n');
            // Broadcast the new addition to the RTS, if we are in an user's space
            if (socket && user)
            {
                socket.Emit(ClientOpcodes.OPCODE_REAL_TIME_NEW, {
                    userId: user.id,
                    newType: RealTimeNewTypes.NEW_TYPE_NEW_GAME,
                    extraInfo: {
                        newGameTitle: gameTitle,
                        friendName: user.username,
                        timestamp: Math.round((new Date().getTime() / 1000)),
                    },
                });
            }
            // Add game to the added games list
            self.addedGames[self.addedGames.length + 1] = gameId;
            // And add the click event to the image in the games list
            $("div.myGamesListedGame").unbind("click");
            $("div.myGamesListedGame").click(function (event) {
                myGames.backToGamesList = true;
                $("#myGamesSearchInputContainer").hide();
                myGames.LoadGame($(event.target.parentElement.parentElement).attr("data-id"));
                // This timeout is only for visual purposes, it doesn't affect functionality.
                setTimeout(function () {
                    $("div#myGamesOptionDatabase").trigger("click");
                }, 50);
            });
        }
    });
}

MyGames.prototype.RemoveGame = function(gameId)
{
    var self = this;
    
    $.post("core/ajax/games/removegame.php", { gameId: gameId }, function(data) {
        if (data && data == "SUCCESS")
        {
            $("label.myGamesRemoveGameButton").parent().html('<label class="myGamesAddGameButton">Add game</label>\n');
            $("label.myGamesAddGameButton").click(function(event) {
                myGames.AddGame(gameId, data.title);
                $(event.target).unbind("click");
            });
            $("div.myGamesListedGame").each(function(index) {
                if ($(this).attr("data-id") == gameId)
                    $(this).remove();
            });
            // Remove the game from the list of added games.
            for (var i in self.addedGames)
            {
                if (self.addedGames[i] == gameId)
                {
                    delete self.addedGames[i];
                    break;
                }
            }
        }
    });
}

var myGames = new MyGames();