Namespace('SC.components.resources');

    
SC.components.resources.getMenuItems = function(moderator){

    
    var items = {
        text: "Documents", 
        submenu: {  
            id: "documentsmenu", 
            itemdata: [
                { text: "Upload", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }}
            ]
        }
        };
    
    return {'index': 50, 'items': items};
};    

SC.components.resources.getTab = function(moderator){

    var tab = new YAHOO.widget.Tab({
        label: 'Docs',
        content: '<div id="docstab"></div>',
        active: false
    });
    
    return {'index': 40, 'tab': tab};
};
    
SC.components.resources.initLayout = function(){
    return false;
};