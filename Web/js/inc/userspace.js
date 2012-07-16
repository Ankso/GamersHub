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
// (CONST) Opcodes used by the client
var ClientOpcodes = {
    OPCODE_NULL               : 0, // Null opcode, used for testing/debug.
    OPCODE_LOGOFF            : 1, // Received when the client loggs off.
    OPCODE_PING               : 2, // Received each time that the client pings the server.
    OPCODE_ENABLE_AFK         : 3, // Received when AFK mode is enabled client-side.
    OPCODE_DISABLE_AFK        : 4, // Received when the client tries to disable AFK mode with his or her password.
    OPCODE_CHAT_INVITATION    : 5, // Received when a client invites other client to a chat conversation.
    OPCODE_CHAT_MESSAGE       : 6, // Received with each chat message between clients.
    TOTAL_CLIENT_OPCODES_COUNT: 7, // Total opcodes count (Not used by the way).
};
// (CONST) Opcodes used server-side
var ServerOpcodes = {
    // Not used by the way
};

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
    this.totalMessages = null;
    this.lastLoadedComment = null;
    this.idleTime = 0;
    this.userFriends = new Array();
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
    var bio = $("#bioInput").val();
    var birthday = $("#birthdayInput").val();
    var country = $("#countryInput").val();
    var city = $("#cityInput").val();
    $.post("core/ajax/editdetailedprofile.php", {bio : bio, birthday : birthday, country : country, city : city}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                this.previousBio = bio;
                this.previousBirthday = birthday;
                this.previousCountry = country;
                this.previousCity = city;
                this.CancelEditProfileDetails();
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
    $(panelName).text("Loading...");
    switch (panelName)
    {
        case "#myAccount":
            $(panelName).load("core/ajax/accountsettings.php");
            break;
        case "#mySocial":
            $(panelName).load("core/ajax/socialsettings.php");
            break;
        case "#myGames":
            $(panelName).load("core/ajax/gamessettings.php");
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
    
    $(self.openedControlPanel).slideUp(400, function() {
        // Here we must implement a cache system or something...
        $(self.openedControlPanel).html("");
    });
    $(self.openedControlPanel + "Button").css("background-color", "transparent");
    $(self.openedControlPanel + "Button").unbind("click");
    $(self.openedControlPanel + "Button").click(TriggerOpenControlPanel);
    self.openedControlPanel = "#none";
};

/**
 *  Increments the idle timer by the amout specified in IDLE_TIMER_STEP. If necessary, enables AFK mode.
 */
Space.prototype.IncrementIdleTimer = function() {
    if (user.isAfk)
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
    $("div.afkWindow").fadeIn(750);
    $("body").css("overflow-y", "hidden");
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
    self.socket = io.connect("http://127.0.0.1:5124");
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
            $("div#nodeServerStatus").text("Websocket connection status: CONNECTED.");
            self.status = STATUS.CONNECTED;
            self.pingTimeout = setTimeout(function() {
                socket.Ping();
            }, TIME_BETWEEN_PINGS);
        }
        else if (data.status == "INCORRECT")
        {
            $("div#nodeServerStatus").text("Websocket connection status: INCORRECT.");
            self.status = STATUS.DISCONNECTED;
        }
        else
        {
            $("div#nodeServerStatus").text("Websocket connection status: FAILED.");
            self.status = STATUS.DISCONNECTED;
        }
    });
    // Called when the user is logged off of the Real Time Server, due to inactivity, bad login credentials or other possible reasons.
    self.socket.on("disconnection", function(data) {
        $("a#topbarLogOffButton").trigger("click");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.DISCONNECTED;
    });
    // Called when the connection to the Real Time Server is lost. It has no handler server-side.
    self.socket.on("disconnect", function(data) {
        $("div#nodeServerStatus").text("Websocket connection status: CONNECTION LOST.");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.CONNECTION_LOST;
    });
    // Called when the socket client-side can't connect to the Real Time Server. It has no handler server-side.
    self.socket.on("error", function() {
        $("div#nodeServerStatus").text("Websocket connection status: SERVER OFFLINE.")
        clearTimeout(self.pingTimeout);
        self.status = STATUS.SERVER_OFFLINE;
    });
    // Called when a friend of this user logs in. Used to display the log in notification, etc.
    self.socket.on("friendLogin", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" '
                + 'style="width:35px; height:35px; border:2px #00FF00 solid; border-radius:0.3em; float:left; margin-left:10px;" /> '
                + '<span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged in.</span>');
        $("div#realTimeNotification").stop().fadeIn(1500);
        $("img#friendOnlineImg" + data.friendId).attr("src", "images/friend_online.png");
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 6500);
    });
    // Called when a friend of this user logs off. Used to display the log off notification, etc.
    self.socket.on("friendLogoff", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" style="width:35px; height:35px; border:2px #FF0000 solid; border-radius:0.3em; float:left; margin-left:10px;" /> <span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged off.');
        $("div#realTimeNotification").stop().fadeIn(1500);
        $("img#friendOnlineImg" + data.friendId).attr("src", "images/friend_offline.png");
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 6500);
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
            $("body").css("overflow-y", "auto");
            user.isAfk = false;
        }
        else
        {
            $("div.afkWindowContainer").append('<span id="span#errorAfkPassword" style="color:#FF0000;">Incorrect password</span>')
        }
    });
    self.socket.on("receivePrivateMessage", function(data) {
        if (data.senderId)
        {
            $("div#friendWrapper" + data.senderId)
        }
        else
        {
            
        }
    });
    self.socket.on("receiveNew", function(data) {
        // Not implemented
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
    if (this.focusConversation.name)
    {
        $("div#chatTab" + this.focusConversation.name).attr("style", "");
        $("div#chatBoxText" + this.focusConversation.name).hide();
    }
    $("div.chatTabsWrapper").prepend('<div class="chatTab" id="chatTab' + friendName + '" data-id="' + friendId + '" style="background-color:#222222; cursor:inherit;">' + friendName + '</div>')
    $("div#chatTab" + friendName).click(function(event) {
        chatManager.SwitchChatConversation(event);
    });
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div.chatBoxTextWrapper").prepend('<div class="chatBoxText" id="chatBoxText' + friendName + '" data-id="' + friendId + '" style="display:inherit"></div>');
    $("input.chatBoxInput").focus();
    this.focusConversation.id = friendId;
    this.focusConversation.name = friendName;
    this.activeChats.push(parseInt(friendId));
    if (!isInvitation)
        socket.Emit(ClientOpcodes.OPCODE_CHAT_INVITATION, { userId: user.id, friendId: friendId });
};

ChatManager.prototype.CloseChatConversation = function(friendId, friendName) {
    // Not yet implemented
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

    $("div#chatTab" + this.focusConversation.name).attr("style", "");
    $(event.target).css("background-color", "#222222");
    $(event.target).css("cursor", "inherit");
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div#chatBoxText" + this.focusConversation.name).hide();
    $("div#chatBoxText" + friendName).show();
    $("div#chatBoxText" + friendName).prop({ scrollTop: $("div#chatBoxText" + friendName).prop("scrollHeight") });
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
    
    $("div#chatBoxText" + this.focusConversation.name).append("<br /><b>You: </b>" + message)
    $("div#chatBoxText" + this.focusConversation.name).prop({ scrollTop: $("div#chatBoxText" + this.focusConversation.name).prop("scrollHeight") });
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
    $("div#chatBoxText" + friendName).append('<br /><b>' + friendName + ': </b>' + message);
    $("div#chatBoxText" + friendName).prop({ scrollTop: $("div#chatBoxText" + friendName).prop("scrollHeight") });
    if (friendName != this.focusConversation.name)
        $("div#chatTab" + friendName).css("background-color", "#CC6633");
};

/**
 * Friend object constructor. The Friend object is used to manage the friends panel on the left.
 * @returns {Friend}
 */
function Friend() {
    this.id = null;
    this.username = null;
    this.isOnline = null;
};

Friend.prototype.AddToList = function() {
    // Not yet implemented.
    return false;
};

Friend.prototype.RemoveFromList = function() {
    // Not yet implemented.
    return false;
};

Friend.prototype.SwitchOnline = function() {
    // Not yet implemented.
    return false;
};

/**
 * CommandsManager object constructor.
 * @returns {CommandsManager}
 */
function CommandsManager() {
};

/**
 * Welcome to GamersHub's Command Parser ™. This will be the main function to parse commands written by the users.
 * Commands must start with the character "/", the same character used in World of Warcraft chat system.
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
                    // Do nothing by the way, the friend request system must be structured properly.
                    return 0;
                case "remove":
                    // Remove a friend system must be structured also...
                    // Create temp link to spawn fancybox:
                    $("body").append('<a id="tempFancyboxLink" href="core/ajax/removefriendconfirmation.php?friendName=' + cmdParams[2] + '" style="display:none"></a>')
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