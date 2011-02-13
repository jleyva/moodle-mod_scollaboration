Namespace('SC.components.session');

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
    
    if (!YAHOO.lang.isUndefined(actions.userlist)){
        SC.components.session.userList = actions.userlist;
    }
};


SC.components.session.getCompActionsRequestParams = function(){
    var params = '';
    
    for(var comp in SC.components){
        if(! YAHOO.lang.isUndefined(SC.components[comp].getActionRequestParams)){            
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
        if(! YAHOO.lang.isUndefined(comp.processActions)){            
            comp.processActions(actions[component]);
        }
    }
};

SC.components.session.showLoadingWindow = function(){
    
    SC.components.session.loadingPanel = new YAHOO.widget.Panel("wait",  
			{ width:"240px", 
			  fixedcenter:true, 
			  close:false, 
			  draggable:false, 
			  zindex:4,
			  modal:true,
			  visible:false
			} 
		);

    
    SC.components.session.loadingPanel.setHeader("Loading, please wait...");
    SC.components.session.loadingPanel.setBody('<img src="pix/loading.gif" />');
    SC.components.session.loadingPanel.render(document.body);
    setTimeout('SC.components.session.hideLoadingWindow()',5000);
    SC.components.session.loadingPanel.show();
    SC.components.session.loadingOn = true;
};

SC.components.session.hideLoadingWindow = function(){
    SC.components.session.loadingPanel.hide();
    SC.components.session.loadingOn = false;
}

SC.components.session.getMenuItems = function(moderator){

    var items = {
        text: "Session", 
        submenu: {  
            id: "sessionmenu", 
            itemdata: [

                { text: "Raise hand", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                { 
                    text: "My status", 
                    submenu: { 
                        id: "status", 
                        itemdata: [
                            { text: "Away", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},                            { text: ":)", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                            { text: ":(", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                            { text: ":s", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                        ] 
                    } 
                },
                { 
                    text: "Layout", 
                    submenu: { 
                        id: "layout", 
                        itemdata: [
                            { text: "Away", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},                                { text: ":)", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                            { text: ":(", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                            { text: ":s", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }},
                        ] 
                    } 
                },
               { text: "Leave session", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }}
            ] 
        }        
    };
    
    var items = {
        text: "Session", 
        submenu: {  
            id: "sessionmenu", 
            itemdata: [
                { text: "Leave session", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }}
            ]
        }
        };
    if(moderator){
        //items.submenu.itemdata.push({ text: "Statistics", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }});
        //items.submenu.itemdata.push({ text: "Terminate session", helptext: "", onclick: { fn: SC.components.session.hideLoadingWindow }});        
    }
    
    return {'index': 10, 'items': items};
};

// Returns the list of users that is updated via the processActions method
SC.components.session.getListUsers = function() {
        return SC.components.session.userList;
    }
    
SC.components.session.initLayout = function(){
    // List of users
    var usersTable = null;
     //Create the Column Definitions
    var myColumnDefs = [
        {key:'', formatter:YAHOO.widget.DataTable.formatCheckbox, width: 10 }, // use the built-in checkbox formatter 
        {key:"audiovideo", sortable:true, width: 15 },
        {key:"chat", sortable:true, width: 15 },
        {key:"whiteboard", sortable:true, width: 15 },
        {key:"user", sortable:true}
    ];
    
    //Create the datasource    
    var myDataSource = new YAHOO.util.FunctionDataSource(SC.components.session.getListUsers);


    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY; 
    myDataSource.responseSchema = { 
        fields: ["audiovideo","chat","whiteboard","user"] 
    }; 
    
    dataTable = new YAHOO.widget.DataTable("userstable",
            myColumnDefs, myDataSource, { scrollable: true, height: '150px', width: '94%' });
    
};
