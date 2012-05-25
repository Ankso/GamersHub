/**
 * Private messages system
 */

function SendPrivateMessage(receiverId)
{
    if ($('#newMessageBody').val() === "")
    {
        $('#newMessage').append('<div class="errorSending">You must write at least one character!</div>');
        return;
    }
    $.post("core/friends/sendprivatemessage.php", { receiverId : receiverId, message : $('#newMessageBody').val() }, function(data) {
        if (data.length > 0)
        {
            $('div.errorSending').remove();
            if (data === "SUCCESS")
            {
                var date = new Date();
                var div = '        <div class="message">' + "\n";
                div = div + '            <div class="messageHeader">Sended by <b>You</b> (' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + ')</div>';
                div = div + '            <div class="messageBody">' + $('#newMessageBody').val() + '</div>';
                div = div + '        </div>';
                $('#emptyMessageHistory').remove();
                $('#conversationHistory').prepend(div);
            }
            else
                $('#newMessage').append('<div class="errorSending">An error occurred. Please try again in a few moments.</div>');
        }
        else
            $('#newMessage').append('<div class="errorSending">An error occurred. Please try again in a few moments.</div>');
    });
}