Namespace('SC.components.session');

SC.components.session.layout = null;
SC.components.session.loadingOn = false;
SC.components.session.userListTime = 0;
SC.components.session.userList = null;
SC.components.session.userListDataTable = null;

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
        SC.components.session.userListDataTable.getRecordSet().replaceRecords(SC.components.session.userList); 
        SC.components.session.userListDataTable.render(); 
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
    SC.components.session.loadingPanel.show();
    SC.components.session.loadingOn = true;
};

SC.components.session.hideLoadingWindow = function(){
    var canHide = true;
    
    for(var comp in SC.components){
        if(! YAHOO.lang.isUndefined(SC.components[comp].canHideLoadingWindows)){            
            canHide = SC.components[comp].canHideLoadingWindows();
            if(! canHide){
                break;
            }    
        }
    } 
    if(canHide){
        SC.components.session.loadingPanel.hide();
        SC.components.session.loadingOn = false;
    }
    else{
        setTimeout('SC.components.session.hideLoadingWindow()',2000)
    }
}

SC.components.session.getMenuItems = function(moderator){

    var items = {
        text: "Session", 
        submenu: {  
            id: "sessionmenu", 
            itemdata: [
                [
                { text: "Raise hand", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                { 
                    text: "My status", 
                    submenu: { 
                        id: "status", 
                        itemdata: [
                            { text: "Away", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},                            { text: ":)", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                            { text: ":(", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                            { text: ":s", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                        ] 
                    } 
                },
                { 
                    text: "Layout", 
                    submenu: { 
                        id: "layout", 
                        itemdata: [
                            { text: "Left", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},                            { text: "Right", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                            { text: "Top", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                            { text: "Bottom", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                        ] 
                    } 
                },
               { text: "Leave session", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
                ]
            ] 
        }        
    };
    

    if(moderator){
        items.submenu.itemdata[1] = [];
        items.submenu.itemdata[1].push({ text: "Statistics", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }});
        items.submenu.itemdata[1].push({ text: "Terminate session", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }});        
    }
    
    return {'index': 10, 'items': items};
};

// Returns the list of users that is updated via the processActions method
SC.components.session.getListUsers = function() {
        return SC.components.session.userList;
    }
    
SC.components.session.initLayout = function(){

    var oUserActions = new YAHOO.widget.Button("menubuttonua", {  
	                                        type: "menu",  
	                                        menu: "menubuttonuaselect" });

    // Poll buttoms
    var oPollButtonOK = new YAHOO.widget.Button("userpollok", { onclick: { fn: SC.utils.alert } });
    var oPollButtonNOK = new YAHOO.widget.Button("userpollnot", { onclick: { fn: SC.utils.alert } });
                                                
    // List of users
    var usersTable = null;
     //Create the Column Definitions
    var myColumnDefs = [
        {key:'', formatter:YAHOO.widget.DataTable.formatCheckbox, width: 10 }, // use the built-in checkbox formatter 
        {key:"status", label:"<img alt=\"Status\" src='../../pix/s/martin.gif'>", sortable:true, width: 15, abbr: 'Status' },
        {key:"canspeak", label:"<img src='../../pix/f/audio.gif'>", sortable:true, width: 15 },
        {key:"canchat", label:"<img src='../../pix/i/feedback.gif'>", sortable:true, width: 15 },
        {key:"candraw", label:"<img src='../../lib/editor/tinymce/jscripts/tiny_mce/themes/advanced/images/image.gif'>", sortable:true, width: 15 },
        {key:"cansharedocs", label:"<img src='../../pix/f/pdf.gif'>", sortable:true, width: 15 },
        {key:"username", label:"User", sortable:true, maxAutoWidth: 100}
    ];
    
    //Create the datasource    
    var myDataSource = new YAHOO.util.FunctionDataSource(SC.components.session.getListUsers);
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY; 
    myDataSource.responseSchema = { 
        fields: ["canspeak","canchat","candraw","cansharedocs","username"] 
    }; 
    
    SC.components.session.userListDataTable = new YAHOO.widget.DataTable("userstable",
            myColumnDefs, myDataSource, { initialLoad: false, scrollable: true, height: '100px', width: '98%' });
    
    SC.components.session.userListDataTable.set('selectionMode','standard');

    // Context menubar
    
    // TODO - Overflows
    
    var oUserListItemData = [

        {
            text: "Enable", 
            submenu: { 
                id: "userpermissions", 
                itemdata: [
                    { text: "Audio/video", onclick: { fn: SC.utils.alert, obj: "#99cc66", checked: true } }, 
                    { text: "Chat", onclick: { fn: SC.utils.alert, obj: "#669933" } }, 
                    { text: "Dark Green", onclick: { fn: SC.utils.alert, obj: "#336600" } }
                ] 
            } 
        },
        {
            text: "Disable", 
            submenu: { 
                id: "userpermissions", 
                itemdata: [
                    { text: "Audio/video", onclick: { fn: SC.utils.alert, obj: "#99cc66", checked: true } }, 
                    { text: "Chat", onclick: { fn: SC.utils.alert, obj: "#669933" } }, 
                    { text: "Dark Green", onclick: { fn: SC.utils.alert, obj: "#336600" } }
                ] 
            } 
        },
        { text: "Delete all", onclick: { fn: SC.utils.alert } },
        { text: "New Ewe", onclick: { fn: SC.utils.alert } }

    ];    
    
    var myContextMenu = new YAHOO.widget.ContextMenu("userslistcontextmenu",
                {trigger:SC.components.session.userListDataTable.getTbodyEl(), itemdata: oUserListItemData, zindex: 10});
    
    myContextMenu.render("userstable");
    myContextMenu.clickEvent.subscribe(SC.utils.alert, SC.components.session.userListDataTable);
        
    SC.components.session.layout.on('resize', function(){
        if(SCMoodle.LAYOUT == 'left'){            
            SC.components.session.userListDataTable.set('width', parseInt(SC.components.session.layout.getUnitByPosition('left').getStyle('width')) - 20 +'px');
        }
    },SC.components.session.layout,true);        
    
};
