Namespace('SC.components.skype');

    
SC.components.skype.getMenuItems = function(moderator){

    
    var items = {
        text: "Skype", 
        submenu: {  
            id: "skypemenu", 
            itemdata: [
                { text: "Call selected users", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
            ]
        }
        };
    
    return {'index': 20, 'items': items};
};    
    
SC.components.skype.initLayout = function(){
    return false;
};