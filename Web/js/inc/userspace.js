/**
 * User's space javascript functions and procedures.
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

/**
 * User object constructor
 * @returns {User}
 */
function User() {
    this.id = null;
    this.username = null;
    this.randomSessionId = null;
    this.avatarPath = null;
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
    this.previousBio = $('#bioSpan').html();
    $('#bioDiv').html('Bio: <textarea id="bioInput" style="min-height:100px; width:95%;">' + this.previousBio + '</textarea>');
    this.previousBirthday = $('#birthdaySpan').text();
    $('#birthdayDiv').html('Birthday: <input type="text" id="birthdayInput"  value="' + this.previousBirthday + '" />');
    this.previousCountry = $('#countrySpan').text();
    $('#countryDiv').html('Country: <input type="text" id="countryInput"  value="' + this.previousCountry + '" />');
    this.previousCity = $('#citySpan').text();
    $('#cityDiv').html('City: <input type="text" id="cityInput"  value="' + this.previousCity + '" />');
    $('div.editProfileText').hide();
    $('#profileDetails').append('<div id="submitCancelEdit" style="height:20px; margin-top:7px;"><span style="float:left; color:#00FF00; cursor:pointer;" onclick="space.SubmitEditedProfileDetails();">Submit</span><span style="float:right; color:#FF0000; cursor:pointer;" onclick="space.CancelEditProfileDetails();">Cancel</span></div>');
}

/**
 * Shows/hides the profile details of the space owner.
 */
Space.prototype.SwitchProfileDetails = function () {
    if ($('#profileDetails').is(":hidden"))
    {
        $('#profileDetails').slideDown(400);
        $('div.editProfileButton').text("Hide");
    }
    else
    {
        $('#profileDetails').slideUp(400);
        $('div.editProfileButton').text("View profile");
    }
}

/**
 * Deactivates "Edition mode" for the space owner's profile details.
 */
Space.prototype.CancelEditProfileDetails = function () {
    $('#bioDiv').html('Bio: <span id="bioSpan">' + this.previousBio + '</span>');
    $('#birthdayDiv').html('Birthday: <span id="birthdaySpan">' + this.previousBirthday + '</span>');
    $('#countryDiv').html('Country: <span id="countrySpan">' + this.previousCountry + '</span>');
    $('#cityDiv').html('City: <span id="citySpan">' + this.previousCity + '</span>');
    $('#submitCancelEdit').remove();
    $('#submitProfileError').remove();
    $('div.editProfileText').show();
}

/**
 * Submits the changes done to the space owner profile details.
 */
Space.prototype.SubmitEditedProfileDetails = function () {
    var bio = $('#bioInput').val();
    var birthday = $('#birthdayInput').val();
    var country = $('#countryInput').val();
    var city = $('#cityInput').val();
    $.post("ajax/editdetailedprofile.php", {bio : bio, birthday : birthday, country : country, city : city}, function(data) {
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
                $('#submitProfileError').remove();
                $('#profileDetails').append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">An error has occurred. Please try again.</div>');
            }
        }
        else
        {
            $('#submitProfileError').remove();
            $('#profileDetails').append('<div id="submitProfileError" style="text-align:center; color:#FF0000;">Unable to connect to the server. Please make sure that you are connected to the internet and try again.</div>');
        }
    });
}

/**
 * Shows the My Friends panel.
 */
Space.prototype.ShowMyFriendsPanel = function () {
    $('#myFriendsPanelFlapClosed').hide("slide", 150, function() {
        setTimeout("$('#myFriendsPanelFlapOpened').show();", 150);
        $('#myFriendsPanel').show("slide", 300);
    });
    $.cookie("FriendsPanel", "opened");
}

/**
 * Hides the My Friends panel.
 */
Space.prototype.CloseMyFriendsPanel = function () {
    $('#myFriendsPanel').hide("slide", 300);
    $('#myFriendsPanelFlapOpened').hide("slide", 300, function() {
        $('#myFriendsPanelFlapClosed').show("slide", 150);
    })
    $.cookie("FriendsPanel", "closed");
}

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

    $.post("ajax/boardmessages.php", { message: message }, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                if (space.totalMessages == 0)
                    $('#commentsHistory').text("");
                ++space.totalMessages;
                self.LoadBoardComments(1, 1, true)
                $('.commentInputTextBox').val("Something interesting to say?");
            }
            else
                $('#commentsHistory').prepend("An error occurred, please try again in a few moments.");
        }
        else
            $('#commentsHistory').prepend("An error occurred while connecting to the server, please try again in a few moments.");
    });
}

