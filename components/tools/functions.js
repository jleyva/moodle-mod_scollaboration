Namespace('SC.components.tools');

    
SC.components.tools.getMenuItems = function(moderator){

    
    var items = {
        text: "Tools", 
        submenu: {  
            id: "toolsmenu", 
            itemdata: [
                { text: "Polls", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
            ]
        }
        };
    
    return {'index': 60, 'items': items};
};    

SC.components.tools.getTab = function(moderator){

    var tab = new YAHOO.widget.Tab({
        label: 'Tools',
        content: '<div id="tooltab"></div>',
        active: false
    });
    
    return {'index': 50, 'tab': tab};
};   
    
SC.components.tools.initLayout = function(){
    return false;
};