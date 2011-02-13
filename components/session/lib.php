<?php

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_session_get_actions($session,$user){
    global $USER, $CFG;
    
    $userlist = optional_param('userlist',false,PARAM_BOOL);
    if($userlist){
        // TODO - Constant or config
        $user->lastping = time();
        update_record('scollaboration_session_users',$user);
        
        $pingtime = time() - 30;
        $sql = "lastping > $pingtime AND sid = {$session->id} AND banned = 0";
        $userlist = array();
        if($users = get_records_select('scollaboration_session_users',$sql)){
            foreach($users as $u){
                $userlist[] = $u;
            }
        }
        
        return array('userlist'=>$userlist);
    }    
}

function scollaboration_session_process_request($session,$user){
    global $USER;

    return '';
}


?>