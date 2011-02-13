<?php

/**
 * This page prints a particular instance of scollaboration
 *
 * @author  Juan Leyva <juanleyvadelgado@gmail.com>
 * @version $Id: view.php,
 * @package mod/scollaboration
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // scollaboration instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('scollaboration', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $scollaboration = get_record('scollaboration', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $scollaboration = get_record('scollaboration', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $scollaboration->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, "scollaboration", "view", "view.php?id=$cm->id", "$scollaboration->id");

/// Print the page header
$strscollaborations = get_string('modulenameplural', 'scollaboration');
$strscollaboration  = get_string('modulename', 'scollaboration');

$navlinks = array();
$navlinks[] = array('name' => $strscollaborations, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($scollaboration->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);


print_header_simple(format_string($scollaboration->name), '', $navigation, '', '', true,
update_module_button($cm->id, $course->id, $strscollaboration), navmenu($course, $cm));

/// Print the main part of the page
print_container_start();

$groupmode    = groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm);     
groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/chat/view.php?id=$cm->id");              

// currentgroup = 0 means no groups, the sessions is shared between all users
if(!$groupmode || !$currentgroup)
    $currentgroup = 0;


if (has_capability('mod/scollaboration:collaborate',$context)) {
    /// Print the main part of the page
    print_box_start('generalbox', 'enterlink');
    
    print_heading(format_string($scollaboration->name));    
    
    $timenow = time();
    
    if($timenow > $scollaboration->scollaborationtime){ 
        // For a single Simple Collaboration instance, multiple sessions are posible
        if( $sessionid = scollaboration_sessionid($scollaboration, $currentgroup)){        
            $sessionurl = "/mod/scollaboration/session.php?id=$sessionid&amp;groupid=$currentgroup";
        
            echo '<p>';
            link_to_popup_window ($sessionurl,
                    "session{$course->id}{$scollaboration->id}$currentgroup", get_string('entersession', 'scollaboration'), 800, 600, get_string('modulename', 'scollaboration'));
            echo '</p>';
        }
        else{
            echo '<p>';
            echo get_string('sessionfinished','scollaboration');
            echo '</p';
        }
    }    
    else if ($scollaboration->scollaborationtime and $scollaboration->schedule){
        $strnextsession = get_string('nextsession', 'scollaboration');
        echo "<p >$strnextsession: ".userdate($scollaboration->chattime).' ('.usertimezone($USER->timezone).')</p>';
    }
    
    // TODO, View old sessions

    print_box_end();

} else {
    print_box_start('generalbox', 'notallowenter');
    echo '<p>'.get_string('notallowenter', 'chat').'</p>';
    print_box_end();
}


print_container_end();

/// Finish the page
print_footer($course);

?>