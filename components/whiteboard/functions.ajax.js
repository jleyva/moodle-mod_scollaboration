// Implementation for networking interface based on AJAX

Namespace('SC.components.whiteboard.net');


SC.components.whiteboard.net.paint = function(data){
        var callback = {
            success: SC.components.whiteboard.paintSuccess,
            failure: SC.components.whiteboard.paintFailure
        };
        var transaction = YAHOO.util.Connect.asyncRequest('GET', 'requests.php?id='+SCMoodle.SESSION_ID+'&component=whiteboard&sesskey='+SCMoodle.SESSKEY+'&png='+encodeURIComponent(data), callback, '');    
        
};
