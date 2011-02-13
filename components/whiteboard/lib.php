<?php

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_whiteboard_get_actions($session,$user){
    global $USER, $CFG;    
    
    $lastdrawid = optional_param('lastdrawingid',0,PARAM_INT);
    
    // Look for a full canvas
    if($lastdrawid == 0){
        if($maxid = get_record_sql("SELECT MAX(id) as id FROM {$CFG->prefix}scollaboration_whiteboard WHERE sid = {$session->id} AND fullcanvas = 1")){
            $lastdrawid = $maxid->id - 1;
        }
    }
    
    $response = array();
    $sql = "sid = {$session->id} AND id > {$lastdrawid} ORDER BY id ASC";
    $drawings = get_records_select('scollaboration_whiteboard',$sql);
    if($drawings){
        $timenow = time();
        foreach($drawings as $d){
            //TODO, improve use constant ...
            if($d->userid == $USER->id && $timenow - $d->timestamp < 30)
                continue;
            $r = new stdclass;
            $r->id = $d->id;
            $r->url = $CFG->wwwroot."/mod/scollaboration/requests.php?id={$session->id}&component=whiteboard&imageid={$d->id}";
            $response[] = $r;
        }
    }
    return array('drawings'=>$response);

}

function scollaboration_whiteboard_process_request($session,$user){
    global $USER;

    $png = optional_param('png',0,PARAM_RAW);    
    $imageid = optional_param('imageid',0,PARAM_INT);
    
    if($png){
        if($user->candraw){
            //header('Content-type: application/json');
            $regex = '/^data:([^;,]+);base64,(.+)$/';
            $matches = array();        
            if (preg_match($regex, $png, $matches)) {        
                $mimetype   = $matches[1];
                $base64data = $matches[2];
                $imgdataurl = null;
                $drawing = new stdclass();
                $drawing->sid = $session->id;
                $drawing->userid = $USER->id;
                $drawing->imgdata = $base64data;
                $drawing->fullcanvas = 0;
                $drawing->timestamp = time();
                
                if(insert_record('scollaboration_whiteboard',$drawing)){
                    echo json_encode(array('response'=>'success'));
                    exit;
                }                
            }
        }    
        // TODO: On failure, revert the whiteboard drawing made by the user. Inverting the colors for creating a transparency or similar
        echo json_encode(array('response'=>'failure'));
        exit;
    }
    
    if($imageid){
        if($drawing = get_record('scollaboration_whiteboard','id',$imageid,'sid',$session->id)){
            $imgdata = base64_decode($drawing->imgdata);        
            header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
            header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
            header('Pragma: no-cache');
            header('Content-Length: '.strlen($imgdata));
            header('Content-Type: image/png');
            echo $imgdata;
            exit;
        }
    }
    
    header('Content-type: application/json');
    echo json_encode(array('response'=>'failure'));
    exit;
    
}


?>