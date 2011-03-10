Namespace('SC.components.audiovideo');

    
SC.components.audiovideo.getTab = function(moderator){

    
    var tab = new YAHOO.widget.Tab({
        label: 'A/V',
        content: '<div id="audiovideotab"></div>',
        active: false
    });
    
    return {'index': 20, 'tab': tab};
};    
    
SC.components.audiovideo.initLayout = function(){
    return false;
};