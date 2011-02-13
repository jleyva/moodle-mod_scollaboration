// Implementation for networking interface based on AJAX

Namespace('SC.components.chat.net');

SC.components.chat.net.sendChatMessage = function(msg){
        var callback = {
            success: SC.components.chat.messageDelivered,
            failure: SC.components.chat.messageDeliverFailed
        };
        var transaction = YAHOO.util.Connect.asyncRequest('GET', 'requests.php?id='+SCMoodle.SESSION_ID+'&sesskey='+SCMoodle.SESSKEY+'&component=chat&message='+msg, callback, '');
    };
