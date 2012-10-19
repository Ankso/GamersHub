/**
 * User's space javascript objects and methods.
 * TODO: A rewrite of a lot of functions is needed. Structure design.
 */

/**
 * (CONST) Socket connection statuses
 */
var STATUS = {
    DISCONNECTED    : 0,
    CONNECTED       : 1,
    SERVER_OFFLINE  : 2,
    CONNECTION_LOST : 3
};

// (CONST) Time between pings to the Real Time Server (in ms)
var TIME_BETWEEN_PINGS = 10000;
// (CONST) The maximum time that an user can be idle (in minutes)
var MAX_USER_IDLE_TIME = 20;
// (CONST) Time between idle timer increments (and step size) (in ms)
var IDLE_TIMER_STEP = 60000;
// (CONST) Time between checks of processes for the games system (in ms)
var TIME_BETWEEN_PROCESSES_CHECKS = 5000;
// (CONST) Opcodes used by the client
var ClientOpcodes = {
    OPCODE_NULL                : 0,  // Null opcode, used for testing/debug.
    OPCODE_LOGOFF              : 1,  // Sended when the client loggs off.
    OPCODE_PING                : 2,  // Sended each time that the client pings the server.
    OPCODE_ENABLE_AFK          : 3,  // Sended when AFK mode is enabled client-side.
    OPCODE_DISABLE_AFK         : 4,  // Sended when the client tries to disable AFK mode with his or her password.
    OPCODE_CHAT_INVITATION     : 5,  // Sended when a client invites other client to a chat conversation.
    OPCODE_CHAT_MESSAGE        : 6,  // Sended with each chat message between clients.
    OPCODE_ONLINE_FRIENDS_LIST : 7,  // Sended to request an online friends list for this user.
    OPCODE_START_PLAYING       : 8,  // Sended when the user starts a game that is on his games list.
    OPCODE_STOP_PLAYING        : 9,  // Sended when the user stops playing a game.
    OPCODE_REAL_TIME_NEW       : 10, // Sended with each real time new.
    TOTAL_CLIENT_OPCODES_COUNT : 11, // Total opcodes count (Not used by the way).
};
// (CONST) Opcodes used server-side
var ServerOpcodes = {
    // Not used by the way
};
// (CONST) Types of real time news that the client may receive from the RTS
var RealTimeNewTypes = {
    NEW_TYPE_NEW_BOARD_MESSAGE : 1,
    NEW_TYPE_NEW_FRIEND        : 2,
    NEW_TYPE_NEW_GAME          : 3,
}

/**
 * User object constructor
 * @returns {User}
 */
function User() {
    this.id = null;
    this.username = null;
    this.randomSessionId = null;
    this.avatarPath = null;
    this.isAfk = false;
    this.isPlaying = false;
};

/**
 * Space object constructor
 * @returns {Space}
 */
function Space() {
    this.previousBio = null;
    this.previousBirthday = null;
    this.previousCountry = null;
    this.previousCity = null;
    this.openedControlPanel = "#none";
    this.onControlPanelOpenAction = {
        action: null,
        data: null,
    };
    this.totalMessages = null;
    this.lastLoadedComment = null;
    this.idleTime = 0;
    this.userFriends = new Array();
    this.isMyAccountPanelLoaded = false;
    this.isSocialPanelLoaded = false;
    this.isMyGamesPanelLoaded = false;
    this.totalOnlineFriends = 0;
    this.plugin = null;
    this.checkProcessTimeout = null;
    this.updateTimestampsTimeout = null;
};

/**
 * Gets the equivalent width in pixels given a width in percentage for any DOM element using a jQuery selector.
 * If no element is specified, window is assumed.
 * @param percentageWidth integer The width in percentage (100% based...)
 * @param element string Optional. A string representing a jQuery selector. It will return the width relative to that DOM element.
 * @returns mixed An integer representing the pixels, or false if something fails.
 */
Space.prototype.PercentageWidthToPx = function(percentWidth, element) {
    if (!percentWidth)
        return false;
    
    if (!element)
        return ($(window).width() / 100) * percentWidth;
    
    return ($(element).width() / 100) * percentWidth;
};

/**
 * Activates "Edit mode" for the profile details of a space owner.
 */
Space.prototype.EditProfileDetails = function () {
    this.previousBio = $("#bioSpan").html();
    $("#bioDiv").html('Bio: <textarea id="bioInput" style="min-height:100px; width:95%;">' + this.previousBio + '</textarea>');
    this.previousBirthday = $("#birthdaySpan").text();
    $("#birthdayDiv").html('Birthday: <input type="text" id="birthdayInput"  value="' + this.previousBirthday + '" />');
    this.previousCountry = $("#countrySpan").text();
    $("#countryDiv").html('Country: <input type="text" id="countryInput"  value="' + this.previousCountry + '" />');
    this.previousCity = $("#citySpan").text();
    $("#cityDiv").html('City: <input type="text" id="cityInput"  value="' + this.previousCity + '" />');
    $("div.editProfileText").hide();
    $("#profileDetails").append('<div id="submitCancelEdit" style="height:20px; margin-top:7px;"><span style="float:left; color:#00FF00; cursor:pointer;" onclick="space.SubmitEditedProfileDetails();">Submit</span><span style="float:right; color:#FF0000; cursor:pointer;" onclick="space.CancelEditProfileDetails();">Cancel</span></div>');
};

/**
 * Shows/hides the profile details of the space owner.
 */
Space.prototype.SwitchProfileDetails = function () {
    if ($("#profileDetails").is(":hidden"))
    {
        $("#profileDetails").slideDown(400);
        $("div.editProfileButton").text("Hide");
    }
    else
    {
        $("#profileDetails").slideUp(400);
        $("div.editProfileButton").text("View profile");
    }
};

/**
 * Deactivates "Edition mode" for the space owner's profile details.
 */
Space.prototype.CancelEditProfileDetails = function () {
    $("#bioDiv").html('Bio: <span id="bioSpan">' + this.previousBio + '</span>');
    $("#birthdayDiv").html('Birthday: <span id="birthdaySpan">' + this.previousBirthday + '</span>');
    $("#countryDiv").html('Country: <span id="countrySpan">' + this.previousCountry + '</span>');
    $("#cityDiv").html('City: <span id="citySpan">' + this.previousCity + '</span>');
    $("#submitCancelEdit").remove();
    $("#submitProfileError").remove();
    $("div.editProfileText").show();
};

/**
 * Submits the changes done to the space owner profile details.
 */
Space.prototype.SubmitEditedProfileDetails = function () {
    var self = this;
    
    var bio = $("#bioInput").val();
    var birthday = $("#birthdayInput").val();
    var country = $("#countryInput").val();
    var city = $("#cityInput").val();
    $.post("core/ajax/editdetailedprofile.php", {bio : bio, birthday : birthday, country : country, city : city}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                self.previousBio = bio;
                self.previousBirthday = birthday;
                self.previousCountry = country;
                self.previousCity = city;
                self.CancelEditProfileDetails();
            }
            else
            {
                $("#submitProfileError").remove();
                $("#profileDetails").append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">An error has occurred. Please try again.</div>');
            }
        }
        else
        {
            $("#submitProfileError").remove();
            $("#profileDetails").append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">Unable to connect to the server. Please make sure that you are connected to the internet and try again.</div>');
        }
    });
};

