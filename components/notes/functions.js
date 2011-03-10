Namespace('SC.components.notes');

// Function for add a Tab in the Tab Panel    
SC.components.notes.getTab = function(moderator){

    
    var tab = new YAHOO.widget.Tab({
        label: 'Notes',
        content: '<div id="notestab"></div>',
        active: false
    });
    
    return {'index': 30, 'tab': tab};
};    
    
SC.components.notes.initLayout = function(){
    return false;
};