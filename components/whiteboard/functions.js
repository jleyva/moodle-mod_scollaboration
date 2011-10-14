Namespace('SC.components.whiteboard');

SC.components.whiteboard.lastDrawingId = 0;
SC.components.whiteboard.paintwebLoaded = false;
SC.components.whiteboard.imgDraws = [];
SC.components.whiteboard.areaHeight = 0;
SC.components.whiteboard.currentFullCanvas = null;
SC.components.whiteboard.sendingFullCanvas = false;
SC.components.whiteboard.loadingImages = false;
SC.components.whiteboard.loadedImages = [];
SC.components.whiteboard.lastFullCanvasId = 0;

SC.components.whiteboard.canHideLoadingWindows = function(){
    return SC.components.whiteboard.paintwebLoaded;
};

// Actions is a JSON decoded object
SC.components.whiteboard.processActions = function(actions){
    // JSON 
    // { drawings: [{sender: 'username', url: 'http://...'},..],
    //   }
   
    // Sync full canvas means not to process any new drawing until is uploaded to the server
    if (SC.components.whiteboard.paintwebLoaded && !SC.components.whiteboard.sendingFullCanvas && !SC.utils.isUndefined(actions.drawings)){
        var i = 0;
        var tmpImg = null;
        var imgDraws = [];
        
        SC.components.whiteboard.loadedImages = [];
        
        // TODO - This does not create the images in the correct order, just in the order that are loaded by the browser
        var dlength = actions.drawings.length;
        for(var draw in actions.drawings){
            if(actions.drawings[draw].id == SC.components.whiteboard.lastFullCanvasId)
                continue;
            tmpImg = new Image();
            tmpImg.src = actions.drawings[draw].url+'&sesskey='+SCMoodle.SESSKEY;
            console.log('calling '+i+' total length; '+dlength);
            SC.components.whiteboard.addImgCanvas(tmpImg, i, dlength);
            SC.components.whiteboard.lastDrawingId = parseInt(actions.drawings[draw].id);
            i++;
        }
        
    }
};

SC.components.whiteboard.addImgCanvas = function(image, i, dlength){    
    image.onload = function(){
        SC.components.whiteboard.loadedImages[i] = image;
        console.log('adding '+i+' total: '+SC.components.whiteboard.loadedImages.length);
        if(SC.components.whiteboard.loadedImages.length == dlength){
            console.log('painting ...');
            for(var j =0; j < dlength; j++){
                if(typeof(SC.components.whiteboard.loadedImages[j] == 'image'))
                    SC.components.whiteboard.canvasLayer.context.drawImage(SC.components.whiteboard.loadedImages[j], 0, 0);                
            }            
            SC.components.whiteboard.loadedImages = [];
        }
    }
};

SC.components.whiteboard.getActionRequestParams = function(){
    if(SC.components.whiteboard.paintwebLoaded)
        return 'lastdrawingid='+SC.components.whiteboard.lastDrawingId;
    else
        return '';
};

// Callback for success
SC.components.whiteboard.paintSuccess = function(){
    //TODO
    
};

// Callback for failure
SC.components.whiteboard.paintFailure = function(){
    //TODO
    
};

// This is called from paintweb.js
SC.components.whiteboard.paintwebUpdated = function(dataURL){
    SC.components.whiteboard.net.paint(dataURL);
};

SC.components.whiteboard.paintFullCanvasRequestSuccess = function(o){
    var m = YAHOO.lang.JSON.parse(o.responseText);
    if(!SC.utils.isUndefined(m.drawid)){
        SC.components.whiteboard.lastFullCanvasId = parseInt(m.drawid);
        SC.components.whiteboard.net.paintFullCanvas(m.drawid,SC.components.whiteboard.currentFullCanvas);
        SC.components.whiteboard.sendingFullCanvas = false;
        setTimeout('SC.components.whiteboard.sendFullCanvas()',SCMoodle.FULLCANVAS_UPDATE);
    }
    else{
        SC.components.whiteboard.paintFullCanvasRequestFailure(o);
    }
};

SC.components.whiteboard.paintFullCanvasRequestFailure = function(o){
    SC.components.whiteboard.sendingFullCanvas = false;
    setTimeout('SC.components.whiteboard.sendFullCanvas()',5000);
};

SC.components.whiteboard.sendFullCanvas = function(){
    // No way to make working toDataURL function with excanvas or explorer
    if(! SCMoodle.OBSOLETE_IE){
        SC.components.whiteboard.currentFullCanvas = SC.components.whiteboard.canvasLayer.canvas.toDataURL('image/png');
    }    
    SC.components.whiteboard.sendingFullCanvas = true;
    SC.components.whiteboard.net.paintFullCanvasRequest();
};

SC.components.whiteboard.paintwebLoaded = function(){
     SC.components.whiteboard.paintwebLoaded = true;
     if(! SCMoodle.OBSOLETE_IE){
        SC.components.whiteboard.canvasLayer = pw.layer;
     }
     
    //alert($('.paintweb_viewport').height());
    $('.paintweb_viewport').height(600);
     
    setTimeout('SC.components.whiteboard.sendFullCanvas()',SCMoodle.FULLCANVAS_UPDATE);
};

SC.components.whiteboard.getMenuItems = function(moderator){

    
    var items = {
        text: "Whiteboard", 
        submenu: {  
            id: "whiteboardmenu", 
            itemdata: [
                { text: "Save", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }},
                { text: "Upload image", helptext: "", onclick: { fn: SC.utils.alert, obj: 'Not yet implemented' }}
            ]
        }
        };
    
    return {'index': 40, 'items': items};
};   

SC.components.whiteboard.initLayout = function(){
    // Paintweb
        
    // TODO, load default image using custom resolution
    var img    = document.getElementById('editableImage');
    
    if(! SCMoodle.OBSOLETE_IE){
        
        var target = document.getElementById('PaintWebTarget');
            
        pw = new PaintWeb();
        pw.config.guiPlaceholder = target;

        pw.config.imageLoad      = img;
        pw.config.configFile     = 'config.json';
        pw.init(SC.components.whiteboard.paintwebLoaded);
    }
    else{
        SC.components.whiteboard.canvasLayer = { canvas: YAHOO.util.Dom.getElementsByClassName('paintweb_layerCanvas')[0], context: YAHOO.util.Dom.getElementsByClassName('paintweb_layerCanvas')[0].getContext('2d')};        
        SC.components.whiteboard.paintwebLoaded();        
    }
};