/**
 * Shows the My Friends panel.
 */
Space.prototype.ShowMyFriendsPanel = function () {
    $("#myFriendsPanelFlapClosed").hide("slide", 150, function() {
        setTimeout(function() {
            $("#myFriendsPanelFlapOpened").show()
        }, 150);
        $("#myFriendsPanel").show("slide", 300);
    });
    $.cookie("FriendsPanel", "opened");
};

/**
 * Hides the My Friends panel.
 */
Space.prototype.CloseMyFriendsPanel = function () {
    $("#myFriendsPanel").hide("slide", 300);
    $("#myFriendsPanelFlapOpened").hide("slide", 300, function() {
        $("#myFriendsPanelFlapClosed").show("slide", 150);
    })
    $.cookie("FriendsPanel", "closed");
};

/**
 * Sends a new board message to the server.
 * @param message string The message to be sent.
 */
Space.prototype.SendBoardComment = function(message) {
    var self = this;
    
    if (message.length == 0 || message.length > 255)
    {
        // Just do nothing
        return;
    }
    
    if (message.charAt(0) == "/")
    {
        // Board comments starting with "/" are going to be parsed as commands
        commandsManager.ParseCommand(message);
        return;
    }

    $.post("core/ajax/boardmessages.php", { message: message }, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                if (space.totalMessages == 0)
                    $("#commentsHistory").text("");
                ++space.totalMessages;
                self.LoadBoardComments(1, 1, true)
                $(".commentInputTextBox").val("Something interesting to say?");
                // Send packet to RTS with the news
                socket.Emit(ClientOpcodes.OPCODE_REAL_TIME_NEW, {
                    userId: user.id,
                    newType: RealTimeNewTypes.NEW_TYPE_NEW_BOARD_MESSAGE,
                    extraInfo: {
                        friendName: user.username,
                        timestamp: Math.round((new Date().getTime() / 1000)),
                    },
                });
            }
            else
                $("#commentsHistory").prepend("An error occurred, please try again in a few moments.");
        }
        else
            $("#commentsHistory").prepend("An error occurred while connecting to the server, please try again in a few moments.");
    });
};

/**
 * Deletes a board message from both the space view and the server.
 * @param event object The event object generated by jQuery when the user clicks.
 */
Space.prototype.DeleteBoardComment = function(event) {
    var messageId = $(event.target.parentElement.parentElement).attr("data-id");
    if (!messageId)
        return;
    $.post("core/ajax/boardmessages.php", { messageId: messageId, spaceOwner: spaceOwner.id}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                $(event.target.parentElement.parentElement).fadeOut(500);
                --space.totalMessages;
            }
            else
                $("#commentsHistory").prepend("An error occurred, please try again in a few moments.");
        }
        else
            $("#commentsHistory").prepend("An error occurred while connecting to the server, please try again in a few moments.");
    });
};

/**
 * Loads a specified number of board comments in the user's space.
 * @param from integer From where we must begin to load comments.
 * @param to integer Where we must stop of loading comments.
 * @param prepend boolean Determines if we must add the comment at the top or at the bottom of the comment's section in the space. Only for visual purposes.
 */
Space.prototype.LoadBoardComments = function(from, to, prepend) {
    if (prepend === null)
        prepend = false;

    var self = this;
    
    self.lastLoadedComment = to;
    from = from - 1;
    to = to - 1;
    realFrom = self.totalMessages - to;
    realTo = self.totalMessages - from;
    $.post("core/ajax/boardmessages.php", { from: realFrom, to: realTo, spaceOwner: spaceOwner.id }, function(data) {
        if (data.length > 0)
        {
            if (data == "You haven't write anything yet. Start ASAP!" && !prepend)
                return;
            
            if (prepend)
                $("#commentsHistory").prepend(data);
            else
                $("#commentsHistory").append(data);
            
            $("div.boardComment").unbind("mouseenter");
            $("div.boardComment").mouseenter(function(event) {
                $(event.target).children("div.deleteBoardComment").stop().fadeIn(200);
            });
            $("div.boardComment").unbind("mouseleave");
            $("div.boardComment").mouseleave(function(event) {
                $(event.target).children("div.deleteBoardComment").stop().fadeOut(200);
            });
            $("div.deleteBoardComment").unbind("click");
            $("div.deleteBoardComment").click(function(event) {
                space.DeleteBoardComment(event);
            });
            $("div.displayCommentBoardReplies").unbind("click");
            $("div.displayCommentBoardReplies").click(function(event) {
                if ($(event.target).prev().is(":hidden"))
                {
                    $(event.target).prev().slideDown(200);
                    $(event.target).text("Hide Replies");
                }
                else
                {
                    $(event.target).prev().slideUp(200);
                    $(event.target).text("Show Replies");
                }
            });
            $("div.newReplyCommentBoardInputSend").unbind("click");
            $("div.newReplyCommentBoardInputSend").click(function(event) {
                SendMessageBoardReply(event);
            });
            $("input.newReplyCommentBoardInputTextbox").unbind("click");
            $("input.newReplyCommentBoardInputTextbox").keydown(function(event) {
                if (event.keyCode == 13)
                    $(event.target.nextElementSibling).trigger("click");
            });
            $("img.deleteBoardReply").unbind("click");
            $("img.deleteBoardReply").click(function(event) {
                space.DeleteBoardCommentReply(event);
            });
        }
        else
            $("#commentsHistory").text("An error occurred while connecting to the server. Please try again in a few moments.");
        space.UpdateTimestamps();
    });
};

/**
 * Sends a reply to a message board.
 * @param event object The event object generated by jQuery when the reply is sended.
 */
Space.prototype.SendMessageBoardReply = function(event) {
    var element = event.target;
    var reply = $(element).prev().val();
    var messageId = $(element).attr("data-id");
   
    $.post("core/ajax/boardreplies.php", {reply: reply, messageId: messageId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                // Add the new reply to the list
                var date = new Date();
                var newReply = '<div class="boardCommentReply" data-id="' + messageId + '">' + "\n"
                + '<img src="images/delete_16.png" class="deleteBoardReply" />' + "\n"
                + '<div class="boardCommentReplyBody">' + "\n"
                + '<div class="boardCommentReplyAvatar"><img src="' + user.avatarPath + '" style="width:40px; height:40px; border-radius:0.3em;" alt="avatar" /></div>' + "\n"
                + '<div class="boardCommentReplyContent">' + reply + '</div>' + "\n"
                + '</div>' + "\n"
                + '<div class="boardCommentReplyBottom">By You ' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '</div>' + "\n"
                + '</div>' + "\n";
                $(element.parentElement.parentElement).prev().prepend(newReply);
                $("img.deleteBoardReply").click(function(e) {
                    space.DeleteBoardCommentReply(e);
                });
                $(element).prev().val("");
            }
            else
                $(element).prev().val("An error occurred while sending your reply. Please try again.");
        }
        else
            $(element).prev().val("Connection to the server lost. Please try again in a few moments.");
    });
};

