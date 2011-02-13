// Main Layout
(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;
 
    Event.onDOMReady(function() {
        // Show the Loading modal window        
        SC.components.session.initNetworkCommunication();
        
        var layout = new YAHOO.widget.Layout({
            units: [        
                { position: 'left', header: 'dddddddddd', width: 300, resize: false, gutter: '5px', collapse: true, collapseSize: 50, scroll: false, animate: true },
                { position: 'center', body: 'whiteboardlayer' }
            ]
        });
        layout.on('render', function() {
            layout.getUnitByPosition('left').on('close', function() {
                closeLeft();
            });

            var el = layout.getUnitByPosition('left').get('wrap');
            var layout2 = new YAHOO.widget.Layout(el, {
                parent: layout,
                minWidth: 280,
                minHeight: 740,
                units: [
                    { position: 'top',   body: 'userlistlayer', header: 'Users', height: 200, gutter: '2px', scroll: false, resize: false },
                    { position: 'center', body: 'chatlayer', header: 'Messages', gutter: '2px', scroll: false, resize: false},
                     { position: 'bottom', body: 'tooltabs', header: 'Tools', gutter: '2px', scroll: true, resize: true, height: 300}
                ]
            });
            
            layout2.on('render', function(){ 
                var leftUnitH = parseInt(layout.getUnitByPosition('left').getStyle('height'));
                var leftTopUnitH = parseInt(layout2.getUnitByPosition('top').getStyle('height'));
                var leftCenterUnitH = parseInt(layout2.getUnitByPosition('center').getStyle('height'));
                var leftBottomUnitH = parseInt(layout2.getUnitByPosition('bottom').getStyle('height'));
                var chatArea = parseInt(YAHOO.util.Dom.getStyle('chatlist','height'));
                
                if(leftUnitH > 750){
                    var newBotoomH = leftUnitH - leftTopUnitH - chatArea;
                    var newTopH = leftTopUnitH + leftCenterUnitH;
                    layout2.getUnitByPosition('bottom').setStyle('height',newBotoomH+''+'px');
                    layout2.getUnitByPosition('bottom').setStyle('top',newTopH+''+'px');
                }
                
                layout2.getUnitByPosition('bottom').subscribe('heightChange', function() {                     
                    var leftCenterUnitH = parseInt(layout2.getUnitByPosition('center').getStyle('height'));
                    var newH = leftCenterUnitH - 100;
                    YAHOO.util.Dom.setStyle('chatlist','height',newH+''+'px');
                    
                }, null, false );
                
            });

            layout2.render();            
            //layout2.getUnitByPosition('bottom').subscribe('heightChange', function() { console.log(layout2.getUnitByPosition('center').getStyle('height')); }, null, false );
            
            
            
        });
        layout.render();
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
        

    });   
 
})();