/**
 * Deletes a board message from both the space view and the server.
 * @param event object The event object generated by jQuery when the user clicks.
 */
Space.prototype.DeleteBoardComment = function(event) {
    var messageId = $(event.srcElement.parentElement.parentElement).attr("data-id");
    if (!messageId)
        return;
    $.post("ajax/boardmessages.php", { messageId: messageId, spaceOwner: spaceOwner.id}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                --space.totalMessages;
                $(event.srcElement.parentElement.parentElement).fadeOut(500);
            }
            else
                $('#commentsHistory').prepend("An error occurred, please try again in a few moments.");
        }
        else
            $('#commentsHistory').prepend("An error occurred while connecting to the server, please try again in a few moments.");
    });
}

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
    $.post("ajax/boardmessages.php", { from: realFrom, to: realTo, spaceOwner: spaceOwner.id }, function(data) {
        if (data.length > 0)
        {
            if (data == "You haven't write anything yet. Start ASAP!" && !prepend)
                return;
            
            if (prepend)
                $("#commentsHistory").prepend(data);
            else
                $("#commentsHistory").append(data);
            $('div.deleteBoardComment').click(function(event) {
                space.DeleteBoardComment(event);
            });
            $('div.displayCommentBoardReplies').click(function(event) {
                if ($(event.srcElement).prev().is(":hidden"))
                {
                    $(event.srcElement).prev().slideDown(200);
                    $(event.srcElement).text("Hide Replies");
                }
                else
                {
                    $(event.srcElement).prev().slideUp(200);
                    $(event.srcElement).text("Show Replies");
                }
            });
            //$('div.newReplyCommentBoardInputSend').click(function(event) {
            //    SendMessageBoardReply(event);
            //});
            $('input.newReplyCommentBoardInputTextbox').keydown(function(event) {
                if (event.keyCode == 13)
                    $(event.srcElement.nextElementSibling).trigger("click");
            });
            $('img.deleteBoardReply').click(function(event) {
                space.DeleteBoardCommentReply(event);
            });
        }
        else
            $("#commentsHistory").text("An error occurred while connecting to the server. Please try again in a few moments.");
    });
}

/**
 * Sends a reply to a message board.
 * @param event object The event object generated by jQuery when the reply is sended.
 */
Space.prototype.SendMessageBoardReply = function(event) {
    var element = null;
    if (event.isTrigger)
        element = event.target;
    else
        element = event.srcElement;
    var reply = $(element).prev().val();
    var messageId = $(element).attr("data-id");
   
    $.post("ajax/boardreplies.php", {reply: reply, messageId: messageId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
            {
                // Add the new reply to the list
                var date = new Date();
                var newReply = '<div class="boardCommentReply" data-id="' + messageId + '">' + "\n"
                + '<div class="deleteBoardComment" style="margin-top:4px;"><img src="images/delete_16.png" style="width:10px; height:10px;" /></div>' + "\n"
                + '<div class="boardCommentReplyBody">' + "\n"
                + '<div class="boardCommentReplyAvatar"><img src="' + user.avatarPath + '" style="width:40px; height:40px; border-radius:0.3em;" alt="avatar" /></div>' + "\n"
                + '<div class="boardCommentReplyContent">' + reply + '</div>' + "\n"
                + '</div>' + "\n"
                + '<div class="boardCommentReplyBottom">By You ' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + '</div>' + "\n"
                + '</div>' + "\n";
                $(element.parentElement.parentElement).prev().prepend(newReply);
                // TODO: This isn't working as expected
                $('img.deleteBoardReply').click(function(event) {
                    space.DeleteBoardCommentReply(event);
                });
            }
            else
                $(element).prev().val("An error occurred while sending your reply. Please try again.");
        }
        else
            $(element).prev().val("Connection to the server lost. Please try again in a few moments.");
    });
}

/**
 * Deletes a reply from the user's board. The reply can only be deleted by the space owner or by the writer (this check is server-side)
 * @param event object The event object generated by jQuery when the user clicks the red cross.
 */