/**
 * Deletes a reply from the user's board. The reply can only be deleted by the space owner or by the writer (this check is server-side)
 * @param event object The event object generated by jQuery when the user clicks the red cross.
 */
Space.prototype.DeleteBoardCommentReply = function(event) {
    var replyId = $(event.target.parentElement).attr("data-id");
    if (!replyId)
        return;
        
    $.post("core/ajax/boardreplies.php", {replyId: replyId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
                $(event.target.parentElement).fadeOut(500);
        }
    });
};

/**
 * Opens (or moves to) another control panel.
 */
Space.prototype.OpenControlPanel = function(panelName) {
    var self = this;
    var direction = "right";

    if (self.openedControlPanel != "#none")
    {
        if (panelName == "#myAccount")
            direction = "left";
        else if (panelName == "#mySocial" && self.openedControlPanel == "#myGames")
            direction = "left";
        $(self.openedControlPanel).hide("slide", { direction: (direction == "right" ? "left" : "right") }, 500);
        if (self.openedControlPanel == panelName)
            $(panelName).slideUp(500);
        else
            $(panelName).show("slide", { direction: direction }, 500);
        $(self.openedControlPanel + "Button").css("background-color", "transparent");
        // Note this unbind(). It's very important unbind the previous attached event handler.
        $(self.openedControlPanel + "Button").unbind("click");
        $(self.openedControlPanel + "Button").click(TriggerOpenControlPanel);
    }
    else
        $(panelName).slideDown(500);
    $(panelName + "Button").css("background-color", "#333333");
    $(panelName + "Button").unbind("click");
    $(panelName + "Button").click(function(/*event*/) {
        space.CloseControlPanel();
    });
    switch (panelName)
    {
        case "#myAccount":
            if (!self.isMyAccountPanelLoaded)
            {
                $(panelName).text("Loading...");
                $(panelName).load("core/ajax/accountsettings.php");
                self.isMyAccountPanelLoaded = true;
            }
            break;
        case "#mySocial":
            if (!self.isSocialPanelLoaded)
            {
                $(panelName).text("Loading...");
                $(panelName).load("core/ajax/socialsettings.php");
                self.isSocialPanelLoaded = true;
            }
            break;
        case "#myGames":
            if (!self.isMyGamesPanelLoaded)
            {
                $(panelName).text("Loading...");
                $(panelName).load("core/ajax/gamessettings.php");
                self.isMyGamesPanelLoaded = true;
            }
            break;
        default:
            break;
    }
    self.openedControlPanel = panelName;
};

/**
 * Closes the opened control panel.
 */
Space.prototype.CloseControlPanel = function() {
    var self = this;
    
    $(self.openedControlPanel).slideUp(400);
    $(self.openedControlPanel + "Button").css("background-color", "transparent");
    $(self.openedControlPanel + "Button").unbind("click");
    $(self.openedControlPanel + "Button").click(TriggerOpenControlPanel);
    self.openedControlPanel = "#none";
};

/**
 *  Increments the idle timer by the amout specified in IDLE_TIMER_STEP. If necessary, enables AFK mode.
 */
Space.prototype.IncrementIdleTimer = function() {
    if (user.isAfk || user.isPlaying)
        return;
    
    this.idleTime = this.idleTime + (IDLE_TIMER_STEP / 60000);
    if (this.idleTime >= MAX_USER_IDLE_TIME)
    {
        this.EnableAfkMode();
        this.idleTime = 0;
    }
};

/**
 * Enables AFK mode for this user.
 */
Space.prototype.EnableAfkMode = function() {
    if (user.isPlaying)
        return;
    
    $("div.afkWindow").fadeIn(750);
    socket.Emit(ClientOpcodes.OPCODE_ENABLE_AFK, { userId : user.id });
    user.isAfk = true;
};

/**
 * Sends a solicitation to the Real Time Server to disable AFK mode.
 */
Space.prototype.DisableAfkMode = function() {
    var password = $("input#afkPassword").val();
    
    if (password)
        socket.Emit(ClientOpcodes.OPCODE_DISABLE_AFK, { userId : user.id, password : password });
    $("input#afkPassword").val("");
};

/**
 * Loads the GamershubTools plugin.
 */
Space.prototype.LoadPlugin = function() {
    this.plugin = document.getElementById("gamershubPlugin");
    if (this.plugin.valid)
    {
        // Nothing else to do here
        $("div#pluginStatus").text("Plugin status: Loaded");
    }
    else
    {
        // We are here probably because the user hasn't installed the plugin.
        $("div#pluginStatus").text("Plugin status: Error. Plugin not loaded.");
        $("div#advertMessagePopUp").html('<span>You need the GamersHub tools plugin to access all web features. <a id="pluginDownloadLink" href="plugin/GamersHubtools.msi">Download it now!</a></span>');
        // This timeouts are only for visual purposes.
        setTimeout(function() {
            $("div#advertMessagePopUp").slideDown(500);
        }, 3000);
        $("a#pluginDownloadLink").click(function() {
            // Create temp link to spawn fancybox:
            $("body").append('<a id="tempFancyboxLink" href="plugin/plugin.html" style="display:none"></a>');
            $("a#tempFancyboxLink").fancybox();
            setTimeout(function() {
                $("a#tempFancyboxLink").trigger("click");
                $("a#tempFancyBoxLink").remove();
            }, 100);
        });
    }
}

/**
 * Updates all page timestamps, like the ones in the latest news section.
 */
Space.prototype.UpdateTimestamps = function() {
    $(".timestamp").each(function() {
        var timestamp = $(this).attr("data-timestamp");
        
        if (!timestamp)
            return;
        
        var timePassed = Math.round(new Date().getTime() / 1000) - timestamp;
        // just now or XX minutes ago
        if (timePassed < 3600)
        {
            timePassed = Math.round(timePassed / 60);
            if (timePassed < 1)
                $(this).text("just now");
            else if (timePassed == 1)
                $(this).text("1 minute ago");
            else
                $(this).text(timePassed + " minutes ago");
        }
        // More than XX hours ago
        else if (timePassed < 86400)
        {
            timePassed = Math.round(timePassed / 3600);
            if (timePassed == 1)
                $(this).text("more than 1 hour ago");
            else
                $(this).text("more than " + timePassed + " hours ago");
        }
        // XX days ago
        else if (timePassed < 2592000)
        {
            timePassed = Math.round(timePassed / 86400);
            if (timePassed == 1)
                $(this).text("1 day ago");
            else
                $(this).text(timePassed + " days ago");
        }
        // XX months ago
        else
        {
            timePassed = Math.round(timePassed / 2592000);
            if (timePassed == 1)
                $(this).text("1 month ago");
            else
                $(this).text("more than " + timePassed + " months ago");
        }
    });
    // This is because the function can be called in any moment by various scripts, so we can clear the old timeout.
    clearTimeout(this.updateTimestampsTimeout);
    this.updateTimestampsTimeout = setTimeout(function() {
        space.UpdateTimestamps();
    }, 30000);
}

