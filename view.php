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
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record('course',array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $scollaboration = $DB->get_record('scollaboration',array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }

} else if ($a) {
    if (! $scollaboration = $DB->get_record('scollaboration',array('id' => $a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course',array('id' => $scollaboration->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('scollaboration', $scollaboration->id, $course->id)) {
        print_error('invalidcoursemodule');
    }

} else {
    print_error('missingparameter');
}

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

$PAGE->set_context($context);
$PAGE->set_url($CFG->wwwroot.'/mod/scollaboration/view.php?id='.$cm->id);

add_to_log($course->id, "scollaboration", "view", "view.php?id=$cm->id", "$scollaboration->id");

// TODO Check for json library

/// Print the page header
$strscollaborations = get_string('modulenameplural', 'scollaboration');
$strscollaboration  = get_string('modulename', 'scollaboration');


$PAGE->navbar->add($strscollaborations);
$PAGE->set_title(format_string($scollaboration->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

/// Print the main part of the page
echo $OUTPUT->container_start();

$groupmode    = groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm);     
groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/chat/view.php?id=$cm->id");              

// currentgroup = 0 means no groups, the sessions is shared between all users
if(!$groupmode || !$currentgroup)
    $currentgroup = 0;


if (has_capability('mod/scollaboration:collaborate',$context)) {
    /// Print the main part of the page
    echo $OUTPUT->box_start('generalbox', 'enterlink');
    
    echo $OUTPUT->heading(format_string($scollaboration->name));    
    
    $timenow = time();
    
    if($timenow > $scollaboration->scollaborationtime){ 
        // For a single Simple Collaboration instance, multiple sessions are posible
        if( $sessionid = scollaboration_sessionid($scollaboration, $currentgroup)){        
            $sessionurl = "$CFG->wwwroot/mod/scollaboration/session.php?id=$sessionid&groupid=$currentgroup";
        
            echo '<p>';
            echo html_writer::link($sessionurl,  get_string('entersession', 'scollaboration'), array('target'=>'_blank'));            
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

    echo $OUTPUT->box_end();

} else {
    echo $OUTPUT->box_start('generalbox', 'notallowenter');
    echo '<p>'.get_string('notallowenter', 'chat').'</p>';
    echo $OUTPUT->box_end();
}


echo $OUTPUT->container_end();

/// Finish the page
echo $OUTPUT->footer();

?>