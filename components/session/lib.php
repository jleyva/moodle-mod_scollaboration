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

function scollaboration_session_get_actions($session,$scollaboration,$user){
    global $DB, $USER, $CFG;
    
    $userlist = optional_param('userlist',false,PARAM_BOOL);
    if($userlist){
        // TODO - Constant or config
        $user->lastping = time();
        $DB->update_record('scollaboration_session_users',$user);
        
        $pingtime = time() - 30;
        $sql = "lastping > ? AND sid = ? AND banned = ?";
        $userlist = array();
        if($users = $DB->get_records_select('scollaboration_session_users',$sql,array($pingtime,$session->id,0))){
            foreach($users as $u){
                $userdata = $DB->get_record('user',array('id' => $u->userid));
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
    global $DB, $USER;

    return '';
}


?>