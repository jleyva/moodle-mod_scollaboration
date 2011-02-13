Namespace('SC.components.session.net');

SC.components.session.net.initNetworkCommunication = function(){
    SC.components.session.net.getActions();
};

SC.components.session.net.getActions = function(){
    var callback = {
            success: SC.components.session.net.getActionsSuccess,
            failure: SC.components.session.net.getActionsFailure
        };

    var params = SC.components.session.getCompActionsRequestParams();    
    var transaction = YAHOO.util.Connect.asyncRequest('GET', 'requests.php?id='+SCMoodle.SESSION_ID+'&sesskey='+SCMoodle.SESSKEY+'&actions=1'+params, callback, '');    
};

SC.components.session.net.getActionsSuccess = function(o){
    SC.components.session.processCompActions(o.responseText);
    if(SC.components.session.loadingOn)
        SC.components.session.hideLoadingWindow();
    setTimeout('SC.components.session.net.getActions()',SCMoodle.AJAX_POLLING_INT);
};

SC.components.session.net.getActionsFailure = function(o){
    setTimeout('SC.components.session.net.getActions()',SCMoodle.AJAX_POLLING_INT);
}