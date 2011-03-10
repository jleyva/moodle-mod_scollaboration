Namespace('SC.components.help');

    
SC.components.help.getMenuItems = function(moderator){

    
    var items = {
        text: "Help", 
        submenu: {  
            id: "helpmenu", 
            itemdata: [
                { text: "Online Help", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                { text: "About", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
            ]
        }
        };
    
    return {'index': 70, 'items': items};
};    
    
SC.components.help.initLayout = function(){
    return false;
};