Space.prototype.DeleteBoardCommentReply = function(event) {
    var replyId = $(event.srcElement.parentElement).attr("data-id");
    if (!replyId)
        return;
        
    $.post("ajax/boardreplies.php", {replyId: replyId}, function(data) {
        if (data.length > 0)
        {
            if (data == "SUCCESS")
                $(event.srcElement.parentElement).fadeOut(500);
        }
    });
}

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
        $(self.openedControlPanel + 'Button').css("background-color", "transparent");
        $(self.openedControlPanel + "Button").unbind("click");
        $(self.openedControlPanel + "Button").click(TriggerOpenControlPanel);
    }
    else
        $(panelName).slideDown(500);
    $(panelName + 'Button').css("background-color", "#333333");
    $(panelName + "Button").unbind("click");
    $(panelName + 'Button').click(function(/*event*/) {
        space.CloseControlPanel();
    });
    $(panelName).text("Loading...");
    switch (panelName)
    {
        case "#myAccount":
            $(panelName).load("ajax/accountsettings.php");
            break;
        case "#mySocial":
            $(panelName).load("ajax/socialsettings.php");
            break;
        case "#myGames":
            $(panelName).load("ajax/gamessettings.php");
            break;
        default:
            break;
    }
    self.openedControlPanel = panelName;
}

/**
 * Closes the opened control panel.
 */
Space.prototype.CloseControlPanel = function() {
    var self = this;
    
    $(self.openedControlPanel).slideUp(400, function() {
        // Here we must implement a cache system or something...
        $(self.openedControlPanel).html("");
    });
    $(self.openedControlPanel + 'Button').css("background-color", "transparent");
    $(self.openedControlPanel + "Button").unbind("click");
    $(self.openedControlPanel + 'Button').click(TriggerOpenControlPanel);
    self.openedControlPanel = "#none";
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
    self.socket = io.connect("http://127.0.0.1:5124");
    self.socket.on("requestCredentials", function(data) {
        self.socket.emit("sendCredentials", {
            userId: user.id,
            sessionId: user.randomSessionId,
        });
    });
    self.socket.on("logged", function(data) {
        if (data.status == "SUCCESS")
        {
            $("div#nodeServerStatus").text("Websocket connection status: CONNECTED.");
            self.status = STATUS.CONNECTED;
            self.pingTimeout = setTimeout(function() {
                socket.Ping();
            }, 60000);
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
    self.socket.on("disconnection", function(data) {
        $("a#topbarLogOffButton").trigger("click");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.DISCONNECTED;
    });
    self.socket.on("disconnect", function(data) {
        $("div#nodeServerStatus").text("Websocket connection status: CONNECTION LOST.");
        clearTimeout(self.pingTimeout);
        self.status = STATUS.CONNECTION_LOST;
    });
    self.socket.on("error", function() {
        $("div#nodeServerStatus").text("Websocket connection status: SERVER OFFLINE.")
        clearTimeout(self.pingTimeout);
        self.status = STATUS.SERVER_OFFLINE;
    });
    self.socket.on("friendLogin", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" style="width:35px; height:35px; border:2px #00FF00 solid; border-radius:0.3em; float:left; margin-left:10px;" /> <span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged in.</span>');
        $("div#realTimeNotification").stop().fadeIn(1500);
        $("img#friendOnlineImg" + data.friendId).attr("src", "images/friend_online.png");
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 6500);
    });
    self.socket.on("friendLogoff", function(data) {
        $("div#realTimeNotification").html('<img src="' + data.friendAvatarPath + '" alt="Avatar" style="width:35px; height:35px; border:2px #FF0000 solid; border-radius:0.3em; float:left; margin-left:10px;" /> <span style="float:left; margin-top:10px; margin-left:7px;"><b>' + data.friendName + '</b> has logged off.');
        $("div#realTimeNotification").stop().fadeIn(1500);
        $("img#friendOnlineImg" + data.friendId).attr("src", "images/friend_offline.png");
        setTimeout(function() {
            $("div#realTimeNotification").stop().fadeOut(1500);
        }, 6500);
    });
    self.socket.on("enterChat", function(data) {
        chatManager.CreateChatConversation(data.friendId, data.friendName, true);
    });
    self.socket.on("parseChatMessage", function(data){
        chatManager.ReceiveChatMessage(data.friendName, data.message);
    });
}

/**
 * Pings the Real Time Server to refresh inactivity time.
 */
Socket.prototype.Ping = function() {
    this.socket.emit("ping", { userId: user.id });
    this.pingTimeout = setTimeout(function() {
        socket.Ping();
    }, 60000);
}

/**
 * Sends a message to the Real Time Server.
 * @param opcode string The string opcode.
 * @param packet object The object with the data that must be sent.
 */
Socket.prototype.Emit = function(opcode, packet) {
    this.socket.emit(opcode, packet);
}

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
        SwitchChatConversation(event);
    });
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div.chatBoxTextWrapper").prepend('<div class="chatBoxText" id="chatBoxText' + friendName + '" data-id="' + friendId + '" style="display:inherit">-----' + friendName + '-----</div>');
    $("input.chatBoxInput").focus();
    this.focusConversation.id = friendId;
    this.focusConversation.name = friendName;
    if (!isInvitation)
        socket.Emit("chatInvitation", { userId: user.id, friendId: friendId });
}

