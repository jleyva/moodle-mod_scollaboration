Namespace('SC.components.session');

SC.components.session.layout = null;
SC.components.session.loadingOn = false;
SC.components.session.userListTime = 0;
SC.components.session.userList = null;

SC.components.session.initNetworkCommunication = function(){    
    SC.components.session.showLoadingWindow();
    SC.components.session.net.initNetworkCommunication();    
};


SC.components.session.getActionRequestParams = function(){
    // TODO - Constant or config or something
    var d = new Date();
    if(d.getTime() - SC.components.session.userListTime > 30){
        SC.components.session.userListTime = d.getTime();        
        return 'userlist=1';        
    }
    return '';
}

SC.components.session.processActions = function(actions){
    // JSON 
    // { userlist: [...],
    //   }
    
    if (!SC.utils.isUndefined(actions.userlist)){
        SC.components.session.userList = actions.userlist;
	var html = "";
	for(var el in actions.userlist){
	    html += "<p>"+actions.userlist[el].username+"</p>";
	}
	$("#userstable").html(html);
    }
};


SC.components.session.getCompActionsRequestParams = function(){
    var params = '';
    
    for(var comp in SC.components){
        if(! SC.utils.isUndefined(SC.components[comp].getActionRequestParams)){            
            params += '&'+SC.components[comp].getActionRequestParams();  
        }
    }    
    return params;
}

// Function called when actions are received from server (via AJAX or WebSockets)
// TODO: Use a register pattern to avoid iteration over components without processActions method
SC.components.session.processCompActions = function(jsonactions){
    var actions = YAHOO.lang.JSON.parse(jsonactions);
    for(var component in actions){
        var comp = eval('SC.components.'+component);
        if(! SC.utils.isUndefined(comp.processActions)){            
            comp.processActions(actions[component]);
        }
    }
};

SC.components.session.showLoadingWindow = function(){
    
    SC.components.session.loadingDialog = $('<div></div>')
	    .html('<div id="progressbar"></div>')
	    .dialog({
		    autoOpen: false,
		    modal: true,
		    resizable: false,
		    closeOnEscape: false,
		    open: function(event, ui) { $(".ui-dialog-titlebar-close").hide();},
		    height: 100,
		    title: SC.getString('loading')
	    });
    
    $(function() {
		$( "#progressbar" ).progressbar({
			value: 100
		});
	});    
    SC.components.session.loadingDialog.dialog('open');
    SC.components.session.loadingOn = true;
	    
};

SC.components.session.hideLoadingWindow = function(){
    var canHide = true;
        
    for(var comp in SC.components){
        if(! SC.utils.isUndefined(SC.components[comp].canHideLoadingWindows)){            
            canHide = SC.components[comp].canHideLoadingWindows();
            if(! canHide){
                break;
            }    
        }
    } 
    if(canHide){
        SC.components.session.loadingDialog.dialog('close');
    }
    else{
        setTimeout('SC.components.session.hideLoadingWindow()',2000)
    }
}

SC.components.session.getMenuItems = function(moderator){

    var items = '';
    
    //items += '<li><a href="#status">'+SC.getString('status')+'</a></li>';
    //items += '<li><a href="#raisehand">'+SC.getString('raisehand')+'</a></li>';
    items += '<li><a href="#leavesession">'+SC.getString('leavesession')+'</a></li>';    

    /*if(moderator){
        items += '<li><a href="#statistics">'+SC.getString('statistics')+'</a></li>';
	items += '<li><a href="#terminatesession">'+SC.getString('terminatesession')+'</a></li>';
    }*/
    
    items = '<li><a href="#session">'+SC.getString('session')+'</a><ul>'+items+'</ul></li>'
    
    return {'index': 10, 'items': items};
};

    
SC.components.session.initLayout = function(){

    
};