/**
 * Socket object constructor
 * @returns {Socket}
 */
function Socket() {
    this.socket = null;
    this.pingTimeout = null;
    this.status = STATUS.DISCONNECTED;
};

/**
 * Establishes the connection to the Real Time Server and initilizes the socket object.
 */
Socket.prototype.ConnectToRealTimeServer = function() {
    var self = this;
    
    if (self.status === STATUS.CONNECTED)
        return;
    
    // Open new socket with the real time events server
    self.socket = io.connect("http://gamershub.no-ip.org:5124");
    // Called when the user tries to log in the Real Time Server and wants to open a socket.
    self.socket.on("requestCredentials", function(data) {
        // This is a special exception, the is no opcode here because this is part of the client-server handshaking process.
        self.socket.emit("sendCredentials", {
            userId:    user.id,
            sessionId: user.randomSessionId,
        });
    });
    // Called after trying to log in by the user. The data is the connection status.
    self.socket.on("logged", function(data) {
        if (data.status == "SUCCESS")
        {
            $("div#nodeServerStatus").text("RTS connection status: CONNECTED.");
            setTimeout(function() {
                $("div#nodeServerStatus").fadeOut(1000);
            }, 9700);
            friendsManager.AskForList();
            self.status = STATUS.CONNECTED;
            self.pingTimeout = setTimeout(function() {
                socket.Ping();
            }, TIME_BETWEEN_PINGS);
        }
        else if (data.status == "INCORRECT")
        {
            $("div#nodeServerStatus").text("RTS connection status: INCORRECT.");
            self.status = STATUS.DISCONNECTED;
        }
        else
        {
            $("div#nodeServerStatus").text("RTS connection status: FAILED.");
            self.status = STATUS.DISCONNECTED;
        }
    });
    // Called when the user is logged off of the Real Time Server, due to inactivity, bad login credentials or other possible reasons.
    self.socket.on("disconnection", function(data) {
        $("a#topbarLogOffButton").trigger("click");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.DISCONNECTED;
        $("div#nodeServerStatus").show();
    });
    // Called when the connection to the Real Time Server is lost. It has no handler server-side.
    self.socket.on("disconnect", function(data) {
        $("div#nodeServerStatus").text("RTS connection status: CONNECTION LOST.");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.CONNECTION_LOST;
        $("div#nodeServerStatus").show();
    });
    // Called when the socket client-side can't connect to the Real Time Server. It has no handler server-side.
    self.socket.on("error", function() {
        $("div#nodeServerStatus").text("RTS connection status: SERVER OFFLINE.")
        clearTimeout(self.pingTimeout);
        self.status = STATUS.SERVER_OFFLINE;
        $("div#nodeServerStatus").show();
    });
    // Called when a friend of this user logs in. Used to display the log in notification, etc.
    self.socket.on("friendLogin", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" '
                + 'style="width:35px; height:35px; border:2px #00FF00 solid; border-radius:0.3em; float:left; margin-left:10px;" /> '
                + '<span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged in.</span>');
        $("div#realTimeNotification").stop().fadeIn(1500);
        friendsManager.AddToList(data.friendId, data.friendName, data.friendAvatarPath, false, null, false);
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 5000);
        $("div#socialFriend" + data.friendId).css("border-color", "#00FF00");
        $("div#chatBoxText" + data.friendId).append('<br />-- <b>' + data.friendName + '</b> is online --');
        $("div#chatBoxText" + data.friendId).prop({ scrollTop: $("div#chatBoxText" + data.friendId).prop("scrollHeight") });
    });
    // Called when a friend of this user logs off. Used to display the log off notification, etc.
    self.socket.on("friendLogoff", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" '
                + 'style="width:35px; height:35px; border:2px #FF0000 solid; border-radius:0.3em; float:left; margin-left:10px;" /> '
                + '<span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged off.');
        $("div#realTimeNotification").stop().fadeIn(1500);
        friendsManager.RemoveFromList(data.friendId);
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 5000);
        $("div#socialFriend" + data.friendId).css("border-color", "#FF0000");
        $("div#chatBoxText" + data.friendId).append('<br />-- <b>' + data.friendName + '</b> is offline --');
        $("div#chatBoxText" + data.friendId).prop({ scrollTop: $("div#chatBoxText" + data.friendId).prop("scrollHeight") });
    });
    // Called when a friend opens a chat window with this user.
    self.socket.on("enterChat", function(data) {
        chatManager.CreateChatConversation(data.friendId, data.friendName, true);
    });
    // Called when the user receives a chat message from another user.
    self.socket.on("parseChatMessage", function(data) {
        chatManager.ReceiveChatMessage(data.friendId, data.friendName, data.message);
    });
    // Called in response to a "disable AFK mode" request. Disables AFK mode if the sended password to unlock the session was correct.
    self.socket.on("afkModeDisabled", function(data) {
        $("span#errorAfkPassword").remove();
        if (data.success)
        {
            $("div.afkWindow").fadeOut(750);
            user.isAfk = false;
        }
        else
            $("div.afkWindowContainer").append('<span id="span#errorAfkPassword" style="color:#FF0000;">Incorrect password</span>')
    });
    self.socket.on("receivePrivateMessage", function(data) {
        // Not implemented
    });
    // Called each time a new real time new is received.
    self.socket.on("realTimeNew", function(data) {
        if (!data.extraInfo.friendName || !data.newType)
            return;
        
        $("div#noLatestNews").remove();
        switch (data.newType)
        {
            case RealTimeNewTypes.NEW_TYPE_NEW_BOARD_MESSAGE:
                $("div#latestNews").prepend('<div class="latestNew" style="display:none">\n'
                        + '<div><a href="/' + data.extraInfo.friendName + '" style="text-decoration:none; color:#FFFFFF;"><b>' + data.extraInfo.friendName + '</b></a> has posted a new message!</div>\n'
                        + '<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="' + data.extraInfo.timestamp + '"><i>Just now</i></span></div>\n'
                        + '</div>');
                break;
            case RealTimeNewTypes.NEW_TYPE_NEW_FRIEND:
                if (!data.extraInfo)
                    return;
                
                $("div#latestNews").prepend('<div class="latestNew" style="display:none">\n'
                        + '<a href="/' + data.extraInfo.friendName + '" style="text-decoration:none; color:#FFFFFF;"><b>' + data.extraInfo.friendName + '</b></a> is now friend of '
                        + '<a href="/' + data.extraInfo.newFriendName + '" style="text-decoration:none; color:#FFFFFF;"><b>' + data.extraInfo.newFriendName + '</b></a>\n'
                        + '<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="' + data.extraInfo.timestamp + '"><i>Just now</i></span></div>\n'
                        + '</div>');
                break;
            case RealTimeNewTypes.NEW_TYPE_NEW_GAME:
                if (!data.extraInfo)
                    return;
                
                $("div#latestNews").prepend('<div class="latestNew" style="display:none">\n'
                        + '<a href="/' + data.extraInfo.friendName + '" style="text-decoration:none; color:#FFFFFF;"><b>' + data.extraInfo.friendName + '</b></a> has a new game: <b>' + data.extraInfo.newGameTitle + '</b>\n'
                        + '<div style="text-align:right; font:14px Calibri; color:#AAAAAA; text-style:italic; margin-top:3px;"><span class="timestamp" data-timestamp="' + data.extraInfo.timestamp + '"><i>Just now</i></span></div>\n'
                        + '</div>');
                break;
            default:
                break;
        }
        // This will affect only the new, hidden new.
        $("div.latestNew").fadeIn(500);
        space.UpdateTimestamps();
    });
    // Called when the online friends list is received from the RTS.
    self.socket.on("onlineFriendsList", function(data) {
        friendsManager.CreateList(data.friendsList);
    });
    // Called when a friend starts playing a game.
    self.socket.on("friendStartsPlaying", function(data) {
        var friend = friendsManager.GetFriend(data.friendId);
        
        // If the user is already marked as "playing", then this is received because he has reloaded the page, so ignore the message.
        if (friend && !friend.isPlaying)
        {
            $("div#realTimeNotification").html('<img src="' + friend.avatarPath + '" alt="Avatar" '
                    + 'style="width:35px; height:35px; border:2px #222222 solid; border-radius:0.3em; float:left; margin-left:10px;" /> '
                    + '<span style="float:left; margin-left:7px;"><b>' + friend.userName + '</b> is now playing:<br/><b>' + data.gameTitle + '</b></span>');
            $("div#realTimeNotification").stop().fadeIn(1500);
            setTimeout(function() {
                $("div#realTimeNotification").stop().fadeOut(1500);
            }, 6500);
            //$("div#socialFriend" + friend.id).append('<span id="socialIsPlaying' + friend.id + '"> - <i>Playing <b>" + data.gameTitle + "</b></i></span>');
            friendsManager.SetPlaying(friend.id, true, data.gameId, data.gameTitle, data.gameImagePath);
        }
    });
    // Called when a friend stops playing a game.
    self.socket.on("friendStopsPlaying", function(data) {
        $("div#socialIsPlaying" + data.friendId).remove();
        friendsManager.SetPlaying(data.friendId, false, null, null, null);
    });
};

