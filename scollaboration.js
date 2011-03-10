Namespace('SC.utils');

SC.utils.alert = function(text){
    window.alert(text);
};

SC.utils.confirm = function(text, callback){
    window.alert(text);
};

// Main Layout
(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;
 
    Event.onDOMReady(function() {
        // Show the Loading modal window        
        SC.components.session.initNetworkCommunication();
        
        SC.components.session.layout = new YAHOO.widget.Layout({
            units: [        
                { position: 'left', header: 'dddddddddd', width: 300, minWidth: 300, resize: true, gutter: '5px', collapse: true, collapseSize: 50, scroll: false, animate: true },
                { position: 'center', body: 'whiteboardlayer' }
            ]
        });
        SC.components.session.layout.on('render', function() {
            SC.components.session.layout.getUnitByPosition('left').on('close', function() {
                closeLeft();
            });

            var el = SC.components.session.layout.getUnitByPosition('left').get('wrap');
            var layout2 = new YAHOO.widget.Layout(el, {
                parent: SC.components.session.layout,
                minWidth: 280,
                minHeight: 740,
                units: [
                    { position: 'top',   body: 'userlistlayer', header: 'Users', height: 200, gutter: '2px', scroll: false, resize: false },
                    { position: 'center', body: 'chatlayer', header: 'Messages', gutter: '2px', scroll: false, resize: false},
                     { position: 'bottom', body: 'tooltabslayer', header: 'Tools', gutter: '2px', scroll: true, resize: true, height: 400}
                ]
            });
            
            layout2.on('render', function(){ 
                var leftUnitH = parseInt(SC.components.session.layout.getUnitByPosition('left').getStyle('height'));
                var leftTopUnitH = parseInt(layout2.getUnitByPosition('top').getStyle('height'));
                var leftCenterUnitH = parseInt(layout2.getUnitByPosition('center').getStyle('height'));
                var leftBottomUnitH = parseInt(layout2.getUnitByPosition('bottom').getStyle('height'));
                var chatArea = parseInt(YAHOO.util.Dom.getStyle('chatlist','height'));
                
                // More resolution = higher tabs area
                if(leftUnitH > 750){
                    var textareaLayerH = parseInt(YAHOO.util.Dom.getStyle('textarealayer','height'));
                    
                    var newBotoomH = leftUnitH - leftTopUnitH - chatArea;
                    chatArea = (chatArea < 100)? 100: chatArea;
                    var newTopH = leftTopUnitH + chatArea + textareaLayerH + 50;
                    layout2.getUnitByPosition('bottom').setStyle('height',newBotoomH+''+'px');
                    layout2.getUnitByPosition('bottom').setStyle('top',newTopH+''+'px');
                }
                
                layout2.getUnitByPosition('bottom').subscribe('heightChange', function() {                     
                    var leftCenterUnitH = parseInt(layout2.getUnitByPosition('center').getStyle('height'));
                    var newH = leftCenterUnitH - 80;
                    YAHOO.util.Dom.setStyle('chatlist','height',newH+''+'px');
                    
                }, null, false );
                
                //var toolTabs = new YAHOO.widget.TabView("tooltabslayer");
                
                // TODO - Set height whiteboard area
                
            });
            // Whiteboard Area

            SC.components.whiteboard.areaHeight = parseInt(SC.components.session.layout.getUnitByPosition('center').getStyle('height'));
                
            layout2.render();            
            //layout2.getUnitByPosition('bottom').subscribe('heightChange', function() { console.log(layout2.getUnitByPosition('center').getStyle('height')); }, null, false );            
            
        });
        SC.components.session.layout.render();
        //YAHOO.util.Dom.get('chatlist').Style.height = layout2.getUnitByPosition('center').height;
        //alert(layout2.getUnitByPosition('center').);
        //layout.on('render', function () {console.log(layout.getUnitByPositiong('center').getStyle('height'))});
                
         
        // TODO: Use a register pattern to avoid iteration over components without initLayout method
        for(var comp in SC.components){
            if(! YAHOO.lang.isUndefined(SC.components[comp].initLayout)){
                SC.components[comp].initLayout();  
            }
        }                
        
        // Displaying menu
        // TODO: Use a register pattern to avoid iteration over components without getMenuItems method
        var menuItemsUnsorted = [];
        
        for(var comp in SC.components){
            if(! YAHOO.lang.isUndefined(SC.components[comp].getMenuItems)){
                menuItemsUnsorted.push(SC.components[comp].getMenuItems(SCMoodle.MODERATOR));
            }
        }
        
        function sortMenuItems(a,b){
            return a.index - b.index;
        }        
        menuItemsUnsorted.sort(sortMenuItems);
        
        var menuItems = [];
        for(var el in menuItemsUnsorted){
            menuItems.push(menuItemsUnsorted[el].items);
        }
        
        // Building menu
        
        var oMenuBar = new YAHOO.widget.MenuBar("scmenubar", { 
                                                    lazyload: false, 
                                                    itemdata: menuItems,
                                                    position: 'static'
                                                   });
        /*
             Since this MenuBar instance is built completely from 
             script, call the "render" method passing in a node 
             reference for the DOM element that its should be 
             appended to.
        */
        oMenuBar.render(document.body);
        
        // Tab View
        
        var tabsUnsorted = [];
        
        var tabView = new YAHOO.widget.TabView();
        for(var comp in SC.components){
            if(! YAHOO.lang.isUndefined(SC.components[comp].getTab)){
                tabsUnsorted.push(SC.components[comp].getTab(SCMoodle.MODERATOR));
            }
        }
        
        function sortTabItems(a,b){
            return a.index - b.index;
        }        
        tabsUnsorted.sort(sortTabItems);
        
        var tabItems = [];
        for(var el in tabsUnsorted){
            tabView.addTab(tabsUnsorted[el].tab);
        }        
        tabView.appendTo('tooltabslayer'); 

    });   
 
})();