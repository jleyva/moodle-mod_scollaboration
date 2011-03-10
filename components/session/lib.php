<?php

/**
 * Lib file for component
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: lib.php
 * @package mod/scollaboration
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

function scollaboration_session_get_actions($session,$scollaboration,$user){
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
                $userdata = get_record('user','id',$u->userid);
                $scollaboration->usernameformat =(!isset($scollaboration->usernameformat))? '': $scollaboration->usernameformat;
                switch($scollaboration->usernameformat){
                    case 'fullname': $u->username = fullname($userdata);
                                    break;
                    case 'firstname': $u->username = $userdata->firstname;
                                    break;
                    case 'lastname': $u->username = $userdata->lastname;
                                    break;     
                    case 'idnumber': $u->username = $userdata->idnumber;
                                    break;                                     
                    default: $u->username = $userdata->username;
                }
                $userlist[] = $u;
            }
        }
        
        return array('userlist'=>$userlist);
    }    
}

function scollaboration_session_process_request($session,$scollaboration,$user){
    global $USER;

    return '';
}


?>