/**
 * Pings the Real Time Server to refresh inactivity time.
 */
Socket.prototype.Ping = function() {
    this.Emit(ClientOpcodes.OPCODE_PING, {
        userId : user.id,
    });
    this.pingTimeout = setTimeout(function() {
        socket.Ping();
    }, TIME_BETWEEN_PINGS);
};

/**
 * Sends a message to the Real Time Server. It adds the random session ID to all packets automatically.
 * @param opcode string The string opcode.
 * @param packet object The object with the data that must be sent.
 */
Socket.prototype.Emit = function(opcode, packet) {
    packet.opcode = opcode;
    packet.sessionId = user.randomSessionId;
    this.socket.emit("packet", packet);
};

/**
 * ChatManager object constructor.
 * TODO: A rewrite of the chat system is needed.
 * @returns {ChatManager}
 */
function ChatManager() {
    this.focusConversation = {
            id  : null,
            name: null
    };
    this.activeChats = new Array();
};

/**
 * Creates a new chat conversation between the user and a friend or vice-versa
 * @param friendId integer The ID of the friend to start chat with.
 * @param friendName string The friend's username.
 * @param isInvitation boolean True if the user has been invited, else false.
 */
ChatManager.prototype.CreateChatConversation = function(friendId, friendName, isInvitation) {
    // Cancel the operation if the window is already created.
    if ($.inArray(parseInt(friendId), this.activeChats) != -1)
        return;
    
    /*if (this.focusConversation.name)
    {
        $("div#chatTab" + this.focusConversation.id).attr("style", "");
        $("div#chatBoxText" + this.focusConversation.id).hide();
    }
    */
    $("div.chatTabsWrapper").prepend('<div class="chatTab" id="chatTab' + friendId + '" data-id="' + friendId + '" style="background-color:#' + ((this.GetTotalChatsCount() == 0) ? "222222" : "CC6633") + ';">' + friendName + '</div>')
    $("div#chatTab" + friendId).unbind("click");
    $("div#chatTab" + friendId).click(function(event) {
        chatManager.SwitchChatConversation(event);
    });
    $("div.chatBox").show();
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div.chatBoxTextWrapper").prepend('<div class="chatBoxText" id="chatBoxText' + friendId + '" data-id="' + friendId + '" style="display:' + ((this.GetTotalChatsCount() == 0) ? "inherit" : "none") + '"></div>');
    $("input.chatBoxInput").focus();
    if (this.GetTotalChatsCount() == 0)
    {
        this.focusConversation.id = friendId;
        this.focusConversation.name = friendName;
    }
    this.activeChats.push(parseInt(friendId));
    /*
    if (!isInvitation)
        socket.Emit(ClientOpcodes.OPCODE_CHAT_INVITATION, { userId: user.id, friendId: friendId });
    */
};

ChatManager.prototype.CloseChatConversation = function() {
    for (var i in this.activeChats)
        if (this.activeChats[i] == this.focusConversation.id)
            delete this.activeChats[i];
    
    $("div#chatTab" + this.focusConversation.id).remove();
    $("div#chatBoxText" + this.focusConversation.id).remove();
    $("div.chatBox").hide();
    if (this.GetTotalChatsCount() == 0)
        $("div.chatBoxWrapper").hide();
};

/**
 * Switchs between chat windows.
 * @param event object The event object created by jQuery when the action is triggered.
 */
ChatManager.prototype.SwitchChatConversation = function(event) {
    if ($(event.target).text() == this.focusConversation.name)
        return;
    
    var friendName = $(event.target).text();
    var friendId = $(event.target).attr("data-id");

    $("div#chatTab" + this.focusConversation.id).attr("style", "");
    $(event.target).css("background-color", "#222222");
    $("div.chatBox").show();
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div#chatBoxText" + this.focusConversation.id).hide();
    $("div#chatBoxText" + friendId).show();
    $("div#chatBoxText" + friendId).prop({ scrollTop: $("div#chatBoxText" + friendId).prop("scrollHeight") });
    this.focusConversation.id = friendId;
    this.focusConversation.name = friendName;
};

/**
 * Sends a chat message.
 * @param event object The jQuery event object created when the message is sent.
 */
