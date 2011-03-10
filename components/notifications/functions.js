Namespace('SC.components.notifications');

    
SC.components.notifications.getTab = function(moderator){

    
    var tab = new YAHOO.widget.Tab({
        label: 'Notif.',
        content: '<div id="notificationstab"></div>',
        active: true
    });
    
    return {'index': 10, 'tab': tab};
};    
    
SC.components.notifications.initLayout = function(){
    return false;
};