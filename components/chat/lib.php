<?php

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_chat_get_actions($session,$user){
    global $USER, $CFG;
    
    $lastchatid = optional_param('lastmessageid',0,PARAM_INT);
    $response = array();
    $sql = "SELECT c.id, u.id as userid, u.username, c.message FROM {$CFG->prefix}scollaboration_chat c, {$CFG->prefix}user u WHERE c.userid = u.id AND c.sid = {$session->id} AND c.id > {$lastchatid} ORDER BY id ASC";
    $messages = get_records_sql($sql);
    if($messages){
        foreach($messages as $m){

            $r = new stdclass;
            $r->id = $m->id;
            $r->sender = $m->username;
            $r->message = $m->message;
            $response[] = $r;
        }
    }
    return array('messages'=>$response);
}

function scollaboration_chat_process_request($session,$user){
    global $USER;

    if($user->canchat){
        $message = optional_param('message','',PARAM_TEXT);
        
        if($message){
            $m = new stdclass;
            $m->sid = $session->id;
            $m->userid = $USER->id;
            $m->message = $message;
            $m->timestamp = time();
        }
        header('Content-type: application/json');
        if(insert_record('scollaboration_chat',$m)){
            echo json_encode(array('sender'=>$USER->username,'message'=>$m->message));
            die;
        }
    }
    // TODO add messages
    echo json_encode(array('response'=>'failure'));
}


?>