// Implementation for networking interface based on AJAX

Namespace('SC.components.whiteboard.net');


SC.components.whiteboard.net.paint = function(data){
        var callback = {
            success: SC.components.whiteboard.paintSuccess,
            failure: SC.components.whiteboard.paintFailure
        };
        var transaction = YAHOO.util.Connect.asyncRequest('POST', 'requests.php', callback, 'id='+SCMoodle.SESSION_ID+'&component=whiteboard&sesskey='+SCMoodle.SESSKEY+'&png='+encodeURIComponent(data));    
        
};

SC.components.whiteboard.net.paintFullCanvasRequest = function(data){
        var callback = {
            success: SC.components.whiteboard.paintFullCanvasRequestSuccess,
            failure: SC.components.whiteboard.paintFullCanvasRequestFailure
        };
        var transaction = YAHOO.util.Connect.asyncRequest('POST', 'requests.php', callback, 'id='+SCMoodle.SESSION_ID+'&component=whiteboard&sesskey='+SCMoodle.SESSKEY+'&fullcanvas=1');    
        
};

SC.components.whiteboard.net.paintFullCanvas = function(id,data){
        var transaction = YAHOO.util.Connect.asyncRequest('POST', 'requests.php', null, 'id='+SCMoodle.SESSION_ID+'&component=whiteboard&sesskey='+SCMoodle.SESSKEY+'&fullcanvas='+id+'&png='+encodeURIComponent(data));    
        
};
