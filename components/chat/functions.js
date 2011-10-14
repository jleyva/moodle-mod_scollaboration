Namespace('SC.components.chat');

SC.components.chat.lastMessageId = 0;

// Actions is a JSON decoded object
SC.components.chat.processActions = function(actions){
    // JSON 
    // { messages: [{sender: 'username', message: 'text', id: N},..], actions: [..]..
    //   }
    
    if (!SC.utils.isUndefined(actions.messages)){
        for(var msg in actions.messages){            
            SC.components.chat.appendMessage(actions.messages[msg]);	    
            
	    if(parseInt(actions.messages[msg].id) > SC.components.chat.lastMessageId){                
		SC.components.chat.lastMessageId = parseInt(actions.messages[msg].id);		
            }
        }
        SC.components.chat.messagesLayerParent.scrollTop = SC.components.chat.messagesLayerParent.scrollHeight;
    }
};

SC.components.chat.getActionRequestParams = function(){
    return 'lastmessageid='+SC.components.chat.lastMessageId;
};

SC.components.chat.appendMessage = function(o){
    var appendedText = '<div class="chatmessage"><b>'+o.sender+'</b>: '+o.message+'</div>'
    SC.components.chat.messagesLayer.innerHTML += appendedText;
};

// Callback for success
SC.components.chat.messageDelivered = function(o){
    //var m = YAHOO.lang.JSON.parse(o.responseText);
    //SC.components.chat.appendMessage({'sender' : m.sender, 'message' : m.message});
    YAHOO.util.Dom.get('chattextid').value = '';
}

// Callback for failure
SC.components.chat.messageDeliverFailed = function(o){
    //TODO
    alert('Message not delivered');
}

SC.components.chat.sendChatMessage = function(){
        var msg = YAHOO.util.Dom.get('chattextid').value;
        SC.components.chat.net.sendChatMessage(msg);
    }

SC.components.chat.initLayout = function(){
    var fnCallbackChattext = function(e) {
		var charCode = YAHOO.util.Event.getCharCode(e);
		if(charCode == 13)
            SC.components.chat.sendChatMessage();
	};
	YAHOO.util.Event.on('chattextid', 'keypress', fnCallbackChattext);
    
    var oChatSendButton = new YAHOO.widget.Button("chatsendb", { onclick: { fn: SC.components.chat.sendChatMessage } });
    YAHOO.util.Dom.setStyle('chattextid','height',YAHOO.util.Dom.getStyle('chatsendb','height'));
    
    // Focus on chat input text element
    YAHOO.util.Dom.get('chattextid').focus();
    SC.components.chat.messagesLayer = YAHOO.util.Dom.get('chatlist');
    SC.components.chat.messagesLayerParent = SC.components.chat.messagesLayer;
    //SC.components.chat.messagesLayerParent = YAHOO.util.Dom.getAncestorByTagName(SC.components.chat.messagesLayer,'div');
};

SC.components.chat.changePermissions = function(el){
    if(SCMoodle.MODERATOR){
        var canchat = (el.className == 'canchat')? 0: 1;
        var userid = el.id.replace('cpermissu','');
        SC.components.chat.net.changePermissions(userid, canchat);
    }
}