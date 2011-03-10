Namespace('SC.components.options');

    
SC.components.options.getMenuItems = function(moderator){

    
    var items = {
        text: "Options", 
        submenu: {  
            id: "optionsmenu", 
            itemdata: [
                { text: "Preferences", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
            ]
        }
        };
    
    return {'index': 65, 'items': items};
};    
    
SC.components.options.initLayout = function(){
    return false;
};