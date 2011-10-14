<?php

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_notifications_get_actions($session,$scollaboration,$user){
    global $DB, $USER, $CFG;
    
    $lastid = optional_param('notifications',0,PARAM_INT);
    $response = array();
    $sql = "SELECT id, data FROM {scollaboration_actions} WHERE component = ? AND sid = ? AND id > ? ORDER BY id ASC";
    $records = $DB->get_records_sql($sql,array('notifications',$session->id,$lastid));
    if($records){
        foreach($records as $r){
            $res = new stdclass;
            $res->id = $r->id;
            $res->sender = $r->data;
            $response[] = $r;
        }
    }
    return array('notifications'=>$response);
}

function scollaboration_notifications_process_request($session,$scollaboration,$user){
    global $USER;

    return '';
}


?>