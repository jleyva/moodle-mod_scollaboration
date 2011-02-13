Namespace('SC.components.whiteboard');

SC.components.whiteboard.lastDrawingId = 0;
SC.components.whiteboard.imgDraws = [];

// Actions is a JSON decoded object
SC.components.whiteboard.processActions = function(actions){
    // JSON 
    // { drawings: [{sender: 'username', url: 'http://...'},..],
    //   }
   
    if (!YAHOO.lang.isUndefined(actions.drawings)){
        var i = 0;
        var tmpImg = null;
        var imgDraws = [];
        
        // TODO - This does not create the images in the correct order, just in the order that are loaded by the browser
        for(var draw in actions.drawings){
            tmpImg = new Image();
            tmpImg.src = actions.drawings[draw].url+'&sesskey='+SCMoodle.SESSKEY;
            SC.components.whiteboard.addImgCanvas(tmpImg);
            SC.components.whiteboard.lastDrawingId = actions.drawings[draw].id;
            i++;
        }
    }
};

SC.components.whiteboard.addImgCanvas = function(image, i){    
    image.onload = function(){
        SC.components.whiteboard.canvasLayer.context.drawImage(image, 0, 0);
    }
}

SC.components.whiteboard.getActionRequestParams = function(){
    return 'lastdrawingid='+SC.components.whiteboard.lastDrawingId;
};

// Callback for success
SC.components.whiteboard.paintSuccess = function(){
    //TODO
    
}

// Callback for failure
SC.components.whiteboard.paintFailure = function(){
    //TODO
    
}

// This is called from paintweb.js
SC.components.whiteboard.paintwebUpdated = function(dataURL){
    SC.components.whiteboard.net.paint(dataURL);
};

SC.components.whiteboard.initLayout = function(){
    // Paintweb
    
    var target = document.getElementById('PaintWebTarget');
    
    pw = new PaintWeb();
    pw.config.guiPlaceholder = target;
    // TODO, load default image using custom resolution
    var img    = document.getElementById('editableImage');
    pw.config.imageLoad      = img;
    pw.config.configFile     = 'config.json';
    pw.init();
    
    SC.components.whiteboard.canvasLayer = pw.layer;

};