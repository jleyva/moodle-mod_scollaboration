Namespace('SC.components.resources');

SC.components.resources.lastId = 0;

SC.components.resources.test = function(){ 
    alert('a'); 
    };

    
SC.components.resources.getMenuItems = function(moderator){

    
    var items = {
        text: "Documents", 
        submenu: {  
            id: "documentsmenu", 
            itemdata: [
                { text: "Leave session", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }}
            ]
        }
        };
    
    return {'index': 20, 'items': items};
};    
    
SC.components.resources.initLayout = function(){
    return false;
};