ChatManager.prototype.SendChatMessage = function(event) {
    var message = $(event.target).val();
    if (message == "")
        return;
    
    $("div#chatBoxText" + this.focusConversation.id).append("<br /><b>You: </b>" + message)
    $("div#chatBoxText" + this.focusConversation.id).prop({ scrollTop: $("div#chatBoxText" + this.focusConversation.Id).prop("scrollHeight") });
    $(event.target).val("");
    socket.Emit(ClientOpcodes.OPCODE_CHAT_MESSAGE, { userId: user.id, friendId: this.focusConversation.id , message: message });
};

/**
 * Processes a received message from the Real Time Server.
 * @param friendName string The name of the friend that sends the message.
 * @param message strign The message itself.
 */
ChatManager.prototype.ReceiveChatMessage = function(friendId, friendName, message) {
    // Create new chat window if none exists
    if ($.inArray(parseInt(friendId), this.activeChats) == -1)
        this.CreateChatConversation(friendId, friendName, true);
    $("div#chatBoxText" + friendId).append('<br /><b>' + friendName + ': </b>' + message);
    $("div#chatBoxText" + friendId).prop({ scrollTop: $("div#chatBoxText" + friendId).prop("scrollHeight") });
    if (friendName != this.focusConversation.name)
        $("div#chatTab" + friendId).css("background-color", "#CC6633");
};

ChatManager.prototype.GetTotalChatsCount = function() {
    var count = 0;
    for (var i in this.activeChats)
        if (this.activeChats[i])
            ++count;
    return count;
};
/**
 * Friend object constructor.
 * @returns {Friend}
 */
function Friend() {
    this.id = null;
    this.username = null;
    this.isOnline = null;
};

/**
 * Friends Manager object constructor. The friends manager is used to administrate the friends panel on the left.ç
 * Basically, it's a technical and visual simple list implementation.
 * TODO: Use the Friend object to store the data in the list.
 * @returns {FriendsManager}
 */
function FriendsManager() {
    this.totalFriends = 0;          // Total user friends (not used by the moment)
    this.totalOnlineFriends = 0;    // Total online user friends.
    this.isListInitialized = false; // Control variable, this will ensure that the plain list is asked to the RTS only once.
    this.list = new Array();        // The array that stores all online friends.
};

/**
 * Asks the RTS for a plain list already ordered with all the user online friends.
 * @returns boolean True on success, else false.
 */
FriendsManager.prototype.AskForList = function() {
    if (this.isListInitialized)
        return false;
    
    $("div#closeMyFriendsPanel").before(
            '<div id="friendWrapperLoading" class="friendWrapper">' +
            '    <div id="friendHeader" class="friendHeader" style="border-bottom-left-radius:0.5em;">' +
            '        <div class="friendName"><a class="friendSpaceLink">Loading...</a></div>' +
            '     </div>' +
            '</div>'
    );
    socket.Emit(ClientOpcodes.OPCODE_ONLINE_FRIENDS_LIST, { userId: user.id });
    this.isListInitialized = true;
    return true;
};

/**
 * Initializes the list with the data gathered from the RTS. This will happen only once.
 * @param friendsList array An array with the data returned by the RTS
 * @returns boolean True on succes, else false.
 */
FriendsManager.prototype.CreateList = function(friendsList) {
    var self = this;
    
    if (!friendsList)
        return false;
    
    $("div#friendWrapperLoading").remove();
    
    if (friendsList[0] === "NO_ONLINE_FRIENDS")
    {
        $("div#closeMyFriendsPanel").before(
                '<div id="friendWrapperNoOnlineFriends" class="friendWrapper">' +
                '    <div id="friendHeader" class="friendHeader" style="border-bottom-left-radius:0.5em;">' +
                '        <div class="friendName"><a class="friendSpaceLink">You have no online friends.</a></div>' +
                '     </div>' +
                '</div>'
        );
        return true;
    }
    
    for (var i in friendsList)
        self.AddToList(friendsList[i].id, friendsList[i].userName, friendsList[i].avatarPath, friendsList[i].isPlaying, friendsList[i].gameInfo, true);
    return true;
};

/**
 * Adds a friend to the list, internally and visually, and sorts if necessary.
 * @param friendId integer The friend's unique ID.
 * @param friendName string The friend's username.
 * @param isPlaying boolean True if the friend is playing any game, else false.
 * @param gameInfo object Basic information about the game the friend is playing. NOTE: only passed if the above param is true.
 * @param skipChecks boolean Optional. If sets to true, the friend will be added to the list at the bottom, without sorting.
 * @returns boolean True on success, else false.
 */
