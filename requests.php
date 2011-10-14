<?php

/**
 * AJAX Controller for all components and actions
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: requests.php
 * @package mod/scollaboration
 */

 // TODO
 // Improve performance. Uses a cache to avoid multiple SQL queries for each request
 // A user usually call this script every 2 seconds

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT); // session ID

if(!confirm_sesskey())
    scollaboration_json_error('Invalid sesskey',false);

if (! $session = $DB->get_record('scollaboration_sessions',array('id' =>  $id))) {
    scollaboration_json_error('Session ID was incorrect',false);
}

// A session is completed when a moderator uses the Session -> Terminate link
if($session->completed){
    scollaboration_json_error('Session completed',false);
}

if (! $scollaboration = $DB->get_record('scollaboration',array('id' =>  $session->scid))) {
    scollaboration_json_error('Collaboration ID was incorrect',false);
}
if (! $course = $DB->get_record('course',array('id' =>  $scollaboration->course))) {
    scollaboration_json_error('Course is misconfigured',false);
}
if (! $cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id)) {
    scollaboration_json_error('Course Module ID was incorrect',false);
}

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
if(! has_capability('mod/scollaboration:collaborate',$context)){
    scollaboration_json_error('No permissions',false);
}

$groupid = $session->groupid;

if($groupid && ! groups_is_member($groupid)){
    scollaboration_json_error('User is not member of the group selected');
}

// Register user
$user = $DB->get_record('scollaboration_session_users',array('sid' => $id,'userid' => $USER->id));
$moderator = has_capability('mod/scollaboration:moderate',$context);

if(!$user){
        
    $user = new stdclass;
    $user->sid = $id;
    $user->userid = $USER->id;
    $user->banned = 0;
    $user->canspeak = ($moderator)? 1 : $scollaboration->userscanspeak;
    $user->canchat = ($moderator)? 1 : $scollaboration->userscanchat;
    $user->candraw = ($moderator)? 1 : $scollaboration->userscandraw;
    $user->cansharedocs = ($moderator)? 1 : $scollaboration->userscansharedocs;
    // Lastping updated every 30 secs (see javascript files)
    $user->lastping = time();
    $user->timejoined = time();
    //TODO - Do something, display an error...
    
    if(!$user->id = $DB->insert_record('scollaboration_session_users',$user)){
        scollaboration_json_error('Problem connecting to database',false);
    }
    else{
        $action = new stdclass;
        $action->sid = $id;
        $action->component = 'notifications';
        $action->action = 'msg';
        // TODO add name
        $action->data = scollaboration_user_nick($scollaboration->usernameformat).' '.get_string('hasjoinedthechat','mod_scollaboration');
        $action->timestamp = time();
        $DB->insert_record('scollaboration_actions',$action);
    }
}

if($user->banned){
    scollaboration_json_error('userbanned');
}

$timenow = time();

// TODO - Add a constant for time
if($timenow - $user->lastping > 60){
    $action = new stdclass;
    $action->sid = $id;
    $action->component = 'notifications';
    $action->action = 'msg';
    // TODO add name
    $action->data = scollaboration_user_nick($scollaboration->usernameformat).' '.get_string('hasrejoinedthechat','mod_scollaboration');
    $action->timestamp = $timenow;
    $DB->insert_record('scollaboration_actions',$action);
}

$user->lastping = $timenow;
$DB->update_record('scollaboration_session_users',$user);

$user->moderator = $moderator;


$actionsid = optional_param('actions',false,PARAM_BOOL);
$component = optional_param('component','',PARAM_ALPHA);

if($actionsid){
    $actions = array();
    $plugins = get_list_of_plugins('components','',$CFG->dirroot.'/mod/scollaboration');
    
    foreach($plugins as $p){
        $libfile = $CFG->dirroot.'/mod/scollaboration/components/'.$p.'/lib.php';
        if(file_exists($libfile)){
            require_once($libfile);
            $function = 'scollaboration_'.$p.'_get_actions';
            if(function_exists($function)){
                $actions[$p] = $function($session,$scollaboration,$user);
            }
        }
    }
    
    // Get logged actions
    $sql = "sid = ? AND id > ? ORDER BY id ASC";
    if($loggedactions = $DB->get_records_select('scollaboration_actions',$sql,array($id,$actionsid))){
        foreach($loggedactions as $a){
            $actions[$a->component]['actions'][] = $a;
        }
    }    
    
    header('Content-type: application/json');
    echo json_encode($actions);
    die;

}

if($component){ 
    $libfile = $CFG->dirroot.'/mod/scollaboration/components/'.$component.'/lib.php';
    if(file_exists($libfile)){
        require_once($libfile);
        $function = 'scollaboration_'.$component.'_process_request';
        if(function_exists($function))
            $function($session,$scollaboration,$user);
        die;
    }
}
