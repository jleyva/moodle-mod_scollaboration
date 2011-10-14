<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_chat_get_actions($session,$scollaboration,$user){
    global $DB, $USER, $CFG;
    
    $lastchatid = optional_param('lastmessageid',0,PARAM_INT);
    $response = array();
    $sql = "SELECT c.id, u.id as userid, u.username, c.message FROM {scollaboration_chat} c, {user} u WHERE c.userid = u.id AND c.sid = ? AND c.id > ? ORDER BY id ASC";
    $messages = $DB->get_records_sql($sql,array($session->id,$lastchatid));
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

function scollaboration_chat_process_request($session,$scollaboration,$user){
    global $DB, $USER;
    
    $canchat = optional_param('canchat',-1,PARAM_INT);
    
    if($user->moderator && $canchat > -1){
        if($userid = optional_param('puserid', 0, PARAM_INT)){
            if(set_field('scollaboration_session_users','canchat',$canchat,'sid',$session->id,'userid',$userid)){
                header('Content-type: application/json');
                echo json_encode(array('status'=>'success'));
                die;
            }
            scollaboration_json_error();
        }        
    }

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
        if($DB->insert_record('scollaboration_chat',$m)){
            echo json_encode(array('sender'=>$USER->username,'message'=>$m->message));
            die;
        }
    }
    // TODO add messages
    echo json_encode(array('response'=>'failure'));
}