FriendsManager.prototype.AddToList = function(friendId, friendName, friendAvatarPath, isPlaying, gameInfo, skipChecks) {
    if (!gameInfo)
        gameInfo = null;
    
    var self = this;
    var htmlCode = '<div id="friendWrapper' + friendId + '" class="friendWrapper" style="display:none">' +
            '    <div id="friendHeader" class="friendHeader"' + ''/*' style="border-bottom-left-radius:0.5em;"'*/ + '>' +
            '        <div class="friendName"><img src="images/friend_online.png" style="width:22px; height:22px; margin-bottom:-4px;" /><a class="friendSpaceLink" href="/' + friendName + '">' + friendName + '</a>';
    if (isPlaying)
        htmlCode = htmlCode + '<span style="font:12px Calibri;"> - Playing <i>' + gameInfo.title + '</i></span></div>';
    else
        htmlCode = htmlCode + '</div>';
    htmlCode = htmlCode + '        <div class="plusImg"><img id="moreOptionsImg" src="images/more_info_large.png" style="height:25px; width:25px;" /></div>' +
            '    </div>' +
            '    <div class="friendPanelOptions">' +
            '        <div id="friendOptionChat' + friendId + '" class="friendOption">Invite to chat</div>' +
            '        <div id="friendOptionLiveStream' + friendId + '" class="friendOption">Invite to LiveStream</div>' +
            '        <div id="friendOptionPrivateMessage' + friendId + '" class="friendOption" style="border-bottom:2px #434343 solid;"><a id="sendPrivateMessage" href="core/ajax/privatemessage.php?friendName=' + friendName + '" style="text-decoration:none; color:#FFFFFF;">Send private message</a></div>' +
            '    </div>' +
            '</div>';
    
    if (skipChecks === undefined)
        skipChecks = false;
    
    $("div#friendWrapperNoOnlineFriends").remove();
    
    // The skipChecks feature is only used when the list is being initialized, because the
    // usernames are already ordered, or when the list is empty (you can't sort only one name)
    if (skipChecks || self.totalOnlineFriends == 0)
    {
        $("div#closeMyFriendsPanel").before(htmlCode);
        $("div#friendWrapper" + friendId).show();
        self.list[parseInt(self.totalOnlineFriends)] = {
            id: friendId,
            userName: friendName,
            avatarPath: friendAvatarPath,
            isPlaying: isPlaying,
            gameInfo: gameInfo,
        };
    }
    else
    {
        // This is the normal case. We must find the correct position for the new friend in the
        // list, in alphabetic order.
        for (var i = 0; i < self.totalOnlineFriends; ++i)
        {
            if (self.list[i].userName.toLowerCase() > friendName.toLowerCase())
            {
                // Once we find the position, we must move all the following elements forward.
                for (var j = self.totalOnlineFriends - 1; j >= i; --j)
                    self.list[j + 1] = {
                        id: self.list[j].id,
                        userName: self.list[j].userName,
                        avatarPath: friendAvatarPath,
                        isPlaying: isPlaying,
                        gameInfo: gameInfo,
                    };
                $("div#friendWrapper" + (self.list[i + 1].id)).before(htmlCode);
                break;
            }
        }
        // This will happen when all the elements have been checked
        // we should insert the new friend at the bottom of the list.
        if (i >= self.totalOnlineFriends)
            $("div#closeMyFriendsPanel").before(htmlCode);
        self.list[i] = {
            id: friendId,
            userName: friendName,
            avatarPath: friendAvatarPath,
            isPlaying: isPlaying,
            gameInfo: gameInfo,
        };
        $("div#friendWrapper" + friendId).fadeIn(500);
    }
    $(".friendPanelOptions").hide();
    $("img#moreOptionsImg").unbind("click");
    $("img#moreOptionsImg").click(function(event) {
        SwitchFriendOptionsMenu(event);
    });
    $("img#moreOptionsImg").hide();
    $(".friendHeader").unbind("mouseenter");
    $(".friendHeader").mouseenter(function(event) {
        try {
            $(event.target.children[1].children[0]).stop().fadeIn(100);
        }
        catch(e) {
            $('img#moreOptionsImg').hide();
        }
    });
    $(".friendHeader").unbind("mouseleave");
    $(".friendHeader").mouseleave(function(event) {
        try {
            $(event.target.children[1].children[0]).stop.fadeOut(100);
        }
        catch(e) {
            $("img#moreOptionsImg").hide();
        }
    });
    $("div#friendOptionChat" + friendId).click(function() {
        chatManager.CreateChatConversation(friendId, friendName, false);
    });
    $("div#friendOptionLiveStream" + friendId).click(function() {
        // Not implemented/used
    });
    $("a#sendPrivateMessage").fancybox();
    ++self.totalOnlineFriends;
    return true;
};

/**
 * Removes a friend from the list.
 * @param friendId integer The friend's unique ID.
 * @returns boolean True.
 */
FriendsManager.prototype.RemoveFromList = function(friendId) {
    var self = this;
    
    // If there's more than one friend in the list, we must reorder it.
    if (self.totalOnlineFriends > 1)
    {
        for (var i = 0; i < self.totalOnlineFriends; ++i)
        {
            if (self.list[i].id == friendId)
            {
                for (var j = i; j <= self.totalOnlineFriends - 2; ++j)
                {
                    self.list[j] = {
                        id: self.list[j + 1].id,
                        userName: self.list[j + 1].userName,
                        avatarPath: self.list[j + 1].avatarPath,
                        isPlaying: self.list[j + 1].isPlaying,
                        gameInfo: self.list[j + 1].gameInfo,
                    };
                }
                break;
            }
        }
    }
    delete self.list[self.totalOnlineFriends - 1];
    --self.totalOnlineFriends;
    $("div#friendWrapper" + friendId).fadeOut(500, function() {
        $("div#friendWrapper" + friendId).remove();
        // If there aren't any more online friends.
        if (self.totalOnlineFriends == 0)
        {
            $("div#closeMyFriendsPanel").before(
                    '<div id="friendWrapperNoOnlineFriends" class="friendWrapper">' +
                    '    <div id="friendHeader" class="friendHeader" style="border-bottom-left-radius:0.5em;">' +
                    '        <div class="friendName"><a class="friendSpaceLink">You have no online friends.</a></div>' +
                    '     </div>' +
                    '</div>'
            );
        }
    });
    return true;
};

/**
 * Obtains the friend data stored in the list based on the given friend ID.
 * @param friendId long The ID of the friend we are looking for.
 * @return object Returns an object with the friend's data, or false if something fails.
 */
FriendsManager.prototype.GetFriend = function(friendId) {
    if (this.totalOnlineFriends == 0)
        return false;
    
    for (var i in this.list)
    {
        if (this.list[i].id == friendId)
            return this.list[i];
    }
    return false;
}

/**
 * Sets a friend as he is playing, stores the game data and prints the info in the screen.
 * @param friendId long The ID of the friend that's going to be modified.
 * @param isPlaying boolean True if the friend starts playing, false if he stops doing so.
 * @param gameId long The game's unique ID.
 * @param gameTitle string The game's title.
 * @param gameImagePath string The path to where the image of the cover of the game is located.
 * @return boolean True on success, else false.
 */
FriendsManager.prototype.SetPlaying = function(friendId, isPlaying, gameId, gameTitle, gameImagePath) {
    for (var i in this.list)
    {
        if (this.list[i].id == friendId)
        {
            this.list[i].isPlaying = isPlaying;
            if (isPlaying)
            {
                this.list[i].gameInfo = {
                    id: gameId,
                    title: gameTitle,
                    imagePath: gameImagePath,
                };
                $("div#friendWrapper" + friendId).children().children("div.friendName").append('<span style="font:12px Calibri;"> - Playing <i>' + gameTitle + '</i></span></div>')
            }
            else
            {
                this.list[i].gameInfo = null;
                $("div#friendWrapper" + friendId).children().children().children("span").remove();
            }
            return true;
        }
    }
    return false;
}

/**
 * GamesManager object constructor. Basically developed to monitor if
 * the user is playing something, and if he is, what's the game.
 * @returns {GamesManager}
 */
function GamesManager() {
    this.gamesList = null;
    this.checkProcessTimeout = null;
    this.game = {
        id: null,
        title: null,
        imagePath: null,
        exeName: null,
    }
};

/**
 * Gets the games list from the DB using an ajax request.
 */
GamesManager.prototype.GetGamesList = function() {
    var self = this;
    
    $.post("core/ajax/games/gameslist.php", { userId: user.id }, function(data){
        if (data.status == "SUCCESS")
        {
            self.gamesList = data.list;
            // We can now start the check in process
            self.CheckClientProcessList();
        }
    }, "json");
}

/**
 * Checks the runnging processes using the GamersHub Tools Plugin, and searchs for a game in the list.
 */
