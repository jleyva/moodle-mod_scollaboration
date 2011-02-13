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

if (! $session = get_record('scollaboration_sessions', 'id', $id)) {
    error('Session ID was incorrect');
}

// A session is completed when a moderator uses the Session -> Terminate link
if($session->completed){
    error('Session completed');
}

if(!confirm_sesskey())
    error('Invalid sesskey');

if (! $scollaboration = get_record('scollaboration', 'id', $session->scid)) {
    error('Collaboration ID was incorrect');
}
if (! $course = get_record('course', 'id', $scollaboration->course)) {
    error('Course is misconfigured');
}
if (! $cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id)) {
    error('Course Module ID was incorrect');
}

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/scollaboration:collaborate',$context);


$groupid = $session->groupid;

if($groupid && ! groups_is_member($groupid)){
    error('User is not member of the group selected');
}

// Register user
$user = get_record('scollaboration_session_users','sid',$id,'userid',$USER->id);
if(!$user){
    $moderator = has_capability('mod/scollaboration:moderate',$context);
    
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
    if(!$user->id = insert_record('scollaboration_session_users',$user))
        die;
}

if($user->banned){
    header('Content-type: application/json');
    echo json_encode(array('failure'=>get_string('userbanned','mod_scollaboration')));
}

$actions = optional_param('actions',false,PARAM_BOOL);
$component = optional_param('component','',PARAM_ALPHA);

if($actions){
    $actions = array();
    $plugins = get_list_of_plugins('components','',$CFG->dirroot.'/mod/scollaboration');
    
    foreach($plugins as $p){
        $libfile = $CFG->dirroot.'/mod/scollaboration/components/'.$p.'/lib.php';
        if(file_exists($libfile)){
            require_once($libfile);
            $function = 'scollaboration_'.$p.'_get_actions';
            if(function_exists($function)){
                $actions[$p] = $function($session,$user);
            }
        }
    }
    if(function_exists('json_encode')){
        header('Content-type: application/json');
        echo json_encode($actions);
    }
}

if($component){ 
    $libfile = $CFG->dirroot.'/mod/scollaboration/components/'.$component.'/lib.php';
    if(file_exists($libfile)){
        require_once($libfile);
        $function = 'scollaboration_'.$component.'_process_request';
        if(function_exists($function))
            $function($session,$user);
        die;
    }
}

?>