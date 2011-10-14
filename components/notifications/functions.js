Namespace('SC.components.notifications');

SC.components.notifications.lastId = 0;
    
SC.components.notifications.getTab = function(moderator){
    return {'index': 10, 'header': 'Notif.', 'content' : '<div id="notificationstab"></div>'};
};    

SC.components.notifications.getActionRequestParams = function(){
     return 'notifications='+SC.components.notifications.lastId;       
}

SC.components.notifications.processActions = function(actions){
    // JSON 
    // { userlist: [...],
    //   }
    
    if (!SC.utils.isUndefined(actions.notifications)){
        var currentNotif = null;
        for(var nIndex in actions.notifications){
            currentNotif = actions.notifications[nIndex];
            var notifTab = YAHOO.util.Dom.get('notificationstab');
            notifTab.innerHTML = '<p>'+currentNotif.data + '</p>' +notifTab.innerHTML;
            SC.components.notifications.lastId = (parseInt(currentNotif.id) > SC.components.notifications.lastId)? parseInt(currentNotif.id) : SC.components.notifications.lastId;
        }
    }
};
    
SC.components.notifications.initLayout = function(){
    return false;
};