GamesManager.prototype.CheckClientProcessList = function() {
    // This is the first thing, we should program the next call whatever happens.
    self.checkProcessTimeout = setTimeout(function() {
        gamesManager.CheckClientProcessList();
    }, TIME_BETWEEN_PROCESSES_CHECKS);
    
    if (!space.plugin.valid)
    {
        // If the plugin is not loaded, we can cancel the next function call, because, at least,
        // the page should be reloaded after the plugin installation.
        clearTimeout(this.checkProcessTimeout);
        return;
    }
    
    if (!this.gamesList)
    {
        // If the games list is not initialized for whatever the reason is, we can try to get the list again.
        this.GetGamesList();
        return;
    }

    var processesList = space.plugin.GetProcessList();
    var processes = processesList.split(";");
    processes.pop();

    // If the user is not playing any game, we should check if that has changed
    if (!user.isPlaying)
    {
        for (var i in processes)
        {
            for (var j in this.gamesList)
            {
                // Look up for a known process name.
                // If we find one, we should:
                if (this.gamesList[j].exeName.indexOf(processes[i]) != -1)
                {
                    // Set locally that the user is playing.
                    this.game.id = this.gamesList[j].id;
                    this.game.title = this.gamesList[j].title;
                    this.game.imagePath = this.gamesList[j].imagePath;
                    this.game.exeName = this.gamesList[j].exeName;
                    // Send a packet to the RTS telling him that we are playing, and send him the game id and the game name.
                    socket.Emit(ClientOpcodes.OPCODE_START_PLAYING, {
                        userId: user.id,
                        gameId: this.game.id,
                        gameTitle: this.game.title,
                        gameImagePath: this.game.imagePath
                    });
                    // This will block AFK mode
                    user.isPlaying = true;
                    // And finally show a message
                    $("div#gameNotification").html('<img src="' + this.game.imagePath + '" alt="gameCover" '
                            + 'style="width:40px; height:60px; border:2px #222222 solid; border-radius:0.3em; float:left; margin-left:15px;" /> '
                            + '<span style="float:left; margin-left:30px; font:20px Calibri;">Now playing:<br/><b>' + this.game.title + '</b></span>');
                    $("div#gameNotification").show("slide", { direction: "left" }, 750);
                    // We found a match, so we can stop searching
                    break;
                }
            }
        }
    }
    else
    {
        // Else, we should check if the user stills playing that game.
        var found = false;
        for (var i in processes)
        {
            if (this.game.exeName.indexOf(processes[i]) != -1)
            {
                found = true;
                break;
            }
        }
        if (!found)
        {
            // The user is not playing anymore
            // So do the same as above, but the other way around
            this.game.id = null;
            this.game.title = null;
            this.game.imagePath = null;
            this.game.exeName = null;
            // Send a packet to the RTS telling him that we STOP playing.
            socket.Emit(ClientOpcodes.OPCODE_STOP_PLAYING, {
                userId: user.id,
            });
            // This will unlock AFK mode
            user.isPlaying = false;
            // And finally hide the message
            $("div#gameNotification").fadeOut(1500, function() {
                $("div#gameNotification").html("");
            });
        }
            
    }
}

/**
 * CommandsManager object constructor.
 * @returns {CommandsManager}
 */
function CommandsManager() {
};

/**
 * Welcome to GamersHub's Command Parser (TM). This will be the main function to parse commands written by the users.
 * Commands must start with the character "/".
 * Almost all things in the web are going to be able to be done with commands. From start a window chat to delete a message board or remove a friend from the friends list.
 * @param string command A text string representing the command that must be executed.
 * @returns integer By the way, returns 0 if the command is executed successfully, or -1 if something fails. Later, we must add codes for each specific error (like syntax error, invalid params, etc...)
 */
CommandsManager.prototype.ParseCommand = function(command) {
    if (command.length < 2)
        return -1;
    
    var cmdParams = new Array();
    cmdParams = command.substring(1).split(" ");
    switch (cmdParams[0])
    {
        case "message":
        case "msg":
        case "m":
            // Send private message to target user
            // Check if the user has set the needed params
            if (!cmdParams[1])
                return -1;

            // Create temp link to spawn fancybox:
            $("body").append('<a id="tempFancyboxLink" href="core/ajax/privatemessage.php?friendName=' + cmdParams[1] + '" style="display:none"></a>');
            $("a#tempFancyboxLink").fancybox();
            $("a#tempFancyboxLink").trigger("click");
            $(".commentInputTextBox").val("Something interesting to say?");
            $("a#tempFancyBoxLink").remove();
            return 0;
        case "friend":
            if (!cmdParams[1] || !cmdParams[2])
                return -1;
            
            switch(cmdParams[1])
            {
                case "add":
                    // Do nothing by the way, the friend requests system must be structured properly.
                    return 0;
                case "remove":
                    // Remove a friend system must be restructured also...
                    // Create temp link to spawn fancybox:
                    $("body").append('<a id="tempFancyboxLink" href="core/ajax/removefriendconfirmation.php?friendName=' + cmdParams[2] + '" style="display:none"></a>');
                    $("a#tempFancyboxLink").fancybox();
                    $("a#tempFancyboxLink").trigger("click");
                    $(".commentInputTextBox").val("Something interesting to say?");
                    $("a#tempFancyBoxLink").remove();
                    return 0;
                default:
                    return -1;
            }
        default:
            return -1;
    }
}

var user = new User();
var spaceOwner = new User();
var space = new Space();
var socket = new Socket();
var friendsManager = new FriendsManager();
var gamesManager = new GamesManager();
var chatManager = new ChatManager();
var commandsManager = new CommandsManager();

function FadeOut(event, redirectUrl)
{
    event.preventDefault();
    if (!event.isTrigger)
        socket.Emit(ClientOpcodes.OPCODE_LOGOFF, {
            userId: user.id,
        });
    $("body").fadeOut(1000, function() { window.location = redirectUrl; });
}

function FadeIn()
{
    $("body").fadeIn(2000);
}

function TriggerOpenControlPanel(event)
{
    var targetPanel = "";
    switch ($(event.target).attr("id"))
    {
        case "myAccountButton":
            targetPanel = "#myAccount";
            break;
        case "mySocialButton":
            $("span#socialNewsAdvert").remove();
            targetPanel = "#mySocial";
            break;
        case "myGamesButton":
            targetPanel = "#myGames";
            break;
    }
    space.OpenControlPanel(targetPanel);
}

function SwitchFriendOptionsMenu(event)
{
    var node = event.target.parentElement.parentElement.parentElement.children[1];
    if ($(node).is(":hidden"))
    {
        $(".friendPanelOptions").slideUp();
        $(".friendHeader").mouseleave(function(event) {
            $(event.target.children[1].children[0]).hide();
        });
        $("img#moreOptionsImg").hide();
        $(event.target).show();
        $(node).slideDown();
        $(event.target.parentElement.parentElement).off("mouseleave");
    }
    else
    {
        $(".friendHeader").mouseleave(function(event) {
            try {
                $(event.target.children[1].children[0]).hide();
            }
            catch(e) {
                $("img#moreOptionsImg").hide();
            }
        });
        $(node).slideUp();
    }
}

function SetMainWrapperHeight()
{
    $("div.mainWrapper").height($(window).height() - 51);
    setTimeout(function() {
        SetMainWrapperHeight();
    }, 100);
}