/**
 * Switchs between chat windows.
 * @param event object The event object created by jQuery when the action is triggered.
 */
ChatManager.prototype.SwitchChatConversation = function(event) {
    if ($(event.srcElement).text() == this.focusConversation.name)
        return;
    
    var friendName = $(event.srcElement).text();
    var friendId = $(event.srcElement).attr("data-id");

    $("div#chatTab" + this.focusConversation.name).attr("style", "");
    $(event.srcElement).css("background-color", "#222222");
    $(event.srcElement).css("cursor", "inherit");
    if ($("div.chatBoxWrapper").is(":hidden"))
        $("div.chatBoxWrapper").show();
    $("div#chatBoxText" + this.focusConversation.name).hide();
    $("div#chatBoxText" + friendName).show();
    $("div#chatBoxText" + friendName).prop({ scrollTop: $("div#chatBoxText" + friendName).prop("scrollHeight") });
    this.focusConversation.id = friendId;
    this.focusConversation.name = friendName;
}

/**
 * Sends a chat message.
 * @param event object The jQuery event object created when the message is sent.
 */
ChatManager.prototype.SendChatMessage = function(event) {
    var message = $(event.srcElement).val();
    if (message == "")
        return;
    
    $("div#chatBoxText" + this.focusConversation.name).append("<br /><b>You: </b>" + message)
    $("div#chatBoxText" + this.focusConversation.name).prop({ scrollTop: $("div#chatBoxText" + this.focusConversation.name).prop("scrollHeight") });
    $(event.srcElement).val("");
    socket.Emit("chatMessage", { userId: user.id, friendId: this.focusConversation.id , message: message });
}

/**
 * Processes a received message from the Real Time Server.
 * @param friendName string The name of the friend that sends the message.
 * @param message strign The message itself.
 */
ChatManager.prototype.ReceiveChatMessage = function(friendName, message) {
    $("div#chatBoxText" + friendName).append('<br /><b>' + friendName + ': </b>' + message);
    $("div#chatBoxText" + friendName).prop({ scrollTop: $("div#chatBoxText" + friendName).prop("scrollHeight") });
    if (friendName != this.focusConversation.name)
        $("div#chatTab" + friendName).css("background-color", "#CC6633");
}

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
            $('body').append('<a id="tempFancyboxLink" href="ajax/privatemessage.php?friendName=' + cmdParams[1] + '" style="display:none"></a>');
            $('a#tempFancyboxLink').fancybox();
            $('a#tempFancyboxLink').trigger("click");
            $('.commentInputTextBox').val("Something interesting to say?");
            $('a#tempFancyBoxLink').remove();
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
                    $('body').append('<a id="tempFancyboxLink" href="ajax/removefriendconfirmation.php?friendName=' + cmdParams[2] + '" style="display:none"></a>')
                    $('a#tempFancyboxLink').fancybox();
                    $('a#tempFancyboxLink').trigger("click");
                    $('.commentInputTextBox').val("Something interesting to say?");
                    $('a#tempFancyBoxLink').remove();
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
    socket.Emit("logoff");
    $('body').fadeOut(1000, function() { window.location = redirectUrl; });
}

function FadeIn()
{
    $("body").fadeIn(2000);
}

function TriggerOpenControlPanel(event)
{
    var targetPanel = "";
    switch ($(event.srcElement).attr("id"))
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
    var node = event.srcElement.parentElement.parentElement.parentElement.children[1];
    if ($(node).is(':hidden'))
    {
        $('.friendPanelOptions').slideUp();
        $('.friendHeader').mouseleave(function(event) {
            $(event.srcElement.children[1].children[0]).hide();
        });
        $('img#moreOptionsImg').hide();
        $(event.srcElement).show();
        $(node).slideDown();
        $(event.srcElement.parentElement.parentElement).off('mouseleave');
    }
    else
    {
        $('.friendHeader').mouseleave(function(event) {
            try {
                $(event.srcElement.children[1].children[0]).hide();
            }
            catch(e) {
                $('img#moreOptionsImg').hide();
            }
        });
        $(node).slideUp();
    }
}