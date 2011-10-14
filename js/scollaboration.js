Namespace('SC.utils');

SC.getString = function(name){
    return name;
}

SC.utils.alert = function(text){
    window.alert(text);
};

SC.utils.confirm = function(text, callback){
    if(confirm(text))
        callback;
};

SC.utils.isUndefined = function(el){
 if(typeof(el) == "undefined")
    return true;
 return false;
};

// Main Layout

$(function() {
    // Show the Loading modal window        
    SC.components.session.initNetworkCommunication();
    
    // Creating the layouts
    SC.components.session.outerLayout = $("body").layout({west__minSize: 350, north__resizable: false,north__size: 50, north__showOverflowOnHover:	true});
    SC.components.session.innerLayout = $("#blockssection").layout({ north__size: 250,
                                                                   south__size: 300,
                                                                   north__resizable: false,
                                                                   center__resizable: false,
                                                                   south__resizable: false});        
     
     
    // TODO: Use a register pattern to avoid iteration over components without initLayout method
    for(var comp in SC.components){
        if(! SC.utils.isUndefined(SC.components[comp].initLayout)){                
            SC.components[comp].initLayout();  
        }
    }                
    
    // Displaying menu
    // TODO: Use a register pattern to avoid iteration over components without getMenuItems method
    var menuItemsUnsorted = [];
    
    for(var comp in SC.components){
        if(! SC.utils.isUndefined(SC.components[comp].getMenuItems)){
            //tmp
            if(comp == 'session')
                menuItemsUnsorted.push(SC.components[comp].getMenuItems(SCMoodle.MODERATOR));
        }
    }
    
    function sortMenuItems(a,b){
        return a.index - b.index;
    }        
    menuItemsUnsorted.sort(sortMenuItems);
    
    var menuItemsHtml = '';
    for(var el in menuItemsUnsorted){
        menuItemsHtml += menuItemsUnsorted[el].items;
    }
    
    // Building menu
    
    function select(event, ui) {                    
                    if(ui.item.text() == "leavesession")
                        self.close();
            }
    
    var menuhtml = '<ul id="bar1" class="menubar">'+menuItemsHtml+'</ul>';
    $("#scmenubar").html(menuhtml);       
    $("#bar1").menubar({                        
                    select: select
            });
   
    // Tab View
    
    var tabsUnsorted = [];
            
    for(var comp in SC.components){
        if(! SC.utils.isUndefined(SC.components[comp].getTab)){
            // tmp
            if(comp == 'notifications')
                tabsUnsorted.push(SC.components[comp].getTab(SCMoodle.MODERATOR));
        }
    }
    
    function sortTabItems(a,b){
        return a.index - b.index;
    }        
    tabsUnsorted.sort(sortTabItems);
            
    var navbar = '';
    var tabs = '';
    var i = 1;
    for(var el in tabsUnsorted){            
        navbar += '<li><a href="#tabs-'+i+'">'+tabsUnsorted[el].header+'</a></li>';
        tabs += '<div id="tabs-'+i+'">'+tabsUnsorted[el].content+'</div>';
        i++;
    }
    $( "#tooltabslayer" ).html('<ul>'+navbar+'</ul>'+tabs);
    $( "#tooltabslayer" ).tabs().find( ".ui-tabs-nav" ).sortable({ axis: "x" });

});   

